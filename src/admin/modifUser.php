<?php 
require('../conf/connexion_param.php');
require('top.php');


if(isset($_POST["formModif"]))//si l'utilisateur à valider le formulaire, on traite la modification
{
	//récup des infos
	$idUser=$_POST["idUser"];
	$login=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["login"]));
	$nom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nom"])));
	$prenom=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["prenom"])));
	
	
	if(!empty($_POST['tel']))
		$tel="'".htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['tel']))."'";
	else
		$tel='NULL';
		
	if(!empty($_POST['mail']))
		$mail="'".htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['mail']))."'";
	else
		$mail='NULL';
	
	//variable de traitement d'erreur
	$erreur="";
	
	//on update les nouvelles infos
	$str="update utilisateur set logUser='$login' where idUser=$idUser";
	$req=@mysqli_query($bdd, $str);
	if(!$req)
		$erreur.="Erreur de modification de l'utilisateur ";
	
	$str="update employe set nomemp='$nom', prenomemp='$prenom', telephone_1=$tel, mail=$mail where idEmp in
	(select idEmp_EMPLOYE from utilisateur where idUser=$idUser);";
	$req=@mysqli_query($bdd, $str);
	if(!$req)
		$erreur.="Erreur de modification de l'employé ";
	
	//on ne modifie le service de l'utilisateur séléctionné que si celui ci n'est pas résponsable d'un labo et que son service est de type 1/2/3/7 (EMC/VIB/VTH/DEMANDE LP) -> service non initialisé
	if(isset($_POST["service"]))
	{
		$idService=$_POST["service"];
		//on affecte la categorie d utilisateur
		if($idService==7){
			//demandeur
			$categ=2;
		}else{
			//redacteur
			$categ=4;
		}
		
		//On modifie le service de l employe
		$str="update EMPLOYE set idService_SERVICE=$idService where idEmp in
		(select idEmp_EMPLOYE from utilisateur where idUser=$idUser);";
		$req=@mysqli_query($bdd, $str);
		if(!$req)
			$erreur.="Erreur du service de l'employé ";
	
		//On imodifie la categorie de l'utilisateur
		$str="update UTILISATEUR set categUser=$categ where idUser=$idUser;";
		$req=@mysqli_query($bdd, $str);
		if(!$req)
			$erreur.="Erreur de modification de la catégorie de l'utilisateur ";
	}
	
	if($erreur != "")
		echo "<div class='alert alert-danger'><strong>$erreur</strong></div>";
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
	$str="select u.idUser,u.logUser, e.nomEmp, e.prenomEmp, e.telephone_1,e.mail, s.nomService, s.idService from utilisateur u, employe e, service s
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
		$idService=$lg->idService;
		
		$tel=$lg->telephone_1;
		$mail=$lg->mail;
		
		//test si l'utilisateur est resp de labo -> resultat dans booleen resp
		$str="select idService from service where idEmp_EMPLOYE in
		(select idEmp_EMPLOYE from utilisateur where idUser=$idUser);";
		$req=mysqli_query($bdd, $str);
		$resp=true;
		if(mysqli_num_rows($req)==0)
			$resp=false;
		
			
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
			
				<?php 
				//on ne peut modifier le service de l'utilisateur que si celui ci n'est pas résponsable d'un labo et que son service est de type 1/2/3/7 (EMC/VIB/VTH/DEMANDE LP)
				if(!$resp && ($idService==1 || $idService==2 || $idService==3 || $idService==7))
				{
					echo '<select title="Service" class="form-control" name="service" >';
					echo '<option value="" disabled >Choisir le type d\'utilisateur</option>';
					if($idService==1)
						echo '<option selected value="1">Rédacteur EMC</option>';
					else
						echo '<option value="1">Rédacteur EMC</option>';
					if($idService==2)
						echo '<option selected value="2">Rédacteur VIB</option>';
					else
						echo '<option value="2">Rédacteur VIB</option>';
					if($idService==3)
						echo '<option selected value="3">Rédacteur VTH</option>';
					else
						echo '<option value="3">Rédacteur VTH</option>';
					if($idService==4)
						echo '<option selected value="7">Demandeur LP</option>';
					else
						echo '<option value="7">Demandeur LP</option>';
					echo "</select>";
				}

				?>
			
				<input value="<?php echo $mail; ?>" name="mail" title="Mail" type="text" class="form-control" placeholder="Mail" />
				<input value="<?php echo $tel; ?>" name="tel" title="Téléphone" type="text" class="form-control" placeholder="Téléphone" />
			
			</div>
			<input type="hidden" name="idUser" value=<?php echo $idUser; ?> />
			<center>
				<button name="formModif" class="btn btn-lg btn-primary">Valider</button>
				<input type="button" class="btn btn-lg btn-primary "  onclick="document.location.href='detailsUser.php?idUser=<?php echo $idUser; ?>'" value="Annuler"/>
			</center>
		</form>
	</div>
	
</div><!-- /.container -->

<?php
	}
}
require('bottom.php');

?>