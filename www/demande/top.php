<?php
/*cette partie est présente sur toutes les pages de la partie demande
test si droit d'accées + demarrage de la session (avec le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur)
*/
session_start();
if(isset($_SESSION["infoUser"]) && isset($_SESSION["infoUser"]["nom"]) && $_SESSION['infoUser']['categUser']==2) //on verifie que l'utilisateur a le droit d'etre sur cette page
{
	$nom=$_SESSION['infoUser']['nom'];
	$prenom=$_SESSION['infoUser']['prenom'];
	
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	$this_emp=$_SESSION['infoUser']['idEmp'];
	//on regarde s il a des DP a modifier
	$str="select idDp from DEMANDE_PROCEDURE
	where idEmp_EMPLOYE=$this_emp and validiteDP not in (-1,4);";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de recupération des demandes à valider</strong></div>';
	else
	{
		$nbDP=(mysqli_num_rows($req));
		mysqli_free_result($req); //libere de la memoire
		mysqli_close($bdd); //fermeture de la bd
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
		
		<title>Demande de procédures</title>
		<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
		<!-- Bootstrap core CSS -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />

		<!-- Custom styles for this template -->
		<link href="../css/starter-template.css" rel="stylesheet" />
		
		
		<!-- Placeholders.js v3.0.2 -> ajoute le support du placeholder pour IE sans effet pour ceux qui le supporte deja -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		
		 <!--[if lt IE 10]>
		 <script src="../js/Placeholder.js"></script>
		 
		 <script src="../js/html5shiv.js"></script>
		  <script src="../js/respond.min.js"></script>
		<![endif]-->
		
		
		<script src="../bootstrap/js/jquery.min.js"></script>
				
		
	  </head>

	  <body>

		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		  <div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand" href="index.php">Accueil</a>
			</div>
			<div class="collapse navbar-collapse">
			  <ul class="nav navbar-nav">
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Demande de procédure<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="DProc_1.php">Nouvelle demande de procédure</a></li>
					<?php if($nbDP !=0) echo '<li><a href="listDP.php?mode=0">Terminer une demande de procédure</a></li>'; ?>
					<li><a href="listDP.php?mode=1">Supprimer une demande de procédure</a></li>
				  </ul>
				</li>
			 
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Demande validée<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listDP.php?mode=2">Modifier une demande validée</a></li>
					<li><a href="evoProc.php">Évolution d'une procédure</a></li>
				  </ul>
				</li>
			
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Suivi et documents<b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="listDP.php?mode=3">Suivi avancement rédaction procédure</a></li>
					<li><a href="docClient.php">Ajouter / Rechercher des documents client</a></li>
					<li><a href='rechProc.php'>Procédure</a></li>
				  </ul>
				</li>
			  </ul>
			  <ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $prenom." ".$nom;  ?><b class="caret"></b></a>
				  <ul class="dropdown-menu">
					<li><a href="../modifMonPwd.php">Modifier mon mot de passe</a></li>
					<li><a href="../deconnexion.php">Déconnexion</a></li>
				  </ul>
				</li>
			  </ul>
			</div><!--/.nav-collapse -->
			
			
		  </div>
		</div>

	 <!-- les balises noscript n'affichent leurs contenues que si le javascript est désactivé -->
	<noscript><div class="alert alert-danger">
	<strong>Veuillez activer le JavaScript de votre navigateur pour le bon fonctionnement de l'application !</strong>
	</div></noscript>
<?php
}
else
	header("Location: ../index.php");

