<?php
/*
 * Controller/CompteController.php;
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

use EpargneBundle\Entity\EpargneCompte;
use EpargneBundle\Entity\EpargneLigneCompte;

use Ob\HighchartsBundle\Highcharts\Highchart;

use Doctrine\Common\Collections\ArrayCollection;

class CompteController extends Controller{

    public function listeAction(){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
                        /****** On liste tous les services ******/
                        // On récupère tous les services actuellement en BDD
                        $listeCompte = $em
                                ->getRepository('EpargneBundle:EpargneCompte')
                                ->findAll()
                        ;

                        return $this->render('EpargneBundle:Compte:liste.html.twig', array('listeCompte' => $listeCompte));
                }
                // Ici, $user est une instance de notre classe User mais n'est pas Admin
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));
                }
    }
    
    public function mescomptesAction(){
        // On récupère tous les comptes de l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $lstCompte = $em->getRepository('Thibautg16UtilisateurBundle:Utilisateur')->find($this->getUser()->getId());

        dump($lstCompte); exit;
    }    
    
        public function lignesValidationAction($idLigne){
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
			return $this->redirect($this->generateUrl('epargne_compte_lignes', array('idCompte' => $oLigne->getCompte()->getId())));               
                }
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));        
                }
        }
               
        public function lignesModifierAction($idLigne, Request $request){
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
                                ->add('type', 'choice', array('choices' => array('PRE' => 'PRE', 'VER' => 'VER', 'CB' => 'CB', 'VIR' => 'VIR')))
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
        
                                return $this->redirect($this->generateUrl('epargne_compte_lignes', array('idCompte' => $oLigne->getCompte()->getId())));
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
        
        public function GraphAction($idCompte){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
                        
                        //Calcul de la periode
                        $date = new \DateTime();
                        $finPeriode = $date->format('Y-m-d');
                        $finTplPeriode = $date->format('d-m-Y');
                        $debPeriode = $date->sub(new \DateInterval('P1M'))->format('Y-m-d');
                        $debTplPeriode = $date->format('d-m-Y');
                                                
                        //On recupére toutes les valeurs pour le compte
                        $oValeurs = $em
                                ->getRepository('EpargneBundle:EpargneLigneCompte')
                                ->myTotalGainColone($idCompte, 'type', $debPeriode, $finPeriode);                                                  
                        
                        foreach($oValeurs as $valeur){
                                $data[] = floatval($valeur['1']);
                                $x[]    =  $valeur['type'];
                        }     
                        
                        // Chart
                        $serie = array(
                                array("name" => "Type", "data" => $data)
                        );
                                                                        
                        //Graphique evolution gain %
                        $ob = new Highchart();
                        $ob->chart->renderTo('chart');
                        $ob->chart->type('column');
                        $ob->chart->inverted(true);
                        $ob->chart->zoomType('x');
                        $ob->title->text('Type Mouvements');
                        //$ob->xAxis->title(array('text'  => 'type'));
                        $ob->xAxis->categories($x);
                        $ob->tooltip->useHTML(TRUE);
                        $ob->tooltip->headerFormat('<table><tr><td style="color: {series.color}">{point.key}: </td></tr>');
                        $ob->tooltip->pointFormat(' <tr><td style="text-align: right"><b>{point.y} €</b></td></tr>');
                        $ob->tooltip->footerFormat('</table>');
                        $ob->yAxis->title(array('text'  => "Type Mouvements (€)"));
                        $ob->series($serie);	                              
                                        
                        return $this->render('EpargneBundle:Compte:graph.html.twig', array('ob' => $ob, 'idCompte' => $idCompte, 'graph' => 'type', 'debPeriode' => $debTplPeriode, 'finPeriode' => $finTplPeriode)); 		
	        }
                // Ici, $user est une instance de notre classe User mais n'est pas Admin
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));
                }
        }    

        public function GraphAjaxAction(){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'user' (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
                        //Récupération des variables $_POST
                        $request = Request::createFromGlobals();
                        $idCompte = $request->request->get('idCompte');
                        $graph = $request->request->get('graph', 'type');     
                        $debPeriode = $request->request->get('debPeriode');  
                        $finPeriode = $request->request->get('finPeriode');                            
                        
                        //Calcul de la periode
                        $date = \DateTime::CreateFromFormat('j-m-Y', $finPeriode);
                        $finPeriode = $date->format('Y-m-d');
                        $date = \DateTime::CreateFromFormat('j-m-Y', $debPeriode);
                        $debPeriode = $date->format('Y-m-d');
                                                
                        //On recupére toutes les valeurs pour le compte
                        $oValeurs = $em
                                ->getRepository('EpargneBundle:EpargneLigneCompte')
                                ->myTotalGainColone($idCompte, $graph, $debPeriode, $finPeriode);                                                        
                        
                        foreach($oValeurs as $valeur){
                                $data[] = floatval($valeur['1']);
                                $x[]    =  $valeur[$graph];
                        }     
                        
                        // Chart
                        $serie = array(
                                array("name" => $graph, "data" => $data)
                        );
                                                                        
                        //Graphique evolution gain %
                        $ob = new Highchart();
                        $ob->chart->renderTo('chart');
                        $ob->chart->type('column');
                        $ob->chart->inverted(true);
                        $ob->chart->zoomType('x');
                        $ob->title->text($graph.' Mouvements');
                        //$ob->xAxis->title(array('text'  => 'type'));
                        $ob->xAxis->categories($x);
                        $ob->tooltip->useHTML(TRUE);
                        $ob->tooltip->headerFormat('<table><tr><td style="color: {series.color}">{point.key}: </td></tr>');
                        $ob->tooltip->pointFormat(' <tr><td style="text-align: right"><b>{point.y} €</b></td></tr>');
                        $ob->tooltip->footerFormat('</table>');
                        $ob->yAxis->title(array('text'  => $graph." Mouvements (€)"));
                        $ob->series($serie);	                              
                                        
                        return $this->render('EpargneBundle:Compte:graph_ajax.html.twig', array('ob' => $ob, 'graph' => $graph)); 		
	        }
                // Ici, $user est une instance de notre classe User mais n'est pas Admin
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));
                }
        }       
}
