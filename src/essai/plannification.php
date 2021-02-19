<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require('../conf/connexion_param.php'); 
require('../conf/connexionPDO_param.php');// connexion a la base
require('top.php');

$labo=$_SESSION['infoUser']['idService'];// service du labo

if (!isset($_GET["annee"])){
	
	$annee = date("Y");
	
}else {
	
	$annee = $_GET["annee"];
}

if (isset($_POST["annee"])){
	
	$annee = $_POST["annee"];
	
	$janvier = $_POST["janvier"];
	$fevrier = $_POST["fevrier"];
	$mars = $_POST["mars"];
	$avril = $_POST["avril"];
	$mai = $_POST["mai"];
	$juin = $_POST["juin"];
	$juillet = $_POST["juillet"];
	$aout = $_POST["aout"];
	$septembre = $_POST["septembre"];
	$octobre = $_POST["octobre"];
	$novembre = $_POST["novembre"];
	$decembre = $_POST["decembre"];
	
	$janvier_vu = $_POST["janvier_vu"];
	$fevrier_vu = $_POST["fevrier_vu"];
	$mars_vu = $_POST["mars_vu"];
	$avril_vu = $_POST["avril_vu"];
	$mai_vu = $_POST["mai_vu"];
	$juin_vu = $_POST["juin_vu"];
	$juillet_vu = $_POST["juillet_vu"];
	$aout_vu = $_POST["aout_vu"];
	$septembre_vu = $_POST["septembre_vu"];
	$octobre_vu = $_POST["octobre_vu"];
	$novembre_vu = $_POST["novembre_vu"];
	$decembre_vu = $_POST["decembre_vu"];
	
	//Requete permettant de récupérer les informations dans la base de données
	$str ="SELECT idPlannification FROM plannification WHERE annee = $annee and idService_SERVICE = $labo";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) != 0){
		
		$str = "UPDATE plannification SET janvier = '$janvier', fevrier='$fevrier', mars = '$mars', avril = '$avril', mai = '$mai', juin = '$juin', juillet = '$juillet', aout = '$aout', septembre = '$septembre', octobre = '$octobre', novembre = '$novembre', decembre = '$decembre' WHERE annee = $annee and idService_SERVICE = $labo";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req = mysqli_query($bdd, $str);
		
	}else {
		
		$str = "INSERT INTO plannification VALUES ( NULL, $annee , '$janvier', '$fevrier', '$mars', '$avril', '$mai', '$juin', '$juillet', '$aout', '$septembre', '$octobre', '$novembre', '$decembre' ,$labo)";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req = mysqli_query($bdd, $str);
		
	}
	
	$str ="SELECT idPlannificationVu FROM plannification_vu WHERE annee = $annee and idService_SERVICE = $labo";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) != 0){
		
		$str = "UPDATE plannification_vu SET janvier = '$janvier_vu', fevrier='$fevrier_vu', mars = '$mars_vu', avril = '$avril_vu', mai = '$mai_vu', juin = '$juin_vu', juillet = '$juillet_vu', aout = '$aout_vu', septembre = '$septembre_vu', octobre = '$octobre_vu', novembre = '$novembre_vu', decembre = '$decembre_vu' WHERE annee = $annee and idService_SERVICE = $labo";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req = mysqli_query($bdd, $str);
		
	}else {
		
		$str = "INSERT INTO plannification_vu VALUES ( NULL , $annee , '$janvier_vu', '$fevrier_vu', '$mars_vu', '$avril_vu', '$mai_vu', '$juin_vu', '$juillet_vu', '$aout_vu', '$septembre_vu', '$octobre_vu', '$novembre_vu', '$decembre_vu',$labo)";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req = mysqli_query($bdd, $str);
		
	}
	
	//Script permettant la redirection après la validation du formulaire
	echo '<script>function redirection(){
	
			document.location.href="index.php";
		}

		swal({
			
			title : "Informations validées",
			text : "Redirection dans quelques instants",
			icon : "success"
			
		});
		setTimeout(redirection, 1250);</script>';
	
}else{
	
	//Requete permettant de récupérer de remplir le tableau
	$str = "SELECT * FROM plannification WHERE idService_SERVICE=$labo and annee = $annee";
	$req = mysqli_query($bdd, $str);
	$lg_plan = mysqli_fetch_object($req);
	
	$str = "SELECT * FROM plannification_vu WHERE idService_SERVICE=$labo and annee = $annee";
	$req = mysqli_query($bdd, $str);
	$lg_plan_vu = mysqli_fetch_object($req);
	
}
?>

<div class="container-fluid">
	<div class="page-header">
		<h2>Efficience plannification</h2> 
	</div>
	<div class="jumbotron">
		<div class="row">
			<div class="col-md-4">
				<button class="btn btn-block btn-warning" onclick="changeDate(1)">Année précédente</button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-block btn-info" onclick="changeDate(2)">Année en cours</button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-block btn-warning" onclick="changeDate(3)">Année suivante</button>
			</div>
		</div>
	</div>
	<div class="jumbotron">
		<form enctype="multipart/form-data" action="plannification.php" method="post">
			<input id="date" type="hidden" name="annee" value="<?php echo $annee ?>" />
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $annee ?></th>
						<th>Janvier</th>
						<th>Février</th>
						<th>Mars</th>
						<th>Avril</th>
						<th>Mai</th>
						<th>Juin</th>
						<th>Juillet</th>
						<th>Août</th>
						<th>Septembre</th>
						<th>Octobre</th>
						<th>Novembre</th>
						<th>Décembre</th>
					</tr>
				</thead>
				</tbody>
					<tr>
						<th>Esssais plannifiés</th>
						<td><input class="form-control" size="4" type="text" name="janvier" value="<?php if (isset($lg_plan->janvier)) echo $lg_plan->janvier ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="fevrier" value="<?php if (isset($lg_plan->fevrier)) echo $lg_plan->fevrier ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="mars" value="<?php if (isset($lg_plan->mars)) echo $lg_plan->mars ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="avril" value="<?php if (isset($lg_plan->avril)) echo $lg_plan->avril ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="mai" value="<?php if (isset($lg_plan->mai)) echo $lg_plan->mai ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="juin" value="<?php if (isset($lg_plan->juin)) echo $lg_plan->juin ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="juillet" value="<?php if (isset($lg_plan->juillet)) echo $lg_plan->juillet ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="aout" value="<?php if (isset($lg_plan->aout)) echo $lg_plan->aout ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="septembre" value="<?php if (isset($lg_plan->septembre)) echo $lg_plan->septembre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="octobre" value="<?php if (isset($lg_plan->octobre)) echo $lg_plan->octobre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="novembre" value="<?php if (isset($lg_plan->novembre)) echo $lg_plan->novembre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="decembre" value="<?php if (isset($lg_plan->decembre)) echo $lg_plan->decembre ?>"/></td>
					</tr>
					<tr>
						<th>Essais plannifiés vus</th>
						<td><input class="form-control" size="4" type="text" name="janvier_vu" value="<?php if (isset($lg_plan_vu->janvier)) echo $lg_plan_vu->janvier ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="fevrier_vu" value="<?php if (isset($lg_plan_vu->fevrier)) echo $lg_plan_vu->fevrier ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="mars_vu" value="<?php if (isset($lg_plan_vu->mars)) echo $lg_plan_vu->mars ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="avril_vu" value="<?php if (isset($lg_plan_vu->avril)) echo $lg_plan_vu->avril ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="mai_vu" value="<?php if (isset($lg_plan_vu->mai)) echo $lg_plan_vu->mai ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="juin_vu" value="<?php if (isset($lg_plan_vu->juin)) echo $lg_plan_vu->juin ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="juillet_vu" value="<?php if (isset($lg_plan_vu->juillet)) echo $lg_plan_vu->juillet ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="aout_vu" value="<?php if (isset($lg_plan_vu->aout)) echo $lg_plan_vu->aout ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="septembre_vu" value="<?php if (isset($lg_plan_vu->septembre)) echo $lg_plan_vu->septembre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="octobre_vu" value="<?php if (isset($lg_plan_vu->octobre)) echo $lg_plan_vu->octobre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="novembre_vu" value="<?php if (isset($lg_plan_vu->novembre)) echo $lg_plan_vu->novembre ?>"/></td>
						<td><input class="form-control" size="4" type="text" name="decembre_vu" value="<?php if (isset($lg_plan_vu->decembre)) echo $lg_plan_vu->decembre ?>"/></td>
					</tr>
				</tbody>
			
			</table>
			<center>
				<input type="submit" value="Valider" class="btn btn-primary btn-lg" />
			</center>
		</form>
	
	</div>
</div>
<script>
function changeDate(num){
	
	if (num == 1){
		
		var date = $("#date").val();
		date -= 1;
		document.location.href = "plannification.php?annee="+date;
		
	}else if (num == 2){

		document.location.href = "plannification.php";
		
	}else {
		
		var date = $("#date").val();
		date ++;
		document.location.href = "plannification.php?annee="+date;
		
	}
	
}
</script>
<?php
require('bottom.php');