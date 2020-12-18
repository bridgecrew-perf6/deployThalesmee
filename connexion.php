<?php
//Traitement du formulaire
session_start();

//La configuration des navigateurs à Thales supprime tous les cookies à la fin de session -> case remember-me non utilisable
//Je laisse le code pour plus tard, au cas ou
/*
if(isset($_COOKIE['pseudo']) && isset($_COOKIE['mdp']))
{
	$login=$_COOKIE['pseudo'];
	$pwd=$_COOKIE['mdp'];
	
	require('conf/connexion_param.php'); 
	//test si l'utilisateur existe et recuperation des données
	$str="select u.logUser, u.categUser, e.idService_SERVICE, e.idEmp, e.nomEmp, e.prenomEmp from UTILISATEUR u, EMPLOYE e where u.idEmp_EMPLOYE=e.idEmp and u.logUser='$login' and u.pwdUser='$pwd' ;";
	$req=@mysqli_query($bdd, $str);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des utilisateurs</strong></div>';
	else
	{
		
		//on verifie qu'un utilisateur (une ligne de la table) corresponde bien et on stocke ses infos dans session
		if(mysqli_num_rows($req)==1)
		{
			$infoUser=array();
			while($lg=mysqli_fetch_object($req))
			{ 	$infoUser['idEmp']=$lg->idEmp;
				$infoUser['idService']=$lg->idService_SERVICE;
				$infoUser['categUser']=$lg->categUser;
				$infoUser['nom']=$lg->nomEmp;
				$infoUser['prenom']=$lg->prenomEmp;	
				$infoUser['login']=$lg->logUser;	
			}
			
			//on sauve les infos de l'utilisateur dans la session courante
			$_SESSION['infoUser']=$infoUser;
			
			//redirection vers la page index qui s'occupera de rediriger vers le bon endroit
			header("Location: index.php");
		}
		else //on affiche qu'il y a echec de connexion
			echo '<center><div class="alert alert-warning"><strong>Login ou mot de passe incorrect</strong></div></center>';
	}
}
*/
if (isset($_POST['login'])){ // le formulaire a ete envoye
	require('conf/connexion_param.php'); 
	require('conf/salt.php');
	
	//Recuperation des donnees de l utilisateur 
	$login=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['login']));
	$pwd=$PREFIXE_SALT.sha1(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['pwd'])));
	//test si l'utilisateur existe et recuperation des données
	$str="select u.logUser, u.categUser, e.idService_SERVICE, e.idEmp, e.nomEmp, e.prenomEmp from UTILISATEUR u, EMPLOYE e where u.idEmp_EMPLOYE=e.idEmp and u.logUser='$login' and u.pwdUser='$pwd' and actif = 1;";
	$req=mysqli_query($bdd, $str);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des utilisateurs</strong></div>';
	else
	{
		//on verifie qu'un utilisateur (une ligne de la table) corresponde bien et on stocke ses infos dans session
		if(mysqli_num_rows($req)==1)
		{
			$infoUser=array();
			$lg=mysqli_fetch_object($req);
			$infoUser['idEmp']=$lg->idEmp;
			$infoUser['idService']=$lg->idService_SERVICE;
			$infoUser['categUser']=$lg->categUser;
			$infoUser['nom']=ucfirst(mb_strtolower($lg->nomEmp, 'UTF-8')); //Premiere lettre en majuscule, le reste en minuscule, encodage en utf-8
			$infoUser['prenom']=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));	
			$infoUser['login']=$lg->logUser;
			$infoUser['date']['jour'] = 0;
			$infoUser['date']['mois'] = 0;
			$infoUser['date']['semaine'] = 0;
			$infoUser['affichage'] = "semaine";
			
			
			//on sauve les infos de l'utilisateur dans la session courante
			$_SESSION['infoUser']=$infoUser;
			
			/*
			//si case remember me cocher on crée les cookies
			if(isset($_POST["remember"]))
			{
				$expire = time() + 3600 * 24 *30;
				setcookie('pseudo', $login, $expire);
				setcookie('mdp', $pwd, $expire); 
			}
			*/
			
			//redirection vers la page index qui s'occupera de rediriger vers le bon endroit
			header("Location: index.php");
		}
		else //on affiche qu'il y a echec de connexion
			echo '<center><div class="alert alert-warning"><strong>Login ou mot de passe incorrect</strong></div></center>';
	}
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
 
    <title>Connexion</title>
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/starter-template.css" rel="stylesheet">
	

<!-- Placeholders.js v3.0.2 -> ajoute le support du placeholder pour IE sans effet pour ceux qui le supporte deja -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		
		 <!--[if lt IE 10]>
		 <script src="js/Placeholder.js"></script>
		 
		 <script src="js/html5shiv.js"></script>
		  <script src="js/respond.min.js"></script>
		<![endif]-->
	
   </head>

  <body style="background-color:#eef">
   <!--[if lt IE 10]>
		<center><div class="alert alert-warning"><strong>Ce navigateur est obsolète, utilisez plutôt Firefox pour un fonctionnement optimal de l'application</strong></div></center>';
    <![endif]-->
    <div class="container">
      <form method="post" action="connexion.php" class="form-signin" role="form">
        <p >
			<input name="login" type="text" class="form-control" placeholder="Login" required autofocus />
			<input name="pwd" type="password" class="form-control" placeholder="Mot de passe" required />
			<!-- <input type="checkbox" name="remember" value="remember"> Se souvenir de moi -->
		</p>

        <button class="btn btn-lg btn-primary btn-block">Connexion</button>
      </form>
	  <center><div class="alert alert-success"><strong>Le mot de passe par défaut est: azerty</strong></div></center>
	<!--<div class="test" ><img  src="img/tas.gif" /></div>-->
    </div> <!-- /container -->
	
	
  </body>
</html>