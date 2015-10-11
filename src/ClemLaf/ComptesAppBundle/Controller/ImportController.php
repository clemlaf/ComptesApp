<?php

namespace ClemLaf\ComptesAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Entree;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Category;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Compte;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic;
use Symfony\Component\HttpFoundation\Response;

class ImportController extends Controller
{
    public function indexAction($name)
    {
	
	$id=mysql_connect('localhost',$_SESSION['usr'],$_SESSION['pass']);
	mysql_select_db('comptes',$id);
	$em=$this->getDoctrine()->getManager();

        return $this->render('ClemLafComptesAppBundle:Default:index.html.twig', array('name' => $name));
    }
    public function catAction($name)
    {
	$em=$this->getDoctrine()->getManager();
	$id=mysql_connect('localhost',$_SESSION['usr'],$_SESSION['pass']);
	mysql_select_db('comptes',$id);
	$res=mysql_query('SELECT c_nam FROM categories');
	while($res && $tab=mysql_fetch_row($res)){
	    $new=new Category();
	    $new->setCnam(tab[0]);
	    $em->persist($new);
	}
	$em->flush();
        return $this->render('ClemLafComptesAppBundle:Default:index.html.twig', array('name' => $name));
    }
}
