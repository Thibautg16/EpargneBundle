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
                //On ne controle pas l'accés, c'est une page public
                return $this->render('EpargneBundle:Epargne:accueil.html.twig');
        }
}