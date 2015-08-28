<?php
namespace ClemLaf\ComptesAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ClemLaf\ComptesAppBundle\Entity\Test\Entree;
use Symfony\Component\HttpFoundation\Request;
use ClemLaf\ComptesAppBundle\Form\Type\EntreeType;
use Symfony\Component\HttpFoundation\Response;
use Xlab\pChartBundle\pData;
use Xlab\pChartBundle\pDraw;
use Xlab\pChartBundle\pImage;
use Xlab\pChartBundle\pScatter;
use Xlab\pChartBundle\pPie;

class MainController extends Controller
{
  
  public static function getChoicesList(array $arrayin){
    $choices=array();
    for($i=0;$i<count($arrayin);$i++){
      $choices[$arrayin[$i]['id']]=$arrayin[$i]['name'];
    }
    return $choices;
  }

  public function getSolde($nom,$e){
    $em=$this->getDoctrine()->getManager();
    $solde=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpS=:nom AND e.date<=:dd AND e.id<=:id')->setParameters(array('nom'=> $nom, 'dd' => $e->getDate(), 'id' => $e->getId()))->getSingleScalarResult();
    $solde=$solde-$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpD=:nom AND e.date<=:dd AND e.id<=:id')->setParameters(array('nom'=> $nom, 'dd' => $e->getDate(), 'id' => $e->getId()))->getSingleScalarResult();
    return $solde; 
  }

  public function getSoldePointe($nom){
    $em=$this->getDoctrine()->getManager();
    $solde=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpS=:nom AND e.poS=1')->setParameters(array('nom'=> $nom))->getSingleScalarResult();
    $solde=$solde-$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpD=:nom AND e.poD=1')->setParameters(array('nom'=> $nom))->getSingleScalarResult();
    return $solde;
}

  public function indexAction(Request $request)
  {
    /*gestion du formulaire*/
    $em=$this->getDoctrine()->getManager();
    $comptes=$em->createQuery('SELECT c.id, c.cpNam as name FROM ClemLafComptesAppBundle:Test\Compte c')->getResult();
    $categories=$em->createQuery('SELECT c.id, c.cNam as name FROM ClemLafComptesAppBundle:Test\Category c ORDER BY c.cNam ASC')->getResult();
    $moyens=$em->createQuery('SELECT m.id, m.mNam as name FROM ClemLafComptesAppBundle:Test\Moyen m')->getResult();
    
    $dummy= new Entree();
    $form=$this->createForm(new EntreeType(array(
						 'cpchoices' => TestController::getChoicesList($comptes), 'catchoices' => TestController::getChoicesList($categories), 'moychoices' => TestController::getChoicesList($moyens)
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
    $nom=$dummy->getCpS()==null?null:implode(', ',$dummy->getCpS());
    $d1=$form->get('date1')->getData();
    $d2=$form->get('date2')->getData(); //'3000-01-01';
    if($d1==''){
      $d1='2000-01-01';
    }
    if($d2==''){
      $d2='3000-01-01';
    }
    $fcat=$dummy->getCat()==null?null:implode(', ',$dummy->getCat());
    $fmoy=$dummy->getMoy();
    $fdes=$dummy->getCpD()==null?null:implode(', ',$dummy->getCpD());
    $rcom=$dummy->getCom();
    $ord=false;
    $deb=$form->get('deb')->getData();
    $nbr=$form->get('nb')->getData();
    
    $dqlcom=' WHERE ((e.cpS IN('.($nom==null?'9999':$nom).')'.($fdes==null?'':' AND e.cpD IN('.$fdes.')').') OR (e.cpD IN('.($nom==null?'9999':$nom).')'.($fdes==null?'':' AND e.cpS IN('.$fdes.')').'))';
    $dqlquery=' AND e.date >= :d1 AND e.date <= :d2';
    $dqlquery=$dqlquery.($fcat==null?'':' AND c.id IN('.$fcat.')');
    $dqlquery=$dqlquery.($rcom==null?'':' AND e.com LIKE :com');
    $dqlquery=$dqlquery.($fmoy==''?'':' AND e.moy='.$fmoy);
    $dqlquery=$dqlquery.' ORDER BY e.date '.($ord?'DESC':'ASC');
    $param=array(
		 //'nom' => $nom,
			'd1' => $d1,
			'd2' => $d2,
		 );
    $param=($rcom==''?$param:array_merge($param,array('com'=> $rcom)));
    //$param=($fdes==null?$param:array_merge($param,array('des'=> $fdes)));
    //fin de la gestion du formlaire

    if($type!='table'){
      return $this->render('ClemLafComptesAppBundle:Test:graph.html.twig',
			   array('form' => $form->createView(),
				 'type'=> $type,
				 'cpid' => $dummy->getCpS(),
				 'test'=> $dqlcom.''.$dqlquery,
				 'param'=> $param,)
			   );

    }else{
      /*gestion de la table*/
      $nbtot=$em->createQuery('SELECT COUNT(e) FROM ClemLafComptesAppBundle:Test\Entree e LEFT JOIN e.category c '.$dqlcom.''.$dqlquery)->setParameters($param)->getSingleScalarResult();
      
      $query=$em->createQuery('SELECT e FROM ClemLafComptesAppBundle:Test\Entree e LEFT JOIN e.category c '.$dqlcom.''.$dqlquery)->setParameters($param)->setFirstResult($ord?$deb*$nbr:max($nbtot-($deb+1)*$nbr,0))->setMaxResults($nbr);
      $entrees=$query->getResult();
      
      $soldefiltre=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e LEFT JOIN e.category c WHERE e.cpS in ('.($nom==null?'0':$nom).')'.($fdes==null?'':' AND e.cpD in ('.$fdes.')').$dqlquery)->setParameters($param)->getSingleScalarResult() -
	$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e LEFT JOIN e.category c WHERE e.cpD in ('.($nom==null?'0':$nom).')'.($fdes==null?'':' AND e.cpS in ('.$fdes.')').$dqlquery)->setParameters($param)->getSingleScalarResult();
      
      $soldes=array();
      $solde=0;
      foreach($entrees as $e){
	if(count($soldes)==0){
	  if(in_array($e->getCpS(),$dummy->getCpS()==null?array():$dummy->getCpS()))
	    $solde=$this->getSolde($e->getCpS(),$e);
	  else
	    $solde=$this->getSolde($e->getCpD(),$e);
	}else{
	if(in_array($e->getCpS(),$dummy->getCpS()==null?array():$dummy->getCpS()))
	  $solde=$solde+$e->getPr();
	else
	  $solde=$solde-$e->getPr();
	}
	$soldes[$e->getId()]=$solde;
      }
      $soldes['new']=$soldefiltre;
      $newline=new Entree();
      return $this->render('ClemLafComptesAppBundle:Test:table2.html.twig',
			   array('form' => $form->createView(),
				 'entrees' => $entrees,
				 'categories' => $categories,
				 'moyens' => $moyens,
				 'comptes' => $comptes,
				 'cpid' => $nom,
				 'newline' => $newline,
				 'ord' => $ord,
				 'soldes'=> $soldes,
				 'test'=> $dqlcom,
				 'solp'=> $this->getSoldePointe($nom)/100
				 )
			   );
    }
  }

  public function graphAction($type, $querystr, $param, $cpsarray){
    $em=$this->getDoctrine()->getManager();
    $response=new Response();
    $response->headers->set('Content-Type', 'image/png');
    $myData = new pData();
    $myPicture=new pImage(700,500,$myData);
    /* Draw the background */
    $Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>1, "DashR"=>220, "DashG"=>220, "DashB"=>220);
    $myPicture->drawFilledRectangle(0,0,700,500,$Settings);
    /* Add a border to the picture */
    $myPicture->drawRectangle(0,0,699,499,array("R"=>0,"G"=>0,"B"=>0));
    /* Set the graph area */
    $myPicture->setGraphArea(50,50,690,430);
    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
    $query=$em->createQuery('SELECT e FROM ClemLafComptesAppBundle:Test\Entree e LEFT JOIN e.category c '.$querystr)->setParameters($param);
    $entrees=$query->getResult();
    if(count($entrees)==0)
      return $response;
    switch ($type){
    case 'solde':
      $d1='';
      $d2='';
      $last=0;
      $myData->initialise('solde');
      $myData->initialise('date');  
      $solde=0;
      foreach($entrees as $e){
	if($d1==''){
	  $d1=$e->getDate();
	  if(in_array($e->getCpS(),$cpsarray==null?array():$cpsarray))
	    $solde=$this->getSolde($e->getCpS(),$e);
	  else
	    $solde=$this->getSolde($e->getCpD(),$e);	    
	}else{
	  if(in_array($e->getCpS(),$cpsarray==null?array():$cpsarray))
	    $solde=$solde+$e->getPr();
	  else
	    $solde=$solde-$e->getPr();
	}
	$d2=$e->getDate();
	$myData->addPoints($e->getDate()->format('U'),'date');
	$myData->addPoints($last/100,'solde');
	$last=$solde;
	$myData->addPoints($e->getDate()->format('U'),'date');
	$myData->addPoints($last/100,'solde');
      }
      $myData->addPoints(array($d1->format('U'),$d2->format('U')),'dz');
      $myData->addPoints(array(0,0),'zero');

      $myData->setAxisName(0,'Date');
      $myData->setAxisXY(0,AXIS_X);
      $myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);
      $myData->setAxisDisplay(0,AXIS_FORMAT_DATE);
      
      $myData->setSerieOnAxis(array('solde'),1);
      $myData->setAxisName(1,'Solde');
      $myData->setAxisXY(1,AXIS_Y);
      $myData->setAxisUnit(1,'Euros');
      $myData->setAxisPosition(1,AXIS_POSITION_LEFT);
      $myData->setAxisDisplay(1,AXIS_FORMAT_CURRENCY);

      $myData->setSerieOnAxis('zero',1);
      
      /* Create the 1st scatter chart binding */
      $myData->setScatterSerie('date','solde',0);
      $myData->setScatterSerieDescription(0,"");
      $myData->setScatterSerieTicks(0,0);
      $myData->setScatterSerieColor(0,array("R"=>0,"G"=>0,"B"=>0));
      
      $myData->setScatterSerie('dz','zero',1);
      $myData->setScatterSerieDescription(1,"");
      $myData->setScatterSerieTicks(1,4);
      $myData->setScatterSerieColor(1,array("R"=>255,"G"=>0,"B"=>0));
      /* Set the default font */
      $myPicture->setFontProperties(array("FontName"=>__DIR__."/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
      /* Create the Scatter chart object */
      $myScatter = new pScatter($myPicture,$myData);
      /* Draw the scale */
      $myScatter->drawScatterScale();
      /* Draw a scatter plot chart */
      $myScatter->drawScatterLineChart();
      /* Capture output and return the response */
      break;
    case 'rev_dep':
      $d1='';
      $d2='';
      $ld='';
      $in=0;
      $out=0;
      $last=0;
      $myData->initialise('rev');
      $myData->initialise('dep');
      $myData->initialise('solde');
      $myData->initialise('date');            
      foreach($entrees as $e){
	if($d1=='')
	  $d1=$e->getDate()->format('U');
	if($ld!=''){
	  while($ld->format('Y-m')!=$e->getDate()->format('Y-m')){
	    $myData->addPoints($ld->format('U'),'date');
	    $myData->addPoints($in/100,'rev');
	    $myData->addPoints($out/100,'dep');
	    $myData->addPoints(($out+$in)/100,'solde');
	    $in=0;
	    $out=0;
	    $ld->modify('+1 month');
	  }
	}
	$ppr=$e->getPr();
	$d2=$e->getDate()->format('U');
	if(in_array($e->getCpS(),$cpsarray==null?array():$cpsarray))
	  $ppr=$ppr;
	else
	  $ppr=-1*$ppr;
	$in=$in+max($ppr,0);
	$out=$out+min($ppr,0);
	$ld=date_create_from_format('Y-m-d',$e->getDate()->format('Y-m').'-01');
	$ld->setTime(12,0,0);
      }
      if($ld!=''){
	$myData->addPoints($ld->format('U'),'date');
	$myData->addPoints($in/100,'rev');
	$myData->addPoints($out/100,'dep');
	$myData->addPoints(($out+$in)/100,'solde');
      }
      $myData->setAbscissa("date");
      $myData->setXAxisDisplay(AXIS_FORMAT_DATE);
      $myData->setSerieOnAxis("rev",0);
      $myData->setAxisName(0,"");
      /*$myData->setAxisXY(1,AXIS_Y);*/
      $myData->setAxisUnit(0,"Euros");
      $myData->setAxisPosition(0,AXIS_POSITION_LEFT);
      $myData->setAxisDisplay(0,AXIS_FORMAT_CURRENCY);
      /*$myData->setSerieOnAxis("zero",0);*/
      $myData->setSerieOnAxis("dep",0);
      $myData->setSerieOnAxis("solde",0);
      /* Set the default font */
      $myPicture->setFontProperties(array("FontName"=>__DIR__."/../Resources/fonts/pf_arma_five.ttf","FontSize"=>6));
      $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200, "GridAlpha"=>80,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"LabelRotation"=>90);
      /* Draw the scale */
      $myPicture->drawScale($scaleSettings);
      /* Draw a scatter plot chart */
      $myPicture->drawBarChart(array("DisplayValues"=>True,"DisplayColor"=>DISPLAY_AUTO));
      $myPicture->drawThreshold($myData->getSerieAverage("solde"),array("WriteCaption"=>TRUE,"Caption"=>"Moy = ".$myData->getSerieAverage("solde")));
      break;
    case 'pie':
      $somcat=$em->createQuery('SELECT c.cNam, SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e JOIN e.category c'.str_replace('ORDER',' GROUP BY c.id ORDER',$querystr))->setParameters($param)->getResult();
      foreach($somcat as $s){
	if($s['sol']<0){
	  $myData->addPoints($s['cNam'],"Labels");
	  $myData->addPoints(($s['sol'] / 100),"somme");
	}
      }
      $myData->setAbscissa("Labels");      
      /* Set the default font */
      $myPicture->setFontProperties(array("FontName"=>__DIR__."/../Resources/fonts/calibri.ttf","FontSize"=>10,"R"=>0,"G"=>0,"B"=>0 ));
      $PieChart = new pPie($myPicture,$myData);
      /* Draw an AA pie chart */ 
      $PieChart->draw2DPie(240,240,array("WriteValues"=>PIE_VALUE_PERCENTAGE, "Border"=>TRUE, "Radius"=>180,"ValueR"=>0,"ValueG"=>0,"ValueB"=>0,"ValuePosition"=>PIE_VALUE_INSIDE)); 
      $PieChart->drawPieLegend(500,40);
      /* Render the picture (choose the best way) */
      break;
    }
    ob_start();
    $myPicture->autoOutput();
    $response->setContent(base64_encode(ob_get_clean()));
    return $response;
  }

  public function updateAction(Request $request){
    $params=$this->getParam($request);
    $param=$params['param'];
    $fdes=$params['des'];
    $dqlquery=$params['query'];

    $em=$this->getDoctrine()->getManager();
    $id=$request->request->get('id');
    if ($id!='new'){
      $new= $em->getRepository('ClemLafComptesAppBundle:Test\Entree')->find($id);
      if($new->getCpD()==$request->request->get('cp_s') or $new->getCpS()==$request->request->get('cp_d'))
	$new->reverse($new->getCpD());
    }else
      $new= new Entree();
    $new->setCpS(intval($request->request->get('cp_s')));//parseint
    $new->setCpD(intval($request->request->get('cp_d')));//parseint
    $dadate=date_create_from_format('d/m/Y',$request->request->get('date'));
    $new->setDate($dadate);//parsedate yyyy-mm-dd
    $new->setCategory($em->getRepository('ClemLafComptesAppBundle:Test\Category')->find(intval($request->request->get('cat'))));//parseint
    $new->setCom($request->request->get('com'));//texte
    $new->setMoy(intval($request->request->get('moy')));//parseint
    $new->setPr(intval(floatval($request->request->get('pr'))*100));//parsefloat ? gestion , ou .
    $new->setPoS($request->request->get('pt')=='true');
    if ($id=='new')
      $new->setPoD(false);
      $em->persist($new);
    $em->flush();
    $solde=$this->getSolde($new->getCpS(),$new);
    $sp=$this->getSoldePointe($new->getCpS());
    $sf=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpS=:nom'.($fdes==null?'':' AND e.cpD=:des').$dqlquery)->setParameters($param)->getSingleScalarResult() -
      $em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpD=:nom'.($fdes==null?'':' AND e.cpS=:des').$dqlquery)->setParameters($param)->getSingleScalarResult();
    //$sf=$nom.' '.$d1;
    $resp = new Response($this->renderView('ClemLafComptesAppBundle:Test:update.xml.twig',
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
    $del=$em->getRepository('ClemLafComptesAppBundle:Test\Entree')->find($id);
    $em->remove($del);
    $em->flush();
    $sp=$this->getSoldePointe($del->getCpS());
    
    $sf=$em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpS=:nom'.($fdes==null?'':' AND e.cpD=:des').$dqlquery)->setParameters($param)->getSingleScalarResult() -
      $em->createQuery('SELECT SUM(e.pr) as sol FROM ClemLafComptesAppBundle:Test\Entree e WHERE e.cpD=:nom'.($fdes==null?'':' AND e.cpS=:des').$dqlquery)->setParameters($param)->getSingleScalarResult();
    $resp = new Response($this->renderView('ClemLafComptesAppBundle:Test:update.xml.twig',
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
    $dqlquery=$dqlquery.($fcat==''?'':' AND e.cat='.$fcat);
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

