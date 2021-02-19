<?php
require('../conf/connexion_param.php'); 

$target = $_GET["target"];
$idTarget = $_GET["idTarget"];
$str = "UPDATE target SET valeur = $target WHERE idTarget = $idTarget";
$req = mysqli_query($bdd, $str);
