<?php
require('../conf/connexion_param.php');
require('top.php');
require('bottom.php');
$str="SELECT * FROM `typecapteur` ORDER BY `idTypeC` ASC ";
$reqMoyen=mysqli_query($bdd, $str);
echo '<div class="container-fluid">';
echo '<div class="jumbotron" style="margin-top:50px"><h3>Tableau récapitulatif des instruments/capteurs</h3></div>';
?>
<div class="jumbotron">
<div class="row">
<div class="col-md-12"><a type="button" style="margin-top:10px" href="cherche_capteurs.php" class="btn btn-info btn-lg btn-block" value=Tous>Tous les capteurs</a></div>
<div class="col-md-12"><a type="button" style="margin-top:10px" href="cherche_instruments.php" class="btn btn-info btn-lg btn-block" value=Tous>Tous les instruments non capteurs</a></div>
</div>
</div>
<div class="jumbotron">
<div class="row">
<?php
while($lg=mysqli_fetch_object($reqMoyen))
	{
		
		echo '<div class="col-md-4">';
		echo '<a type="button" style="margin-top:10px" href="cherche_capteurs.php?type='.$lg->nomTypeC.'" class="btn btn-info btn-lg btn-block" value='.$lg->nomTypeC.'>'.$lg->nomTypeC.'</a>';
		echo '</div>';
					
		}
?>
</div>
</div>
</div>
<div class="text-center"><a type="button" style="margin-top:10px" href="index.php" class="btn btn-primary btn-lg" value="Retour">Retour</a></div>;

