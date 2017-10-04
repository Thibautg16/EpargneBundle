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

class EpargneController extends Controller{

        public function accueilAction(){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'utilisateur (via les groupes) est autorisé à administrer "les utilisateurs"
                if($em->getRepository('Thibautg16UtilisateurBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), "thibautg16_utilisateur_liste") == TRUE){    
                    return $this->render('EpargneBundle:Epargne:accueil.html.twig', array('admin' => TRUE));                
                }
                else{
                    return $this->render('EpargneBundle:Epargne:accueil.html.twig', array('admin' => FALSE));
                }    
        }
}