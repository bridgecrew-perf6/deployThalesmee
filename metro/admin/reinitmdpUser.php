<?php 
require('top.php');
if(!isset($_GET["idUser"])) //on verifie que l'id est bien renseigné
	echo '<div class="alert alert-danger"><strong>Erreur de réception de l\'utilisateur !</strong></div>';
else
{
	$idUser=$_GET["idUser"];
	if(isset($_POST["pwd"]))
	{
		require('../conf/connexion_param.php'); 
		require('../conf/salt.php');
		
		$pwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['pwd']));
		$confPwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['confPwd']));
		if($pwd==$confPwd)//on verifie que les deux mdp saisis sont identiques
		{	
			
			
			$pwd=$PREFIXE_SALT.sha1($pwd); //hashage du mdp
			//update du mdp
			$str="update utilisateur set pwdUser='$pwd' where idUser=$idUser;";
			$req=@mysqli_query($bdd, $str);
			if(!$req) //une erreur dans la requete renvera false
				echo '<div class="alert alert-danger"><strong>Erreur de modification du mot de passe</strong></div>';
			else{
				echo "<div class='text-center'><div class='alert alert-success'><strong>La modification a bien été éffectué</strong></div>";
				echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"detailsUser.php?idUser=$idUser\"' /></div>";
			}

		}
		else
			echo '<center><div class="alert alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques</strong></div></center>';
	}
	else
	{

	?>
	
	<div class="container">
		<div class="page-header">
			<h2>Réinitialiser le mot de passe</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<form onsubmit="return testPwd()" action="reinitmdpUser.php?idUser=<?php echo $idUser; ?>" method="post" class="form-user" role="form">
					<div class="form-group">
						<input id="pwd" name="pwd" type="password" class="form-control" placeholder="Nouveau mot de passe" required>
						<input id="confPwd" name="confPwd" type="password" class="form-control" placeholder="Confirmer mot de passe" required>
					</div>
						<button class="btn btn-lg btn-primary " >Réinitialiser mot de passe</button>
						<input type="button" class="btn btn-lg btn-primary "  onclick="document.location.href='detailsUser.php?idUser=<?php echo $idUser; ?>'" value="Annuler"/>
				</form>
			</div>
			
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
}
?>