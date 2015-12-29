<?php
/*
 * Controller/LigneController.php;
 *
 * Copyright 2015 GILLARDEAU Thibaut (aka Thibautg16)
 *
 * Authors :
 *  - Gillardeau Thibaut (aka Thibautg16)
 */

namespace EpargneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use EpargneBundle\Entity\EpargneCompte;
use EpargneBundle\Entity\EpargneLigneCompte;

use Ob\HighchartsBundle\Highcharts\Highchart;

use Doctrine\Common\Collections\ArrayCollection;

class LigneController extends Controller{

        public function ajouterAction($idCompte, Request $request){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){         
        	       // Creation de l'objet Ligne
		      $oLigne = new EpargneLigneCompte();
		
		      // On récupére les informations concernant le compte
		      $oCompte = $em->getRepository('EpargneBundle:EpargneCompte')->find(intval($idCompte));     
		
		      // On associe la ligne au compte
                      $oLigne->setCompte($oCompte);
		
                      // On crée le FormBuilder grâce au service form factory
                      $formBuilder = $this->get('form.factory')->createBuilder('form', $oLigne);
                        
                      // On ajoute les champs de l'entité que l'on veut à notre formulaire
                      $formBuilder
                              ->add('dateOperation', 'date', array('widget' => 'choice', 'input' => 'datetime', 'format' => "dd MM yyyy"))
                              ->add('dateValeur', 'date', array('widget' => 'choice', 'input' => 'datetime', 'format' => "dd MM yyyy"))
                              ->add('libelle', 'text')
                              ->add('type', 'choice', array('choices' => array('PRE' => 'PRE', 'VER' => 'VER', 'CB' => 'CB', 'VIR' => 'VIR')))
                              ->add('designation', 'text')
                              ->add('montant', 'number')
                              ->add('refBanque', 'text')
                              ->add('Ajouter', 'submit')
                      ;

                     // À partir du formBuilder, on génère le formulaire
                     $form = $formBuilder->getForm();
                        		
                      // On fait le lien Requête <-> Formulaire
                      $form->handleRequest($request);
                        
                      if ($form->isValid()) {
                              $oLigne->setValider(FALSE);
                              $oLigne->setSolde(NULL);
                              $em->persist($oLigne);
                              $em->flush();
                                
                              $request->getSession()->getFlashBag()->add('succes', 'Ligne : '.$oLigne->getId().' ajoutée avec succés');
        
                              return $this->redirect($this->generateUrl('epargne_compte_lignes', array('idCompte' => $oLigne->getCompte()->getId())));
                      }
                      
		      return $this->render('EpargneBundle:Lignes:ajouter.html.twig', array('form' => $form->createView(), 'oLigne' => $oLigne));
                }
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));        
                }   
       }
          
    public function listeAction($idCompte){
        $em = $this->getDoctrine()->getManager();
        // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
        if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
                /****** On liste toutes les lignes du compte ******/
                // On récupère tous les services actuellement en BDD
                $listeLignes = $em
                        ->getRepository('EpargneBundle:EpargneLigneCompte')
                        ->findBy(array('compte' => intval($idCompte)), array('id' => 'DESC'), 200, NULL)
                ;
                
                // On récupére les informations concernant le compte
		$oCompte = $em->getRepository('EpargneBundle:EpargneCompte')->find(intval($idCompte));   
                
    	        /* On récupère la ligne qui pourra être valider */
    	        $oValider = $em->getRepository('EpargneBundle:EpargneLigneCompte')->findOneBy(array('compte' => $idCompte, 'valider' => FALSE), array('dateOperation' => 'ASC','id' => 'ASC'), 1, NULL);                
                
                if(is_object($oValider)){
                        $valider = $oValider->getId();
                }
                else{
                        $valider = FALSE;
                }
                return $this->render('EpargneBundle:Lignes:lister.html.twig', array('listeLignes' => $listeLignes, 'valider' => $valider, 'oCompte' => $oCompte));
        }
        // Ici, $user est une instance de notre classe User mais n'est pas Admin
        else{
                return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));
        }
    }
    
        public function validerAction($idLigne){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
 		        // On recupére la ligne
                        $oLigne = $em->getRepository('EpargneBundle:EpargneLigneCompte')->find(intval($idLigne)); 
                        
                        // On récupère la ligne qui devrait être validé //
    	                $oValider = $em->getRepository('EpargneBundle:EpargneLigneCompte')->findOneBy(array('compte' => $oLigne->getCompte(), 'valider' => FALSE), array('dateOperation' => 'ASC','id' => 'ASC'), 1, NULL);  
                         
                        if($oLigne->getId() == $oValider->getId()){
                                /** On calcul le solde **/
                                // On récupére le solde de la ligne précédente 
                                $oSolde = $em->getRepository('EpargneBundle:EpargneLigneCompte')->findOneBy(array('compte' => $oLigne->getCompte(), 'valider' => TRUE), array('dateOperation' => 'DESC','id' => 'DESC'), 1, NULL);
                                
                                // On calcul le nouveau solde à partir du solde précédent
                                if(is_object($oSolde)){
                                        $oLigne->setSolde($oSolde->getSolde() + $oLigne->getMontant());                                        
                                }
                                // C'est la toute premiére ligne pour ce compte
                                else{
                                        $oLigne->setSolde($oLigne->getMontant());
                                }
                                  
                                // On valide la ligne
                                $oLigne->setValider(TRUE);
                                $em->persist($oLigne);	  
                                $em->flush();
                        
                                 $this->container->get('session')->getFlashBag()->add('success', 'Ligne N°'.$oLigne->getId().' validée avec succès.');
                        }
                        else{
                                 $this->container->get('session')->getFlashBag()->add('error', 'Erreur durant la validation de la ligne N°'.$oLigne->getId());        
                        }
                        
			// On redirige vers la liste des lignes
			return $this->redirect($this->generateUrl('epargne_ligne_liste', array('idCompte' => $oLigne->getCompte()->getId())));               
                }
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));        
                }
        }
        

        
        public function modifierAction($idLigne, Request $request){
                 $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){               
                        // On récupére les informations concernant la ligne à modifier
                        $oLigne = $em->getRepository('EpargneBundle:EpargneLigneCompte')->find($idLigne);
                        
                        // On crée le FormBuilder grâce au service form factory
                        $formBuilder = $this->get('form.factory')->createBuilder('form', $oLigne);
                        
                        // On ajoute les champs de l'entité que l'on veut à notre formulaire
                        $formBuilder
                                ->add('dateOperation', 'date', array('widget' => 'choice', 'input' => 'datetime', 'format' => "dd MM yyyy"))
                                ->add('dateValeur', 'date', array('widget' => 'choice', 'input' => 'datetime', 'format' => "dd MM yyyy"))
                                ->add('libelle', 'text')
                                ->add('type', 'choice', array('choices' => array('PRE' => 'PRE', 'VER' => 'VER', 'CB' => 'CB', 'VIR' => 'VIR', 'CHQ' => 'CHQ')))
                                ->add('designation', 'text')
                                ->add('refBanque', 'text')
                                ->add('Modifier', 'submit')
                        ;
                        
                        if($oLigne->getValider() == FALSE){
                                $formBuilder->add('montant', 'number');
                        }	
                        
                        // À partir du formBuilder, on génère le formulaire
                        $form = $formBuilder->getForm();
                                
                        // On fait le lien Requête <-> Formulaire
                        // À partir de maintenant, la variable $produit contient les valeurs entrées dans le formulaire par le visiteur
                        $form->handleRequest($request);
                        
                        if ($form->isValid()) {                                
                                $em->persist($oLigne);
                                $em->flush();
                                
                                $request->getSession()->getFlashBag()->add('notice', 'Ligne : '.$oLigne->getId().' modifée avec succés');
                                
                                return $this->redirect($this->generateUrl('epargne_ligne_liste', array('idCompte' => $oLigne->getCompte()->getId())));
                        }
                        
                        // À ce stade, le formulaire n'est pas valide car :
                        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
                        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
                        return $this->render('EpargneBundle:Lignes:modifier.html.twig', array('form' => $form->createView(), 'oLigne' => $oLigne));              
                }
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));        
                }               
        }
        
        public function importerAction($idCompte, Request $request){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){                   
                        // Creation du formulaire 
                        $form = $this->createFormBuilder()
                                ->add('submitFile', 'file', array('label' => 'File to Submit'))
                                ->getForm();
                                
                        // On fait le lien Requête <-> Formulaire        
                        $form->handleRequest($request);
                        
                        //Si le formulaire est valide
                        if ($form->isValid()) {
                                $fichierUpload = $form['submitFile']->getData();                              
                               
                                $row = 1;
                                if (($handle = fopen($fichierUpload->getPathName(), "r")) !== FALSE) {
                                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                                //On récupére le nombre de champs dans la ligne 
                                                $num = count($data);
                                                
                                                // On regarde la premiére ligne, qui doit contenir le nom des colones et on vérifie que tout est "ok"
                                                if($row == 1){
                                                        // Action a appeler suivant le champ présent dans le CSV
                                                        $colones = array('Date opération' => 'setDateOperation', 'Date Valeur' => 'setDateValeur', 'Libellé' => 'setLibelle', 'Référence' => 'setRefBanque', 'Montant' => 'setMontant');    
                                                        for ($c=0; $c < $num; $c++) {
                                                                $csvColones[] = $colones[utf8_encode($data[$c])];
                                                        }                                           
                                                }
                                                else{
                                                        // Creation de l'objet Ligne
                                                        $oLigne = new EpargneLigneCompte();
                                                                                                
                                                        for ($c=0; $c < $num; $c++) {
                                                                if(!empty($data[$c])){
                                                                        if(stripos($csvColones[$c], 'Date') !== FALSE){
                                                                                //On format la date
                                                                                $date = \DateTime::CreateFromFormat('d/m/y', $data[$c]);
                                                                                $oLigne->$csvColones[$c]($date);        
                                                                        }
                                                                        elseif(stripos($csvColones[$c], 'Montant') !== FALSE){
                                                                                $oLigne->$csvColones[$c](floatval(str_replace(',', '.', $data[$c])));
                                                                        }                                                                        
                                                                        else{
                                                                                $oLigne->$csvColones[$c](utf8_encode($data[$c]));
                                                                        }
                                                                }
                                                        }
                                                        
                                                        // On compléte l'objet avant de l'enregistrer
                                                        $oLigne->setValider(FALSE);
                                                        $oLigne->setSolde(NULL);
                                                        $oLigne->setType($oLigne->getLibelle());    
                                                        $oLigne->setDesignation($oLigne->getLibelle()); 
                                                        
                                                        // On récupére les informations concernant le compte
                                                        $oCompte = $em->getRepository('EpargneBundle:EpargneCompte')->find(intval($idCompte));     
                        
                                                        // On associe la ligne au compte
                                                        $oLigne->setCompte($oCompte);
                                                        
                                                        // On vérifie que la ligne n'est pas déjà présente dans la BDD
                                                        $oVerifLigne = $em->getRepository('EpargneBundle:EpargneLigneCompte')->findBy(array('libelle' => $oLigne->getLibelle(),
                                                                                                                                             'montant' => $oLigne->getMontant(),
                                                                                                                                             'dateOperation' => $oLigne->getDateOperation()));
                                         
                                                        if(!empty($oVerifLigne)){
                                                                $request->getSession()->getFlashBag()->add('error', 'La ligne : '.$oLigne->getLibelle().' existe déjà');        
                                                        }
                                                        else{
                                                                // On persiste la ligne
                                                                $em->persist($oLigne);	  
                                                                $em->flush();
                                                                                                                                
                                                                $request->getSession()->getFlashBag()->add('succes', 'La ligne : '.$oLigne->getLibelle().' a été ajoutée avec succès');
                                                        }                                                                                             
                                                }                                                 
                                                
                                                // On passe à la ligne suivante
                                                $row++;
        
                                        }
                                fclose($handle);                                                             
        
                                return $this->redirect($this->generateUrl('epargne_ligne_liste', array('idCompte' => $oLigne->getCompte()->getId())));                                
                                }                               
                        }
                        
                        // À ce stade, le formulaire n'est pas valide car :
                        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
                        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
                        return $this->render('EpargneBundle:Lignes:importer.html.twig', array('form' => $form->createView()));    
                }
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));        
                }    
        }
}