
<?php
require("top.php");
require('../conf/connexion_param.php');
if(isset($_POST["loca"]))//validation du formulaire
{
	//recup des parametres
	$loca=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["loca"]));
	$erreur="";
	if(isset($_POST["idProjet"]))//modification d'un projet existant
	{
		$idProjet=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["idProjet"]));
		$str="update projetBidon set idLocal_localisation='$loca' where idProjet='$idProjet'";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			$erreur="Erreur de modification de la localisation";
	}
	else //nouveau projet
	{
		$str="insert into projetBidon values(null,'$loca')";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			$erreur="Erreur d'ajout du projet";
		$idProjet=mysqli_insert_id($bdd);
	}
	
	//maj des lignes : on supprime + reset des localisation
	$str="update instrument set idLocal_localisation='1'
	where numInstru in
		(select numInstru_instrument from instrument_vib_capteur iv, concerneBidon c 
			where idProjet_projetBidon='$idProjet'
			and iv.idInstruCapt=c.idInstruCapt_instrument_vib_capteur);";
	$req=mysqli_query($bdd, $str);
	$str="delete from concerneBidon where idProjet_projetBidon='$idProjet'";
	$req=mysqli_query($bdd, $str);
	
	if(!$req)
		$erreur="Erreur de suppression des anciens capteurs";
	$ordre=0;
	//on insere les lignes, liste vide acceptable
	//les capteurs classiques
	if(isset($_POST["numInstru"]))
	{
		$tabNumInstru=$_POST["numInstru"];
		foreach($tabNumInstru as $numInstru){
			$numInstru=htmlspecialchars(mysqli_real_escape_string($bdd,$numInstru));
			//update de la localisation
			$str="update instrument set idLocal_localisation='$loca' where numInstru ='$numInstru'";
			$req=mysqli_query($bdd, $str);
			//recup du numero de capteur
			$str="select idInstruCapt from instrument_vib_capteur where numInstru_instrument='$numInstru'";
			$req=mysqli_query($bdd, $str);
			$numCapteur=mysqli_fetch_object($req)->idInstruCapt;
			//si le capteur est deja present dans sur d'autre pot, on le supprime
			$str="delete from concerneBidon where idInstruCapt_instrument_vib_capteur='$numCapteur'";
			$req=mysqli_query($bdd, $str);
			//insertion sur ce pot
			$str="insert into concerneBidon values('$idProjet','$numCapteur','$ordre',null)";
			$req=mysqli_query($bdd, $str);
			if(!$req)
				$erreur="Erreur d'ajout des capteurs classiques";
			$ordre++;
		}
	}
	//les capteurs de force
	if(isset($_POST["numInstruForce"]))
	{
		$tabNumInstruForce=$_POST["numInstruForce"];
		$tabPied=$_POST["sPied"];
		$i=0;
		foreach($tabNumInstruForce as $numInstru){
			$numInstru=htmlspecialchars(mysqli_real_escape_string($bdd,$numInstru));
			//update de la localisation
			$str="update instrument set idLocal_localisation='$loca' where numInstru ='$numInstru'";
			$req=mysqli_query($bdd, $str);
			//recup du numero de capteur
			$str="select idInstruCapt from instrument_vib_capteur where numInstru_instrument='$numInstru'";
			$req=mysqli_query($bdd, $str);
			$numCapteur=mysqli_fetch_object($req)->idInstruCapt;
			//si le capteur est deja present dans sur d'autre pot, on le supprime
			$str="delete from concerneBidon where idInstruCapt_instrument_vib_capteur='$numCapteur'";
			$req=mysqli_query($bdd, $str);
			//insertion sur ce pot
			$pied=htmlspecialchars(mysqli_real_escape_string($bdd,$tabPied[$i]));
			$str="insert into concerneBidon values('$idProjet','$numCapteur','$ordre','$pied')";
			$req=mysqli_query($bdd, $str);
			if(!$req)
				$erreur="Erreur d'ajout des capteurs de force";
			$ordre++;
			$i++;
		}
	}
	if($erreur!="")
		echo "<div class='alert alert-danger'><strong>$erreur</strong></div>";
	else
	{
		echo '<script> function redirection(){
	
				document.location.href="./detailsBidon.php?idProjet='.$idProjet.'";
				}

				swal({
	
						title : "Informations validées",
						text : "Redirection dans quelques instants",
						icon : "success"
						
				});
				setTimeout(redirection, 1250);</script>';
	}
}
else
{
	$conditionLoc="";
	if(isset($_GET["idProjet"])) //accés a la page pour modifications
	{
		$idProjet=htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["idProjet"]));
		//on recup la localisation
		$str="select l.idLocal, l.nomLocal from projetBidon p, localisation l
		where p.idProjet='$idProjet'
		and p.idLocal_localisation=l.idLocal";
		$reqLoc=mysqli_query($bdd, $str);
		$lg=mysqli_fetch_object($reqLoc);
		$loc=$lg->idLocal;
		
		$titre=$lg->nomLocal;
		//pour un projet existant on ajoute sa propre localisation a la liste
		$conditionLoc="and idProjet!='$idProjet'";
		
		
		
		$str="select i.numInstru, de.nomDes, s.nomStatut, i.modele, i.marque, i.date_futureInt, i.numSerie, l.nomLocal, c.pied
		from statut s, 
		instrument_vib_capteur ic,
		concerneBidon c,
		instrument i 
		LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		where c.idProjet_projetBidon='$idProjet'
		and c.idInstruCapt_instrument_vib_capteur=ic.idInstruCapt
		and i.idStatut_statut=s.idStatut
		and ic.numInstru_instrument=i.numInstru
		order by ordre;";
		$reqCapteur=mysqli_query($bdd, $str);
		
	}
	else //accés a la page pour créer un nouveau pot
	{
		$titre="Nouveau pot";
	}
	//un seul projet par localisation pour le moment, pas sur d'etre définitif donc pas d'index unique, gestion au niveau applicatif
	$str="select l.idLocal, l.nomLocal from localisation l  
	where l.idLabo_labo=2
	and NOT EXISTS(select idLocal_localisation from projetBidon p where l.idLocal=p.idLocal_localisation $conditionLoc);";
	$reqLoc=mysqli_query($bdd, $str);
	?>
	<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">

	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre;?></h2>
		</div>
		<h4>Informations</h4>
		<form method="post" action="./creerModifierBidon.php" id="form">
			<div class="jumbotron">
				<select id="loca" title="Localisation" class="form-control" name="loca" required>
					<option value="" disabled selected>Localisation</option>
					<?php
						while($lgLoc=mysqli_fetch_object($reqLoc))
						{
							if(isset($loc) && $lgLoc->idLocal==$loc)
								echo '<option value="'.$lgLoc->idLocal.'" selected>'.$lgLoc->nomLocal.'</option>';
							else
								echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
						}
					?>
				</select>
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
							<th>Supprimer</th>
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
							<th>Supprimer</th>
						</tr>
					</thead>
					<tbody id="tabCaptForce">
						
					</tbody>
				</table>
			</div>
			<input id="addL" type="button" class="btn  btn-success btn-lg" value="Ajouter un capteur"/>
			<div class="text-center">
				<input type="submit" value="Valider" class="btn btn-primary btn-lg" id='btnValid'/>
				<?php 
				if(isset($idProjet)) //si modification, on ajoute l'idprojet en hidden et le btn annuler renvoi vers la page detail
				{
					echo '<input type="hidden" name="idProjet" value="'.$idProjet.'" />'; 
					echo '<a href="detailsBidon.php?idProjet='.$idProjet.'" class="btn btn-primary btn-lg" role="button">Annuler</a>';
				}
				else //sinon le btn annuler renvoi vers la page général
					echo '<a href="gestionBidon.php" class="btn btn-primary btn-lg" role="button">Annuler</a>';
				?>
			</div>
		</form>
	</div>

	<div id="dialog_t" title="Veuillez saisir un numéro de capteur">
		<input placeholder="Numéro d'instrument - de série - trescal id" class="form-control" type="text" id="nouvNumCapt" title="Numéro d'instrument - de série - trescal id" />
	</div>

	<script src="../jquery-ui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" language="javascript" src="../js/projetBidon.js"></script>
	<style>
	.val:hover{
		
		cursor:pointer;
	}
	</style>
<?php
	//on ajout les capteurs deja présent sur le pot
	if(isset($reqCapteur))
	{
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
			$dataJS=json_encode( $data );
			echo '<script>ajouterModifierCapteur('.$dataJS.')</script>';
		}
	}
}
require("bottom.php");