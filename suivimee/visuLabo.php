<?php
require('top.php');
require("../visuessai/planningVisu.php");
?>
<?php
/*$idService=$labo;
$anneeCour=date("Y");
$dateDebNonFifo=$anneeCour."-01-01";
$dateFinNonFifo=$anneeCour."-12-31";

//formatage pour la value des input
$dateDebFormNonFifo=explode("-",$dateDebNonFifo);
$dateDebFormNonFifo=$dateDebFormNonFifo[2]."/".$dateDebFormNonFifo[1]."/".$dateDebFormNonFifo[0];
$dateFinFormNonFifo=explode("-",$dateFinNonFifo);
$dateFinFormNonFifo=$dateFinFormNonFifo[2]."/".$dateFinFormNonFifo[1]."/".$dateFinFormNonFifo[0];
?>
<div class="container">
	<div class="page-header clear">
		<h2>Retour suivi de procédure</h2>
	</div>
	<a class="btn btn-info" target="_blank" href="genPDFrexFifo.php?idService=<?php echo $idService ;?>&arg=procedure&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">PDF</a>
	
</div>

<link href="../calendrier/calendrier.css" rel="stylesheet" />			
<div class="row div-graph">
	<div class="col-md-4">
		<figure class="figure-left" >  
			<img class="graph" src="../graph/suivi_proc.php"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" href = "../graph/suivi_proc.php">Plein Ecran</a></center>
		</figure>
	</div>
	<div class="col-md-4">
		<figure class="figure-center" >  
			<img class="graph" src="../graph/duree_redac.php?idService=<?php echo $labo; ?>" alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" href = "../graph/duree_redac.php?idService=<?php echo $labo; ?>">Plein Ecran</a></center>
		</figure>
	</div>
	<div class="col-md-4">
		<figure class="figure-right" >  
			<img class="graph" src="../graph/ecart_proc.php?idService=<?php echo $labo; ?>" alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" href = "../graph/ecart_proc.php?idService=<?php echo $labo; ?>">Plein Ecran</a></center>
		</figure>
	</div>
</div>

<div class="container">
	<div class="page-header">
		<h2>Retour d'experience non FIFO</h2>
	</div>
	<center>
		<div class="jumbotron">
			<div class="row">
				<div class="col-md-3">
					<div class="autre-form">
						Date de début : <input id="dateDebNonFifo" placeholder="01/01/2014" value="<?php echo $dateDebFormNonFifo;?>"  type="text" class="calendrier"  size="8"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="autre-form">
						Date de fin : <input id="dateFinNonFifo" placeholder="01/01/2014" value="<?php echo $dateFinFormNonFifo;?>"  type="text" class="calendrier"  size="8"/>
					</div>
				</div>
				<div class="col-md-3">
					<input type="button" value="Valider" class="btn btn-lg btn-success" onClick="changeDate(0,<?php echo $idService ?>)" />
				</div>
				<div class="col-md-3">
					<div class="autre-form"><a class="btn btn-info" id="pdf_nonFifo" target="_blank" href="genPDFrexFifo.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">PDF</a></div>
					<a class="btn btn-info" id="excel_nonFifo" href="rex_exportEx.php?idService=<?php echo $idService ;?>&dateDeb=<?php echo $dateDebNonFifo ;?>&fifo=0&dateFin=<?php echo $dateFinNonFifo ;?>">Excel</a>
				</div>
			</div>
		</div>
	</center>
</div>
<div class="row div-graph">

	<div class="col-md-4">
		<figure class="figure-left" >  
			<img class="graph" id="rex_att_av_NonFifo" src="../graph/attente_equip_av.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_av_NonFifo_PleinEcran" href="../graph/attente_equip_av.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">Plein Ecran</a></center>
		</figure>
		
	</div>
	<div class="col-md-4">
		<figure class="figure-center" >  
			<img class="graph" id="rex_att_fin_NonFifo" src="../graph/attente_equip_fin.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_fin_NonFifo_PleinEcran" href = "../graph/attente_equip_fin.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">Plein Ecran</a></center>
		</figure>
		
	</div>
	<div class="col-md-4">
		<figure class="figure-center" >  
			<img class="graph" id="rex_att_fin_tous" src="../graph/attente_equip_fin.php?idService=<?php echo $idService ;?>&fifo=2&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_fin_tous_PleinEcran" href = "../graph/attente_equip_fin.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">Plein Ecran</a></center>
		</figure>
		
	</div>
	<div class="col-md-4">
		<figure class="figure-left" >  
			<img class="graph" id="rex_att_av_tous" src="../graph/attente_equip_av.php?idService=<?php echo $idService ;?>&fifo=2&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_av_tous_PleinEcran" href="../graph/attente_equip_av.php?idService=<?php echo $idService ;?>&fifo=0&dateDeb=<?php echo $dateDebNonFifo ;?>&dateFin=<?php echo $dateFinNonFifo ;?>">Plein Ecran</a></center>
		</figure>
		
	</div>
</div>
<?php

$idService2=$labo;
$anneeCour=date("Y");
$dateDeb=$anneeCour."-01-01";
$dateFin=$anneeCour."-12-31";

//formatage pour la value des input
$dateDebForm=explode("-",$dateDeb);
$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
$dateFinForm=explode("-",$dateFin);
$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];

?>

<div class="container">
	<div class="page-header">
		<h2>Retour d'experience FIFO</h2>
	</div>
	<center>
		<div class="jumbotron">
			<div class="row">
				<div class="col-md-3">
					<div class="autre-form">
						Date de début : <input id="dateDeb" placeholder="01/01/2014" value="<?php echo $dateDebForm;?>"  type="text" class="calendrier"  size="8"/>
					</div>
				</div>
				<div class="col-md-3">
					<div class="autre-form">
						Date de fin : <input id="dateFin" placeholder="01/01/2014" value="<?php echo $dateFinForm;?>"  type="text" class="calendrier"  size="8"/>
					</div>
				</div>
				<div class="col-md-3">
					<input type="button" value="Valider" class="btn btn-lg btn-success" onClick="changeDate(1,<?php echo $idService2 ?>)" />
				</div>
				<div class="col-md-3">
					<div class="autre-form"><a class="btn btn-info" id="pdf_Fifo" target="_blank" href="genPDFrexFifo.php?idService=<?php echo $idService2 ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>">PDF</a></div>
					<a class="btn btn-info" id="excel_Fifo" href="rex_exportEx.php?idService=<?php echo $idService2 ;?>&dateDeb=<?php echo $dateDeb ;?>&fifo=1&dateFin=<?php echo $dateFin ;?>">Excel</a>
				</div>
			</div>
		</div>
	</center>
</div>
<div class="row div-graph">
	<div class="col-md-4">
		<figure class="figure-left" >  
			<img class="graph" id="rex_test" src="../graph/rex_fifo_test.php?idService=<?php echo $idService ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_test_PleinEcran" href = "../graph/rex_fifo_test.php?idService=<?php echo $idService2 ;?>&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>&fifo=1">Plein Ecran</a></center>
		</figure>
	</div>
	<div class="col-md-4">
		<figure class="figure-center" >  
			<img class="graph" id="rex_att_av" src="../graph/attente_equip_av.php?idService=<?php echo $idService2 ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_av_PleinEcran" href = "../graph/attente_equip_av.php?idService=<?php echo $idService2 ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>">Plein Ecran</a></center>
		</figure>
	</div>
	<div class="col-md-4">
		<figure class="figure-right" >  
			<img class="graph" id="rex_att_fin" src="../graph/attente_equip_fin.php?idService=<?php echo $idService2 ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" id="rex_att_fin_PleinEcran" href = "../graph/attente_equip_fin.php?idService=<?php echo $idService2 ;?>&fifo=1&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>">Plein Ecran</a></center>
		</figure>
	</div>
</div>


<div class="container">
	<div class="page-header">
		<h2>Retard de livraison des équipements avant test</h2>
	</div>
	<a class="btn btn-info" target="_blank" href="genPDFrexFifo.php?idService=<?php echo $idService2 ;?>&arg=retard&dateDeb=<?php echo $dateDeb ;?>&dateFin=<?php echo $dateFin ;?>">PDF</a>
	
</div>
<div class="row div-graph">
	<div class="col-md-4">
		<figure class="figure-left" >  
			<img class="graph" src="../graph/retardEnregistre.php?idService=<?php echo $idService2 ;?>"  alt="Erreur de chargement du diagramme"/>
			<center><a class="btn btn-info" href = "../graph/retardEnregistre.php?idService=<?php echo $idService2 ;?>">Plein Ecran</a></center></a>
		</figure>
	</div>
	
	
</div>




<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
*/

require('bottom.php');