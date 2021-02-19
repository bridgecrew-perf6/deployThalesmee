<?php //premier formulaire de remplissage d'une demande de procédure
require('top.php');
if(!isset($_SESSION["idDP"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de demande</strong></div>';
else
{
	$idDP=$_SESSION["idDP"];
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	//on verifie l'avancement de l'etape
	$str="select validiteDP from DEMANDE_PROCEDURE where idDP=$idDP;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de l\'étape</strong></div>';
	else
	{
		$etape=(mysqli_fetch_object($req)->validiteDP);
		if ($etape>=1 && $etape<4)
		{
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
			
			//recup des labo concernés
			$str="select idService_SERVICE from PROCEDURES where idDP_DEMANDE_PROCEDURE=$idDP;";
			$req=mysqli_query($bdd, $str);
			
			$isEMC=false;$isVIB=false;$isVTH=false;
			while($lg=mysqli_fetch_object($req)){ 
				if($lg->idService_SERVICE==1)
					$isEMC=true;
				elseif($lg->idService_SERVICE==2)
					$isVIB=true;
				elseif($lg->idService_SERVICE==3)
					$isVTH=true;
			}
			
			//Pour l interface ELEC
			$str="select a.noArticle_EQUIPEMENT_ART ,b.reference, b.type_, b.issue, b.rev  from referencerDocArt a,  DOCUMENT_3IT b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idDoc=a.idDoc_DOCUMENT_3IT and b.idTypeDoc_TYPE_DOC=16 order by noArticle_EQUIPEMENT_ART;";
			$reqDocArtIE=mysqli_query($bdd,$str);
			$tabIElec=array();
			if (mysqli_num_rows($reqDocArtIE)!=0)
			{	
				$i=0;
				while($lg=mysqli_fetch_object($reqDocArtIE)){ 
					$tabIElec[$i][0]=$lg->noArticle_EQUIPEMENT_ART;
					$tabIElec[$i][1]=$lg->reference;
					$tabIElec[$i][2]=$lg->type_;
					$tabIElec[$i][3]=$lg->issue;
					$tabIElec[$i][4]=$lg->rev;
					$i++;
				}
			}
			else
			{
				//on n a pas recuperer les donnees, on met celles par defaut	
				//pour chaque article
				for ($i=0;$i<$nbArt;$i++){
					$this_art=$tabArt[$i][0];
					$tabIElec[$i][0]=$this_art;
					$tabIElec[$i][1]=$this_art;
					$tabIElec[$i][2]="904";
					$tabIElec[$i][3]="00";
					$tabIElec[$i][4]="";
				}
			}
			
			//Pour l interface MECA
			$str="select a.noArticle_EQUIPEMENT_ART , b.reference, b.type_, b.issue, b.rev from referencerDocArt a,  DOCUMENT_3IT b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idDoc=a.idDoc_DOCUMENT_3IT and b.idTypeDoc_TYPE_DOC=17 order by noArticle_EQUIPEMENT_ART;;";
			$reqDocArtIM=mysqli_query($bdd,$str);				
			$tabIMeca=array();
			if (mysqli_num_rows($reqDocArtIM)!=0)
			{
				$i=0;
				while($lg=mysqli_fetch_object($reqDocArtIM)){ 
					$tabIMeca[$i][0]=$lg->noArticle_EQUIPEMENT_ART;
					$tabIMeca[$i][1]=$lg->reference;
					$tabIMeca[$i][2]=$lg->type_;
					$tabIMeca[$i][3]=$lg->issue;
					$tabIMeca[$i][4]=$lg->rev;
					$i++;
				}
			}
			else
			{
				//on n a pas recuperer les donnees, on met celles par defaut
				//pour chaque article
				for ($i=0;$i<$nbArt;$i++){
					$this_art=$tabArt[$i][0];
					$tabIMeca[$i][0]=$this_art;
					$tabIMeca[$i][1]=$this_art;
					$tabIMeca[$i][2]="041";
					$tabIMeca[$i][3]="00";
					$tabIMeca[$i][4]="";
				}
			}
			
			//Recuperation des documents 3IT
			$str="select b.idTypeDoc_TYPE_DOC, b.reference, b.type_, b.issue, b.rev  from referencerDoc a,  DOCUMENT_3IT b where a.idDP_DEMANDE_PROCEDURE=$idDP and b.idDoc=a.idDoc_DOCUMENT_3IT ;";
			$reqDoc=mysqli_query($bdd,$str);
			
			
			while($lg=mysqli_fetch_object($reqDoc)){ 
				
				$tp=$lg->idTypeDoc_TYPE_DOC;
				
				if($tp==11) //Plan de test
				{
					$PT=array();
					$PT[0]=$lg->reference;
					$PT[1]=$lg->type_;
					$PT[2]=$lg->issue;
					$PT[3]=$lg->rev;
				}
				elseif($tp==12) //Analyse Electrique
				{
					$AE=array();
					$AE[0]=$lg->reference;
					$AE[1]=$lg->type_;
					$AE[2]=$lg->issue;
					$AE[3]=$lg->rev;
				}
				elseif($tp==13) //Analyse Mecanique
				{
					$AM=array();
					$AM[0]=$lg->reference;
					$AM[1]=$lg->type_;
					$AM[2]=$lg->issue;
					$AM[3]=$lg->rev;
				}
				elseif($tp==14) //Analyse Thermique
				{
					$AT=array();
					$AT[0]=$lg->reference;
					$AT[1]=$lg->type_;
					$AT[2]=$lg->issue;
					$AT[3]=$lg->rev;
				}
				elseif($tp==15) //DOC Definition Cyclage
				{
					$DC=array();
					$DC[0]=$lg->reference;
					$DC[1]=$lg->type_;
					$DC[2]=$lg->issue;
					$DC[3]=$lg->rev;
				}				
			}
			//si les documents n'ont pas deja été renseigné -> valeurs par défaut
			if(!isset($PT)){ //Plan de test
				$PT=array();
				$PT[0]="";
				$PT[1]="957";
				$PT[2]="00";
				$PT[3]="";
			}
			if($isEMC){ //Analyse Electrique
				if(!isset($AE)){
					$AE=array();
					$AE[0]="";
					$AE[1]="921";
					$AE[2]="00";
					$AE[3]="";
				}
			}
			if ($isVIB){
				if(!isset($AM)){ //Analyse Mecanique
					$AM=array();
					$AM[0]="";
					$AM[1]="373";
					$AM[2]="00";
					$AM[3]="";
				}
			}			
			if ($isVTH){ 
				if(!isset($AT)){ //Analyse Thermique
					$AT=array();
					$AT[0]="";
					$AT[1]="374";
					$AT[2]="00";
					$AT[3]="";
				}
				if (!isset($DC)){ //DOC Definition Cyclage
					$DC=array();
					$DC[0]="";
					$DC[1]="000";
					$DC[2]="00";
					$DC[3]="";
				}
			}
			//Recuperation du nom des employes references
			$str="select r.idFonc_fonctionemp, e.nomEmp from referencerEmp r, EMPLOYE e 
			where r.idDP_DEMANDE_PROCEDURE=$idDP 
			and r.idEmp_EMPLOYE=e.idEmp;";
			$reqEmp=mysqli_query($bdd,$str);
			$IRP="";$QA="";$IEMC="";$IVIB="";$IVTH="";
			while($lg=mysqli_fetch_object($reqEmp)){ 
				$f=$lg->idFonc_fonctionemp;
				if($f==1) //QA
					$QA=$lg->nomEmp;
				elseif($f==2) //IRP
					$IRP=$lg->nomEmp;
				elseif($f==3) //Ical EMC
					$IEMC=$lg->nomEmp;
				elseif($f==4) //Ical VIB
					$IVIB=$lg->nomEmp;
				elseif($f==5) //Ical VTH
					$IVTH=$lg->nomEmp;
			}
			
			
			?>
		<div class="container">
			<div class="page-header">
				<h2>Demande de procédure: n° <?php echo $idDP; ?></h2>
			</div>
			<form method="post" action="traitement_DProc_2.php" onsubmit="return validProc_2()">
				<div class="container theme-showcase" role="main">
					<h4>Références Employés</h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-2">	
									<div class="label-control"><label>IRP</label></div>
									<div class="label-control"><label>QA</label></div>
									<?php 
									if($isEMC)
										echo '<div class="label-control"><label>ICAL EMC</label></div>';
									if($isVIB)										
										echo '<div class="label-control"><label>ICAL VIB</label></div>';
									if($isVTH)
										echo '<div class="label-control"><label>ICAL VTH</label></div>';
									?>
							</div>
							<div class="col-md-5">
								<input placeholder="IRP" title="IRP" type="text" class="form-control"  name="IRP" value="<?php echo $IRP;?>" required autofocus/>
								<input placeholder="QA" title="QA" type="text" class="form-control" name="QA" value="<?php echo $QA;?>" required />
								<?php
									if($isEMC)
										echo "<input placeholder='ICAL EMC' title='ICAL EMC' type='text' class='form-control'  name='icalEMC' value='$IEMC'  />";
									if($isVIB)
										echo "<input placeholder='ICAL VIB' title='ICAL VIB' type='text' class='form-control'  name='icalVIB' value='$IVIB'  />";
									if($isVTH)
										echo "<input placeholder='ICAL VTH' title='ICAL VTH' type='text' class='form-control'  name='icalVTH' value='$IVTH'  />";
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">Documents</h4>
					<div class="jumbotron">
						 <table class="table" >
							<tr>
								<th class="td-label">Document</th>
								<th>Référence</th>
								<th>Type</th>
								<th>Issue</th>
								<th>Révision</th>
							</tr>
							<tr>
								<td class="td-label">Plan de test</td>
								<td ><input placeholder="Référence" type="text" class="form-control" name="PTref" value="<?php echo $PT[0];?>" required/></td>
								<td><input placeholder="Type" type="text" class="form-control" name="PTtype" value="<?php echo $PT[1];?>" required/></td>
								<td><input placeholder="Issue" type="text" class="form-control" name="PTissue" value="<?php echo $PT[2];?>" required/></td>
								<td><input placeholder="Révision" type="text" class="form-control" name="PTrev" value="<?php echo $PT[3];?>" /></td>
							</tr>
							<?php if($isEMC){ ?>
							<tr>
								<td class="td-label">Analyse EMC</td>
								<td><input placeholder="Référence" type="text" class="form-control" name="AEref" value="<?php echo $AE[0];?>" required/></td>
								<td><input placeholder="Type" type="text" class="form-control" name="AEtype" value="<?php echo $AE[1];?>" required/></td>
								<td><input placeholder="Issue" type="text" class="form-control" name="AEissue" value="<?php echo $AE[2];?>" required/></td>
								<td><input placeholder="Révision" type="text" class="form-control" name="AErev" value="<?php echo $AE[3];?>" /></td>
							</tr>
							<?php } 
							 if($isVIB){ ?>
							<tr>
								<td class="td-label">Analyse Mecanique</td>
								<td><input placeholder="Référence" type="text" class="form-control" name="AMref" value="<?php echo $AM[0];?>" required/></td>
								<td><input placeholder="Type" type="text" class="form-control" name="AMtype" value="<?php echo $AM[1];?>" required/></td>
								<td><input placeholder="Issue" type="text" class="form-control" name="AMissue" value="<?php echo $AM[2];?>" required/></td>
								<td><input placeholder="Révision" type="text" class="form-control" name="AMrev" value="<?php echo $AM[3];?>" /></td>
							</tr>
							<?php } 
							 if($isVTH){ ?>
							<tr>
								<td class="td-label">Analyse Vide-Thermique</td>
								<td><input placeholder="Référence" type="text" class="form-control" name="ATref" value="<?php echo $AT[0];?>" required/></td>
								<td><input placeholder="Type" type="text" class="form-control" name="ATtype" value="<?php echo $AT[1];?>" required/></td>
								<td><input placeholder="Issue" type="text" class="form-control" name="ATissue" value="<?php echo $AT[2];?>" required/></td>
								<td><input placeholder="Révision" type="text" class="form-control" name="ATrev" value="<?php echo $AT[3];?>" /></td>
							</tr>	
							<tr>
								<td class="td-label">Doc définition cyclage</td>
								<td><input placeholder="Référence" type="text" class="form-control" name="DCref" value="<?php echo $DC[0];?>" /></td>
								<td><input placeholder="Type" type="text" class="form-control" name="DCtype" value="<?php echo $DC[1];?>" /></td>
								<td><input placeholder="Issue" type="text" class="form-control" name="DCissue" value="<?php echo $DC[2];?>" /></td>
								<td><input placeholder="Révision" type="text" class="form-control" name="DCrev" value="<?php echo $DC[3];?>" /></td>
							</tr>
							<?php } ?>	
						</table>	
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">Documents/Articles pour Interface Mécanique</h4>
					<div class="jumbotron">
						<table class="table">
							<tr>
								<th class="td-label">Article</th>
								<th>Référence</th>
								<th>Type</th>
								<th>Issue</th>
								<th>Révision</th>
							</tr>
							<?php 
							for($i=0;$i<$nbArt;$i++){
								$this_art=$tabIMeca[$i][0];
								$ref="Mref_".$this_art;
								$type="Mtype_".$this_art;
								$issue="Missue_".$this_art;
								$rev="Mrev_".$this_art;
								$com=$tabArt[$i][1];
								
								if ($com!="")
									$valArt="$com / $this_art";
								else
									$valArt=$this_art;
									
								$valRef=$tabIMeca[$i][1];
								$valType=$tabIMeca[$i][2];
								$valIssue=$tabIMeca[$i][3];
								$valRev=$tabIMeca[$i][4];
							?>						
							<tr>
								<td ><?php echo $valArt; ?></td>
								<td><input type="text" class="form-control" name="<?php echo $ref; ?>" value="<?php echo $valRef; ?>" /></td>
								<td><input type="text" class="form-control" name="<?php echo $type; ?>" value="<?php echo $valType; ?>" /></td>
								<td><input type="text" class="form-control" name="<?php echo $issue; ?>" value="<?php echo $valIssue; ?>" /></td>
								<td><input type="text" class="form-control" name="<?php echo $rev; ?>" value="<?php echo $valRev; ?>" /></td>
							</tr>
							
							<?php
							}?>
						</table>
					</div>
					<?php if($isEMC){ ?>
					<h4 class="sub-header">Documents/Articles pour Interface Electrique</h4>
					<div class="jumbotron">
						<table class="table">
							<tr>
								<th class="td-label">Article</th>
								<th>Référence</th>
								<th>Type</th>
								<th>Issue</th>
								<th>Révision</th>
							</tr>
							<?php 
							for($i=0;$i<$nbArt;$i++){
								$this_art=$tabIElec[$i][0];
								$ref="Eref_".$this_art;
								$type="Etype_".$this_art;
								$issue="Eissue_".$this_art;
								$rev="Erev_".$this_art;
								$com=$tabArt[$i][1];
								
								if ($com!="")
									$valArt="$com / $this_art";
								else
									$valArt=$this_art;
								
								$valRef=$tabIElec[$i][1];
								$valType=$tabIElec[$i][2];
								$valIssue=$tabIElec[$i][3];
								$valRev=$tabIElec[$i][4];
								
							?>					
								<tr>
									<td ><?php echo $valArt; ?></td>
									<td><input type="text" class="form-control" name="<?php echo $ref; ?>" value="<?php echo $valRef; ?>" /></td>
									<td><input type="text" class="form-control" name="<?php echo $type; ?>" value="<?php echo $valType; ?>" /></td>
									<td><input type="text" class="form-control" name="<?php echo $issue; ?>" value="<?php echo $valIssue; ?>" /></td>
									<td><input type="text" class="form-control" name="<?php echo $rev; ?>" value="<?php echo $valRev; ?>" /></td>
								</tr>
							
							<?php
							}
							?>
						</table>
					</div>
					<?php } ?>
				</div>
				<input type="submit" class="btn btn-lg btn-primary" style="float:right;" value="Suivant" />
				
			</form>
				<input type="button" class="btn btn-lg btn-primary" style="float:left;" value="Précédent" onclick="document.location.href='DProc_1.php?idDP=<?php echo $idDP; ?>'"/>
		</div><!-- /.container -->
			
	<script>
		var submit=false; //si on a cliquer sur suivant on n'affiche pas le message de confirmation de fermeture de la page
		window.onbeforeunload = function(e){//affiche un message si l'utilisateur quitte la page sans cliquer sur suivant
			if(!submit)
			{
				return e;
				
			}
		}

		function validProc_2()
		{
			//pas de verications en plus de celles deja faites directements en html, la fonction se contente de mettre submit a true et de return true
			submit=true;
			return true;
		}
	 </script>			
			
			
			
			
			<?php
		}
		else
			echo '<div class="alert alert-danger"><strong>L\'étape précédente n\'a pas correctement été validé</strong></div>';
	}	
}	
require('bottom.php');
?>