<?php
require("top.php");
if(!isset($_GET["idProjet"])) //on verifie la bonne reception des parametre
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramètres</strong></div>";
else
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idProjet=$_GET["idProjet"];
	
	//recupération des info sur le projet
	$str="select l.nomLocal from localisation l, projetBidon p
	where p.idLocal_localisation=l.idLocal
	and p.idProjet='$idProjet';";
	$req=mysqli_query($bdd, $str);
	$nomLocal=mysqli_fetch_object($req)->nomLocal;
	
	$str="select i.numInstru, de.nomDes, s.nomStatut, i.modele, i.marque, i.date_futureInt, i.numSerie, l.nomLocal, c.pied
	from statut s, instrument_vib_capteur ic, concerneBidon c,
	instrument i 
	LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	where c.idProjet_projetBidon='$idProjet'
	and ic.idInstruCapt=c.idInstruCapt_instrument_vib_capteur
	and i.idStatut_statut=s.idStatut
	and ic.numInstru_instrument=i.numInstru
	order by c.ordre;";
	$reqCapteur=mysqli_query($bdd, $str);
	
	
	?>
	<div class="container">
		<div class="page-header">
			<h2><?php echo $nomLocal; ?></h2>
		</div>
		<h4>Capteurs</h4>
		<div class="jumbotron">
			<table class="table table-striped table-tri" >
				<thead>
					<tr >
						<th>Numéro</th>
						<th>Désignation</th>
						<th>Statut</th>
						<th>Fournisseur</th>
						<th>Modèle</th>
						<th>N°Série</th>
						<th>Date FI</th>
						<th>Localisation</th>
					</tr>
				</thead>
				<tbody id="tabCapt">
					
				</tbody>
			</table>
		</div>
		<h4>Capteurs de force</h4>
		<div class="jumbotron">
			<table class="table table-striped table-tri" >
				<thead>
					<tr >
						<th>Numéro</th>
						<th>Désignation</th>
						<th>Statut</th>
						<th>Fournisseur</th>
						<th>Modèle</th>
						<th>N°Série</th>
						<th>Date FI</th>
						<th>Localisation</th>
						<th>Pied</th>
					</tr>
				</thead>
				<tbody id="tabCaptForce">
					
				</tbody>
			</table>
		</div>
		<div class="text-center">
			<a href="creerModifierBidon.php?idProjet=<?php echo $idProjet; ?>" class="btn btn-primary btn-lg" role="button">Modifier</a>
			<a id="excel" href="excelBidon.php?idProjet=<?php echo $idProjet; ?>" class="btn btn-primary btn-lg" role="button">Excel</a>
			<form  style="display:inline;" method="post" action="suppBidon.php" onsubmit="return confirmSupp();">
				<input type="hidden" name="idProjet" value="<?php echo $idProjet;?>"/>
				<input type="submit" class="btn btn-lg btn-primary" value="Supprimer"/>
			</form>
			<a href="gestionBidon.php" class="btn btn-primary btn-lg" role="button">Retour</a>
		</div>
	</div>
	<script type="text/javascript" language="javascript" src="../js/projetBidon.js"></script>
<?php
	$tab = array();
	//appel de la fonction JS pour remplir les tableaux
	while($lg=mysqli_fetch_object($reqCapteur))
	{	
		$data = array(
			"numInstru" => $lg->numInstru,
			"nomDes" => $lg->nomDes,
			"nomStatut" => $lg->nomStatut,
			"modele" => $lg->modele,
			"marque" => $lg->marque,
			"date_futureInt" => $lg->date_futureInt,
			"numSerie" => $lg->numSerie,
			"nomLocal" => $lg->nomLocal,
			"pied" => $lg->pied,
		);
		array_push($tab, $lg->numInstru);
		$dataJS=json_encode( $data );
		echo '<script>detailCapteur('.$dataJS.')</script>';
	}

	$_SESSION["capteur"] = $tab;
	
	//echo '<script>init()</script>';
}

require("bottom.php");