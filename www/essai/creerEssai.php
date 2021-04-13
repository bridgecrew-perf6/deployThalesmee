<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php');
$idLabo=$_SESSION['infoUser']['idService'];// service du labo
if (isset($_POST['badge'])){
	
	$hDeb = intval(str_replace (":", "", $_POST['hDebut']));
	$hFin = intval(str_replace (":", "", $_POST['hFin']));
	if (($_POST['dateDebut'] < $_POST['dateFin']) or ($_POST['dateDebut'] == $_POST['dateFin'] and $hDeb < $hFin)){
		
	//htmlspecialchars remplace les caracteres speciaux par leurs équivalent html, évite la plupart des erreurs/failles d'injection sql avec par exemple '
	//mysqli_real_escape_string($bdd,evite les injection sql et les erreur produites par des caracteres comme '
	$affaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_aff']));
	$equipement=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['nom_eq']));
	$os=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['n_os']));
	$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['remarque']));
	$badge=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['badge']));
	$depositaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['depositaire']));
	$telDep=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['tel']));
	$fifo=0;

	if($_POST['moyen']!=-1)
		$moyen=$_POST['moyen'];
	else
		$moyen="";
	
	$tabnOf=array();
	if(isset($_POST['n_of']))
	{
		$tabnOf=$_POST['n_of'];
		$tabModele=$_POST['modele'];
		$tabArticle=$_POST["article"];
		
	}
	
	if(isset($_POST['ligneProd']) && $_POST['ligneProd'] != -1)
	{
		$ligne = $_POST['ligneProd'];
		
	}else{
		
		$ligne = "NULL";
	
	}

	//construction des dates format sql
	$dateDeb=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateDebut'])));
	$dateFin=explode('/',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['dateFin'])));
	$hDebut=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hDebut'])));
	$hFin=explode(':',htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['hFin'])));
	
	//date renvoi une date dans un format, et mktime crée un timestamp selon les arguments fournies
	// -> dans l'ordre : heure / minute / seconde / mois / jours / année
	$dateFormDeb=date("Y-m-d H:i:s",mktime($hDebut[0], $hDebut[1], 0, $dateDeb[1], $dateDeb[0], $dateDeb[2]));
	$dateFormFin=date("Y-m-d H:i:s",mktime($hFin[0], $hFin[1], 0, $dateFin[1], $dateFin[0], $dateFin[2]));

	//Stockage du dépositaire
	$idDep=$_POST["depositaire"];
	if ($idDep == "-1") $idDep = null;

	$duree = dureePrimavera($dateFormDeb, $dateFormFin);

	$str="INSERT into essai values (NULL,NULL,'$badge','$affaire','$equipement','$os','$remarque','$idLabo','$moyen','$fifo','0','0','$idDep','$dateFormDeb','$dateFormFin','$dateFormDeb','$dateFormFin', '$dateFormDeb',0,1,0, $ligne, 0, $duree, $duree, $duree, 0, 0);";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=@mysqli_query($bdd,$str);
	if(!$req){
		echo '<div class="alert alert-danger"><strong>Erreur insert</strong></div>';
		$stop=true;
	}
	$idEs=mysqli_insert_id($bdd);

	//on rajoute les nouveaux match
	$stop=false; //sert a stoper immediatement la boucle en cas d'erreur, permet aussi de tester si il y a eu des erreurs
	for($i=0; !$stop && $i < count($tabnOf);$i++){
		$noOF=htmlspecialchars(mysqli_real_escape_string($bdd,$tabnOf[$i]));
		$article = $tabArticle[$i];
		$modele=$tabModele[$i];
		
		//on verifie que l'OF existe
		$str="SELECT noOF as nb from EQUIPEMENT_OF where noOF='$noOF';";
		$req=@mysqli_query($bdd,$str);
		if(!$req){
			echo '<div class="alert alert-danger"><strong>Erreur de verification de l\'of</strong></div>';
			$stop=true;
		}
		else{
			if(mysqli_num_rows($req)==0) //nouvel of
			{
				$str="INSERT into EQUIPEMENT_OF values('$noOF',$modele,'$article');";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de création de l\'of</strong></div>';
					$stop=true;
				}
			}
			else{
				$str="UPDATE EQUIPEMENT_OF set idModele_TYPE_MODELE=$modele where noOF='$noOF';";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de mis à jours de l\'of</strong></div>';
					$stop=true;
				}
			}
			//insertion du match essai OF
			
			$str="INSERT into tester values('$noOF',$idEs);";
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo '<div class="alert alert-danger"><strong>Erreur de d\'ajout du test entre l\'of crée et l\'essai</strong></div>';
				$stop=true;
			}				
		}
		
	}

	//Famille de produit
	$str = "SELECT famille_Famille FROM famille_essai WHERE idEssai_Essai=$idEs;";
	$reqFamActuel=mysqli_query($bdd,$str);
	
	if (isset($_POST['famille']) && $_POST['famille'] != "Non renseigné"){

		$choix = explode ("-",$_POST['famille']);
		$famille = $choix[1];
		$modele = $choix[0];
		$heure = $_POST['heure_famille'];
		if(mysqli_num_rows($reqFamActuel)>0)
		{
			
			$str="UPDATE famille_essai set famille_FAMILLE='$famille', heure_FAMILLE='$heure', modeleFamille_FAMILLE='$modele', resteHeure = '$heure' where idEssai_ESSAI = $idEs";
			$req=mysqli_query($bdd,$str);
			
		}else{
			
			$str="INSERT into famille_essai value($idEs, '$famille', '$heure', '$modele', '$heure', NULL)";
			$req=mysqli_query($bdd,$str);	
			
		}
		
		
	}else if (isset($_POST['famille']) && $_POST['famille'] == "Non renseigné"){
		
		if(mysqli_num_rows($reqFamActuel)>0)
		{
			
			$str="DELETE from famille_essai where idEssai_ESSAI = $idEs";
			$req=mysqli_query($bdd,$str);	
		}
	}
	if(!$stop)
	{
		//nouvel Essai dans la table etatEssai
		//insertion de l etat 21 a la date d aujourd hui
		$str="INSERT into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEs,22);";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de création de l'état de l'essai </strong></div>";
		else{
			echo '<script src="../js/success.js"></script>';
		}
	}
	
}else {
	echo '<div class="text-center">';
	echo '<div class="alert alert-danger"><strong>Erreur de saisie de la date</strong></div>';
	echo '</div>';
}
}
else{
	$str="SELECT idMoyen, nomMoyen from Moyen where idService_SERVICE=$idLabo;";
	$reqEquip=mysqli_query($bdd,$str);

	$str = "SELECT idDep, nomDep, prenomDep FROM depositaire WHERE actif = 1";
	$req_dep = mysqli_query($bdd, $str);

	$str_famille = "SELECT idFamille, nomFamille, modeleFamille FROM famille;";
	$req_famille = mysqli_query($bdd,$str_famille);
	if(!$reqEquip)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des moyens</strong></div>";
	else
	{
		if(isset($_GET["numOF"]))
			$numOF=$_GET["numOF"];
		
		$dateAct=date("d/m/Y");
		$dateFuture=date("d/m/Y",time()+86400);//date actuelle + 1 jours
		?>
		<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
		<link rel="stylesheet" href="../bootstrap/css/select2.css">
		<link href="../calendrier/calendrier.css" rel="stylesheet" />
		<script type="text/javascript" src="../bootstrap/dist/js/select2.js"></script>
		<style type="text/css">
		.select2{
			display: block;
			width: 100%;
			height: 34px;
			padding: 6px 12px;
			font-size: 14px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;
		}

		.select2-container--default .select2-selection--single{
			background-color: transparent !important ;
			border:0px !important;
		}

		.select2-container--default .select2-selection--single .select2-selection__rendered{
			line-height: 1.42857143; !important;
		}

		.select2-container .select2-selection--single .select2-selection__rendered{
			padding-left: 0px;
		}

		.select2{
			margin-bottom: 5px;
		}

			
		</style>
		<div class="container">
			<div class="page-header">
				<h2>Créer un essai</h2>
			</div>
			<form method="post" action="creerEssai.php">
				<div class="container theme-showcase" role="main">
					<h4>Informations générales</h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-8">
										<input id="nom_aff" value='<?php if(isset($aff)) echo $aff; ?>' name="nom_aff" title="Nom de l'affaire" type="text" class="form-control" placeholder="Nom de l'affaire" required  />
									</div>
									<div class="col-md-4">
										<select class="form-control" name="ligneProd" id="ligneProd">
											<option value="-1" selected >Ligne de produit</option>
											<?php
											//Récupération des lignes de produit
											$str = "SELECT idLigne, nomLigne FROM ligneproduit";
											$req_ligne = mysqli_query($bdd, $str);
											
											//Affichage de la liste déroulante en fonction de la liste des lignes de produit
											while ($lg_ligne = mysqli_fetch_object($req_ligne)){
												
												$idligne = $lg_ligne -> idLigne;
												$ligne = $lg_ligne -> nomLigne;
												echo "<option value='$idligne' >$ligne</option>";
											}

											?>
										</select>
									</div>
								</div>
								<input value='<?php if(isset($equip)) echo $equip; ?>' id="nom_eq" name="nom_eq" title="Nom de l'équipement" type="text" class="form-control" placeholder="Nom de l'équipement" required  />
								
								<div class="row">
									<div class="col-md-8">
										<select onchange="change_heure()" class="form-control" name="famille" id="famille">
											<option value="Non renseigné" selected >Famille d'équipement</option>
									
											<?php

											while($lgFamille=mysqli_fetch_object($req_famille))
											{
												$nomFam = $lgFamille->nomFamille;
												$mod = $lgFamille->modeleFamille;
												echo '<option value="'.$mod."-".$nomFam.'" >'.$nomFam." ".$mod.'</option>';		
											}
											?>		
										</select>
									</div>
									<div class="col-md-4">
										<input name="heure_famille" class="form-control" type="text" value =<?php if (isset($heure_famille)) echo $heure_famille; else echo "0";?> required>
									</div>
								</div>
													
							</div>
							<div class="col-md-4">
								<select class="form-control js-example-basic-single" required name="depositaire">
									<option value="-1" selected>Dépositaire</option>
									<?php
										while($lg = mysqli_fetch_object($req_dep))
										{
											echo '<option value="'.$lg->idDep.'">'.$lg->nomDep.' '.$lg->prenomDep.'</option>';
										}
									?>
									<option>Test</option>
								</select>

								<input id="tel" name="tel" title="Téléphone" type="text" class="form-control" placeholder="Téléphone"  />
								<input name="badge" title="Badge valise" type="text" class="form-control" placeholder="Badge valise" />
							</div>
							<div class="col-md-4">					
								
								<select class="form-control" name="moyen" id="moyen">
									<option value="-1" selected >Choisir un moyen</option>
								<?php
									while($lg=mysqli_fetch_object($reqEquip))
									{
										$idMoyen=$lg->idMoyen;
										$nomMoyen=$lg->nomMoyen;
										if(isset($idMoyenActuel) && $idMoyen==$idMoyenActuel)
											echo "<option selected value='$idMoyen' >$nomMoyen</option>";	
										else
											echo "<option value='$idMoyen' >$nomMoyen</option>";	
									}			
									echo '</select>';		
								?>
								<input id="n_os" value='<?php if(isset($os)) echo $os; ?>' name="n_os" title="N° d'OS" type="text" class="form-control" placeholder="N° d'OS" required />								
							</div>
						</div>
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4>Dates</h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="autre-form" >
									<span style="float:left;">Date début: <input placeholder="01/01/2014" value="<?php echo $dateAct; ?>" type="text" name="dateDebut" id="dateDebut" class="calendrier"  size="8" required/></span>
									<input id="hDebut"  name="hDebut" value="09:00" style="float:left;width:25%" title="Heure début" type="text" class="form-control" placeholder="08:00" required />
								</div>
							</div>
							<div class="col-md-4">
								<div class="autre-form">
									<span style="float:left;">Date fin: <input placeholder="01/01/2014" value="<?php echo $dateFuture; ?>" type="text" name="dateFin" id="dateFin" class="calendrier"  size="8" required/></span>
									<input id='hFin' value="09:00" style="float:left;width:25%" name="hFin" title="Heure fin" type="text" class="form-control" placeholder="18:00" required />
								</div>	
							</div>
						</div>
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">N°OF concernés</h4>
					<div class="jumbotron" >
						<table class="table table-striped">
							<thead>
								<tr>
									<th >N°OF</th>
									<th >Type modèle</th>
									<th> Article </th>
									<th >Supprimer</th>
								</tr>
							</thead>
							<tbody id="tabOf">
								<tr>
									<td><input value='<?php if(isset($numOF)) echo $numOF; ?>' class="form-control" placeholder="N°OF"  type="text" id="n_of1" name="n_of[]" title="N°OF" required/></td>
									<td><select class="form-control" name="modele[]" required>
											<option value="" selected disabled>Type Modèle</option>
											<option value="5" >EQM</option>
											<option value="3" >PFM</option>
											<option value="4" >FM</option>	
											<option value="1" >EM</option>
											<option value="2" >QM</option>						
											<option value="6" >EBT</option>						
											<option value="7" >EXT</option>						
										</select>
									</td>
									<td><input class="form-control" placeholder="Article"  type="text" name="article[]"/></td>
									<td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td>
								</tr>
							</tbody>
						</table>
						<center>
							<input type="button" class="btn  btn-primary" value="Ajouter un OF" onclick="ajout_of()"/>		
						</center>
					</div>
				</div>
				<!--
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">Docs utiles</h4>
					<div class="jumbotron">
						<table class="table table-striped" id="doc_utile">
							<thead>
								<tr>
									<th>Réference</th>
									<th>Issue</th>
									<th>Révision</th>
									<th>Type</th>
									<th>Lien</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				-->
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">Remarques</h4>
					<div class="jumbotron">
						<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques"></textarea>
					</div>
				</div>
				<div class="text-center">
					<input type="submit" class="btn btn-lg btn-primary" value="Créer l'essai" />
					<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href='./index.php'" value="Retour" />
				</div>
			</form>
		</div>

		<script src="../jquery-ui/js/jquery-ui.min.js"></script>
		<script src="../calendrier/calendrier.js"></script>
		<script src="../js/creer_modifierEssai.js"></script>

		<?php 
		if(isset($numOF))
			echo "<script>infoOF('$numOF');</script>";

	}
}
require('bottom.php');
?>