<?php
function infoDate($p_id,$p_etat,$bdd){
	// retourne la date de l etat $p_etat de la procedure $p_id
	$str="
	select dateEtat
	from etatProc
	where idProc_PROCEDURES=$p_id and idEtat_ETAT=$p_etat
	;";
	
	$req=mysqli_query($bdd,$str);
	if(mysqli_num_rows($req)!=0)
		$d=date('d/m/Y H:i',strtotime(mysqli_fetch_object($req)->dateEtat));
	else
		$d="";
	return $d;
}

function format_date($date_us)
{
	return date('d/m/Y',strtotime($date_us));
}

if(!isset($idServ))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du service</strong></div>';
else
{
	require('../conf/connexion_param.php'); 	// connexion a la base
	require('../fonction.php');
	//on recupere toutes les procedures concernees par une DP valide
	$str="select p.idProc, d.idDP, d.affaire, d.equipement, d.OS, d.delai, e.nomEtat, d.dateDemandeDP_redigerDP , e1.nomEmp as nomD,e1.prenomEmp as prenomD,e2.nomEmp as nomR,e2.prenomEmp as prenomR
	from DEMANDE_PROCEDURE d, PROCEDURES p, EMPLOYE e1, EMPLOYE e2, ETAT e, etatProc ep
	where e2.idEmp=p.idEmp_EMPLOYE
	and p.idDP_DEMANDE_PROCEDURE=d.idDP
	and p.idService_SERVICE=$idServ
	and d.idEmp_EMPLOYE=e1.idEmp
	and ep.idProc_PROCEDURES=p.idProc
	and ep.idEtat_ETAT=e.idEtat
	and e.idEtat<17
	and ep.dateEtat=(select max(dateEtat)
					from etatProc
					where idProc_PROCEDURES=p.idProc
					)
	";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération des procédures</strong></div>';
	else
	{
	?>
		<div class="jumbotron" id="sTri">
			<table class="table table-striped table-tri" id="tri">
				<thead>
					<tr >
						<th>idProc</th>
						<th>Redacteur</th>
						<th>idDP</th>
						<th>Demandeur</th>
						<th>Affaire</th>
						<th>Equipement</th>
						<th>OS</th>
						<th>Date besoin</th>
						<th>Date demande (derniére modif)</th>
						<th>Dernier état</th>
						<th>Date demande</th>
						<th>Affectation</th>
						<th>Rédaction</th>
						<th>Relecture</th>
						<th>Signature</th>
						<!--<th>Validation</th> -->
					</tr>
				</thead>
				<tbody>
				<?php
					while($lg=mysqli_fetch_object($req)){
					
						$idProc=$lg->idProc;
						$nomR=ucfirst(mb_strtolower($lg->nomR, 'UTF-8'));
						$prenomR=ucfirst(mb_strtolower($lg->prenomR, 'UTF-8'));
						$redac="$nomR $prenomR";//redacteur
					
						$idDP=$lg->idDP;//idDP
						
						$nd=ucfirst(mb_strtolower($lg->nomD, 'UTF-8'));
						$pd=ucfirst(mb_strtolower($lg->prenomD, 'UTF-8'));
						$demandeur="$nd $pd";//demandeur
						
						$affaire=$lg->affaire;//affaire
						$equipement=$lg->equipement;//equipement
						$OS=$lg->OS;//OS
						$delai=format_date($lg->delai);//delais
						$dateDemandeDP_redigerDP=format_date($lg->dateDemandeDP_redigerDP);//date demande de la derniere modification
						
						$nomEtat=$lg->nomEtat;
						
						$attenteAffect=infoDate($idProc,12,$bdd);
						$attente=infoDate($idProc,13,$bdd);
						$redac=infoDate($idProc,14,$bdd);
						$relec=infoDate($idProc,15,$bdd);
						$sign=infoDate($idProc,16,$bdd);
						
						if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
							$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
						else
							$recap="../demande/genPDFDemande.php?idDP=$idDP";
						
						echo "<tr style='cursor:pointer;' onclick='window.open(\"$recap\"); return false'>";
							echo "<td >$idProc</td>";
							echo "<td>$nomR</td>";
							echo "<td>$idDP</td>";
							echo "<td>$demandeur</td>";
							echo "<td>$affaire</td>";
							echo "<td>$equipement</td>";
							echo "<td>$OS</td>";
							echo "<td>$delai</td>";
							echo "<td>$dateDemandeDP_redigerDP</td>";
							echo "<td>$nomEtat</td>";
							echo "<td>$attenteAffect</td>";
							echo "<td>$attente</td>";
							echo "<td>$redac</td>";
							echo "<td>$relec</td>";
							echo "<td>$sign</td>";
							//echo "<td>$valid</td>";

						echo "</tr>";
						
					}
				?>
				</tbody>
				<tfoot>
					<tr >
						<th>idProc</th>
						<th>Redacteur</th>
						<th>idDP</th>
						<th>Demandeur</th>
						<th>Affaire</th>
						<th>Equipement</th>
						<th>OS</th>
						<th>Date besoin</th>
						<th>Date demande (derniére modif)</th>
						<th>Dernier état</th>
						<th>Date demande</th>
						<th>Affectation</th>
						<th>Rédaction</th>
						<th>Relecture</th>
						<th>Signature</th>
						<!--<th>Validation</th> -->
					</tr>
				</tfoot>
			</table>
		</div>
		
		
	<?php
	}
}
