<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php'); //page contenant verifDPRapide
$this_emp=$_SESSION['infoUser']['idEmp'];
$str="select a.idDP , a.affaire , a.equipement, a.os 
from DEMANDE_PROCEDURE a, PROCEDURES p, etatProc et
where a.idDP=p.idDP_DEMANDE_PROCEDURE and p.idProc=et.idProc_PROCEDURES
and a.idEmp_EMPLOYE=$this_emp
and et.dateEtat=(select max(dateEtat) from etatProc where idProc_PROCEDURES=p.idProc)
and a.validiteDP=4
group by a.idDP;";
$req=mysqli_query($bdd,$str);

?>

<div class="container">
	<div class="page-header">
		<input id="btnRetour" class="btn btn-lg btn-primary" style="float:right" type="button" value="Retour choix demande" onclick="retourChoix()"/>
		<h2>Demande d'évolution d'une procédure</h2>
	</div>
	<div class="container theme-showcase" role="main" id="f1">
		<h4 >Veuillez sélectionnez la demande</h4>
		<div class="jumbotron">
			<table class="table table-striped table-tri" id="tri">
				<thead>
					<tr >
						<th>Numéro</th>
						<th>Affaire</th>
						<th>Équipement</th>
						<th>N°OS</th>
						<th>PDF</th>
					</tr>
				</thead>
				<tbody>
					<?php
						while($lg=mysqli_fetch_object($req))
						{
							$idDP=$lg->idDP;
							$affaire=$lg->affaire;
							$equipement=$lg->equipement;
							$os=$lg->os;

							echo "<tr >";
								echo "<td style='cursor:pointer;' onclick='evoDP(\"$idDP\")'>$idDP</td>";
								echo "<td style='cursor:pointer;' onclick='evoDP(\"$idDP\")'>$affaire</td>";
								echo "<td style='cursor:pointer;' onclick='evoDP(\"$idDP\")'>$equipement</td>";
								echo "<td style='cursor:pointer;' onclick='evoDP(\"$idDP\")'>$os</td>";
								
								if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
									$recap="genPDFDemande_rapide.php?idDP=$idDP";
								else
									$recap="genPDFDemande.php?idDP=$idDP";
								echo "<td><a target='_blank' href='$recap'>Voir le PDF</a></td>";
							echo "</tr>";
						}
					?>
				</tbody>
				<tfoot>
					<tr >
						<th>Numéro</th>
						<th>Affaire</th>
						<th>Équipement</th>
						<th>N°OS</th>
						<th>PDF</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<form method="post" action="traitement_evoProc.php" >
		<div class="container theme-showcase" role="main" id="f2">
			<h4 id="titref2"></h4>
			<div class="jumbotron">
				<center>
					<h4>Modification d'une procédure:</h4>
					<!-- on utilise onclick et pas onchange car ie a quelques probleme avec onchange -->
					<input type="checkbox" name="choix_1" onClick="ajoutEMC();" id="choix_1"/> EMC
					<input type="checkbox" name="choix_2" onClick="ajoutVIB();" id="choix_2"/> VIB
					<input type="checkbox" name="choix_3" onClick="ajoutVTH();" id="choix_3"/> VTH
				</center>
			</div>
		</div>
		<div class="container theme-showcase" role="main" id="emc">
			<h4>Modification EMC</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Réference" id="refEMC" name="refEMC" disabled required />
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Issue" id="issEMC" name="issEMC" disabled required/>
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" name="revEMC" placeholder="Révision" />
					</div>
				</div>
				<br/>
				<textarea title="Nom de l'affaire" class="form-control" placeholder="Remarques" name="comEMC"></textarea>
			</div>
		</div>
		<div class="container theme-showcase" role="main" id="vib">
			<h4>Modification VIB</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Réference" disabled id="refVIB" name="refVIB" required />
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Issue" disabled id="issVIB" name="issVIB" required/>
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Révision" name="revVIB" />
					</div>
				</div>
				<br/>
				<textarea title="Nom de l'affaire" class="form-control" placeholder="Remarques" name="comVIB"></textarea>
			</div>
		</div>
		<div class="container theme-showcase" role="main" id="vth">
			<h4>Modification VTH</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Réference" disabled id="refVTH" name="refVTH" required/>
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Issue" disabled id="issVTH" name="issVTH" required/>
					</div>
					<div class="col-md-4">	
						<input type="text" class="form-control" placeholder="Révision" name="revVTH" />
					</div>
				</div>
				<br/>
				<textarea title="Nom de l'affaire" class="form-control" placeholder="Remarques" name="comVTH" ></textarea>
			</div>
		</div>
		<input type="hidden" value="" name="idDP" id="idDP" />
		<center><input type="submit" id="btnSubmit" class="btn btn-lg btn-primary" value="Valider" /></center>
	</form>
</div><!-- /.container -->

<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
<script type="text/javascript" charset="utf-8">

$(document).ready(function() {
	$('#tri').dataTable().columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
} );
</script>
<script src="../js/evoProc.js"></script>
<?php
require('bottom.php');