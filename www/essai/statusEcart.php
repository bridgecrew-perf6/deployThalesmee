<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('../conf/connexion_param.php'); 
require('../conf/connexionPDO_param.php');// connexion a la base
require('top.php');
$labo=$_SESSION["infoUser"]["idService"];
if (isset($_GET["date_deb"]) && isset($_GET["date_fin"])){
	
	//Formatage des paramètres
	$date_deb = date_create($_GET["date_deb"]);
	$date_fin = date_create($_GET["date_fin"]);
	$dateDebForm = date_format($date_deb, "d/m/Y");
	$dateFinForm = date_format($date_fin, "d/m/Y");
	$date_deb = $_GET["date_deb"];
	$date_fin = $_GET["date_fin"];

}else{
	 
	//Date de début date (date actuelle moins deux mois)
	$date_deb = strftime("%Y-%m-%d", mktime(0,0,0,date('m')-2, date('d'), date('Y')));
	//Date de fin (date actuelle)
	$date_fin = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//formatage pour la value des input
	$dateDebForm=explode("-",$date_deb);
	$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
	$dateFinForm=explode("-",$date_fin);
	$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];

}
?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />	
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<link href="../css/starter-template.css" rel="stylesheet">
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>
<div class="container-fluid margin" style="margin-bottom:auto;">
	<div class="page-header">
		<h2>Tableau récapitulatif des anomalies</h2>
	</div>
	<center>
		<div class="jumbotron">
			<div class="row" style="margin-bottom:10px;">
				<div class="col-md-2">
					<div class="autre-form">
						Date de début : 
					</div>
				</div>
				<div class="col-md-3">
					<input name="date_deb" id="dateDeb" placeholder="01/01/2014" value="<?php echo $dateDebForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
				<div class="col-md-2">
					<div class="autre-form">
						Date de fin : 
					</div>
				</div>
				<div class="col-md-3">
					<input name="date_fin" id="dateFin" placeholder="01/01/2014" value="<?php echo $dateFinForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
				<div class="col-md-2">
					<input type="button" value="Valider" class="btn btn-success" onClick="changeDate()" />
				</div>
				
			</div>
		</div>
	</center>
</div>

<div class="container-fluid margin">
	<div class="jumbotron">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Date de lancement</th><th class="th-sm" scope="col">Affaire</th><th class="th-sm" scope="col">Equipement</th><th class="th-sm" scope="col">OF</th>
			<th class="th-sm" scope="col">Anomalie</th><th class="th-sm" scope="col">Cause</th><th class="th-sm" scope="col">Nom du technicien</th><th class="th-sm"  scope="col">Type d'anomalie</th><th class="th-sm" scope="col">Status</th></thead><tbody>
	

<?php
//Date au format dd/mm/yyyy et sans l'heure
function multiexplode($delimiters, $string){
	
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode ($delimiters[0], $ready);
	return $launch;
}

//Ajout des heures pour les dates
$date_deb = date($date_deb.' 00:00:00');
$date_fin = date($date_fin.' 23:59:00');

//Requete permettant de récupérer les essais en anomalie dans l'intervalle de dates
$str="SELECT DISTINCT(a.idEssai), e.affaire, e.equipement, a.quiss_mpti, a.status, a.descriptif, e.date_debut from etatessai et, anomalie  a, essai e where e.idService_SERVICE='$labo' and a.idEssai=et.idEssai_Essai and a.idEssai=e.idEssai and  et.idEtat_Etat >=21 and e.date_debut >= '$date_deb' and e.date_debut <= '$date_fin' GROUP BY a.idEssai order by et.dateEtat DESC;";
$reqEssai=mysqli_query($bdd, $str);

//Pour chaques essais
while($lg=mysqli_fetch_object($reqEssai)){
	
	//Selection du technicien ayant réalisé l'essai
	$str="SELECT vtp.nomEmp from vibtesterpar vtp where idEssai=".$lg->idEssai.";";
	$req=mysqli_query($bdd, $str);

	//Si le nom du technicien est saisi
	if (mysqli_num_rows($req) != 0) $nomEmp = mysqli_fetch_object($req)->nomEmp;
	else $nomEmp = 'Non renseigné';

	//Selection des OFs associés à l'essai
	$str="SELECT noOF_equipement_of from tester where idEssai_ESSAI=".$lg->idEssai.";";
	$reqOF=mysqli_query($bdd,$str);
	$of="";
	while($lgOF=mysqli_fetch_object($reqOF))
		$of.=$lgOF->noOF_equipement_of." ";

	//Stockage des informations
	$idEssai = $lg->idEssai;
	$quiss = $lg->quiss_mpti;
	$status = $lg->status;
	$date=$lg->date_debut;
	$datefr = multiexplode (array("-", " "), $date); //Formatage de la date
	$nom = $nomEmp;
	$descr = $lg->descriptif;
	$affaire = $lg->affaire;
	$equipement = $lg->equipement;
	
	if ($status == 0) $status = "Non validé"; //Status
	else $status = "Ok";

	//Selection des causes
	$str = "SELECT nomCause FROM cause";
	$req_cause = mysqli_query($bdd, $str);

	//Selection de la cause de l'anomalie
	$str = "SELECT nomCause FROM cause_anomalie WHERE idEssai_ESSAI =".$lg->idEssai.";";
	$req = mysqli_query($bdd, $str);

	if (mysqli_num_rows($req) > 0) $cause = mysqli_fetch_object($req)->nomCause; //Cause associée
	else $cause = ""; //Cause non associée
	
	echo '<tr style="cursor:pointer" id="'.$lg->idEssai.'" scope="row"><td scope="row"><span class="ukDate">'.$date.'</span>'.$datefr[2]."/".$datefr[1]."/".$datefr[0].'</td><td>'.$affaire.'</td><td scope="row" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=statusEcart" class="btn-link">'.$equipement.'</td><td scope="row" onclick=document.location.href="detailsEssai.php?idEssai='.$lg->idEssai.'&back=statusEcart" class="btn-link">'.$of.'</td><td scope="row">'.$descr.'</td>';
	echo '<td scope="row">
	<select class="form-control cause" name="cause">
		<option value="" selected >Cause</option>';
		if ($cause != "") echo '<option selected value="'.$cause.'" >'.$cause.'</option>'; //Cause associée => selected
		while($lgCause=mysqli_fetch_object($req_cause))
		{
			$cas = $lgCause->nomCause; //Affichage des autres causes de la iste
			if($cause == "" || $cas!=$cause) echo '<option value="'.$cas.'" >'.$cas.'</option>';		
		}		
	echo '</select></td>';
	echo '<td scope="row">'.$nom.'</td><td>'.$quiss.'</td><td>'.$status.'</td></tr>';
	
}
?>
				</tbody>
			</table>
		</div>
	<div class="text-center">
		<a type="button" href="index.php" class="btn btn-primary btn-lg" value="Retour">Retour</a>
		<div onclick="submit()" class="btn btn-success btn-lg">Valider</div>
	</div>
</div>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
<script src="../js/statusEcart.js"></script>

<?php
require('bottom.php');