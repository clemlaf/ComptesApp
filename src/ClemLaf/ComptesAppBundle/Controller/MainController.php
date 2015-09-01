<?php
namespace ClemLaf\ComptesAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ClemLaf\ComptesAppBundle\Entity\Comptes\Entree;
use Symfony\Component\HttpFoundation\Request;
use ClemLaf\ComptesAppBundle\Form\Type\EntreeType;
use Symfony\Component\HttpFoundation\Response;

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
	/*gestion du formulaire*/
	$em=$this->getDoctrine()->getManager();
	$comptes=$em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->findAll();
	$categories=$em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->findAll();
	$moyens=$em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->findAll();
	$cp_cl=MainController::getChoicesList($comptes,'Compte');
	$ca_cl=MainController::getChoicesList($categories,'Category');
	$mo_cl=MainController::getChoicesList($moyens,'Moyen');
	/* on cree une entree qui sert de base au formulaire de choix de l'affichage
	 * par exemple on choisit les dates qui nous intéressent ainsi
	 * que le compte ou la catégorie*/
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
	}

	$type=$form->get('type')->getData();
	$nom_tmp=array();
	if($form->get('cpS')->getData()==null){
	    $nom=null;
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
	$ord=false;
	$deb=$form->get('deb')->getData();
	$nbr=$form->get('nb')->getData();

	/* a partir des données issues du formulaire, on créé la requête
	 * principale, qui sert à récuperer les données qu'on souhaite afficher
	 * dans la base de données.*/
	$dqlcom=' WHERE ((e.cpS IN('.($nom==null?'9999':$nom).')'.($fdes==null?'':' AND e.cpD IN('.$fdes.')').') OR (e.cpD IN('.($nom==null?'9999':$nom).')'.($fdes==null?'':' AND e.cpS IN('.$fdes.')').'))';
	$dqlquery=' AND e.date >= :d1 AND e.date <= :d2';
	$dqlquery=$dqlquery.($fcat==null?'':' AND c.id IN('.$fcat.')');
	$dqlquery=$dqlquery.($rcom==null?'':' AND e.com LIKE :com');
	$dqlquery=$dqlquery.($fmoy==null?'':' AND e.moy='.$fmoy);
	$dqlquery=$dqlquery.' ORDER BY e.date '.($ord?'DESC':'ASC');
	$param=array(
	    //'nom' => $nom,
	    'd1' => $d1,
	    'd2' => $d2,
	);
	$param=($rcom==''?$param:array_merge($param,array('com'=> $rcom)));
	//$param=($fdes==null?$param:array_merge($param,array('des'=> $fdes)));
	//fin de la gestion du formlaire
	/* Enfin à partir du type de format des données qu'on a choisi,
	 * on génére un tableau ou un graphe. */
	if($type!='table'){
	    return $this->render('ClemLafComptesAppBundle:Comptes:graph.html.twig',
		array('form' => $form->createView(),
		'type'=> $type,
		'cpid' => $form->get('cpS')->getData(),
		'test'=> $dqlcom.''.$dqlquery,
		'param'=> $param,)
	    );

	}else{
	    /*gestion de la table*/
	    /*requete pour obtenir le nombre total d'entrée
	     * correspodant aux critères*/
	    $dql='SELECT COUNT(e) '.
		'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
		'LEFT JOIN e.category c '.$dqlcom.''.$dqlquery;
	    $nbtot=$em->createQuery($dql)->setParameters($param)->getSingleScalarResult();
	    /*requete pour obtenir le nb d'entrée qu'on souhaite 
	     * afficher.*/
	    $dql='SELECT e '.
		'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
		'LEFT JOIN e.category c '.$dqlcom.''.$dqlquery;
	    $query=$em->createQuery($dql)
		->setParameters($param)
		->setFirstResult($ord?$deb*$nbr:max($nbtot-($deb+1)*$nbr,0))
		->setMaxResults($nbr);
	    $entrees=$query->getResult();
	    /*requetes pour obtenir le solde filtré. il s'agit de la
	     * différence entre les entrée pour lequelles les comptes
	     * cpS sont recepteurs et celle dont les comptes cpS sont emetteurs
	     * */
	    $dql1='SELECT SUM(e.pr) as sol '.
		'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
		'LEFT JOIN e.category c '.
		'WHERE e.cpS in ('.($nom==null?'0':$nom).')'.
		($fdes==null?'':' AND e.cpD in ('.$fdes.')').$dqlquery;
	    $dql2='SELECT SUM(e.pr) as sol '.
		'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
		'LEFT JOIN e.category c '.
		'WHERE e.cpD in ('.($nom==null?'0':$nom).')'.
		($fdes==null?'':' AND e.cpS in ('.$fdes.')').$dqlquery;
	    $soldefiltre=$em->createQuery($dql1)
		->setParameters($param)
		->getSingleScalarResult()
	       	-
		$em->createQuery($dql2)
		->setParameters($param)
		->getSingleScalarResult();

	    $soldes=array();
	    $solde=array();
	    if($form->get('cpS')->getData()){
		foreach($form->get('cpS')->getData() as $cp){
		    $solde[$cp->getId()]=null;
		}
	    }
	    $entree_point=$dummy;
	    foreach($entrees as $e){
		$e->reverse($form->get('cpS')->getData());
		if(in_array($e->getCpS(),$form->get('cpS')->getData()==null?array():$form->get('cpS')->getData())){
		    if($solde[$e->getCpS()->getId()]==null){
			$solde[$e->getCpS()->getId()]=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($e->getCpS(),$e);
			$entree_point=$e;
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
	    $soldes['new']=$soldefiltre;
	    $newline=new Entree();
	    $newline->setCpS($entree_point->getCpS());
	    return $this->render('ClemLafComptesAppBundle:Comptes:table2.html.twig',
		array('form' => $form->createView(),
		'entrees' => $entrees,
		'categories' => $ca_cl[1],
		'moyens' => $mo_cl[1],
		'comptes' => $cp_cl[1],
		'cpid' => $form->get('cpS')->getData(),
		'newline' => $newline,
		'ord' => $ord,
		'soldes'=> $soldes,
		'test'=> $dqlcom,
		'solp'=> $em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($entree_point->getCpS(),$entree_point,true)
		)
	    );
	}
    }

    public function updateAction(Request $request){
	$params=$this->getParam($request);
	$param=$params['param'];
	$fdes=$params['des'];
	$dqlquery=$params['query'];

	$em=$this->getDoctrine()->getManager();
	$id=$request->request->get('id');
	if ($id!=null  && $id!='new'){
	    $new= $em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->find($id);
	    if($new->getCpD()==$request->request->get('cp_s') or $new->getCpS()==$request->request->get('cp_d'))
		$new->reverse($new->getCpD());
	}else
	    $new= new Entree();
	$new->setCpS($em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->find(intval($request->request->get('cp_s'))));//parseint
	$new->setCpD($em->getRepository('ClemLafComptesAppBundle:Comptes\Compte')->find(intval($request->request->get('cp_d'))));//parseint
	$dadate=date_create_from_format('d/m/Y',$request->request->get('date'));
	$new->setDate($dadate);//parsedate yyyy-mm-dd
	$new->setCategory($em->getRepository('ClemLafComptesAppBundle:Comptes\Category')->find(intval($request->request->get('cat'))));//parseint
	$new->setCom($request->request->get('com'));//texte
	$new->setMoyen($em->getRepository('ClemLafComptesAppBundle:Comptes\Moyen')->find(intval($request->request->get('moy'))));//parseint
	$new->setPr(intval(floatval($request->request->get('pr'))*100));//parsefloat ? gestion , ou .
	$new->setPoS($request->request->get('pt')=='true');
	if ($id == null || $id=='new')
	    $new->setPoD(false);
	$em->persist($new);
	$em->flush();
	$solde=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($new->getCpS(),$new);
	$sp=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->getSolde($new->getCpS(),$new,1);
	$sf=$em->createQuery(
	    'SELECT SUM(e.pr) as sol '.
	    'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
	    'WHERE e.cpS=:nom'.($fdes==null?'':' AND e.cpD=:des').$dqlquery
	)
	->setParameters($param)->getSingleScalarResult() -
	$em->createQuery(
	    'SELECT SUM(e.pr) as sol '.
	    'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
	    'WHERE e.cpD=:nom'.($fdes==null?'':' AND e.cpS=:des').$dqlquery
	)->setParameters($param)->getSingleScalarResult();
	//$sf=$nom.' '.$d1;
	$resp = new Response($this->renderView('ClemLafComptesAppBundle:Comptes:update.xml.twig',
	    array('entries' => array(array('id'=> $new->getId(), 'solde' => $solde/100)),
	    'type' => $id=='new'?'create':'update',
	    'soldepointe'=>$sp/100,
	    'soldefiltre'=>$sf/100,
	    'tab'=>$new,
	)
    ),200,array('Content-type' => 'text/xml'));
	return $resp;
    }

    public function deleteAction(Request $request){
	$params=$this->getParam($request);
	$param=$params['param'];
	$dqlquery=$params['query'];
	$fdes=$params['des'];
	$em=$this->getDoctrine()->getManager();
	$id=$request->request->get('id');
	$del=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')->find($id);
	$em->remove($del);
	$em->flush();
	$sp=0; //$this->getSoldePointe($del->getCpS());

	$sf=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Comptes\Entree e WHERE e.cpS=:nom'.($fdes==null?'':' AND e.cpD=:des').$dqlquery)->setParameters($param)->getSingleScalarResult() -
	    $em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Comptes\Entree e WHERE e.cpD=:nom'.($fdes==null?'':' AND e.cpS=:des').$dqlquery)->setParameters($param)->getSingleScalarResult();
	$resp = new Response($this->renderView('ClemLafComptesAppBundle:Comptes:update.xml.twig',
	    array('entries'=>array(),
	    'type' => 'delete',
	    'soldepointe'=>$sp/100,
	    'soldefiltre'=>$sf/100,
	    'tab'=>$del,
	)
    ),200,array('Content-type' => 'text/xml'));
	return $resp;
    }

    function getParam(Request $request){
	$nom=$request->request->get('entree_cpS');
	$d1=$request->request->get('entree_date1');
	$d2=$request->request->get('entree_date2'); //'3000-01-01';
	if($d1==''){
	    $d1='2000-01-01';
	}
	if($d2==''){
	    $d2='3000-01-01';
	}
	$fcat=$request->request->get('entree_cat');
	$fmoy=$request->request->get('entree_moy');
	$fdes=$request->request->get('entree_cpD');
	$rcom=$request->request->get('entree_com');
	$ord=false;

	$dqlquery=' AND e.date >= :d1 AND e.date <= :d2';
	$dqlquery=$dqlquery.($fcat==''?'':' AND e.category='.$fcat);
	$dqlquery=$dqlquery.($rcom==''?'':' AND e.com LIKE :com');
	$dqlquery=$dqlquery.($fmoy==''?'':' AND e.moy='.$fmoy);
	$dqlquery=$dqlquery.' ORDER BY e.date '.($ord?'DESC':'ASC');
	$param=array(
	    'nom' => $nom,
	    'd1' => $d1,
	    'd2' => $d2,
	);
	$param=($rcom==''?$param:array_merge($param,array('com'=> $rcom)));
	$param=($fdes==''?$param:array_merge($param,array('des'=> $fdes)));
	return array('param'=> $param, 'query'=>$dqlquery,'des'=>$fdes);
    }


}

