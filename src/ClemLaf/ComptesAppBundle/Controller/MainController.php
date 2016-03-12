<?php
namespace ClemLaf\ComptesAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Entree;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Periodic;
use Symfony\Component\HttpFoundation\Request;
use ClemLaf\ComptesAppBundle\Form\Type\EntreeType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends Controller
{
  public static function getChoicesList(array $arrayin, $type){
    $choices=array();
    $tab_choices=array();
    for($i=0;$i<count($arrayin);$i++){
      switch ($type){
        case 'Compte':
        $choices[$arrayin[$i]->getCpNam()]=$arrayin[$i];
        $tab_choices[]=array('id' => $arrayin[$i]->getId(),
        'name' => $arrayin[$i]->getCpNam());
        break;
        case 'Category':
        $choices[$arrayin[$i]->getCNam()]=$arrayin[$i];
        $tab_choices[]=array('id' => $arrayin[$i]->getId(),'name' => $arrayin[$i]->getCNam());
        break;
        case 'Moyen':
        $choices[$arrayin[$i]->getMNam()]=$arrayin[$i];
        $tab_choices[]=array('id' => $arrayin[$i]->getId(),'name' => $arrayin[$i]->getMNam());
        break;
      }
    }
    return array($choices, $tab_choices);
  }

  public function indexAction(Request $request)
  {
    $logger = $this->get('logger');
    /*gestion du formulaire*/
    $params=$this->getParam($request);
    //$param=($fdes==null?$param:array_merge($param,array('des'=> $fdes)));
    //fin de la gestion du formlaire
    //Il faut ajouter les dernières opérations périodiques.
    $em=$this->getDoctrine()->getManager();
    $periods=$em->getRepository('ClemLafComptesAppBundle:Comptes\Periodic')->findAll();
    foreach($periods as $p){
      $tod=new \DateTime("+1 week");
      if($tod < $p->getEndDate()){
        $firstdate=$p->getLastDate();
        $newdate=new \DateTime($firstdate->format('Y-m-d'));
        $lastdate=new \DateTime($newdate->format('Y-m-d'));
        $newdate->add(new \DateInterval('P'.$p->getMois().'M'.$p->getJours().'D'));
        //$logger->info($lastdate->format('d/m/Y').' to '.$newdate->format('d/m/Y'));
        while($tod > $newdate){
          $newent=new Entree();
          $newent->setDate(new \DateTime($newdate->format('Y-m-d')));
          $newent->setCpS($p->getCpS());
          $newent->setCpD($p->getCpD());
          $newent->setCategory($p->getCategory());
          $newent->setMoyen($p->getMoyen());
          $newent->setCom($p->getCom());
          $newent->setPr($p->getPrix());
          $newent->setPoS(false);
          $newent->setPoD(false);
          $em->persist($newent);
          //$em->flush();
          $lastdate=new \DateTime($newdate->format('Y-m-d'));
          $newdate->add(new \DateInterval('P'.$p->getMois().'M'.$p->getJours().'D'));
        }
        $p->setLastDate($lastdate);
      }
    }
    $em->flush();

    return $this->render('ClemLafComptesAppBundle:Comptes:table3.html.twig',
    array('form' => $params['form']->createView(),
    )
  );
}
public function get_tableAction(Request $request){
  $params=$this->getParam($request);
  $em=$this->getDoctrine()->getManager();
  $response = new JsonResponse();
  if($params['type']!='table'){
    $data=array('image' => GraphController::graph($params['type'],
    $params['common_query'],$params['param'],$params['cpS'],
    $params['sold_query1'],$params['sold_query2'], $em));
  }
  else
  $data=$this->createTable($params);
  $response->setData($data);
  return $response;
}

private function createTable($params, $spec_entree=null){
  $param=$params['param'];
  $comquery=$params['common_query'];
  $lim=$params['lim'];
  /*gestion de la table*/
  /*requete pour obtenir le nombre total d'entrée
  * correspodant aux critères*/
  $em=$this->getDoctrine()->getManager();
  $dql='SELECT COUNT(e) '.
  'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
  $comquery;
  $nbtot=$em->createQuery($dql)->setParameters($param)->getSingleScalarResult();
  /*requete pour obtenir le nb d'entrée qu'on souhaite
  * afficher.*/
  $dql='SELECT e '.
  'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
  $comquery;
  $query=$em->createQuery($dql)
  ->setParameters($param)
  ->setFirstResult($lim['ord']?$lim['deb']*$lim['nbr']:max($nbtot-($lim['deb']+1)*$lim['nbr'],0))
  ->setMaxResults($lim['nbr']);
  $entrees=$query->getResult();
  /*requetes pour obtenir le solde filtré. il s'agit de la
  * différence entre les entrée pour lequelles les comptes
  * cpS sont recepteurs et celle dont les comptes cpS sont emetteurs
  * */
  $dql1='SELECT SUM(e.pr) as sol '.
  'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
  $params['sold_query1'];
  $dql2='SELECT SUM(e.pr) as sol '.
  'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
  $params['sold_query2'];
  $soldefiltre=$em->createQuery($dql1)
  ->setParameters($param)
  ->getSingleScalarResult()
  -
  $em->createQuery($dql2)
  ->setParameters($param)
  ->getSingleScalarResult();

  $soldes=array();
  $solde=array();
  $tab_entrees=array();
  if($params['cpS']){
    foreach($params['cpS'] as $cp){
      $solde[$cp->getId()]=null;
    }
  }
  $found_spec=false;
  foreach($entrees as $e){
    $e->reverse($params['cpS']);
    $tmp=$e->toArray();
    $tmp['spec']=($spec_entree && $e->getId()==$spec_entree->getId());
    $found_spec=$found_spec || $tmp['spec'];
    $tab_entrees[]=$tmp;
    if(in_array($e->getCpS(),$params['cpS']==null?array():$params['cpS'])){
      if($solde[$e->getCpS()->getId()]==null){
        $solde[$e->getCpS()->getId()]=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($e->getCpS(),$e);
      }
      else
      $solde[$e->getCpS()->getId()]=$solde[$e->getCpS()->getId()]+$e->getPr();
      $soldes[$e->getId()]=$solde[$e->getCpS()->getId()];
    }else{
      if($solde[$e->getCpD()->getId()]==null)
      $solde[$e->getCpD()->getId()]=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($e->getCpD(),$e);
      else
      $solde[$e->getCpD()->getId()]=$solde[$e->getCpD()->getId()]-$e->getPr();
      $soldes[$e->getId()]=$solde[$e->getCpD()->getId()];
    }
  }
  if(!$found_spec && $spec_entree){
    $tmp=$spec_entree->toArray();
    $tmp['spec']=true;
    array_shift($tab_entrees);
    $tab_entrees[]=$tmp;
  }
  $soldes['new']=$soldefiltre;

  $compte=reset($params['cpS']);
  $soldepoint=0;
  if(count($params['cpS'])==1){
    //$compte=reset($params['cpS']);
    $dql1='SELECT SUM(e.pr) as sol '.
    'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
    'WHERE e.cpS=:compte AND e.poS=true';
    $dql2='SELECT SUM(e.pr) as sol '.
    'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
    'WHERE e.cpD=:compte AND e.poD=true';
    $parpoint=array('compte'=>$compte);
    $soldepoint=$em->createQuery($dql1)
    ->setParameters($parpoint)
    ->getSingleScalarResult()
    -
    $em->createQuery($dql2)
    ->setParameters($parpoint)
    ->getSingleScalarResult();
  }

  $comptenew=$compte;
  if($spec_entree)
  $comptenew=$spec_entree->getCps();
  $newline=new Entree();
  if($comptenew)
  $newline->setCpS($comptenew);
  return array('entrees' => $tab_entrees,
  'soldes' => $soldes,
  'solp' => $soldepoint/100,
  'ord' => $lim['ord'],
  'newline' => $newline->toArray(),
  'comptes' => $params['cpcl'],
  'categories' => $params['cacl'],
  'moyens' => $params['mocl'],
  'param'=> $params['param'],
);

}

public function updateAction(Request $request){
  $new=null;
  $em=$this->getDoctrine()->getManager();
  $entrees=$request->request->get('entrees');
  if($entrees==null){
    $content=$request->getContent();
    $entrees=json_decode($content, true)['entrees']; //$request->request->get('entrees');
  }
  foreach ($entrees as $e) {
    $id=$e['id'];
    if ($id!=null  && $id!='new'){
      $new= $em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->find($id);
      if($e['cp_s']==($new->getCpD()?$new->getCpD()->getId():null) or $new->getCpS()->getId()==$e['cp_d'])
      $new->reverse(array($new->getCpD()));
    }else{
      $new= new Entree();
    }
    $new->setCpS( $em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')
    ->find(intval($e['cp_s'])) );//parseint
    $new->setCpD( $em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')
    ->find(intval($e['cp_d'])) );//parseint
    $dadate=date_create_from_format('d/m/Y',$e['date']);
    $new->setDate($dadate);//parsedate yyyy-mm-dd
    $new->setCategory($em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->find(intval($e['cat'])));//parseint
    $new->setCom($e['com']);//texte
    $new->setMoyen($em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->find(intval($e['moy'])));//parseint
    $new->setPr(intval(round(floatval(
    str_replace(',','.',$e['pr'])
    )*100) ));//parsefloat ? gestion , ou .
    $new->setPoS($e['pt']=='true');
    if ($id == null || $id=='new'){
      $new->setPoD(false);
    }
    $em->persist($new);
    // code...
  }
  $em->flush();
  $params=$this->getParam($request);
  $response = new JsonResponse();
  $response->setData($this->createTable($params,$new));
  return $response;
}

public function deleteAction(Request $request){
  $em=$this->getDoctrine()->getManager();
  $id=$request->request->get('id');
  $del=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->find($id);
  $em->remove($del);
  $em->flush();
  $params=$this->getParam($request);
  $response = new JsonResponse();
  $response->setData($this->createTable($params));
  return $response;
}

function getParam(Request $request){
  /*gestion du formulaire*/
  $em=$this->getDoctrine()->getManager();
  $comptes=$em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->findBy(array(),array('cpNam'=> 'ASC'));
  $categories=$em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->findBy(array(),array('cNam'=> 'ASC'));
  $moyens=$em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->findBy(array(),array('mNam'=> 'ASC'));
  $cp_cl=MainController::getChoicesList($comptes,'Compte');
  $ca_cl=MainController::getChoicesList($categories,'Category');
  $mo_cl=MainController::getChoicesList($moyens,'Moyen');
  $dummy= new Entree();
  $form=$this->createForm(new EntreeType(array(
    'cpchoices' => $cp_cl[0], 'catchoices' => $ca_cl[0], 'moychoices' => $mo_cl[0]
  )),
  $dummy);
  $form->handleRequest($request);

  if($form->isValid()){

  }elseif(!$form->isSubmitted()){
    $form->get('deb')->setData(0);
    $form->get('nb')->setData(15);
    $form->get('type')->setData('table');
    $form->get('point')->setData('_');
  }

  $type=$form->get('type')->getData();
  $nom_tmp=array();
  if($form->get('cpS')->getData()==null){
    $nom='9999';
  }else{
    foreach($form->get('cpS')->getData() as $tmp){
      $nom_tmp[]=$tmp->getId();
    }
    $nom=implode(',',$nom_tmp);
  }
  $d1=$form->get('date1')->getData();
  $d2=$form->get('date2')->getData(); //'3000-01-01';
  if($d1==''){
    $d1='2000-01-01';//valeur par défaut pour la date de début
  }
  if($d2==''){
    $d2='3000-01-01';//valeur par défaut pour la date de fin
  }
  $fcat=$dummy->getCategory()==null?null:$dummy->getCategory()->getId();
  $fmoy=$dummy->getMoyen()==null?null:$dummy->getMoyen()->getId();//on ne peut choisir qu'un moyen
  $nom_tmp=array();
  if($form->get('cpD')->getData()==null){
    $fdes=null;
  }else{
    foreach($form->get('cpD')->getData() as $tmp){
      $nom_tmp[]=$tmp->getId();
    }
    $fdes=implode(',',$nom_tmp);
  }
  $rcom=$dummy->getCom();
  $fpoi=$form->get('point')->getData();
  $ord=false;
  $deb=$form->get('deb')->getData();
  $nbr=$form->get('nb')->getData();

  $startdql='WHERE ((e.cpS IN ('.$nom.')'.
  ($fpoi=='_'?'':' AND e.poS='.($fpoi=='x'?'true':'false')).
  ($fdes==null?'':' AND e.cpD IN ('.$fdes.')').') '.
  'OR (e.cpD IN('.$nom.')'.
  ($fpoi=='_'?'':' AND e.poD='.($fpoi=='x'?'true':'false')).
  ($fdes==null?'':' AND e.cpS IN('.$fdes.')').'))';
  $solddql1= 'WHERE e.cpS in ('.$nom.')'.
  ($fpoi=='_'?'':' AND e.poS='.($fpoi=='x'?'true':'false')).
  ($fdes==null?'':' AND e.cpD in ('.$fdes.')');
  $solddql2= 'WHERE e.cpD in ('.$nom.')'.
  ($fpoi=='_'?'':' AND e.poD='.($fpoi=='x'?'true':'false')).
  ($fdes==null?'':' AND e.cpS in ('.$fdes.')');
  $dqlquery=' AND e.date >= :d1 AND e.date <= :d2';
  $dqlquery=$dqlquery.($fcat==''?'':' AND e.category='.$fcat);
  $dqlquery=$dqlquery.($rcom==''?'':' AND e.com LIKE :com');
  $dqlquery=$dqlquery.($fmoy==''?'':' AND e.moyen='.$fmoy);
  $dqlquery=$dqlquery.' ORDER BY e.date '.($ord?'DESC':'ASC');
  $param=array(
    'd1' => $d1,
    'd2' => $d2,
  );
  $param=($rcom==''?$param:array_merge($param,array('com'=> $rcom)));

  $lim=array('nbr' => $nbr,
  'deb'=> $deb,
  'ord'=> $ord);
  return array('param'=> $param,
  'common_query'=>$startdql.$dqlquery,
  'sold_query1'=>$solddql1.$dqlquery,
  'sold_query2'=>$solddql2.$dqlquery,
  'query'=>$dqlquery,
  'des'=>$fdes,
  'nom'=>$nom,
  'lim'=>$lim,
  'cpS'=>$form->get('cpS')->getData(),
  'cpcl'=>$cp_cl[1],
  'cacl'=>$ca_cl[1],
  'mocl'=>$mo_cl[1],
  'dummy'=> $dummy,
  'form'=> $form,
  'type'=> $type,
);
}


}
