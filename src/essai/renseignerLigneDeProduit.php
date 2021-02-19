<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
<?php
require('top.php');
$labo=$_SESSION["infoUser"]["idService"];
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['secteur'])){
	
	$err = false;
	$ldp=$_POST['secteur'];
	foreach ($ldp as $l){

			
		$choix = explode ("-",$l);
		$str = "update essai set ligneProd =$choix[0] where idEssai = ".$choix[1].";";
		$req=mysqli_query($bdd,$str);
		if(!$req){
			echo "<div class='alert alert-danger'><strong>Erreur d'affectation des lignes de produits </strong></div>";
			$err = true;
			break;
		}
			
	}
	
	if(!$err && !isset($_GET["back"]))
	{
		echo '<script src="../js/success.js"></script>';
	}
	
	
}
else{
	function multiexplode($delimiters, $string){
	
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode ($delimiters[0], $ready);
		return $launch;
	}
	
	if (isset ($_GET['date'])){
		
			
		$dateAct= $_GET['date'];
		
		$date = multiexplode (array("-", ":", " "), $dateAct);
		
		$time = mktime(0,0,0,$date[1], $date[2], $date[0]);
		$numSemaine = intval(strval(date('W', $time)));
		$datePrec = date('Y-m-d 00:00:00',strtotime($date[0]."-".$date[1]."-".$date[2]." 00:00:00 -1 week"));
		$dateSuiv = date('Y-m-d 00:00:00',strtotime($date[0]."-".$date[1]."-".$date[2]." 00:00:00 +1 week"));
		
		
		
	}else {
		
		$dateAct= date('Y-m-d 00:00:00',strtotime("Monday this week"));
		$date = multiexplode (array("-", ":", " "), $dateAct);
		$time = mktime(0,0,0,$date[1], $date[2], $date[0]);
		$numSemaine = intval(strval(date('W', $time)));
		$str = $date[0]."-".$date[1]."-".$date[2]." 00:00:00 -1 week";
		$datePrec = date('Y-m-d 00:00:00',strtotime($str));
		$dateSuiv = date('Y-m-d 00:00:00',strtotime($date[0]."-".$date[1]."-".$date[2]." 00:00:00 +1 week"));
	}
	?>
	<style>
	input {
		
		background-color: #11ffee00;
		border : 0px;
		color:black;
	}
	</style><link href="../calendrier/calendrier.css" rel="stylesheet" />	
	<link href="../css/addons/datatables.min.css" rel="stylesheet">
	<link href="../css/starter-template.css" rel="stylesheet">
	<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
	<script type="text/javascript" src="../js/table.js"></script>
	<div class="container-fluid">
	<div class="page-header">
		<h2>Ligne de produit - Semaine n° <?php echo $numSemaine; ?></h2>
	</div>
	<div class="jumbotron">
		<div class="row">
			<div class="col-md-4">
				<button onclick="document.location.href = 'renseignerLigneDeProduit.php?date=<?php echo $datePrec?>'" class="btn btn-primary btn-block">Semaine 
précédente</button>
			</div>
			<div class="col-md-4">
				<button onclick="document.location.href = 'renseignerLigneDeProduit.php'" class="btn btn-primary btn-block">Semaine actuelle</button>
			</div>
			<div class="col-md-4">
				<button onclick="document.location.href = 'renseignerLigneDeProduit.php?date=<?php echo $dateSuiv?>'" class="btn btn-primary btn-block">Semaine suivante
</button>
			</div>
		
		</div>
	</div>
	<div class="jumbotron">
		
		<form method="post" action="renseignerLigneDeProduit.php<?php  if (isset ($_GET["back"])) echo "?back=".$_GET["back"]; ?>">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Numéro</th><th class="th-sm" scope="col">Date de 
lancement</th><th class="th-sm" scope="col">Date de fin</th><th class="th-sm" scope="col">Affaire</th>
			<th class="th-sm" scope="col">Equipement</th><th class="th-sm" scope="col">OF</th><th class="th-sm"  scope="col">Ligne de produit</th></thead><tbody>
	<?php
	
	
	$str="SELECT e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire, e.retard_interne, e.planifie,e.pastilleOrange, e.pastilleRouge, 
e.retardME, e.commentaire
			from essai e, etatessai et
			where
			 e.idService_SERVICE='$labo'
			and et.idEssai_ESSAI=e.idEssai
			and e.date_fin>='$dateAct'
			and e.idService_SERVICE='$labo'
			and e.date_debut < DATE_ADD('$dateAct', INTERVAL 7 DAY)
			and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
			and et.idetat_etat != 20
			and (e.planifie=1 or (e.planifie=0 and et.idetat_etat!=22))
			order by date_debut;";
			
			
	$req=mysqli_query($bdd, $str);
	
	
	$cpt = 0;
	while ($lg=mysqli_fetch_object ($req)){
		
		$str_of = "select noOF_EQUIPEMENT_OF, nomModele from type_modele, equipement_of, tester where idEssai_ESSAI = ".$lg->idEssai." and noOF_EQUIPEMENT_OF = noOF and 
idModele = idModele_TYPE_MODELE;";
		$reqof= mysqli_query($bdd,$str_of);
		$of = "";
		while ($lgof=mysqli_fetch_object ($reqof)){
			
			$of.= $lgof->nomModele." ".$lgof->noOF_EQUIPEMENT_OF;
			$of.="\n";
		}
		
		
		$str2 = "select ligneProd from essai where idEssai = ".$lg->idEssai.";";
		$req2= mysqli_query($bdd,$str2);
		$lg2 = mysqli_fetch_object ($req2);
		$autre = $lg2->ligneProd;
		
		
		$date_deb=$lg->date_debut;
		$date_fin=$lg->date_fin;
		$date_fin_fr = multiexplode (array("-", " "), $date_fin);
		$date_deb_fr = multiexplode (array("-", " "), $date_deb);
		
		$str_ligne= "SELECT idLigne, nomLigne FROM ligneproduit";
		$req_ligne = mysqli_query($bdd, $str_ligne);
		
		
		echo '<tr scope="row"><td scope="row">'.$lg->idEssai.'</td><td scope="row"><span class="ukDate">'.$date_deb.'</span>'.$date_deb_fr[2]."/".$date_deb_fr[1]."/".
$date_deb_fr[0].'</td><td scope="row"><span class="ukDate">'.$date_fin.'</span>'.$date_fin_fr[2]."/".$date_fin_fr[1]."/".$date_fin_fr[0].'</td><td>'.$lg->
affaire.'</td><td class="btn-link" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=renseignerLigneDeProduit" scope="row">'.$lg->equipement.'</td><td class="btn-link" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=renseignerLigneDeProduit" scope="row">'.$of.'</td>
		<td scope="row">';
		
		while ($lg_ligne = mysqli_fetch_object($req_ligne)){
			
			echo '<div class="col-md-2">
				<div class="btnRadio">
				<input name="secteur['.$cpt.']" type="radio" value="'.$lg_ligne ->idLigne.'-'.$lg->idEssai.'"';
					if ($autre == $lg_ligne ->idLigne) echo "checked"; 
					echo'> '.$lg_ligne ->nomLigne.'
				</div>
			</div>';
		}
			
		echo '</td></tr>';
		
	
	
		$cpt += 1;

	}
	

	echo '</tbody></table><div class="text-center">
	<input type="submit" class="btn btn-lg btn-primary" value="Valider" />
	<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href=\'./index.php\'" value="Retour" />
	</div></form></div></div>';
		
}
?>
<script>

$(document).ready(function() {
	$('#exemple').DataTable( {
		
		"lengthMenu":[50,100,500],
		"order" : [[1, "desc"]],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'essai _START_ &agrave; _END_ sur _TOTAL_ essai(s)",
			"sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
			"sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			"sInfoPostFix":    "",
			"sLoadingRecords": "Chargement en cours...",
			"sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			"sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
			"oPaginate": {
				"sFirst":      "Premier",
				"sPrevious":   "Pr&eacute;c&eacute;dent",
				"sNext":       "Suivant",
				"sLast":       "Dernier"
			},
			"oAria": {
				"sSortAscending":  ": activer pour trier la colonne par ordre croissant",
				"sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
			},
			"select": {
					"rows": {
						_: "%d lignes séléctionnées",
						0: "Aucune ligne séléctionnée",
						1: "1 ligne séléctionnée"
					} 
			}
		}

	});
});
</script>
<?php

require('bottom.php');
