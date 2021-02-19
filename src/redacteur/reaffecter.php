<?php 
require('top.php');
if($_SESSION['infoUser']['categUser']==3)
{
	if(isset($_POST['idProc']) && isset($_POST['idRedacteur']))
	{
		require('../conf/connexion_param.php'); //connexion a la bdd
		$idRedacteur=$_POST['idRedacteur'];
		$idProc=$_POST['idProc'];
		$remarque=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['remarque']));
		
		//on a deja verifier que la demande été bien en attente d'affectation
		
		//on insere le redacteur dans la procedure + remarque si besoin
		if($remarque=="")
			$str="update PROCEDURES set idEmp_EMPLOYE=$idRedacteur where idProc=$idProc;";
		else
			$str="update PROCEDURES set idEmp_EMPLOYE=$idRedacteur, remarque_labo='".$_SESSION['infoUser']['nom'].": ".$remarque."' where idProc=$idProc;";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			echo '<div class="alert alert-danger"><strong>Erreur d\'affectation du redacteur</strong></div>';
		else
		{
			// on redirige vers l accueil
			echo "<center>";
			echo '<div class="alert alert-success"><strong>Réaffectation validée</strong></div>';
			echo '<input class="btn btn-primary btn-lg" type="button" value="Retour" onclick="document.location.href=\'listProc_affectation.php?rea=0\'" />';
			echo "</center>";
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
		//on recupere les info de la DP
		$str="select a.delai, a.affaire, a.equipement, a.idDP, a.plateforme, b.nomEmp
		from DEMANDE_PROCEDURE a, EMPLOYE b
		where a.idEmp_EMPLOYE=b.idEmp 
		and a.idDP=(select idDP_DEMANDE_PROCEDURE from PROCEDURES where idProc=$idProc);";
		$reqDP=mysqli_query($bdd,$str);				
		$lg=mysqli_fetch_object($reqDP);
		$demandeur=$lg->nomEmp;
		$delai=date('d/m/Y',strtotime($lg->delai));
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$idDP=$lg->idDP;
		$plateforme=$lg->plateforme;
		
		//on recupere le nom et prenom de celui qui s'occupe actuellement de la proc_close
		$str="select idemp, nomemp, prenomemp from employe where idemp = (select idemp_employe from procedures where idproc=$idProc);";	
		$reqEMP=mysqli_query($bdd,$str);
		$lg=mysqli_fetch_object($reqEMP);
		$Emp=$lg->nomemp." ".$lg->prenomemp;
		$idEmpActu=$lg->idemp;
		
		//on recupere tous les employes du labo y compris le responsable car il peut etre redacteur
		$str="select idEmp, nomEmp, prenomEmp
		from EMPLOYE
		where idService_SERVICE=$idServ
		and idEmp!=3
		and idEmp!=4
		and idEmp!=5
		and actif = 1;";
		$reqEmpServ=mysqli_query($bdd,$str);
		
		//Recup des remarques
		$str="select remarque_labo from procedures where idProc=$idProc;";
		$reqRemLavbo=mysqli_query($bdd,$str);
		$remarque=mysqli_fetch_object($reqRemLavbo)->remarque_labo;
		
		if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
			$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
		else
			$recap="../demande/genPDFDemande.php?idDP=$idDP";
		?>
		<div class="container">
			<div class="page-header">
				<h2>Réaffecter procédure</h2>
			</div>
			<form method="post" action="reaffecter.php" role="form" onsubmit="return confirmAffect()">
				<div class="container theme-showcase" role="main">
					<h4>Procédure n° <?php echo $idProc; ?></h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-6">
								<p>Demandeur: <?php echo $demandeur?></p>
								<p>Affaire: <?php echo $affaire?></p>
								<p>Plateforme: <?php echo $plateforme?></p>
								<p><a class="btn btn-primary" target="_blank" style="margin-top:10px" href="<?php echo $recap;?>" >Afficher récapitulatif complet de la demande</a></p>
							</div>
							<div class="col-md-6">
								<p>Equipement: <?php echo $equipement?></p>
								<p>Date de besoin: <?php echo $delai?></p>
								<p>Rédacteur actuel: <?php echo $Emp?></p>
								<select class='form-control' id="idRedacteur" name="idRedacteur" style="max-width:400px">
							<option value="-1" selected disabled>Veuillez choisir un rédacteur</option>
							<?php 
							while($lg=mysqli_fetch_object($reqEmpServ)){
								
								$id=$lg->idEmp;
								$nom=$lg->nomEmp;
								$prenom=$lg->prenomEmp;
								if($idEmpActu==$id)
									echo"<option selected value=\"$id\" >$nom $prenom</option>";
								else
									echo"<option value=\"$id\" >$nom $prenom</option>";
							}?>
						</select>
							</div>
						</div>
						
					</div>
				</div>
				<div class="container theme-showcase">
					<h4 class="sub-header">Remarques</h4>
					<div class="jumbotron">
						<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques" ><?php if($remarque!="") echo $remarque; ?></textarea>
					</div>
				</div>
				<input type="hidden" name="idProc" value="<?php echo $idProc; ?>" />
				<center><input value="Réaffecter la procédure" type="submit" class="btn btn-primary btn-lg" /></center>
			</form>
		</div>
		<script>
		function confirmAffect()
		{
			//supprime les anciens alert
			var t=document.querySelectorAll('.alert'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
			for (var i=0;  i< t.length; i++)
				t[i].parentNode.removeChild(t[i]);
		
			var select = document.getElementById("idRedacteur");
			var choice = select.selectedIndex;  // Récupération de l'index du <option> choisi
			
			if(select.options[choice].value != "-1")
			{
				if(confirm("Le rédacteur sera: "+select.options[choice].innerHTML))
					return true;
			}
			else
			{	
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir un rédacteur</strong></div>';
				document.body.insertBefore(child, document.body.firstChild);
			}
			return false;
		}
		
		</script>				
<?php
	}
}
else
	echo '<div class="alert alert-danger"><strong>Accés non autorisé</strong></div>';
require('bottom.php');
?>