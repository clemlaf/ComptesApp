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

class ParamController extends Controller{
  public function indexAction(Request $request){
    $em=$this->getDoctrine()->getManager();
    $dum_comp=new Compte();
    $form_comp=$this->createFormBuilder($dum_comp)
      ->add('cpNam','text')
      ->add('Enr.','submit')->getForm();
    if($request->request->has("form") && array_key_exists("cpNam",$request->request->get("form"))){
      $form_comp->handleRequest($request);
      if($form_comp->isValid()){
	$em->persist($dum_comp);
	$em->flush();
      }
    }
    $dum_cat=new Category();
    $form_cat=$this->createFormBuilder($dum_cat)
      ->add('cNam','text')
      ->add('Enr.','submit')->getForm();
    if($request->request->has("form") && array_key_exists("cNam",$request->request->get("form"))){
      $form_cat->handleRequest($request);
      if($form_cat->isValid()){
	$em->persist($dum_cat);
	$em->flush();
      }}
    $dum_moy=new Moyen();
    $form_moy=$this->createFormBuilder($dum_moy)
      ->add('mNam','text')
      ->add('Enr.','submit')->getForm();
    if($request->request->has("form") && array_key_exists("mNam",$request->request->get("form"))){
      $form_moy->handleRequest($request);
      if($form_moy->isValid()){
	$em->persist($dum_moy);
	$em->flush();
      }}
    $comptes=$em->createQuery('SELECT c.id, c.cpNam as name FROM ClemLafComptesAppBundle:Comptes\Compte c')->getResult();
    $categories=$em->createQuery('SELECT c.id, c.cNam as name FROM ClemLafComptesAppBundle:Comptes\Category c ORDER BY c.cNam ASC')->getResult();
    $moyens=$em->createQuery('SELECT m.id, m.mNam as name FROM ClemLafComptesAppBundle:Comptes\Moyen m')->getResult();
    return $this->render('ClemLafComptesAppBundle:Comptes:param.html.twig',
			 array('form_comp'=>$form_comp->createView(),
			       'form_cat'=>$form_cat->createView(),
			       'form_moy'=>$form_moy->createView(),
			       'comptes'=>$comptes,
			       'categories'=>$categories,
			       'moyens'=>$moyens)
			 );
  }

  public function deleteAction(Request $request){
    $em=$this->getDoctrine()->getManager();
    $type=$request->request->get('type');
    $id=$request->request->get('id');
    $repo='';
    switch($type){
    case 'comptes':
      $repo='ClemLafComptesAppBundle:Comptes\Compte';
      break;
    case 'categories':
      $repo='ClemLafComptesAppBundle:Comptes\Category';
      break;
    case 'moyens':
      $repo='ClemLafComptesAppBundle:Comptes\Moyen';
      break;
    }
    if($repo!=''){
      $del=$em->getRepository($repo)->find($id);
      $em->remove($del);
      $em->flush();
    }    
    //rediriger vers la page index
    return $this->redirect($this->generateURL('clemlaf_comptes_app_param'));
  }

  public function periodAction(Request $request){
    $em=$this->getDoctrine()->getManager();
    $comptes=$em->createQuery('SELECT c.id, c.cpNam as name FROM ClemLafComptesAppBundle:Comptes\Compte c')->getResult();
    $categories=$em->createQuery('SELECT c.id, c.cNam as name FROM ClemLafComptesAppBundle:Comptes\Category c ORDER BY c.cNam ASC')->getResult();
    $moyens=$em->createQuery('SELECT m.id, m.mNam as name FROM ClemLafComptesAppBundle:Comptes\Moyen m')->getResult();
    $dum_perio=new Periodic();
    $form=$this->createForm(new PeriodicType(array(
						   'cpchoices' => MainController::getChoicesList($comptes), 'catchoices' => MainController::getChoicesList($categories), 'moychoices' => MainController::getChoicesList($moyens)
						 )),
			    $dum_perio);
    $form->handleRequest($request);
    if($form->isValid()){
      $em->persist($dum_perio);
      $em->flush();
    }
    $periods=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->findAll();
    return $this->render('ClemLafComptesAppBundle:Comptes:tab_perio.html.twig',
			 array('periodics'=> $periods,
			       'categories'=>$categories,
			       'moyens'=>$moyens,
			       'comptes'=>$comptes,
			       'form'=>$form->createView(),
			       )
			 );
  }

  public function update_periodAction(Request $request){
    $em=$this->getDoctrine()->getManager();
    $id=$request->request->get('id');
    $new=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->find($id);
    $dadate=date_create_from_format('d/m/Y',$request->request->get('last_date'));
    $new->setLastDate($request->request->get($dadate));
    $dadate=date_create_from_format('d/m/Y',$request->request->get('end_date'));
    $new->setEndDate($request->request->get($dadate));
    $new->setCpS(intval($request->request->get('cp_s')));//parseint
    $new->setCpD(intval($request->request->get('cp_d')));//parseint
    $new->setCategory($em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->find(intval($request->request->get('cat'))));//parseint
    $new->setCom($request->request->get('com'));//texte
    $new->setMoy(intval($request->request->get('moy')));//parseint
    $new->setPr(intval(floatval($request->request->get('pr'))*100));
    $em->persist($new);
    $em->flush();
    return 'ok';
  }

  public function delete_periodAction(Request $request){
    $em=$this->getDoctrine()->getManager();
    $id=$request->request->get('id');
    $del=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->find($id);
    $em->remove($del);
    $em->flush();
    return 'ok';
  }
}
?>
