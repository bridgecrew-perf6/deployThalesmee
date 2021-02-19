<?php 
require('top.php');
if(isset($_POST["nom"]))
{
	require('../conf/salt.php');
	require('../conf/connexion_param.php');
	
	$login=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["login"]));
	$nom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nom"])));
	$prenom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["prenom"])));
	$pwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['pwd']));
	$confPwd=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['confPwd']));
	
	
	if(!empty($_POST['tel']))
		$tel="'".htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['tel']))."'";
	else
		$tel='NULL';
		
	if(!empty($_POST['mail']))
		$mail="'".htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['mail']))."'";
	else
		$mail='NULL';
	
	//on reverifie que les deux mdp sont indentique (au cas ou JS désactivé...) et service !=""
	if($pwd==$confPwd && isset($_POST["service"]))
	{
		$idService=$_POST["service"];	
		
		$pwd=$PREFIXE_SALT.sha1($pwd); //hashage du mdp
		
		//On verifie que le login user est unique
		$str="select logUser from utilisateur, employe where logUser='$login' and idEmp_EMPLOYE = idEmp and (actif = 1 or idService_SERVICE = $idService);";
		$req=@mysqli_query($bdd, $str);
		if(!$req) //une erreur dans la requete renvera false
			echo '<div class="alert alert-danger"><strong>Erreur de verification de login</strong></div>';
		elseif(mysqli_num_rows($req)!=0)//si le login est deja utilisé
			echo '<center><div class="alert alert-warning"><strong>Le login est deja utilisé</strong></div></center>';
		else
		{	
			//on affecte la categorie d utilisateur
			if($idService==7){
				//demandeur
				$categ=2;
			}else{
				//redacteur
				$categ=4;
			}
			//On insere le nouvel employe
			$str="insert into EMPLOYE values (NULL,'$nom','$prenom',$mail,$tel,null,null,$idService, 1);";
			$req=@mysqli_query($bdd, $str);
			echo mysqli_error($bdd);			
			if(!$req) //une erreur dans la requete renvera false
				echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'employe</strong></div>';
			else
			{
				$idEmp=mysqli_insert_id($bdd);
				
				//On insere le nouvel utilisateur
				$str="insert into UTILISATEUR values (NULL,'$login','$pwd',$categ,$idEmp);";
				$req=@mysqli_query($bdd, $str);
				if(!$req) //une erreur dans la requete renvera false
				{
					echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'utilisateur</strong></div>';
					//plus on supprime l'employe crée inutilement
					$req=@mysqli_query($bdd, "delete from employe where idEmp=$idEmp;");
					if(!$req)
						echo '<div class="alert alert-danger"><strong>Erreur de correction de la base, supprimé l\'employé manuellement</strong></div>';
				}
				else{
					echo '<center>';
						echo "<div class='alert alert-success'><strong>Utilisateur crée</strong></div>";
						echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";	
					echo '</center>';
				}
			}
		}
	}
	else
		echo '<center><div class="alert alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques et un service doit être choisie</strong></div></center>';
}
else{
?>
	<div class="container">
		<div class="page-header">
			<h2>Nouvel utilisateur</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form method="post" action="creerUser.php" onsubmit="return testPwd()" role="form">
				<div class="jumbotron">
					
					<input name="nom" title="Nom" type="text" class="form-control" placeholder="Nom" required autofocus />
					<input name="prenom" title="Prénom" type="text" class="form-control" placeholder="Prénom"  />
					<input name="login" title="Login" type="text" class="form-control" placeholder="Login" required />						
				
					<input id="pwd" name="pwd" title="Mot de passe" type="password" class="form-control" placeholder="Mot de passe" required />
					<input id="confPwd" name="confPwd" title="Confirmer mot de passe" type="password" class="form-control" placeholder="Confirmer mot de passe" required />
					<select id="service" title="Service" class="form-control" name="service">
						<option value="-1" disabled selected>Choisir le type d'utilisateur</option>
						<option value="1">Rédacteur EMC</option>
						<option value="2">Rédacteur VIB</option>
						<option value="3">Rédacteur VTH</option>
						<option value="7">Demandeur LP</option>
					</select>
					<input name="mail" title="Mail" type="text" class="form-control" placeholder="Mail" />
					<input name="tel" title="Téléphone" type="text" class="form-control" placeholder="Téléphone" />		
				</div>
				<button style="width:30%; margin:auto;" class="btn btn-lg btn-primary btn-block" >Créer l'utilisateur</button>
			</form>
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
			
		</div>
		
	</div><!-- /.container -->
<?php
}
require('bottom.php');

?>