<?php 
require('top.php');
require('../conf/connexion_param.php');
if(isset($_POST["nom"]))
{
	require('../conf/salt.php');
	
	
	$login=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["login"]));
	$nom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nom"])));
	$prenom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["prenom"])));
	$pwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['pwd']));
	$confPwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['confPwd']));
	$typeU=$_POST["typeU"];
	$serv=$_POST["serv"];
	
	//on reverifie que les deux mdp sont indentique (au cas ou JS désactivé...) et typeU !=""
	if($pwd==$confPwd && isset($_POST["typeU"]))
	{
		$pwd=$PREFIXE_SALT.sha1($pwd); //hashage du mdp
		
		//On verifie que le login user est unique
		$str="select logUser from utilisateur where logUser='$login';";
		$req=@mysqli_query($bdd, $str);
		if(!$req) //une erreur dans la requete renvera false
			echo '<div class="alert alert-danger"><strong>Erreur de verification de login</strong></div>';
		elseif(mysqli_num_rows($req)!=0)//si le login est deja utilisé
			echo '<center><div class="alert alert-warning"><strong>Le login est deja utilisé</strong></div></center>';
		else
		{	
			//On insere le nouvel utilisateur
			$str="insert into UTILISATEUR values (NULL,'$login','$pwd','$nom','$prenom','$typeU','$serv');";
			$req=@mysqli_query($bdd, $str);
			if(!$req) //une erreur dans la requete renvera false
				echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'utilisateur</strong></div>';
			else{
				echo '<div class="text-center">';
					echo "<div class='alert alert-success'><strong>Utilisateur crée</strong></div>";
					echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";	
				echo '</div>';
			}
		}
	}
	else
		echo '<div class="alert text-center alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques et un service doit être choisie</strong></div>';
}
else{
	//on recupere les categories
	$str="select idCateg,nomCateg from categUser where idCateg!=1;";
	$reqServ=@mysqli_query($bdd, $str);
	
	$str="select idLabo,nomLabo from labo;";
	$reqLabo=@mysqli_query($bdd, $str);
?>
	<div class="container">
		<div class="page-header">
			<h2>Nouvel utilisateur</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form method="post" action="creerUser.php" onsubmit="return testPwd()" role="form">
				<div class="jumbotron">
					
					<input id="nom" name="nom" title="Nom" type="text" class="form-control" placeholder="Nom" required autofocus />
					<input id="prenom" name="prenom" title="Prénom" type="text" class="form-control" placeholder="Prénom"  />						
					<input name="login" title="Login" type="text" class="form-control" placeholder="Login" required />	
					<input id="pwd" name="pwd" title="Mot de passe" type="password" class="form-control" placeholder="Mot de passe" required />
					<input id="confPwd" name="confPwd" title="Confirmer mot de passe" type="password" class="form-control" placeholder="Confirmer mot de passe" required />
					<select id="typeU" title="Type Utilisateur" class="form-control" name="typeU" required>
						<option value="" disabled selected>Choisir le type d'utilisateur</option>
						<?php
						while($lg=mysqli_fetch_object($reqServ))
							echo '<option value="'.$lg->idCateg.'">'.$lg->nomCateg.'</option>';
						?>
					</select>
					<select id="serv" title="Service" class="form-control" name="serv" required>
						<option value="" disabled selected>Choisir le service</option>
						<?php
						while($lg=mysqli_fetch_object($reqLabo))
							echo '<option value="'.$lg->idLabo.'">'.$lg->nomLabo.'</option>';
						?>
					</select>
				</div>
				<div class="text-center">
					<input class='btn btn-lg btn-primary' type='submit' value="Créer l'utilisateur" />
					<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
				</div>
			</form>
		</div>
	</div><!-- /.container -->
	<script>
	  //verifi que le mdp et sa confirmation son identique, indique sinon et n'envoie pas la requete au serveur
	  function testPwd(){
		//supprime les anciens alert
		var t=document.querySelectorAll('.alert'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
		for (var i=0;  i< t.length; i++)
			t[i].parentNode.removeChild(t[i]);
			
		if(document.getElementById('confPwd').value == document.getElementById('pwd').value)
			return true;
		else{
			var child= document.createElement("center");
			child.innerHTML='<div class="alert alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques</strong></div>';
			document.body.insertBefore(child, document.body.firstChild);
			return false;
		}
	  }
	</script>
<?php
}
require('bottom.php');