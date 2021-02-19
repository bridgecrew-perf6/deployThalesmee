<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('../conf/connexion_param.php'); 
require('../conf/connexionPDO_param.php');// connexion a la base
require('top.php');

//Date au format dd/mm/yyyy et sans l'heure
function multiexplode($delimiters, $string){
	
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode ($delimiters[0], $ready);
	return $launch;
}

//Récupération du service
$labo=$_SESSION["infoUser"]["idService"];

if (isset($_GET["date_deb"]) && isset($_GET["date_fin"])) //Si les dates sont envoyées
{	
	//Formatage
	$date_deb = date_create($_GET["date_deb"]);
	$date_fin = date_create($_GET["date_fin"]);
	$dateDebForm = date_format($date_deb, "d/m/Y");
	$dateFinForm = date_format($date_fin, "d/m/Y");
	$date_deb = $_GET["date_deb"];
	$date_fin = $_GET["date_fin"];

}else{
	 
	//Date de début date (date actuelle moins deux mois)
	$date_deb = strftime("%Y-%m-%d", mktime(0,0,0,date('m')-1, date('d'), date('Y')));
	//Date de fin (date actuelle)
	$date_fin = strftime("%Y-%m-%d", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//Formatage pour la value des input
	$dateDebForm=explode("-",$date_deb);
	$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
	$dateFinForm=explode("-",$date_fin);
	$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];

}
//Ajoutdes heures aux dates
$date_deb = date($date_deb.' 00:00:00');
$date_fin = date($date_fin.' 23:59:00');

//Requête de récupération des employés actifs ou qui ont pointées sur un essai de la liste
$str = "SELECT e.idEmp, nomEmp, prenomEmp FROM utilisateur, employe e WHERE idEmp_EMPLOYE = idEmp and `idService_SERVICE`=$labo and idEmp != 4 and idEmp != 3 and idEmp != 5 and (actif=1 or EXISTS (
		SELECT idEmp FROM pointage p, essai e, famille_essai f WHERE idEmp_EMPLOYE = e.idEmp 
		AND p.idEssai_ESSAI = e.idEssai 
		AND p.idEssai_ESSAI = f.idEssai_ESSAI";
	//Si la checkbox du reste positif a été cochée
	if (isset($_GET["reste"])) $str .= " and f.resteHeure > 0 ";
	//Si la checkbox du status a été cochée
	if (isset($_GET["status"])) $str .= " and f.status IS NOT NULL ";

	$str .= " and e.date_debut >= '$date_deb' 
		and e.date_debut <= '$date_fin'
	));";
$reqTech=mysqli_query($bdd,$str);

//Remplissage d'un tableau
$tabEmp = array();
while($lg = mysqli_fetch_object($reqTech))
{
	//Remplacement des espaces par des retours a la ligne et prénoms en minuscule
	$tabEmp[str_replace(" ", "<br>", ucfirst(strtolower($lg->prenomEmp))).'<br>'.$lg->nomEmp] = $lg->idEmp;
}

//Récupération des essais pointés
$str = "SELECT idEssai_ESSAI, idEmp_EMPLOYE, heure FROM pointage";
$req = mysqli_query($bdd, $str);
//Remplissage d'un tableau
$tabHeureEmp = array();
while($lg = mysqli_fetch_object($req))
{
	$tabHeureEmp[$lg->idEmp_EMPLOYE][$lg->idEssai_ESSAI] = $lg->heure;
}

//Sélection des essais 
$str="SELECT DISTINCT(idEssai), affaire, equipement, os, e.date_debut, heure_FAMILLE as heure, resteHeure, status from etatessai et, essai e LEFT JOIN famille_essai ON idEssai_ESSAI = idEssai where e.idService_SERVICE='$labo'";
	//Si la checkbox du reste positif a été cochée
	if (isset($_GET["reste"])) $str .= " and resteHeure > 0 ";
	//Si la checkbox du status a été cochée
	if (isset($_GET["status"])) $str .= " and status IS NOT NULL ";
$str .= " and e.date_debut >= '$date_deb' 
	and e.date_debut <= '$date_fin' 
	order by idEssai DESC ";
$reqEssai=mysqli_query($bdd, $str);

$str_status="SELECT idStatus, nomStatus FROM status"; //Récupération des status

?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />	
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<link href="../css/starter-template.css" rel="stylesheet">
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>
<script src="../js/pointageEquipe.js"></script>
<style type="text/css">
	th {
		word-wrap: break-word;;
		word-break: break-all;
		white-space: normal;
	}
</style>
<div class="container-fluid margin" style="margin-bottom:auto;">
	<form action="pointageEquipe.php" method="post">
	<div class="page-header">
		<h2>Pointage des équipes</h2>
	</div>
	<center>
		<div class="jumbotron">
			<div class="row" style="margin-bottom:10px;">
				<div class="col-md-2">
					<div class="autre-form">
						Date de début : 
					</div>
				</div>
				<div class="col-md-4">
					<input name="date_deb" id="dateDeb" placeholder="01/01/2014" value="<?php echo $dateDebForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
				<div class="col-md-2">
					<div class="autre-form">
						Date de fin : 
					</div>
				</div>
				<div class="col-md-4">
					<input name="date_fin" id="dateFin" placeholder="01/01/2014" value="<?php echo $dateFinForm;?>"  type="text" class="calendrier form-control"  size="8"/>
				</div>
				
			</div>
			<div class="row">
				<div class="col-md-2">
					<label class="checkbox-inline"><input type="checkbox" <?php if (isset($_GET["reste"])) echo "checked" ?> id="reste" name="reste" value="1" />Reste positif</label>
				</div>	
				<div class="col-md-2">
					<label class="checkbox-inline"><input type="checkbox" <?php if (isset($_GET["status"])) echo "checked" ?> id="status" name="status" value="1" />Status NOK</label>
				</div>	
			</div>
			<div class="row">
				<center><input type="button" value="Valider" class="btn btn-success" onclick="changeDate()" /></center>
			</div>
		</div>
	</center>

	<div class="jumbotron">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Date</th><th class="th-sm" scope="col">Affaire</th><th class="th-sm" scope="col">Equipement</th><th class="th-sm" scope="col">OS</th>
			<th class="th-sm" scope="col">Heure</th><th class="th-sm" scope="col">Reste</th>
			<?php 
				//Affichage des en-têtes des colonnes (nom des techniciens)
				foreach ($tabEmp as $key => $value) {
					echo '<th class="th-sm" scope="col">'.$key.'</th>';
				}
			?>
			<th class="th-sm" scope="col">Status</th>
				</thead>
				<tbody>
				<?php 
				//Pour chaques essais
				while($lg = mysqli_fetch_object($reqEssai))
				{
					$datefr = multiexplode (array("-", " "), $lg->date_debut); //Formatage de la date
					//Affichage des lignes
					echo '<tr id="'.$lg->idEssai.'">
							<td scope="col"><span class="ukDate">'.$lg->date_debut.'</span>'.$datefr[2]."/".$datefr[1]."/".$datefr[0].'</td>
							<td scope="col">'.$lg->affaire.'</td>
							<td scope="col">'.$lg->equipement.'</td>
							<td scope="col">'.$lg->os.'</td>
							<td scope="col"><input class="form-control heure" disabled type="text" value="'.$lg->heure.'" /></td>
							<td scope="col"><input disabled class="form-control" type="text" value="'.$lg->resteHeure.'" /></td>';
					//POur chaques techniciens affichage des input avec les valeurs si elles existent
					foreach ($tabEmp as $key => $value) {

						if (isset($tabHeureEmp[$value][$lg->idEssai]))
							echo '<td scope="col"><input id="'.$value.'" class="form-control emp" type="text" value="'.$tabHeureEmp[$value][$lg->idEssai].'" /></td>';
						else
							echo '<td scope="col"><input id="'.$value.'" class="form-control emp" type="text" value="0" /></td>';
					}
					
					echo '<td>
							<select class="form-control status">
								<option selected value="NULL">Status</option>';
								//Affichage des option qui sont les status de la table status
								$req_status = mysqli_query($bdd, $str_status); 
								while ($lg_status=mysqli_fetch_object($req_status))
								{
									echo '<option value="'.$lg_status->idStatus.'"';
									if ($lg->status == $lg_status->idStatus) echo "selected";
									echo ' >'.$lg_status->nomStatus.'</option>';
								}

							echo '</select>
						</td></tr>';
						//Modification du reste
					echo "<script>modifReste('".$lg->idEssai."')</script>";
				}
				?>

				</tbody>
			</table>
		</div>
	<div class="text-center"><a type="button" href="index.php" class="btn btn-primary btn-lg" value="Retour">Retour</a><div onclick="submit()" style="margin-left: 10px;" class="btn btn-success btn-lg">Valider</div></div>
	</form>
</div>
<script src="../calendrier/calendrier.js"></script>

<?php
require('bottom.php');