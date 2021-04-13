<?php

require('./conf/connexion_param.php'); //connexion a la bdd


$str="delete from essaiSupp where idEssaiSupp in (7,8,9,10,22);";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);


echo "5";