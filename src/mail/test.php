<?php
require("../fonction.php");

$obj="objTEST";
$dest="boyern";
$emet="boyern";
$cc="";
$corps="Ceci est un test\nsaut de ligne";

envoi_mail($obj, $dest, $emet, $cc, $corps,"32");
