<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('top.php');
$idLabo=$_SESSION['infoUser']['idService'];// service du labo
if (isset($_POST['badge'])){
	require('../conf/connexion_param.php'); //connexion a la bdd
	//htmlspecialchars remplace les caracteres speciaux par leurs équivalent html, évite la plupart des erreurs/failles d'injection sql avec par exemple '

	$os=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['n_os']));
	$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['remarque']));
	$badge=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['badge']));
	$depositaire=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['depositaire']));
	$telDep=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['tel']));
	$idEs=$_POST['idEssai'];
	
	$tabnOf=array();
	if(isset($_POST['n_of']))
	{
		$tabnOf=$_POST['n_of'];
		$tabModele=$_POST['modele'];	
	}	
	
	//Stockage du dépositaire
	$idDep=$_POST["depositaire"];
	if ($idDep == "-1") $idDep = NULL;

	$str="update essai set badge='$badge', os='$os', commentaire='$remarque', idDep_depositaire='$idDep' where idEssai=$idEs ;";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=@mysqli_query($bdd,$str);
	//on rajoute les nouveaux match
	$stop=false; //sert a stoper immediatement la boucle en cas d'erreur, permet aussi de tester si il y a eu des erreurs
	
	//on supprime les anciens match
	$str="delete from tester where idEssai_essai='$idEs' ;";
	$req=mysqli_query($bdd,$str);
	
	for($i=0; !$stop && $i < count($tabnOf);$i++){
		$noOF=htmlspecialchars(mysqli_real_escape_string($bdd,$tabnOf[$i]));
		$modele=$tabModele[$i];
		//on verifie que l'OF existe
		$str="select noOF as nb from EQUIPEMENT_OF where noOF='$noOF';";
		$req=@mysqli_query($bdd,$str);
		if(!$req){
			echo '<div class="alert alert-danger"><strong>Erreur de verification de l\'of</strong></div>';
			$stop=true;
		}
		else{
			if(mysqli_num_rows($req)==0) //nouvel of
			{
				$str="insert into EQUIPEMENT_OF values('$noOF',$modele);";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de création de l\'of</strong></div>';
					$stop=true;
				}
			}
			else{
				$str="update EQUIPEMENT_OF set idModele_TYPE_MODELE=$modele where noOF='$noOF';";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de mis à jours de l\'of</strong></div>';
					$stop=true;
				}
			}
			
			//insertion du match essai OF
			$str="insert into tester values('$noOF',$idEs);";
			
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo '<div class="alert alert-danger"><strong>Erreur de d\'ajout du test entre l\'of crée et l\'essai</strong></div>';
				$stop=true;
			}				
		}
		
	}
	if(!$stop)
	{
		echo '<script src="../js/success.js"></script>';
	}	
}
elseif(!isset($_GET["idEssai"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récéption du numéro de l\'essai</strong></div>';
else{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idEssai=$_GET["idEssai"];
	
	//on recupere les infos
	$str="SELECT e.idEssai, e.badge, e.fifo, e.affaire, e.equipement, e.os, e.commentaire, et.idEtat_ETAT, e.date_debut, e.date_fin, d.nomDep, d.telDep
	FROM etatEssai et, essai e LEFT JOIN depositaire d on e.idDep_depositaire=d.idDep
	where idEssai =$idEssai 
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de l'essai</strong></div>";
	else{
		$lg=mysqli_fetch_object($req);
		$badge=$lg->badge;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$depositaire=$lg->nomDep;
		$telDep=$lg->telDep;
		$os=$lg->os;
		$remarque=$lg->commentaire;
		
		$date_debut=date('d/m/Y',strtotime($lg->date_debut));
		$date_fin=date('d/m/Y',strtotime($lg->date_fin));
		$heure_debut=date('H:i',strtotime($lg->date_debut));
		$heure_fin=date('H:i',strtotime($lg->date_fin));
		
		if($lg->fifo==1)
			$fifo="Oui";
		else
			$fifo="Non";
		
		$str="SELECT m.nomMoyen, m.idMoyen
		FROM essai e, moyen m
		where e.idMoyen_MOYEN=m.idMoyen
		and e.idEssai=$idEssai;";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
		{
			$lg=mysqli_fetch_object($req);
			$moyen=$lg->nomMoyen;
			$idMoyenActuel=$lg->idMoyen;
		}

		//Récupération des dépositaires
		$str="SELECT idDep, nomDep, prenomDep FROM depositaire WHERE actif = 1";
		$req_dep = mysqli_query($bdd, $str);
		
		//on recupere les of concernés
		$str="SELECT e.noOF, m.nomModele, m.idModele FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from tester where idEssai_Essai=$idEssai)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de des of</strong></div>";
		else{
?>
			<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
			<link href="../calendrier/calendrier.css" rel="stylesheet" />
			<link rel="stylesheet" href="../bootstrap/css/select2.css">
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
					line-height: 1.42857143 !important;
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
					<h2>Renseigner l'essai n°<?php echo $idEssai; ?></h2>
				</div>
				<form method="post" action="renseignerEssai.php">
					<div class="container theme-showcase" role="main">
						<h4>Informations générales</h4>
						<div class="jumbotron">
							<div class="row">
								<div class="col-md-4">
									<div class="info"><label>Nom de l'affaire:&nbsp </label><?php echo $affaire; ?></div>
									<div class="info"><label>Date début:&nbsp </label><?php echo $date_debut; ?></div>
									<input name="badge" title="Badge valise" type="text" class="form-control" placeholder="Badge valise" value="<?php echo $badge; ?>" autofocus />
									<input id="n_os" name="n_os" title="N° d'OS" type="text" class="form-control" placeholder="N° d'OS" value="<?php echo $os; ?>" required />
								</div>
								<div class="col-md-4">
									<div class="info"><label>Nom de l'équipement:&nbsp </label><?php echo $equipement; ?></div>
									
									<div class="info"><label>Date fin:&nbsp </label><?php echo $date_fin; ?></div>
									<select class="form-control js-example-basic-single" required name="depositaire">
										<option value="-1" selected>Dépositaire</option>
										<?php
											while($lg_dep = mysqli_fetch_object($req_dep))
											{
												if ($dep == $lg_dep->idDep)

													echo '<option selected value="'.$lg_dep->idDep.'">'.$lg_dep->nomDep.' '.$lg_dep->prenomDep.'</option>';
												else
													echo '<option value="'.$lg_dep->idDep.'">'.$lg_dep->nomDep.' '.$lg_dep->prenomDep.'</option>';
											}
										?>
									</select>
								</div>
								<div class="col-md-4">
									<div class="info"><label>Moyen:&nbsp </label><?php echo $moyen; ?></div>
									<div class="info"><label>Fifo:&nbsp </label><?php echo $fifo; ?></div>
									<input id="tel" name="tel" title="Téléphone" type="text" class="form-control" placeholder="Téléphone" value="<?php echo $telDep; ?>" />
									
								</div>
							</div>
						</div>
					</div>
					<center><input type="submit" class="btn btn-lg btn-success" value="Valider" />
					<input type="button" class="btn btn-lg btn-danger" value="Annuler" onclick="etapePrev(<?php echo $idEssai; ?>)"/>
					</center>
					<div class="container theme-showcase" role="main">
						<h4 class="sub-header">N°OF concernés</h4>
						<div class="jumbotron" >
							<table class="table table-striped table-tri" id="tabOf" >
								<thead>
									<tr>
										<th >N°OF</th>
										<th >Type modèle</th>
										<th >Supprimer</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$first=true;
									while($lg=mysqli_fetch_object($req)){
										$id="";
										if($first)//ajoute l'id au premier input (pour la douchette)
										{
											$id="n_of1";
											$first=false;
										}
										$noOf=$lg->noOF;
										$nomModele=$lg->nomModele;
										$idModele=$lg->idModele;
										?>
										<tr>
											<td><input id='<?php echo $id; ?>' class='form-control' placeholder='N°OF' value='<?php echo $noOf; ?>' type='text' name='n_of[]' title='N°OF' required/></td>
											<td>
												<select class='form-control' name='modele[]'>
													<option value='-1' disabled>Type Modèle</option>
													<option <?php if($idModele== 5) echo "selected" ?> value='5' >EQM</option>
													<option <?php if($idModele== 3) echo "selected" ?> value='3' >PFM</option>
													<option <?php if($idModele== 4) echo "selected" ?> value='4' >FM</option>
													<option <?php if($idModele== 1) echo "selected" ?> value='1' >EM</option>
													<option <?php if($idModele== 2) echo "selected" ?> value='2' >QM</option>
													<option <?php if($idModele== 6) echo "selected" ?> value='6' >EBT</option>
													<option <?php if($idModele== 7) echo "selected" ?> value='7' >EXT</option>
												</select>
											</td>
											<td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td>
										</tr>
										<?php
									}							
									?>	
								</tbody>								
							</table>
							<center>
								<input type="button" class="btn  btn-primary" value="Ajouter un OF" onclick="ajout_of()"/>		
							</center>
						</div>
					</div>
					<div class="container theme-showcase" role="main">
						<h4 class="sub-header">Remarques</h4>
						<div class="jumbotron">
							<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques"><?php echo $remarque;?></textarea>
						</div>
					</div>
					<input type="hidden" value="<?php echo $idEssai; ?>" name="idEssai"/>
				</form>
			</div><!-- /.container -->
			<script src="../jquery-ui/js/jquery-ui.min.js"></script>
			<script src="../calendrier/calendrier.js"></script>
			<script src="../js/creer_modifierEssai.js"></script>
<?php		
		}
	}
}

require('bottom.php');
?>