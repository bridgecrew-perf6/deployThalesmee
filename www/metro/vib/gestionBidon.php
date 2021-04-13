<?php
require("top.php");
require('../conf/connexion_param.php'); //connexion a la bdd
$str="select p.idProjet, l.nomLocal from projetBidon p, localisation l
where p.idLocal_localisation=l.idLocal;";
$req=mysqli_query($bdd, $str);
?>
<div class="container">
	<div class="page-header">
		<h2>Gestion projet bidon</h2>
	</div>
	<div class="row">
		<?php
		while($lg=mysqli_fetch_object($req))
		{
			echo '<div class="col-md-4 text-center">';
				echo '<p><a href="detailsBidon.php?idProjet='.$lg->idProjet.'" class="btn btn-primary btn-lg" role="button">'.$lg->nomLocal.'</a></p>';
			echo '</div>';
		}
		?>
	</div>
	<div class="text-center">
		<p><a href="creerModifierBidon.php" class="btn btn-success btn-lg" role="button">Ajouter un pot</a></p>
	</div>
</div>
<?php
require("bottom.php");
