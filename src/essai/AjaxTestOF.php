<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
$OF=$_GET["OF"];

require('../conf/connexion_param.php');

$str="SELECT t.idEssai_essai from equipement_of e, type_modele m, tester t, etatessai et 
where t.noOf_equipement_of='$OF'
and e.idModele_TYPE_MODELE=m.idModele
and e.noOF = t.noOf_equipement_of
and t.idEssai_essai=et.idEssai_essai
and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=t.idEssai_essai)
and et.idEtat_etat < 25;";
$req=mysqli_query($bdd, $str); 

if(mysqli_num_rows($req)==0)
	echo "1";
else
	echo "0".mysqli_fetch_object($req)->idEssai_essai;