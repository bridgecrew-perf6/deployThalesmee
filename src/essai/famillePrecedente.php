<?php

require('../conf/connexion_param.php'); //connexion a la bdd

$str = "SELECT idSaveFamille as maxFamille, famille FROM saveFamille WHERE idSaveFamille = (SELECT max(idSaveFamille) FROM saveFamille);";
$req = mysqli_query($bdd, $str);
$lg = mysqli_fetch_object ($req);
$id = $lg->maxFamille;
echo $id;
$tab = explode (":",$lg->famille);
$str = "DELETE FROM famille";
$req = mysqli_query($bdd, $str);
for ($i=0 ; $i< count($tab); $i++){
					
	$fam = explode (";",$tab[$i]);
	$str = "INSERT INTO famille VALUES (NULL, '$fam[1]', '$fam[2]', $fam[3]);";
	$req = mysqli_query($bdd, $str);
}

sleep(1);
$str = "DELETE FROM saveFamille WHERE idSaveFamille = $id";
$req = mysqli_query($bdd, $str);

?>