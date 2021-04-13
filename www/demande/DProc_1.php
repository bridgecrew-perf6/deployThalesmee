<?php //premier formulaire de remplissage d'une demande de procédure
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
if(isset($_GET["idDP"])) //si demande deja existante on recharge les anciennes valeurs
{
	//Recuperation des info generales
	$idDP=$_GET["idDP"];
	$_SESSION["idDP"]=$idDP;
	$titre="Demande de procédure: n°$idDP";
	$str="select affaire, equipement, plateforme, OS, remarque, delai from DEMANDE_PROCEDURE where idDP=$idDP;";
	$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Erreur de récuperation de la demande</strong></div>';
	else
	{
		$res=mysqli_fetch_object($req);
		$affaire=$res->affaire;
		$equipement=$res->equipement;
		$plateforme=$res->plateforme;
		$OS=$res->OS;
		$remarque=html_entity_decode(htmlspecialchars_decode($res->remarque));
		$delai=$res->delai;
		//on formate au format français
		$delai=date('d/m/Y',strtotime($delai));
		
		//recup des labo concernés
		$str="select idService_SERVICE from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP;";
		$req=@mysqli_query($bdd, $str);
		
		$isEMC=false;$isVIB=false;$isVTH=false;
		while($lg=mysqli_fetch_object($req)){ 
			if($lg->idService_SERVICE==1)
				$isEMC=true;
			elseif($lg->idService_SERVICE==2)
				$isVIB=true;
			elseif($lg->idService_SERVICE==3)
				$isVTH=true;
		}
		
		
		//Recuperation des modeles a tester
		$str="select idModele_TYPE_MODELE, nbEquipATester,nbMaxEquipParTest,nbMinEquipParTest from vouloirTester where idDP_DEMANDE_PROCEDURE=$idDP;";
		$reqML=mysqli_query($bdd,$str);
		$isEM=false;$isEQM=false;$isPFM=false;$isFM=false;
		while($lg=mysqli_fetch_object($reqML)){

			if($lg->idModele_TYPE_MODELE==1)
			{
				$isEM=true;
				$nb_EM=$lg->nbEquipATester;
				$max_EM=$lg->nbMaxEquipParTest;
				$min_EM=$lg->nbMinEquipParTest;
			}
			elseif($lg->idModele_TYPE_MODELE==2)
			{
				$isEQM=true;
				$nb_EQM=$lg->nbEquipATester;
				$max_EQM=$lg->nbMaxEquipParTest;
				$min_EQM=$lg->nbMinEquipParTest;	
			}
			elseif($lg->idModele_TYPE_MODELE==3)
			{
				$isPFM=true;
				$nb_PFM=$lg->nbEquipATester;
				$max_PFM=$lg->nbMaxEquipParTest;
				$min_PFM=$lg->nbMinEquipParTest;	
			}
			elseif($lg->idModele_TYPE_MODELE==4)
			{
				$isFM=true;
				$nb_FM=$lg->nbEquipATester;
				$max_FM=$lg->nbMaxEquipParTest;
				$min_FM=$lg->nbMinEquipParTest;

			}
				
			
		}
		//Recuperation des articles
		$str="select noArticle_EQUIPEMENT_ART  , comTypeArt  from concernerArt where idDP_DEMANDE_PROCEDURE=$idDP  order by noArticle_EQUIPEMENT_ART;";
		$reqArt=mysqli_query($bdd,$str);		
		$tabArt=array();
		$i=0;
		while($lg=mysqli_fetch_object($reqArt)){
			$tabArt[$i][0]=$lg->noArticle_EQUIPEMENT_ART;
			$tabArt[$i][1]=$lg->comTypeArt;
			$i++;
		}		
		$nbArt=$i;
		//Recuperation des infos concernant les articles
		for($j=0; $j<$nbArt;$j++)
		{
			$str="select d.reference, d.issue, d.rev , d.idTypeDoc_TYPE_DOC
			from referencerDocArt r, document_3IT d, type_doc t
			where r.noArticle_EQUIPEMENT_ART=".$tabArt[$j][0]."
			and r.idDP_DEMANDE_PROCEDURE=$idDP
			and r.idDoc_DOCUMENT_3IT=d.idDoc
			and d.idTypeDoc_TYPE_DOC=t.idTypeDoc
			and t.categorie='PROCEDURE'
			order by d.idTypeDoc_TYPE_DOC;";
			$reqArt=mysqli_query($bdd,$str);
			while($lg=mysqli_fetch_object($reqArt)){
				$type=$lg->idTypeDoc_TYPE_DOC;
				if($type==31) //emc
				{
					$tabArt[$j][2]=$lg->reference;
					$tabArt[$j][3]=$lg->issue;
					$tabArt[$j][4]=$lg->rev;
				}
				elseif($type==32)// vib
				{
					$tabArt[$j][5]=$lg->reference;
					$tabArt[$j][6]=$lg->issue;
					$tabArt[$j][7]=$lg->rev;
				}
				elseif($type==33) //vth
				{
					$tabArt[$j][8]=$lg->reference;
					$tabArt[$j][9]=$lg->issue;
					$tabArt[$j][10]=$lg->rev;
					
				}
			}
		}

	}
}
else
{
	$titre="Nouvelle demande de procédure";
	unset($_SESSION["idDP"]);
}
?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />


<div class="container">
	<div class="page-header">
		<h2><?php echo $titre ?></h2>
	</div>
	<form method="post" action="traitement_DProc_1.php" onsubmit="return verifFormProc_1()">
		<div class="container theme-showcase" role="main">
			<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-6">
						<input name="nom_aff" value="<?php if(isset($affaire)) echo $affaire; ?>" title="Nom de l'affaire" type="text" class="form-control" placeholder="Nom de l'affaire" required autofocus />
						<input name="nom_eq" value="<?php if(isset($equipement)) echo $equipement; ?>" title="Nom de l'équipement" type="text" class="form-control" placeholder="Nom de l'équipement" required  />
						<input name="plateforme" value="<?php if(isset($plateforme)) echo $plateforme; ?>" title="Plateforme" type="text" class="form-control" placeholder="Plateforme" required  />
					
						<input name="n_os" value="<?php if(isset($OS)) echo $OS; ?>" title="N° d'OS" type="text" class="form-control" placeholder="N° d'OS" required />
						<div class="autre-form">
							Date de besoin : <input placeholder="01/01/2014" value="<?php if(isset($delai)) echo $delai; ?>" type="text" name="date" id="date_besoin" class="calendrier"  size="8" required/>
						</div>
						<div class="autre-form">Nature de l'essai:
							<input <?php if(isset($isEMC) && $isEMC) echo 'checked'; ?> onClick="ajoutProc(this,1);"  type="checkbox" id="EMC" name="isEMC" /> EMC&nbsp 
							<input <?php if(isset($isVIB) && $isVIB) echo 'checked'; ?> onClick="ajoutProc(this,2);" type="checkbox" id="VIB" name="isVIB" />  VIB&nbsp 
							<input <?php if(isset($isVTH) && $isVTH) echo 'checked'; ?> onClick="ajoutProc(this,3);" type="checkbox" id="VTH" name="isVTH" /> VTH&nbsp 
						</div>
					</div>
					<div class="col-md-6">
						<table id="typeModele" class="table">
							<tr>
								<td>Type de modeles</td>
								<td><center>EM<br/><input <?php if(isset($isEM) && $isEM) echo 'checked' ?>  type="checkbox" id="EM" name="isEM" onclick="typemodele('EM',this.checked)" /><center></td>
								<td><center>EQM<br/><input <?php if(isset($isEQM) && $isEQM) echo 'checked' ?>  type="checkbox" id="EQM" name="isEQM" onclick="typemodele('EQM',this.checked)" /><center></td>
								<td><center>PFM<br/><input  <?php if(isset($isPFM) && $isPFM) echo 'checked' ?> type="checkbox" id="PFM" name="isPFM" onclick="typemodele('PFM',this.checked)" /><center></td>
								<td><center>FM<br/><input <?php if(isset($isFM) && $isFM) echo 'checked' ?>  type="checkbox" id="FM" name="isFM" onclick="typemodele('FM',this.checked)" /><center></td>
							</tr>
							
							<tr>
								<td>Nombre d'équipements</td>
								<td><input <?php if(isset($isEM) && $isEM) echo "value='$nb_EM'" ?> type="text" class="form-control" id="nb_EM" name="nb_EM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isEQM) && $isEQM) echo "value='$nb_EQM'" ?> type="text" class="form-control" id="nb_EQM" name="nb_EQM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isPFM) && $isPFM) echo "value='$nb_PFM'" ?> type="text" class="form-control" id="nb_PFM" name="nb_PFM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isFM) && $isFM) echo "value='$nb_FM'" ?> type="text" class="form-control" id="nb_FM" name="nb_FM" onkeypress="return onlyNumber(event)" required disabled /></td>
							</tr>
							<tr>
								<td>Nb Max Equip/Test</td>
								<td><input <?php if(isset($isEM) && $isEM) echo "value='$max_EM'" ?> type="text" class="form-control" id="max_EM" name="max_EM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isEQM) && $isEQM) echo "value='$max_EQM'" ?> type="text" class="form-control" id="max_EQM" name="max_EQM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isPFM) && $isPFM) echo "value='$max_PFM'" ?> type="text" class="form-control" id="max_PFM" name="max_PFM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isFM) && $isFM) echo "value='$max_FM'" ?> type="text" class="form-control" id="max_FM" name="max_FM" onkeypress="return onlyNumber(event)" required disabled /></td>
							</tr>
							<tr>
								<td>Nb Min Equip/Test</td>	
								<td><input <?php if(isset($isEM) && $isEM) echo "value='$min_EM'" ?> type="text" class="form-control" id="min_EM" name="min_EM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isEQM) && $isEQM) echo "value='$min_EQM'" ?> type="text" class="form-control" id="min_EQM" name="min_EQM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isPFM) && $isPFM) echo "value='$min_PFM'" ?> type="text" class="form-control" id="min_PFM" name="min_PFM" onkeypress="return onlyNumber(event)" required disabled /></td>
								<td><input <?php if(isset($isFM) && $isFM) echo "value='$min_FM'" ?> type="text" class="form-control" id="min_FM" name="min_FM" onkeypress="return onlyNumber(event)" required disabled /></td>
							</tr>	
						</table>
						
					</div>	
				</div>
			</div>
		</div>
		<div class="container theme-showcase" role="main">
			<h4 class="sub-header">Articles concernés</h4>
			<div class="jumbotron" style="padding:0;">
				<table class="table" id="tabArticle">
					<tr>
						<th ROWSPAN=2>N° d'article</th><th ROWSPAN=2>Désignation</th><th COLSPAN=3>Procédure EMC</th><th COLSPAN=3>Procédure VIB</th><th COLSPAN=3>Procédure VTH</th></tr>
						<th >Ref</th><th >Issue</th><th >Rev</th><th>Ref</th><th >Issue</th><th >Rev</th><th>Ref</th><th >Issue</th><th >Rev</th>
					</tr>
					<?php 
					if(isset($tabArt) && $nbArt!=0)
					{
						for($i=0; $i<$nbArt;$i++)
						{
							?>
							<tr>
								
								<td><input <?php if(isset($tabArt[$i][0])) echo "value='".$tabArt[$i][0]."'"; ?> class="form-control article" pattern=".{3,}" title="3 chiffres minimum" placeholder="N° d'article" onkeyUp="onlyNumber(this)"  type="text" id="article" name="article[]" required /></td>
								<td><input <?php if(isset($tabArt[$i][1])) echo "value='".$tabArt[$i][1]."'"; ?> class="form-control" placeholder="Désignation" type="text" id="type_art" name="type_art[]"/></td>
								<td><input <?php if(isset($tabArt[$i][2])) echo "value='".$tabArt[$i][2]."'"; ?> class="form-control procEMC" placeholder="À créer"  type="text"  name="EMC1[]" /></td>
								<td><input <?php if(isset($tabArt[$i][3])) echo "value='".$tabArt[$i][3]."'"; ?> class="form-control procEMC" placeholder="À créer"  type="text"  name="EMC2[]" /></td>
								<td><input <?php if(isset($tabArt[$i][4])) echo "value='".$tabArt[$i][4]."'"; ?> class="form-control procEMC" placeholder="À créer"  type="text"  name="EMC3[]" /></td>
								<td><input <?php if(isset($tabArt[$i][5])) echo "value='".$tabArt[$i][5]."'"; ?> class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB1[]" /></td>
								<td><input <?php if(isset($tabArt[$i][6])) echo "value='".$tabArt[$i][6]."'"; ?> class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB2[]" /></td>
								<td><input <?php if(isset($tabArt[$i][7])) echo "value='".$tabArt[$i][7]."'"; ?> class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB3[]" /></td>
								<td><input <?php if(isset($tabArt[$i][8])) echo "value='".$tabArt[$i][8]."'"; ?> class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH1[]" /></td>
								<td><input <?php if(isset($tabArt[$i][9])) echo "value='".$tabArt[$i][9]."'"; ?> class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH2[]" /></td>
								<td><input <?php if(isset($tabArt[$i][10])) echo "value='".$tabArt[$i][10]."'"; ?> class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH3[]" /></td>
							</tr>
							<?php
						}					
					}
					else
					{
					?>
						<tr>
							<td><input class="form-control article" pattern=".{3,}" title="3 chiffres minimum" placeholder="N° d'article" onkeyUp="onlyNumber(this)"  type="text" id="article" name="article[]" required /></td>
							<td><input class="form-control" placeholder="Désignation" type="text" id="type_art" name="type_art[]"/></td>
							<td><input class="form-control procEMC" placeholder="À créer"   type="text"  name="EMC1[]" /></td>
							<td><input class="form-control procEMC" placeholder="À créer"  type="text"  name="EMC2[]" /></td>
							<td><input class="form-control procEMC" placeholder="À créer"  type="text"  name="EMC3[]" /></td>
							<td><input class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB1[]" /></td>
							<td><input class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB2[]" /></td>
							<td><input class="form-control procVIB" placeholder="À créer"  type="text"  name="VIB3[]" /></td>
							<td><input class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH1[]" /></td>
							<td><input class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH2[]" /></td>
							<td><input class="form-control procVTH" placeholder="À créer"  type="text"  name="VTH3[]" /></td>
						</tr>
					<?php 
					}
					?>
				</table>
				
				<center>
					<input type="button" class="btn  btn-primary" value="Ajouter un article" onclick="ajout_article()"/>		
					<input type="button" class="btn  btn-primary" value="Supprimer un article" onclick="suppr_un_art()"/>
					<input type="button" class="btn  btn-primary" value="Supprimer les articles"onclick="suppr_art()"/>
				</center>
			</div>
		</div>
		<div class="container theme-showcase" role="main">
			<h4 class="sub-header">Remarques</h4>
			<div class="jumbotron">
				<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques"><?php if(isset($remarque)) echo $remarque; ?></textarea>
			</div>
		</div>
		<input type="submit" class="btn btn-lg btn-primary" style="float:right;" value="Suivant" />
	</form>
</div><!-- /.container -->

<script src="../calendrier/calendrier.js"></script>
<script src="../js/DProc_1.js"></script>
<?php //on apelle la fonction typemodele si nécéssaire (dp deja existante) 
	if(isset($isEM) && $isEM) echo '<script>typemodele("EM",true)</script>';
	if(isset($isEQM) && $isEQM) echo '<script>typemodele("EQM",true)</script>';
	if(isset($isPFM) && $isPFM) echo '<script>typemodele("PFM",true)</script>';
	if(isset($isFM) && $isFM) echo '<script>typemodele("FM",true)</script>';
?>
<script>

var submit=false; //si on a cliquer sur suivant on n'affiche pas le message de confirmation de fermeture de la page
window.onbeforeunload = function(e){//affiche un message si l'utilisateur quitte la page sans cliquer sur suivant
    if(!submit)
	{
		return e;
		
	}
}

ajoutProc(document.getElementById("EMC"),1);
ajoutProc(document.getElementById("VIB"),2);
ajoutProc(document.getElementById("VTH"),3);
 </script>

<?php
require('bottom.php');
?>