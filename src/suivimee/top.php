<?php
/*cette partie est présente sur toutes les pages de la partie suivimee
test si droit d'accées + demarrage de la session (avec le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur)
*/
session_start();
if(isset($_SESSION["infoUser"])&& isset($_SESSION["infoUser"]["nom"]) && ($_SESSION['infoUser']['categUser']==6 || $_SESSION['infoUser']['categUser']==1 || $_SESSION['infoUser']['categUser']==3)) //on verifie que l'utilisateur a le droit d'etre sur cette page
{
	$nom=$_SESSION['infoUser']['nom'];
	$prenom=$_SESSION['infoUser']['prenom'];
	?>
	<!DOCTYPE html>
	<html lang="fr">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Suivi Mee</title>
		<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
		<!-- Bootstrap core CSS -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />

		<!-- Custom styles for this template -->
		<link href="../css/starter-template.css" rel="stylesheet" />
		
		<link href="../bootstrap/css/bootstrap-toggle.css" rel="stylesheet" />

		<!-- Placeholders.js v3.0.2 -> ajoute le support du placeholder pour IE sans effet pour ceux qui le supporte deja -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		
		 <!--[if lt IE 10]>
		 <script src="../js/Placeholder.js"></script>
		 
		 <script src="../js/html5shiv.js"></script>
		  <script src="../js/respond.min.js"></script>
		<![endif]-->
		
		<script src="../bootstrap/js/jquery.min.js"></script>
		<script src="../bootstrap/js/bootstrap-toggle.js"></script>
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
			  <?php 
			  if($_SESSION['infoUser']['categUser']==1) 
				echo '<a class="navbar-brand" href="../admin/index.php">Accueil</a>';
			  else
				echo '<a class="navbar-brand" href="index.php">Accueil</a>';
			  ?>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Suivi MEE<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href='../redacteur/suiviRedacLabo.php'>Suivi rédaction procédure</a></li>
							<li><a href="../redacteur/suiviEssaiLabo.php">Suivi essai</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Suivi activité laboratoire<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href='visuLabo.php?idLabo=1'>Laboratoire EMC</a></li>
							<li><a href='visuLabo.php?idLabo=2'>Laboratoire VIB</a></li>
							<li><a href='visuLabo.php?idLabo=3'>Laboratoire VTH</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Recherche<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href='listDP.php'>Demande de procédure</a></li>
							<li><a href='listDocClient.php'>Document client</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $prenom." ".$nom;  ?><b class="caret"></b></a>
						<ul class="dropdown-menu " >
							<li><a href="../modifMonPwd.php">Modifier mon mot de passe</a></li>
							<li><a href="../deconnexion.php">Déconnexion</a></li>
						</ul>
					</li>
				</ul>
			</div><!-- /.nav-collapse -->
		</div><!--/.nav-collapse -->	
		</div>
		
	 <!-- les balises noscript n'affichent leurs contenues que si le javascript est désactivé -->
	<noscript><div class="alert alert-danger">
	<strong>Veuillez activer le JavaScript de votre navigateur pour le bon fonctionnement de l'application !</strong>
	</div></noscript>
<?php
}
else
	header("Location: ../index.php");
	
?>

