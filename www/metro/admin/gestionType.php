<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php 
require('top.php');
require('../conf/connexion_param.php');
if(isset($_POST["nom"]))
{
	$nom = $_POST['nom'];
	$tabChamp=array();
	$axes = array("axeX","sensiX","axeY","sensiY","axeZ","sensiZ","axeZs","sensiZs");
	$colonnes="";
	if(isset($_POST['champ'])) $tabChamp=$_POST['champ'];
	for ($cpt = 0; $cpt < count($tabChamp); $cpt++)
	{
		$colonnes .= $axes[$cpt]."-";
	}

	$colonnes = substr($colonnes,0,-1);
	$str = "INSERT INTO typecapteur VALUES(NULL, '$nom', '$colonnes', '".implode("-", $tabChamp)."')";
	$req = mysqli_query($bdd, $str);
	if (!$req) echo "<div class='alert alert-danger'><strong>Erreur d'ajout du nouveau type'</strong></div>";
	else echo '<script src="../js/success.js"></script>';

}
else{
	
?>
	<div class="container">
		<div class="page-header">
			<h2>Ajouter un type d'instrument</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form method="post" action="gestionType.php" role="form">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-6">
							<input id="nom" name="nom" title="Nom" type="text" class="form-control" placeholder="Nom du type" required autofocus />
						</div>
						<div class="col-md-6">
							<input id="nombre" type="number" class="form-control" placeholder="Nombre de champs" />
						</div>
					</div>
					<div class="row" id="content">

					</div>
					
					
				</div>
				<div class="text-center">
					<input class='btn btn-lg btn-primary' type='submit' value="Créer le type" />
					<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
				</div>
			</form>
		</div>
	</div><!-- /.container -->
	<script>
		$("#nombre").change(function(){
			$("#content").empty();
			var nb = $("#nombre").val();
			var res = '';
			for (var i=1; i<=nb; i++)
			{
				res += '<div class="col-md-3"><input name="champ[]" title="Nom du champ n° '+i+'" type="text" class="form-control" placeholder="Nom du champ n° '+i+'" required/></div>';
			}
			$("#content").append(res);

		})
	</script>
<?php
}
require('bottom.php');