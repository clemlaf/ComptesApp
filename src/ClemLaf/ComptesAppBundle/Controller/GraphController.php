<?php
namespace ClemLaf\ComptesAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xlab\pChartBundle\pData;
use Xlab\pChartBundle\pDraw;
use Xlab\pChartBundle\pImage;
use Xlab\pChartBundle\pScatter;
use Xlab\pChartBundle\pPie;

class GraphController extends Controller
{
    public function graphAction($type, $querystr, $param, $cpsarray){
	/*On prépare la requête à la base de données et la
	 * response à la requête http*/	
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
	$query=$em->createQuery('SELECT e '.
	    'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
	    'LEFT JOIN e.category c '.
	    $querystr)
	    ->setParameters($param);
	/* la requête est commune aux deux premiers types de graphes*/
	switch ($type){
	    /*Premier cas de graph : évolution du solde en fonction du temps*/
	case 'solde':
	    $entrees=$query->getResult();
	    if(count($entrees)==0)
		return $response;
	    $d1='';
	    $d2='';
	    $last=0;
	    /*on initialise les 2 séries (temps et solde)*/
	    $myData->initialise('solde');
	    $myData->initialise('date');  
	    $solde=0;
	    /*puis on parcourt toutes les entrées pour remplir
	     * les séries avec les valeurs issues de la base de 
	     * données*/
	    foreach($entrees as $e){
		if($d1==''){
		    $d1=$e->getDate();
		    if(in_array($e->getCpS(),$cpsarray==null?array():$cpsarray))
			$solde=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')
			->getSolde($e->getCpS(),$e);
		    else
			$solde=$em->getRepository('ClemLafComptesAppBundle:Comptes\Entree')
			->getSolde($e->getCpD(),$e);	    
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
	    /*on ajoute ensuite une ligne pour marquer le zéro*/
	    $myData->addPoints(array($d1->format('U'),$d2->format('U')),'dz');
	    $myData->addPoints(array(0,0),'zero');

	    /*il s'agit ensuite de mettre en forme le graphe*/
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

	    /*deuxième type de graphe, il s'agit d'un histogramme
	     * récapitulant les revenus et dépenses mois par mois*/
	case 'rev_dep':
	    $entrees=$query->getResult();
	    if(count($entrees)==0)
		return $response;
	    $d1='';
	    $d2='';
	    $ld='';
	    $in=0;
	    $out=0;
	    $last=0;
	    /*on initialise les séries*/
	    $myData->initialise('rev');
	    $myData->initialise('dep');
	    $myData->initialise('solde');
	    $myData->initialise('date');            
	    /*on parcourt les entrées trouvées par la requête pour remplir
	     * les séries depuis la base de données.*/
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
	    /*Finalisation du graphe*/
	    if($ld!=''){
		$myData->addPoints($ld->format('U'),'date');
		$myData->addPoints($in/100,'rev');
		$myData->addPoints($out/100,'dep');
		$myData->addPoints(($out+$in)/100,'solde');
	    }
	    /*Mise en forme du graphe*/
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

	    /*Le troisième type de graphe est un camembert qui trie
	     * les dépenses par catégories.*/
	case 'pie':
	    /* une requête spécifique permet de récupérer les données
	     * nécessaires */
	    $somcat=$em->createQuery('SELECT c.cNam, SUM(e.pr) as sol '.
		'FROM ClemLafComptesAppBundle:Comptes\Entree e '.
		'JOIN e.category c'
		.str_replace('ORDER',' GROUP BY c.id ORDER',$querystr))
		->setParameters($param)
		->getResult();
	    /*Pour chaque catégorie on remplit les séries*/
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
	    break;
	}
	ob_start();//le contenu est envoyé dans un buffer
	/* Render the picture (choose the best way) */
	$myPicture->autoOutput();
	$response->setContent(base64_encode(ob_get_clean()));//le contenu du buffer est vidé dans le contenu de la "response"
	return $response;
    }

}
