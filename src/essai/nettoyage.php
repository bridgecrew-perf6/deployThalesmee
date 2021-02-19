<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('../conf/connexion_param.php'); 
require('top.php');
$labo=$_SESSION['infoUser']['idService'];// service du labo

function multiexplode($delimiters, $string){
	
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode ($delimiters[0], $ready);
	return $launch;
}


//Initialisation du tableau des essais
$tabEssais = "'";
$tabEssaisSuppr = "'";
//Si les dates sont passées en paramètres
if (isset($_POST["date_deb"]) && isset($_POST["date_fin"])){
	
	$first = false;
	//Stockage de la date pour remplir le formulaire JJ/MM/AAAA
	$dateDebForm = $_POST["date_deb"];
	$dateFinForm = $_POST["date_fin"];
	
	$date_deb = $_POST["date_deb"];
	$date_fin = $_POST["date_fin"];
	//echo $date_deb;
	//echo $date_fin;
	$date_f = explode ("/", $date_fin);
	$date_d = explode ("/", $date_deb);
	
	//Transformation au format AAAA-MM-JJ HH:MM:SS
	$dateDeb = $date_d[2]."-".$date_d[1]."-".$date_d[0]." 00:00:00";
	$dateFin = $date_f[2]."-".$date_f[1]."-".$date_f[0]." 00:00:00";
	
	$etat = "";
	$libele_etat = array();

	//Si l'utilisateur veut les essais planifiés
	if (isset ($_POST['plannifie'])){
		
		$etat.="20,";
		array_push($libele_etat, "plannifie");
	}
	//Si l'utilisateur veut les essais reservés
	if (isset ($_POST['reserve'])){
		
		$etat.="21,";
		array_push($libele_etat, "reserve");
	}
	//Si l'utilisateur veut les essais en attente
	if (isset ($_POST['attente'])){
		
		$etat.="22,";
		array_push($libele_etat, "attente");
	}
	
	if (isset ($_POST['suppr'])){
		
		array_push($libele_etat, "suppr");
	}
	
	//On enlève le dernier caractère qui est une virgule
	$etat = substr($etat,0,-1);
		
	
	if (isset ($_POST['plannifie']) or (isset ($_POST['reserve'])) or (isset ($_POST['attente']))){
		
		//Requete pour récupérer les essais
		$str="SELECT e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire
				from essai e, etatessai et
				where
				e.idService_SERVICE='$labo'
				and et.idEssai_ESSAI=e.idEssai
				and et.idetat_etat in ($etat)
				and e.date_debut >= '$dateDeb'
				and e.date_debut < '$dateFin'
				and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
				order by date_debut;";

		$req=mysqli_query($bdd, $str);
	}
	
	if (isset ($_POST['suppr'])){
		//Requete pour récupérer les essais supprimés
		$strSuppr="SELECT e.idessaiSupp, e.dateDeb, e.dateFin, e.equipement, e.affaire, e.raison
				from essaisupp e
				where
				e.idService_SERVICE='$labo'
				and ((e.dateDeb >= '$dateDeb'
				and e.dateDeb < '$dateFin')
				or (e.dateDeb is NULL and e.DateFin is NULL))
				order by dateDeb;";
		$reqSuppr=mysqli_query($bdd, $strSuppr);
	}

//Si la date n'est pas connue
}else{
	
	$first = true;
	//Date de début date (date actuelle moins un an)
	$date_deb = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')-1));
	//Date de fin (date actuelle)
	$date_fin = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$etat = "20";
	$libele_etat = array();
	array_push($libele_etat, "plannifie");
		
	//formatage pour la value des input
	$dateDebForm=explode("-",$date_deb);
	$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
	$dateFinForm=explode("-",$date_fin);
	$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];
	
	$str="SELECT e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire
			from essai e, etatessai et
			where
			 e.idService_SERVICE='$labo'
			and et.idEssai_ESSAI=e.idEssai
			and et.idetat_etat in ($etat)
			and e.date_debut >= '$date_deb'
			and e.date_debut < '$date_fin'
			and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
			order by date_debut;";
	
	$req=mysqli_query($bdd, $str);

}
?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />	
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<link href="../css/starter-template.css" rel="stylesheet">
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>
<div id="se-pre-con-load" ></div>
<div class="container-fluid margin" style="margin-bottom:auto;">
	<div class="page-header">
		<h2>Supprimer des essais</h2>
	</div>
	<center>
		<div class="jumbotron">
		<form id= "form" method="post" action="nettoyage.php" onsubmit="return verif()">
			<div class="row" style="margin-bottom:10px;">
				<div class="col-md-3">
					<div class="autre-form">
						Date de début : 
					</div>
				</div>
				<div class="col-md-3">
					<input name="date_deb" id="dateDeb" placeholder="01/01/2014" value="<?php echo $dateDebForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
				<div class="col-md-3">
					<div class="autre-form">
						Date de fin : 
					</div>
				</div>
				<div class="col-md-3">
					<input name="date_fin" id="dateFin" placeholder="01/01/2014" value="<?php echo $dateFinForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
			</div>
			<div id = "erreur" class='alert alert-danger'><strong>Veuillez remplir le champ ci-dessous</strong></div>
			<div class="row" style="margin-bottom:10px; text-align:left;">
				<div class="col-md-3">
					<label class="checkbox-inline"><input type="checkbox" name="plannifie" value="1" <?php if (in_array("plannifie", $libele_etat)) echo 'checked' ?>><span class='carre bar-plannifie'></span>&nbsp;Essai planifié</label>
				</div>
				<div class="col-md-3">
					<label class="checkbox-inline"><input type="checkbox" name="reserve" value="1" <?php if (in_array("reserve", $libele_etat)) echo 'checked' ?>><span class='carre bar-reserve'></span>&nbsp;Essai reservé</label>
				</div>
				<div class="col-md-3">
					<label class="checkbox-inline"><input type="checkbox" name="attente" value="1" <?php if (in_array("attente", $libele_etat)) echo 'checked' ?>><span class='carre bar-attente'></span>&nbsp;Essai en attente</label>
				</div>
				<div class="col-md-3">
					<label class="checkbox-inline"><input type="checkbox" name="suppr" value="1" <?php if (in_array("suppr", $libele_etat)) echo 'checked' ?>>  &nbsp;Essai supprimés</label>
				</div>
			</div>
			<div class="text-center" style="margin-top:30px;">
				<input type="submit" class="btn btn-block btn-primary" value="Rechercher" />
			</div>
		</form>
		</div>
	</center>
	<div class="jumbotron">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Numéro</th><th class="th-sm" scope="col">Date de lancement</th><th class="th-sm" scope="col">Affaire</th><th class="th-sm" scope="col">Equipement</th><th class="th-sm" scope="col">OF</th>
			<?php
			if (isset ($_POST['suppr'])) echo '<th class="th-sm" scope="col">Raison</th>'
			?>
		</thead><tbody>
		<?php
		if ($first || (isset ($_POST['plannifie']) or (isset ($_POST['reserve'])) or (isset ($_POST['attente'])))){
			
			while ($lg=mysqli_fetch_object ($req)){
				
				//Selection des OF avec lerus modèles
				$str2="select nomModele, noOF_equipement_of from tester, equipement_of, type_modele
						where noOF_equipement_of = noOf and idModele = idModele_TYPE_MODELE and idEssai_ESSAI=".$lg->idEssai.";";
				$req2=mysqli_query($bdd, $str2);
				$of = "";
				//Date en fr pour le tableau
				$date_deb_fr = multiexplode (array("-", " "), $lg->date_debut);
				//Remplissage des OFs car peut avoir plusieurs OF pour un essai
				while ($lg2 = mysqli_fetch_object ($req2)){
					
					$of.= $lg2->nomModele." ".$lg2->noOF_equipement_of."\n";
				}
				//On affiche tous ça sous forme de tableau
				echo '<tr class="essai" id='.$lg->idEssai.' scope="row"><td scope="row">'.$lg->idEssai.'</td><td scope="row"><span class="ukDate">'.$lg->date_debut.'</span>'.$date_deb_fr[2]."/".$date_deb_fr[1]."/".$date_deb_fr[0].'</td><td>'.$lg->affaire.'</td><td onclick=\'document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=nettoyage"\' class="btn-link" scope="row">'.$lg->equipement.'</td><td onclick=\'document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=nettoyage"\' class="btn-link" scope="row">'.$of.'</td>';
				$tabEssais.= $lg->idEssai." ";
			}
		}
		
		if (isset ($_POST['suppr'])){
			
			while ($lg=mysqli_fetch_object ($reqSuppr)){
				
				//Selection des OF avec lerus modèles
				$str2="select noOf_EQUIPEMENT_OF
									from testerSupp
									where idEssai_ESSAISupp=".$lg->idessaiSupp.";";
				$req2=@mysqli_query($bdd,$str2);
				$of = "";
				//Date en fr pour le tableau

				//Remplissage des OFs car peut avoir plusieurs OF pour un essai
				while ($lg2 = mysqli_fetch_object ($req2)){
					
					$of.= $lg2->noOf_EQUIPEMENT_OF."\n";
				}
				//On affiche tous ça sous forme de tableau
				if ($lg->dateDeb != NULL){
					
					$date_deb_fr = multiexplode (array("-", " "), $lg->dateDeb);
					echo '<tr onclick=\'document.location.href="detailsEssaiSupp.php?idEssai='.$lg->idessaiSupp.'"\' class="essai" id='.$lg->idessaiSupp.' scope="row"><td scope="row">'.$lg->idessaiSupp.'</td><td scope="row"><span class="ukDate">'.$lg->dateDeb.'</span>'.$date_deb_fr[2]."/".$date_deb_fr[1]."/".$date_deb_fr[0].'</td><td>'.$lg->affaire.'</td><td scope="row">'.$lg->equipement.'</td><td scope="row">'.$of.'</td><td scope="row">'.$lg->raison.'</td>';
				}else {
					
					echo '<tr onclick=\'document.location.href="detailsEssaiSupp.php?idEssai='.$lg->idessaiSupp.'"\' class="essai" id='.$lg->idessaiSupp.' scope="row"><td scope="row">'.$lg->idessaiSupp.'</td><td scope="row">NULL</td><td>'.$lg->affaire.'</td><td scope="row">'.$lg->equipement.'</td><td scope="row">'.$of.'</td><td scope="row">'.$lg->raison.'</td>';
				}
				
				$tabEssaisSuppr.= $lg->idessaiSupp." ";
			}
			
			
		}
		//On oublie pas de fermer le guillement ouvert lors de l'initialisation
		$tabEssais.= "'";
		$tabEssaisSuppr.= "'";
		
		//On oublie pas non plus de fermer les balises et d'ajouter le bouton
		echo '</tbody></table>
		<div class="text-center" style="margin-top:30px;">
			<input type="button" class="btn btn-block btn-danger" value="Supprimer" onclick="if (confirm (\'Voulez-vous vraiment supprimer les essais ?\')) suppr('.$tabEssais.','.$tabEssaisSuppr.')"/>
		</div>';
		?>
	</div>
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/nettoyage.js"></script>
<?php
require('bottom.php');