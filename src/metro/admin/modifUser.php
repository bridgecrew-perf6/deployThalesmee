<?php 
require('../conf/connexion_param.php');
require('top.php');

if(isset($_POST["idUser"]))//si l'utilisateur à valider le formulaire, on traite la modification
{
	//récup des infos
	$idUser=$_POST["idUser"];
	$login=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["login"]));
	$nom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nom"])));
	$prenom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["prenom"])));
	$typeU=$_POST["typeU"];
	$idLabo=$_POST["idLabo"];
	
	//on update les nouvelles infos
	$str="update utilisateur set logUser='$login', idCateg_categUser=$typeU, nomemp='$nom', prenomemp='$prenom', idLabo_labo='$idLabo' where idUser=$idUser";
	$req=@mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de mise à jour de l'utilisateur</strong></div>";
	else{
		echo '<center>';
			echo "<div class='alert alert-success'><strong>La modification a bien été éffectué</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"detailsUser.php?idUser=$idUser\"' />";
		echo '</center>';
	}
}//sinon c'est que l'on vient de demander la page, on verifie donc qu'on a bien le numéro de l'utilisateur
elseif(!isset($_GET["idUser"])) //numéro de l'utilisateur non reçu
	echo '<div class="alert alert-danger"><strong>Erreur de réception de l\'utilisateur !</strong></div>';
else
{
	$idUser=$_GET["idUser"];
	//on recupere les infos de l'utilisateur séléctionné
	$str="select u.idUser,u.logUser, u.nomEmp, u.prenomEmp, u.idCateg_categUser, u.idLabo_labo from utilisateur u
	where u.idUser=$idUser";
	$req=@mysqli_query($bdd, $str);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données de l\'utilisateur</strong></div>';
	else
	{
		$lg=mysqli_fetch_object($req);
		$logUser=$lg->logUser;
		$nomEmp=ucfirst(mb_strtolower($lg->nomEmp, 'UTF-8'));
		$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));
		$idCateg=$lg->idCateg_categUser;
		$idLabo=$lg->idLabo_labo;
		
		$str="select idCateg,nomCateg from categUser where idCateg!=1;";
		$reqServ=@mysqli_query($bdd, $str);
		
		$str="select idLabo,nomLabo from labo;";
		$reqLabo=@mysqli_query($bdd, $str);
?>

<div class="container">
	<div class="page-header">
        <h2>Modification d'un utilisateur</h2>
	</div>
	<div class="container theme-showcase" role="main">
		<form method="post"  role="form" action="modifUser.php">
			<div class="jumbotron">		
				<input name="nom" title="Nom" type="text" class="form-control" placeholder="Nom" value="<?php echo $nomEmp; ?>" required autofocus />
				<input name="prenom" title="Prénom" type="text" class="form-control" placeholder="Prénom" value="<?php echo $prenomEmp; ?>"  />
				<input name="login" title="Login" type="text" class="form-control" placeholder="Login" value="<?php echo $logUser; ?>" required />
				<select id="typeU" title="Type Utilisateur" class="form-control" name="typeU" required>
					<option value="" disabled selected>Choisir le type d'utilisateur</option>
					<?php
					if($idUser!=1) //on ne change pas le type de l'admin
					{
						while($lg=mysqli_fetch_object($reqServ))
						{
							if($idCateg==$lg->idCateg)
								echo '<option value="'.$lg->idCateg.'" selected>'.$lg->nomCateg.'</option>';
							else
								echo '<option value="'.$lg->idCateg.'">'.$lg->nomCateg.'</option>';
						}
					}
					else
						echo '<option value="1" selected>Administrateur</option>';
					?>
				</select>
				<select id="idLabo" title="Type Utilisateur" class="form-control" name="idLabo" required>
					<option value="" disabled selected>Choisir le service</option>
					<?php
					if($idUser!=1) //on ne change pas le type de l'admin
					{
						while($lg=mysqli_fetch_object($reqLabo))
						{
							if($idLabo==$lg->idLabo)
								echo '<option value="'.$lg->idLabo.'" selected>'.$lg->nomLabo.'</option>';
							else
								echo '<option value="'.$lg->idLabo.'">'.$lg->nomLabo.'</option>';
						}
					}
					else
						echo '<option value="1" selected>Administrateur</option>';
					?>
				</select>
			</div>
			<input type="hidden" name="idUser" value=<?php echo $idUser; ?> />
			<div class="text-center">
				<input type="submit" class="btn btn-lg btn-primary" value="Valider" />
				<input type="button" class="btn btn-lg btn-primary"  onclick="document.location.href='detailsUser.php?idUser=<?php echo $idUser; ?>'" value="Annuler"/>
			</div>
		</form>
	</div>
</div><!-- /.container -->

<?php
	}
}
require('bottom.php');