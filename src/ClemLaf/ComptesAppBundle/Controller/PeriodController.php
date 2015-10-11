<?php
namespace ClemLaf\ComptesAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Entree;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Category;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Moyen;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Compte;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic;
use Symfony\Component\HttpFoundation\Request;
use ClemLaf\ComptesAppBundle\Form\Type\PeriodicType;
use Symfony\Component\HttpFoundation\Response;

class PeriodController extends Controller{

    public function indexAction(Request $request){
	$em=$this->getDoctrine()->getManager();
	$comptes=$em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->findAll();
	$categories=$em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->findAll();
	$moyens=$em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->findAll();
	$cp_cl=MainController::getChoicesList($comptes,'Compte');
	$ca_cl=MainController::getChoicesList($categories,'Category');
	$mo_cl=MainController::getChoicesList($moyens,'Moyen');
	$newperiod=new Periodic();
	$periods=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->findAll();
	return $this->render('ClemLafComptesAppBundle:Comptes:tab_perio.html.twig',
	    array('periodics'=> $periods,
	    'categories'=>$ca_cl[1],
	    'moyens'=>$mo_cl[1],
	    'comptes'=>$cp_cl[1],
	    'newper'=> $newperiod,
	)
    );
    }

    public function updateAction(Request $request){
	$em=$this->getDoctrine()->getManager();
	$id=$request->request->get('id');
	if ($id!=null  && $id!='new'){
	    $new=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->find($id);
	}else
	    $new= new Periodic();
	$dadate=date_create_from_format('d/m/Y',$request->request->get('last_date'));
	if(!$dadate){
	    $dadate=date_create();
	}
	$new->setLastDate($dadate);
	$dadate=date_create_from_format('d/m/Y',$request->request->get('end_date'));
	if(!$dadate){
	    $dadate=date_create('5000-01-01');
	}
	$new->setEndDate($dadate);
	$new->setMois(intval($request->request->get('mois')));
	$new->setJours(intval($request->request->get('jours')));
	$new->setCpS($em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->find(intval($request->request->get('cp_s'))));//parseint
	$new->setCpD($em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->find(intval($request->request->get('cp_d'))));//parseint
	$new->setCategory($em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->find(intval($request->request->get('cat'))));//parseint
	$new->setCom($request->request->get('com'));//texte
	$new->setMoyen($em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->find(intval($request->request->get('moy'))));//parseint
	$new->setPrix(intval(floatval($request->request->get('pr'))*100));
	$em->persist($new);
	$em->flush();
	$response = new Response(
		$new->getId(),
		Response::HTTP_OK,
		array('content-type' => 'text/html'));
	return $response;
    }

    public function deleteAction(Request $request){
	$em=$this->getDoctrine()->getManager();
	$id=$request->request->get('id');
	$del=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->find($id);
	$em->remove($del);
	$em->flush();
	$response = new Response(
		    'ok',
		        Response::HTTP_OK,
			    array('content-type' => 'text/html'));
	return $response;
    }
}
?>
