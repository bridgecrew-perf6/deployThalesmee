<?php 
require('top.php');
require('../conf/connexion_param.php');

//si formulaire envoyé (test aussi que un resp a été choisi, car un option disable ne s'envoi pas en POST)
if(isset($_POST["resp"]))
{
	$erreur="";
	$newId=$_POST["resp"];
	$ancienId=$_POST["ancienId"];
	
	//on change le responsable
	//On modifie le responsable de laboratoire
	$str="update SERVICE set idEmp_EMPLOYE=$newId where idService=2;";
	$req=@mysqli_query($bdd,$str);
	if(!$req)
		$erreur.="Erreur de changement de responsable. ";

	//On modifie la categorie de l'utilisateur ancien RL, devient redacteur
	$str="update UTILISATEUR set categUser=4 where idEmp_EMPLOYE=$ancienId;";
	$req=@mysqli_query($bdd,$str);
	if(!$req)
		$erreur.="Erreur modification de la categorie de l'ancien responsable. ";
	
	//On modifie la categorie de l'utilisateur du nouveau RL, devient RL
	$str="update UTILISATEUR set categUser=3 where idEmp_EMPLOYE=$newId;";
	$req=@mysqli_query($bdd,$str);
	if(!$req)
		$erreur.="Erreur modification de la categorie du nouveau responsable. ";
	
	if($erreur != "") //si erreur
		echo "<div class='alert alert-danger'><strong>$erreur</strong></div>";
	else
	{
		echo '<center>';
			echo "<div class='alert alert-success'><strong>Le responsable a été modifié</strong></div>";
		echo '</center>';
	}
}
		
//recup du resp actuel

//on recupere l'ancien responsable de laboratoire
$idAncienRL=0;
$labo="";
$nomAncienRL="Pas de responsable";
$prenomAncienRL="Veuillez en nommer un";
$str="select e.idEmp, e.nomEmp, e.prenomEmp
from EMPLOYE e, service s
where e.idService_SERVICE=2
and e.idService_SERVICE=s.idService
and e.idEmp=s.idEmp_EMPLOYE;";

$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);

			
while($lg=mysqli_fetch_object($req)){
	$idAncienRL=ucfirst(mb_strtolower($lg->idEmp,'UTF-8'));
	$nomAncienRL=ucfirst(mb_strtolower($lg->nomEmp,'UTF-8'));
	$prenomAncienRL=ucfirst(mb_strtolower($lg->prenomEmp,'UTF-8'));
}

//on recupere tous les employes du labo
$str="select idEmp, nomEmp, prenomEmp
from EMPLOYE
where idService_SERVICE=2
and idEmp!=4;";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);
	
	
?>

<div class="container">
	<div class="page-header">
        <h2>Responsable laboratoire VIB actuel : <?php echo $nomAncienRL." - ".$prenomAncienRL; ?></h2>
	</div>
	<div class="container theme-showcase" role="main">
	<h2 class="sub-header">Modification</h2>
		<div class="jumbotron">
			<form method="post" class="form-user" role="form" action="modifRespVIB.php" onsubmit="return confirmModifResp();">
				<div class="form-group">
					
					<select id="resp" class="form-control" name="resp">
						<option value="-1" disabled selected>Choisir un nouveau responsable</option>
						<?php
						while($lg=mysqli_fetch_object($req)){
							$idEmp=ucfirst(mb_strtolower($lg->idEmp,'UTF-8'));
							$nomEmp=ucfirst(mb_strtolower($lg->nomEmp,'UTF-8'));
							$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp,'UTF-8'));
							echo "<option value='$idEmp'>$nomEmp - $prenomEmp</option>";					
						}
						?>					
					</select>
				</div>
				<input type="hidden" value="<?php echo $idAncienRL; ?>" name="ancienId" />
				<button class="btn btn-lg btn-primary">Modifier le responsable</button>
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
		if(confirm("Voulez vous vraiment modifier le responsable actuel ?"))
		{
			var select = document.getElementById("resp");
			var choice = select.selectedIndex;  // Récupération de l'index du <option> choisi
			
			if(select.options[choice].value != "-1")
				return true;
			else
			{	
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Choisissez un responsable</strong></div>';
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