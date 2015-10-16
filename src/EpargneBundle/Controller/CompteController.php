<?php
/*
 * Controller/CompteController.php;
 *
 * Copyright 2015 GILLARDEAU Thibaut (aka Thibautg16)
 *
 * Authors :
 *  - Gillardeau Thibaut (aka Thibautg16)
 */

namespace Thibautg16\CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Thibautg16\CompteBundle\Entity\Compte;

use Doctrine\Common\Collections\ArrayCollection;

class CompteController extends Controller{

        public function listeAction(){
                $em = $this->getDoctrine()->getManager();
                // On vérifie si l'compte (via les groupes) est autorisé à consulter cette page
                if($em->getRepository('Thibautg16CompteBundle:Groupe')->GroupeAutoriseRoute($this->getUser(), $this->container->get('request')->get('_route')) == TRUE){
                        /****** On liste tous les services ******/
                        // On récupère tous les services actuellement en BDD
                        $listeCompte = $em
                                ->getRepository('Thibautg16CompteBundle:Compte')
                                ->findAll()
                        ;

                        return $this->render('Thibautg16CompteBundle:Compte:liste.html.twig', array('listeCompte' => $listeCompte));
                }
                // Ici, $user est une instance de notre classe User mais n'est pas Admin
                else{
                        return $this->redirect($this->generateUrl('thibautg16_compte_homepage'));
                }
    }
}
