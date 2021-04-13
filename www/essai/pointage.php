<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
<?php
require('top.php');
$labo=$_SESSION["infoUser"]["idService"];
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['famille']) && isset($_POST['heure'])){
	
	$err = false;
	$fam=$_POST['famille'];
	$cpt_modele = count($fam) -1 ;
	$heure = $_POST['heure'];
	$modele = $_POST['modele'];
	$cpt = 0;
	foreach ($fam as $l){
		
		while (!isset($heure[$cpt]) or $heure[$cpt]==""){
			
			$cpt += 1;
			$cpt_modele -=1;
		}
		#famille-idEssai;
		$choix = explode ("-",$l);
		$str = "SELECT idEssai_ESSAI FROM famille_essai WHERE idEssai_ESSAI=".$choix[1].";";
		$req=mysqli_query($bdd,$str);
		
		if (mysqli_num_rows($req) == 0){
			
		$str = "INSERT INTO famille_essai values (".$choix[1].", '".$choix[0]."', '".$heure[$cpt]."', '".$choix[2]."', '".$choix[0]."', NULL);";
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo "<div class='alert alert-danger'><strong>Erreur d'affectation de la famille de l'equipement (INSERTION)</strong></div>";
				$err = true;
				break;
			}
			
		}else{
			
			$str = "UPDATE famille_essai SET famille_FAMILLE='".$choix[0]."', modeleFamille_FAMILLE ='".$choix[2]."', heure_FAMILLE='".$heure[$cpt]."' WHERE idEssai_ESSAI=".$choix[1].";";
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo "<div class='alert alert-danger'><strong>Erreur d'affectation de la famille de l'equipement</strong></div>";
				$err = true;
				break;
			}
		}
		$cpt += 1;		
		$cpt_modele -= 1;
	}
	if (!$err){
		
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
		<h2>Pointage - Semaine n° <?php echo $numSemaine; ?></h2>
	</div>
	<div class="jumbotron">
		<div class="row">
			<div class="col-md-4">
				<button onclick="document.location.href = 'pointage.php?date=<?php echo $datePrec?>'" class="btn btn-primary btn-block">Semaine précédente</button>
			</div>
			<div class="col-md-4">
				<button onclick="document.location.href = 'pointage.php'" class="btn btn-primary btn-block">Semaine actuelle</button>
			</div>
			<div class="col-md-4">
				<button onclick="document.location.href = 'pointage.php?date=<?php echo $dateSuiv?>'" class="btn btn-primary btn-block">Semaine suivante</button>
			</div>
		
		</div>
	</div>
	<div class="jumbotron">

		<div id="err" class="alert alert-danger"><strong>Veuillez compléter toutes les informations</strong></div>
		<form method="post" action="pointage.php" onsubmit="return verif()">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Numéro</th><th class="th-sm" scope="col">Date de lancement</th><th class="th-sm" scope="col">Date de fin</th><th class="th-sm" scope="col">Affaire</th>
			<th class="th-sm" scope="col">Equipement</th><th class="th-sm" scope="col">OF</th><th class="th-sm"  scope="col">Famille</th><th class="th-sm"  scope="col">Modèle</th><th class="th-sm"  scope="col">Heure</th></thead><tbody>
	<?php
	
	
	$str="SELECT e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire, e.retard_interne, e.planifie,e.pastilleOrange, e.pastilleRouge, e.retardME, e.commentaire
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
	
	
	$cpt = mysqli_num_rows($req);
	$ligne = 0;
	$identifiant = 0;
	while ($lg=mysqli_fetch_object ($req)){
		
		#$str_famille = "SELECT distinct(nomFamille) as famille, idFamille, heure FROM famille GROUP BY famille;";
		$str_famille = "SELECT nomFamille as famille, modeleFamille, idFamille, heure FROM famille GROUP By famille;";
		$req_famille= mysqli_query($bdd,$str_famille);
		
		$str_fam = "SELECT famille_FAMILLE, modeleFamille_FAMILLE, heure_FAMILLE FROM famille_essai WHERE idEssai_ESSAI = ".$lg->idEssai.";";
		$req_fam= mysqli_query($bdd,$str_fam);
		$family = "";
		$model = "NULL";
		if (mysqli_num_rows($req_fam)){
			$lg_fam = mysqli_fetch_object($req_fam);
			$family = $lg_fam-> famille_FAMILLE;
			$model = $lg_fam->modeleFamille_FAMILLE;
		}
		
		
		
		
		$str_of = "SELECT noOF_EQUIPEMENT_OF, nomModele from type_modele, equipement_of, tester where idEssai_ESSAI = ".$lg->idEssai." and noOF_EQUIPEMENT_OF = noOF and idModele = idModele_TYPE_MODELE;";
		$reqof= mysqli_query($bdd,$str_of);
		$of = "";
		#$modele = "";
		while ($lgof=mysqli_fetch_object ($reqof)){
			
			$of.= $lgof->nomModele." ".$lgof->noOF_EQUIPEMENT_OF;
			$of.="\n";
			#$modele = $lgof->nomModele;
		}
		
		$date_deb=$lg->date_debut;
		$date_fin=$lg->date_fin;
		$date_fin_fr = multiexplode (array("-", " "), $date_fin);
		$date_deb_fr = multiexplode (array("-", " "), $date_deb);
		
		$saisi = false;
		
		echo '<tr id="'.$identifiant.'" scope="row"><td scope="row">'.$lg->idEssai.'</td><td scope="row"><span class="ukDate">'.$date_deb.'</span>'.$date_deb_fr[2]."/".$date_deb_fr[1]."/".$date_deb_fr[0].'</td><td scope="row"><span class="ukDate">'.$date_fin.'</span>'.$date_fin_fr[2]."/".$date_fin_fr[1]."/".$date_fin_fr[0].'</td><td>'.$lg->affaire.'</td><td scope="row" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=pointage" class="btn-link">'.$lg->equipement.'</td><td scope="row" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=pointage" class="btn-link">'.$of.'</td>
		<td scope="row">';
		
		echo '<div class="row">';
		while ($lg_famille= mysqli_fetch_object($req_famille)){
			
			$heure = $lg_famille->heure;
			$id = $lg_famille->idFamille.$lg->idEssai;
			
			#echo '<div class="col-md-4 col-sm-6">
			#	<div class="btnRadio">
			#		<input id="'.$id.'" name="famille['.$cpt.']" type="radio" onclick="change_heure('.$id.',\''.$lg_famille->famille.'\',\''.$modele.'\')" value="'.$lg_famille->famille.'"-"'.$lg->idEssai.'";';
			#		echo '> '.$lg_famille->famille.'
					
			#	</div>
			#</div>';
			
			echo '<div class="col-md-4 col-sm-6">
				<div class="btnRadio"><label>
					<input ';
				if ($family == $lg_famille->famille){
					
					$saisi = true;
					echo 'checked ';
				}
				
				echo 'id="'.$id.'" name="famille['.$cpt.']" type="radio" onclick="change_heure('.$identifiant.')" value="'.$lg_famille->famille.'-'.$lg->idEssai.'-'.$lg_famille->modeleFamille.'";';
					echo '> '.$lg_famille->famille.'</label>
					
				</div>
			</div>';
			
			
			$ligne +=1;
		}
		echo '</div>';
			
			
			
		$str_modele = "SELECT nomModele FROM type_modele";
		$req_modele = mysqli_query($bdd, $str_modele);

		echo '</td><td id="modele" scope="row"><div class="row">';
		echo '<div class="col-md-6 col-lg-12 col-sm-6"><label><input ';
		if ($model == ""){
			echo 'checked'; 
		}
		echo ' name="modele['.$cpt.']" type="radio" onclick="change_heure('.$identifiant.')" value=""> Non renseigné</label></div>';

		while($lg = mysqli_fetch_object($req_modele))
		{
			echo '<div class="col-md-6 col-lg-12 col-sm-6"><label><input ';
			if ($model == $lg->nomModele){
				echo 'checked'; 
			}
			echo ' name="modele['.$cpt.']" type="radio" onclick="change_heure('.$identifiant.')" value="'.$lg->nomModele.'"> '.$lg->nomModele.'</label></div>';
		}

		echo '</div></td><td scope="row"><input class="form-control" name=heure[] id="heure" type="text"';
		if ($saisi){
							
			echo 'value = '.$lg_fam->heure_FAMILLE;
		}else {
			
			echo 'value=""';
		}
		echo '></td></tr>';
		$cpt -= 1;
		$identifiant += 1;
		

	}
	echo '</tbody></table><div class="text-center">
	<input type="submit" class="btn btn-lg btn-primary" value="Valider" />
	<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href=\'./index.php\'" value="Retour" />
	</div></form></div></div>';
}
?>
<script>
$("#err").hide();
function verif (){
	
	var erreur = false;
	$('input[type=radio]:checked').each (function (){
		
		if ($(this).parent().parent().parent().parent().parent().next().next().children().val()== ""){
			
			$(this).parent().parent().parent().parent().parent().next().next().children().css('border', '2px solid red');
			$('#err').show();
			erreur=true;
			
		}
	});
	if (!erreur){
		
		return true;
		
	}else {
		
		return false;
	}
}
/*function change_heure_modele (id, famille, modele){
	
	console.log(famille);
	console.log(modele);
	console.log(id);
	if (modele != ""){
		
		var fam = famille.replace(/ /g, "");
		$.ajax ({
			
			type :'POST',
			url : 'ajaxFamille.php',
			data : 'famille=' + famille+'&modele='+modele,
			success : function (data){
				
				heure = JSON.parse(data);
				console.log(heure[0]);
				console.log($("#"+id).parent().parent().parent().parent().next().children());
				$("#"+id).parent().parent().parent().parent().next().children().val(heure[0]);
			},
			error : function (data){
				alert("Erreur requête AJAX");

			}	
		})	
	}

}*/

function change_heure (id){
	
	var jid = "#"+id;
	var famille = $(jid+" .btnRadio input[type=radio]:checked").val();
	var modele = $(jid+" #modele input[type=radio]:checked").val();
	famille = famille.split("-")[0];
	
	var val = $(jid+" .btnRadio input[type=radio]:checked").val();
	var tab = val.split("-");
	var newval = tab[0]+"-"+tab[1]+"-"+modele;
	$(jid+" .btnRadio input[type=radio]:checked").val(newval);

	var fam = famille.replace(/ /g, "");

	console.log('ajaxFamille.php?famille=' + famille + "&modele=" +modele);
	$.ajax ({
		
		type :'GET',
		url : 'ajaxFamille.php?famille=' + famille + "&modele=" +modele,
		success : function (data){
			
			heure = JSON.parse(data);
			$(jid +" input[type=text]").val(heure[0]);
		},
		error : function (data){
			alert("Erreur requête AJAX");

		}	
	})	
}

$(document).ready(function() {
	$('#exemple').DataTable( {
		
		"order" : [[1, "desc"]],
		"lengthMenu":[50,100,500],
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
