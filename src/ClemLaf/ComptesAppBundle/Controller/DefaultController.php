<?php

namespace ClemLaf\ComptesAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ClemLafComptesAppBundle:Default:index.html.twig', array('name' => $name));
    }
}
