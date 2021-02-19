<?php 
require('top.php');
require('../conf/connexion_param.php');

//si formulaire envoyé (test aussi que un ope a été choisi, car un option disable ne s'envoi pas en POST)
if(isset($_POST["ope"]))
{
	$erreur="";
	$newId=$_POST["ope"];
	$ancienId=$_POST["ancienId"];
	
	//On modifie l'operateur
	$str="update operateur_vthrecette set idEmp_EMPLOYE=$newId where idOpe=0;";
	$req=@mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de modification de l'opérateur</strong></div>";
	else
	{
		echo '<center>';
			echo "<div class='alert alert-success'><strong>Le responsable a été modifié</strong></div>";
		echo '</center>';
	}
}
		
//recup de l'opérateur actuel

$idAncienOp=0;

$nomAncienOp="Pas d'opérateur";
$prenomAncienOp="Veuillez en nommer un";
$str="select e.idEmp, e.nomEmp, e.prenomEmp
from EMPLOYE e, operateur_vthrecette o
where o.idOpe=0
and o.idEmp_EMPLOYE=e.idEmp;";

$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);

			
while($lg=mysqli_fetch_object($req)){
	$idAncienOp=$lg->idEmp;
	$nomAncienOp=ucfirst(mb_strtolower($lg->nomEmp,'UTF-8'));
	$prenomAncienOp=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));
}

//on recupere tous les employes du labo
$str="select idEmp, nomEmp, prenomEmp
from EMPLOYE
where idService_SERVICE=3
and idEmp!=5;";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);
	
	
?>

<div class="container">
	<div class="page-header">
        <h2>Opérateur actuel : <?php echo $nomAncienOp." - ".$prenomAncienOp; ?></h2>
	</div>
	<div class="container theme-showcase" role="main">
	<h2 class="sub-header">Modification</h2>
		<div class="jumbotron">
			<form method="post" class="form-user" role="form" action="operateur.php" onsubmit="return confirmModifResp();">
				<div class="form-group">
					
					<select id="ope" class="form-control" name="ope">
						<option value="-1" disabled selected>Choisir un opérateur</option>
						<?php
						while($lg=mysqli_fetch_object($req)){
							$idEmp=$lg->idEmp;
							$nomEmp=ucfirst(mb_strtolower($lg->nomEmp,'UTF-8'));
							$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp,'UTF-8'));
							echo "<option value='$idEmp'>$nomEmp - $prenomEmp</option>";					
						}
						?>					
					</select>
				</div>
				<input type="hidden" value="<?php echo $idAncienOp; ?>" name="ancienId" />
				<button class="btn btn-lg btn-primary">Modifier l'opérateur</button>
			</form>
		</div>
	</div>
	
	<script>
	function confirmModifResp()
	{
		//supprime les anciens alert
		var t=document.querySelectorAll('.alert'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
		for (var i=0;  i< t.length; i++)
			t[i].parentNode.removeChild(t[i]);
		if(confirm("Voulez vous vraiment modifier l'opérateur actuel ?"))
		{
			var select = document.getElementById("ope");
			var choice = select.selectedIndex;  // Récupération de l'index du <option> choisi
			
			if(select.options[choice].value != "-1")
				return true;
			else
			{	
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Choisissez un opérateur</strong></div>';
				document.body.insertBefore(child, document.body.firstChild);
			}
		}
		return false;
	}
	
	</script>
	
</div><!-- /.container -->

<?php
require('bottom.php');

?>