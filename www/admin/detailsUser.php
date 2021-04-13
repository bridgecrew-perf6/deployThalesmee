<?php 
require('../conf/connexion_param.php');
require('top.php');

if(!isset($_GET["idUser"])) //numéro de l'utilisateur non reçu
	echo '<div class="alert alert-danger"><strong>Erreur de réception de l\'utilisateur !</strong></div>';
else
{
	$idUser=$_GET["idUser"];
	//on recupere les infos de l'utilisateur séléctionné
	$str="select u.idUser,u.logUser, e.nomEmp, e.prenomEmp,e.telephone_1,e.mail, s.nomService from utilisateur u, employe e, service s
	where u.idEmp_Employe=e.idEmp
	and e.idService_service=s.idservice
	and u.idUser=$idUser";
	$req=@mysqli_query($bdd, $str);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données de l\'utilisateur</strong></div>';
	else
	{
		$lg=mysqli_fetch_object($req);
		$logUser=$lg->logUser;
		$nomEmp=ucfirst(mb_strtolower($lg->nomEmp, 'UTF-8'));
		$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));
		$nomService=$lg->nomService;
		
		$tel=$lg->telephone_1;
		$mail=$lg->mail;
	
?>

		<div class="container">
			<div class="page-header">
				<h2>Détails de l'utilisateur</h2>
			</div>
			<div class="container theme-showcase" role="main">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-6">
						<?php
							echo "<p><label>Nom:</label> $nomEmp</p>";
							echo "<p><label>Prénom:</label> $prenomEmp</p>";
							
							echo "<p><label>Service:</label> $nomService</p>";
						?>
						</div>
						<div class="col-md-6">
						<?php
							echo "<p><label>Login:</label> $logUser</p>";
							echo "<p><label>Mail:</label> $mail</p>";
							echo "<p><label>Téléphone:</label> $tel</p>";
						?>
						</div>
					

					

				</div>
			</div>
			<center>
				<!-- Formulaire pour le bouton suppression, plus de sécurité en passant l'id en post et non en get (empeche une suppression juste en passant par l'url) -->
				<form  style="display:inline;" method="post" action="suppUser.php" onsubmit="return confirmSupp();">
					<input type="hidden" name="idUser" value="<?php echo $idUser;?>"/>
					<input type="submit" class="btn btn-lg btn-primary" value="Supprimer"/>
				</form>
				
				<!-- bouton modifier -->
				<input type="button" class="btn btn-lg btn-primary " onclick="document.location.href='modifUser.php?idUser=<?php echo $idUser; ?>'" value="Modifier" />
				
				<!-- bouton reinitialiser mdp -->
				<input type="button" class="btn btn-lg btn-primary " onclick="document.location.href='reinitmdpUser.php?idUser=<?php echo $idUser; ?>'" value="Réinitialiser mot de passe"/>
			    </center>
		</div><!-- /.container -->

		<script>
		function confirmSupp()
		{
			if(confirm("Voulez vous vraiment supprimer cet utilisateur ?"))
				return true;
			return false;

		}


		</script>
<?php
	}
}
require('bottom.php');

?>