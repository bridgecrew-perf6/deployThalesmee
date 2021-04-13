<?php
/*cette partie est présente sur toutes les pages de la partie essai
test si droit d'accées + demarrage de la session (avec le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur)
*/
session_start();
if(isset($_SESSION["infoUser"]) && isset($_SESSION["infoUser"]["nom"]) && ($_SESSION['infoUser']['categUser']==9)) 
	//on verifie que lutilisateur a le droit detre sur cette page
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
		
		
		<title>Gestion des essais</title>
		<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
		<!-- Bootstrap core CSS -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />

		<link href="../bootstrap/css/bootstrap-toggle.css" rel="stylesheet" />

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
		<script src="../bootstrap/js/bootstrap-toggle.js"></script>
		<script src="../bootstrap/dist/js/bootstrap-checkbox.js" defer></script>		
	  </head>

	  <body>

		<div class="navbar navbar-inverse" role="navigation">
		  <div class="container-fluid">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <div class="navbar-header">
				
				<a class="navbar-brand" href="index.php">Accueil</a>
			  </div>
			  
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $prenom." ".$nom;  ?><b class="caret"></b></a>
						<ul class="dropdown-menu " >
						<?php 
						if($_SESSION['infoUser']['categUser']==3){
							echo'<li><a href="../admin/index.php">Administration</a></li>';}
						?>
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

