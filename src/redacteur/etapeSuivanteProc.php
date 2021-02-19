<?php 
require('top.php');

if(isset($_POST['idProc']) && isset($_POST['idEtat'])) //l'utilisateur a validé le passage a l'etape suivante
{
	$idProc=$_POST['idProc'];
	$idEtat=1+$_POST['idEtat'];//etat suivant
	require('../conf/connexion_param.php');
	//on ajoute le nouvel etat
	$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEtat,$idProc);";
	$req=mysqli_query($bdd,$str);
	if(!$req) //erreur de requete
		echo '<div class="alert alert-danger"><strong>Erreur de passage à l\'étape suivante</strong></div>';
	else //reussi
	{
		$ok=true;
		if($idEtat==15)//on passe en relecture, on ajoute le doc 3it
		{
			$ref=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["ref"]));
			$issue=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["iss"]));
			$rev=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["rev"]));
			
			$idService=$_SESSION['infoUser']['idService'];// service 
			if($idService==1)
				$tpdoc=31;
			elseif($idService==2)
				$tpdoc=32;
			else
				$tpdoc=33;
			
			$str="select idDoc from DOCUMENT_3IT where reference='$ref' and issue='$issue' and rev='$rev'and idTypeDoc_TYPE_DOC='$tpdoc';";
			$req=mysqli_query($bdd,$str);
			if(!$req)
			{
				$ok=false;
				echo '<div class="alert alert-danger"><strong>Erreur de récupération du doc 3it</strong></div>';
			}
			if (mysqli_num_rows($req)==0){
				//le document n'est pas dans la base
				//on l y insere	
				
				$str="insert into DOCUMENT_3IT values (null,'$ref','00','$issue','$rev','$tpdoc');";
				$req=mysqli_query($bdd,$str);
				if(!$req)
				{
					$ok=false;
					echo '<div class="alert alert-danger"><strong>Erreur d\'insertion du doc 3it</strong></div>';
				}
				else
					$idDoc=mysqli_insert_id($bdd);
			}else{
				//on recupere l identifiant du document
				$idDoc=(mysqli_fetch_object($req)->idDoc);
			}
			//le document est desormais dans la base, on le lie a la procédure
			$str="UPDATE procedures SET idDoc_document_3it =$idDoc WHERE idProc = $idProc;";
			$req=mysqli_query($bdd,$str);
			echo mysqli_error($bdd);
			if(!$req)
			{
				$ok=false;
				echo '<div class="alert alert-danger"><strong>Erreur de laison du doc avec la procédure</strong></div>';
			}
		}
		if($ok)
		{
			// on confirme + redirection page acceuil
			echo "<center>";
			echo '<div class="alert alert-success"><strong>Étape validée</strong></div>';
			echo '<input class="btn btn-primary btn-lg" type="button" value="Retour" onclick="document.location.href=\'index.php\'" />';
			echo "</center>";
		}
	}		
}
elseif(!isset($_GET['idProc'])) //on verifi la reception du numero de procedure
	echo '<div class="alert alert-danger"><strong>Erreur de reception du numéro de la procedure</strong></div>';
else
{
	// $idServ affecter dans top.php, contient le numéro du service du responsable
	require('../conf/connexion_param.php'); //connexion a la bdd
	require('../fonction.php'); //page contenant verifDPRapide
	
	$idProc=$_GET['idProc'];
	//on recupere le dernier etat de la procedure (Choix de ne pas le transferé via l'url a partir de la page precedente pour eviter toutes possibilité de corruption de la base)
	$str="select idEtat_ETAT from etatProc b 
	where b.idProc_PROCEDURES=$idProc
	and b.dateEtat = ( 	select max(dateEtat)
						from etatProc
						where idProc_PROCEDURES=$idProc
					 )
	;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération de l\'état</strong></div>';
	else
	{
		if(mysqli_num_rows($req)!=1) //peut etre provoqué si changement du chiffre dans l'url
			echo '<div class="alert alert-danger"><strong>Procédure inconnue</strong></div>';
		else
		{
			$idEtat=mysqli_fetch_object($req)->idEtat_ETAT;
			if ($idEtat==13)
			{
				$titre="Nouvelle procédure";
				$etatSuivant="Passer en rédaction";
			}
			else if ($idEtat==14)
			{
				$titre="Procédure en cours de rédaction";
				$etatSuivant="Mettre en relecture";
			}
			else if ($idEtat==15)
			{
				$titre="Procédure en cours de relecture";
				$etatSuivant="Mettre en signature";
			}
			else if ($idEtat==16)
			{
				$titre="Procédure en cours de signature";
				$etatSuivant="Valider la procédure";
			}
			
			//on recupere les info de la DP
			$str="select a.idDP
			from DEMANDE_PROCEDURE a, EMPLOYE b
			where a.idEmp_EMPLOYE=b.idEmp 
			and a.idDP=(select idDP_DEMANDE_PROCEDURE from PROCEDURES where idProc=$idProc);";
			$reqDP=mysqli_query($bdd,$str);				
			$lg=mysqli_fetch_object($reqDP);

			$idDP=$lg->idDP;
			if($idEtat!=13 && $idEtat!=14)
			{
				$str="select d.reference, d.issue, d.rev from document_3it d, procedures p
				where p.idDoc_document_3it=d.idDoc
				and p.idProc=$idProc;";
				$req=mysqli_query($bdd,$str);
				if(mysqli_num_rows($req)!=0)
				{
					$lg=mysqli_fetch_object($req);
					$ref=$lg->reference;
					$issue=$lg->issue;
					$rev=$lg->rev;
				}
			}
			

			
			if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
				$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
			else
				$recap="../demande/genPDFDemande.php?idDP=$idDP";
			?>
			<div class="container">
				<div class="page-header">
					<h2><?php echo $titre ?></h2>
				</div>
				<form method="post" action="etapeSuivanteProc.php" role="form" onsubmit="return confirmSuiv()" >
					
						<h4 style="display:inline;">Procédure n° <?php echo $idProc; ?></h4>
						<?php
						if($idEtat==14)//si redaction, on demande le doc 3it avant de passer en relecture
						{
						?>
						<div style="float:right; text-align:right">
							<span>Réference 3IT de la procédure :</span>
							<input title="Réference" style="display:inline; width:15%; "  class="form-control" placeholder="Réference"  type="text"  name="ref" required/>
							<input title="Issue" style="display:inline; width:15%; " class="form-control" placeholder="Issue"  type="text"  name="iss" required/>
							<input title="Révision" style="display:inline; width:15%; " class="form-control" placeholder="Révision"  type="text"  name="rev" />
						</div>
						<?php
						}
						elseif($idEtat!=13 && isset($ref)) //on affiche le doc 3it
						{
							echo '<div style="float:right">';
							echo "<label>Réference 3IT de la procédure :</label>";
							echo " <span style='margin-right:20px' title='Réference'>$ref</span><span style='margin-right:20px' title='Issue'>$issue</span><span style='margin-right:20px' title='Révision'>$rev</span>";
							echo "</div>";
						}
						?>
					
					<p><iframe src="<?php echo $recap;?>" class="iframe_resu"></iframe></p>
					<input type="hidden" name="idProc" value="<?php echo $idProc; ?>" />
					<input type="hidden" name="idEtat" value="<?php echo $idEtat; ?>" />
					<center><input value="<?php echo $etatSuivant ;?>" type="submit" class="btn btn-primary btn-lg" />
					<input value="Retour" type="button" class="btn btn-primary btn-lg" onclick="document.location.href='listProc.php?idEtat=<?php echo $idEtat; ?>'"/></center>
					
				</form>
			</div>
			<script>
			//on demande la confirmation pour passer à l'étape suivante 
			function confirmSuiv()
			{
				if(confirm("<?php echo $etatSuivant; ?> ?"))
					return true;
				return false
			}
			</script>
<?php
		}
	}
}
require('bottom.php');
?>