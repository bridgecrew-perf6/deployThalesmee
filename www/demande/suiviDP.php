<?php 
require('top.php');

if (isset($_GET['idDP'])){ //on a bien recu le numero de demande
	// on connait la demande a charger
	$idDP=$_GET['idDP'];
	require('../conf/connexion_param.php'); //connexion a la bdd
	require('../fonction.php'); //page contenant verifDPRapide
	
	$str="select a.affaire , a.equipement ,a.delai, a.dateDemandeDP_redigerDP as dateDP from DEMANDE_PROCEDURE a where a.idDp=$idDP;";
	$req=mysqli_query($bdd,$str);

	while($lg=mysqli_fetch_object($req))
	{ 	$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$delai=$lg->delai;
		//on passe au format français
		$delai=date('d/m/Y',strtotime($delai));
		$dateDP=$lg->dateDP;
		//on passe au format français
		$dateDP=date('d/m/Y',strtotime($dateDP));
	}

	// pour chaque procedure liee a la DP, on recupere :
	// le nom du labo, le nom du redacteur et le dernier etat
	$str="select s.nomService as labo, emp.nomEmp as nom,emp.prenomEmp as prenom,et.nomEtat as lastEtat
	from (SERVICE s, etatProc ep, ETAT et, PROCEDURES p) left join EMPLOYE emp on p.idEmp_EMPLOYE=emp.idEmp
	where p.idService_SERVICE=s.idService
	and p.idProc=ep.idProc_PROCEDURES
	and ep.idEtat_ETAT=et.idEtat
	and p.idDP_DEMANDE_PROCEDURE=$idDP
	and ep.dateEtat=(select max(dateEtat)
					from etatProc
					where idProc_PROCEDURES=p.idProc
					)
	;";
	$reqSuiviProc=mysqli_query($bdd,$str);	

	if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
		$recap="genPDFDemande_rapide.php?idDP=$idDP";
	else
		$recap="genPDFDemande.php?idDP=$idDP";
	
?>
<div class="container">
	<div class="page-header">
		<h2>Suivi des procédures</h2>
	</div>
	<div class="container theme-showcase" role="main">
		<h4>Informations sur la demande de procédure</h4>
		<div class="jumbotron">
			<div class="row">
				<div class="col-md-6">
					Affaire : <?php echo $affaire?><br/>
					Date demande : <?php echo $dateDP?>
				</div>
				<div class="col-md-6">
					Equipement : <?php echo $equipement?><br/>
					Date de besoin: <?php echo $delai?>
				</div>
			</div>
			<a class="btn btn-primary" target="_blank" style="margin-top:10px" href="<?php echo $recap;?>" >Afficher récapitulatif complet de la demande</a>
		</div>
	</div>
	<div class="container theme-showcase" role="main">
		<h4>Suivi rédaction procédure</h4>
		<div class="jumbotron">
			<table style="text-align:center" class="table">
				<tr>
					<th >Laboratoire</th>
					<th >Rédacteur</th>
					<th >Etat en cours</th>
				</tr>
				<?php
				while($lg=mysqli_fetch_object($reqSuiviProc)){
					$labo=$lg->labo;
					$nom=ucfirst(mb_strtolower($lg->nom,'UTF-8'));
					$prenom=ucfirst(mb_strtolower($lg->prenom,'UTF-8'));
					$redacteur="$nom $prenom";
					$suiviProc=$lg->lastEtat;
					echo "<tr>";
						echo "<td>$labo</td>";
						echo "<td>$redacteur</td>";
						echo "<td>$suiviProc</td>";
					echo "</tr>";
					
				}
			?>
			</table>
			
		</div>
	</div>
	<center>
		<input type="button" class="btn btn-lg btn-primary"  value="Retour" id="valider" onclick="document.location.href='listDP.php?mode=3';"/>
	</center>
</div>
<?php
}
else
	echo '<div class="alert alert-danger"><strong>Erreur de réception de la demande</strong></div>';
require('bottom.php');
?>