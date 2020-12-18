<?php 
require('top.php');
if($_SESSION['infoUser']['categUser']==3)
{
	if(isset($_POST['idProc']) && isset($_POST['idRedacteur']))
	{
		require('../conf/connexion_param.php'); //connexion a la bdd
		require('../fonction.php');
		
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
			//on modifie l etat de la procedure
			//on passe l etat 12=en attente d affectation a 13=nouvelle procedure
			$str="insert into etatProc values (date_format(now(),'%Y-%m-%d %H:%i:%s'),13,$idProc);";
			$reqEmp=mysqli_query($bdd,$str);
			if(!$req)
				echo '<div class="alert alert-danger"><strong>Erreur de modification de l\'etat</strong></div>';
			else//tout c'est bien passé
			{				
				//envoi d'un mail au redacteur + demandeur
				
				//recup du demandeur
				$str="select u.logUser, d.affaire from demande_procedure d, procedures p, employe e, utilisateur u
				where p.idProc=$idProc
				and p.idDP_DEMANDE_PROCEDURE=d.idDP
				and d.idEmp_EMPLOYE=e.idEmp
				and u.idEmp_employe=e.idemp;";
				$reqDem=mysqli_query($bdd,$str);
				$lgDem=mysqli_fetch_object($reqDem);
				$affaire=$lgDem->affaire;
				$dem=$lgDem->logUser;
				
				//recup du redacteur
				$str="select u.logUser from procedures p, employe e, utilisateur u
				where p.idEmp_EMPLOYE=$idRedacteur
				and p.idEmp_EMPLOYE=e.idEmp
				and u.idEmp_EMPLOYE=p.idEmp_EMPLOYE;";
				$reqRed=mysqli_query($bdd,$str);
				$lgRed=mysqli_fetch_object($reqRed);

				$obj="Procédure affectée";
				$dest=$lgRed->logUser;
				
				$emet=$_SESSION['infoUser']['login'];
				
				
				$cc=$dem;

				$corps="Bonjour,\r\nLa procédure n°$idProc de l'affaire $affaire vous a été affectée, veuillez vous connecter sur l'outil de demandes de procédure (http://thalesmee) pour commencer la rédaction.";
				
				$resMail=envoi_mail($obj, $dest, $emet, $cc, $corps,$_SESSION['infoUser']['idService']);
				
				if($resMail!=0) //0 -> la fonction mail a correctement été executé
					echo '<div class="alert alert-danger"><strong>Erreur d\'envoi de mail</strong></div>';
				
				
				// on redirige vers la page precedente
				echo "<center>";
				echo '<div class="alert alert-success"><strong>Affectation validée</strong></div>';
				echo '<input class="btn btn-primary btn-lg" type="button" value="Retour" onclick="document.location.href=\'listProc_affectation.php\'" />';
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
		//on verifie que la procedure est bien en attente d'affectation	(facile de changer le numero de la proc via l'url)		
		$str="select distinct idProc_PROCEDURES from etatProc b 
		where b.idProc_PROCEDURES=$idProc and b.idEtat_ETAT =12
		and b.dateEtat = ( 	select max(dateEtat)
							from etatProc
							where idProc_PROCEDURES=$idProc
						 )
		;";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			echo '<div class="alert alert-danger"><strong>Erreur de verification de la procedure</strong></div>';
		else
		{
			if (mysqli_num_rows($req)==1){
			
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

				
				//on recupere tous les employes du labo y compris le responsable car il peut etre redacteur
				$str="select idEmp, nomEmp, prenomEmp
				from EMPLOYE
				where idService_SERVICE=$idServ
				and idEmp!=3
				and idEmp!=4
				and idEmp!=5;";
				$reqEmpServ=mysqli_query($bdd,$str);
				
				if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
					$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
				else
					$recap="../demande/genPDFDemande.php?idDP=$idDP";
				?>
				<div class="container">
					<div class="page-header">
						<h2>Affectation procédure</h2>
					</div>
					<form method="post" action="affectation.php" role="form" onsubmit="return confirmAffect()">
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
										<select class='form-control' id="idRedacteur" name="idRedacteur" style="max-width:400px">
											<option value="-1" selected disabled>Veuillez choisir un rédacteur</option>
											<?php 
											while($lg=mysqli_fetch_object($reqEmpServ)){
												
												$id=$lg->idEmp;
												$nom=$lg->nomEmp;
												$prenom=$lg->prenomEmp;
												echo"<option  value=\"$id\" >$nom $prenom</option>";					
											}?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="container theme-showcase">
							<h4 class="sub-header">Remarques</h4>
							<div class="jumbotron">
								<textarea name="remarque" title="Remarques" class="form-control" placeholder="Remarques"></textarea>
							</div>
						</div>
						<input type="hidden" name="idProc" value="<?php echo $idProc; ?>" />
						<center><input value="Affecter la procédure" type="submit" class="btn btn-primary btn-lg" /></center>
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
			else
				echo '<div class="alert alert-danger"><strong>La procédure choisie n\'est pas en attente d\'affectation</strong></div>';
		}
	}
}
else
	echo '<div class="alert alert-danger"><strong>Accés non autorisé</strong></div>';
require('bottom.php');
?>