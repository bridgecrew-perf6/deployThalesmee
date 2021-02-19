<?php
/*cette partie est présente sur toutes les pages de la partie redacteur
test si droit d'accées + demarrage de la session (avec le test supplementaire de l'existance du nom, 
evite un bug se produisant parfois quand la session est mal detruite a la fermeture du navigateur)
*/
function nbProcRedacteur($idRedac,$lastEtat,$idServ,$bdd)
{
	$str="
		select idProc
		from PROCEDURES a, etatProc b 
		where a.idProc=b.idProc_PROCEDURES and a.idEmp_EMPLOYE=$idRedac 
		and b.idEtat_ETAT =$lastEtat
		and idService_SERVICE=$idServ
		and dateEtat = ( select max(dateEtat)
						 from etatProc
						 where idProc_PROCEDURES=a.idProc
					   )
		;";
	$req=mysqli_query($bdd,$str);
	$nbProc=mysqli_num_rows($req);			
	return $nbProc;
}

session_start();
if(isset($_SESSION["infoUser"]) && isset($_SESSION["infoUser"]["nom"]) && $_SESSION['infoUser']['categUser']==4 or $_SESSION['infoUser']['categUser']==3) //on verifie que l'utilisateur a le droit d'etre sur cette page
{
	$nom=$_SESSION['infoUser']['nom'];
	$prenom=$_SESSION['infoUser']['prenom'];
	require('../conf/connexion_param.php'); 
	
	//On recupere le service
	$idServ=$_SESSION['infoUser']['idService'];
	if($_SESSION['infoUser']['categUser']==3)
	{
		//Responsable Labo 
		
		
		//on regarde si le service du responsable laboratoire a des procedure en attentes d'affectation
		$str="select idProc
			from PROCEDURES a, etatProc b 
			where a.idProc=b.idProc_PROCEDURES and a.idService_SERVICE=$idServ and b.idEtat_ETAT =12
			and dateEtat = ( select max(dateEtat)
							 from etatProc
							 where idProc_PROCEDURES=a.idProc
						   )
			;";

					
		$req=mysqli_query($bdd,$str);
		$nbProc=mysqli_num_rows($req);
	}
	
	$idRedacteur=$_SESSION['infoUser']['idEmp'];
	//Fonction qui renvoie le nombre de procedures du redacteur dont le dernier etat est $lastEtat
	
	$nbNew=nbProcRedacteur($idRedacteur,13,$idServ,$bdd);//nouvelle procedure
	$nbEnCours=nbProcRedacteur($idRedacteur,14,$idServ,$bdd);//en cours de redaction
	$nbEnRelecture=nbProcRedacteur($idRedacteur,15,$idServ,$bdd);//en relecture
	$nbEnSignature=nbProcRedacteur($idRedacteur,16,$idServ,$bdd);// en signature
	
	?>
	<!DOCTYPE html>
	<html lang="fr">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Gestion des procédures</title>
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
		  <div class="container-fluid">
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
				<?php
				if($_SESSION['infoUser']['categUser']==3)
				{
				?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Responsable laboratoire<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php 
								if($nbProc!=0)
									echo "<li><a href='listProc_affectation.php' style='color:red'>$nbProc demande(s) en attente d'affectation</a></li>";
							?>
							<li><a href="suiviEssaiLabo.php">Suivi essai</a></li>
							<li><a href="listProc_affectation.php?rea=0">Réaffecter une procédure</a></li>
							<li><a href="docClient.php">Supprimer un document client</a></li>
						</ul>
					</li>
				<?php
				}
				?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Mes procédures<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php 
							if($nbNew==0)
								echo "<li><a href='#' style='color:green'>Aucune nouvelle procédure</a></li>";
							else
								echo "<li><a href='listProc.php?idEtat=13' style='color:red'>$nbNew nouvelle(s) procédure(s)</a></li>";
							if($nbEnCours==0)
								echo "<li><a href='#' style='color:green'>Aucune procédure en cours de rédaction</a></li>";
							else
								echo "<li><a href='listProc.php?idEtat=14' style='color:red'>$nbEnCours procédure(s) en cours de rédaction</a></li>";
							if($nbEnRelecture==0)
								echo "<li><a href='#' style='color:green'>Aucune procédure en cours de relecture</a></li>";
							else
								echo "<li><a href='listProc.php?idEtat=15' style='color:red'>$nbEnRelecture procédure(s) en cours de relecture</a></li>";
							if($nbEnSignature==0)
								echo "<li><a href='#' style='color:green'>Aucune procédure en cours de signature</a></li>";
							else
								echo "<li><a href='listProc.php?idEtat=16' style='color:red'>$nbEnSignature procédure(s) en cours de signature</a></li>";
							?>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Recherche<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href='listDP.php'>Demande de procédure</a></li>
							<li><a href='rechProc.php'>Procédure</a></li>
							<li><a href='listDocClient.php'>Document client</a></li>
							<li><a href='suiviRedacLabo.php'>Suivi rédaction procédure</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $prenom." ".$nom;  ?><b class="caret"></b></a>
						<ul class="dropdown-menu ">
						<?php 
						if($_SESSION['infoUser']['categUser']==3){
							echo'<li><a href="../admin/index.php">Administration</a></li>';}
						?>
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

