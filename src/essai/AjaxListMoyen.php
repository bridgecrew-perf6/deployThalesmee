<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
session_start();
require('../conf/connexion_param.php'); //connexion a la bdd

$idServ_Labo=$_SESSION['infoUser']['idService'];
$typeEssai=$_GET["typeEssai"];
$moyenSelect=$_GET["moyenSelect"];
// on recupere les moyens 
$str="SELECT idMoyen, nomMoyen from moyen where idService_SERVICE=$idServ_Labo and idMoyen in ($moyenSelect)
union
select null,'Moyen non défini' from dual;";
$reqMoyen=mysqli_query($bdd, $str);
echo mysqli_error($bdd);
$timestamp_unix=$_GET["date"]/1000;

$dateAct= date('Y-m-d 00:00:00',$timestamp_unix);
$moyen="";


$array = array(); // on créé le tableau
$array[0]=array();//contient les infos pour planning
$array[1]=array();//contient les infos pour liste att

while($lg=mysqli_fetch_object($reqMoyen))
{
	$idMoyen=$lg->idMoyen;
	if($idMoyen=="")
		$t="e.idMoyen_Moyen is null";
	else
		$t="e.idMoyen_Moyen='$idMoyen'";
	
	//pour chaque moyen on regarde si ils ont des test dans les 10 jours qui arrivent
	$str="select e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire, e.commentaire, e.retard_interne, e.planifie
	from essai e, etatessai et
	where $t
	and et.idetat_etat in ($typeEssai)
	and e.idservice_service='$idServ_Labo'
	and e.date_fin>='$dateAct'
	and e.date_debut < DATE_ADD('$dateAct', INTERVAL 7 DAY)
	and et.idEssai_ESSAI=e.idEssai
	and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
	and (e.planifie=1 or (e.planifie=0 and et.idetat_etat!=22))
	order by date_debut;"; 
	//and NOT EXISTS(select idEssai_ESSAI from etatessai et3 where et3.idetat_etat=21 and e.date_fin<now() and e.idEssai=et3.idEssai_essai)
	$req=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	if(mysqli_num_rows($req)!=0)
	{
		$moyen.=$lg->nomMoyen;
		while($lg=mysqli_fetch_object($req))
		{
			$str="select noOF_equipement_of from tester
			where idEssai_ESSAI=".$lg->idEssai.";";
			$reqOF=mysqli_query($bdd,$str);
			$of="";
			while($lgOF=mysqli_fetch_object($reqOF))
				$of.=$lgOF->noOF_equipement_of." ";
									
			$moyen.="&&".$lg->idEssai."|".$lg->idetat_etat."|".$lg->date_debut."|".$lg->date_fin."|".$lg->commentaire."|".$lg->affaire."|".$lg->equipement."|".$of."|".$lg->retard_interne."|".$lg->planifie;
		}
		$moyen.="$$";
	}
}
/*
$str="(select e.idessai, e.fifo, e.affaire, e.equipement, e.badge from essai e, service s
where idService=$idServ_Labo
and s.idservice=e.idService_SERVICE
and NOT EXISTS
(SELECT idetat_etat FROM etatessai et2 WHERE e.idessai=et2.idessai_essai
and (et2.idetat_etat!=22))
order by date_debut)
UNION ALL
(select e.idessai, e.fifo, e.affaire, e.equipement, e.badge 
from essai e, service s, etatessai et
where idService=$idServ_Labo
and s.idservice=e.idService_SERVICE
and et.idEssai_ESSAI=e.idEssai
and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
and et.idetat_etat=21
order by date_debut)
";*/

$str="select e.idessai, e.fifo, e.affaire, e.equipement, e.badge from essai e, service s, etatessai et
where idService=$idServ_Labo
and s.idservice=e.idService_SERVICE
and e.planifie=0
and et.idetat_etat=22
and et.idEssai_ESSAI=e.idEssai
and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
order by date_debut;";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);
if(mysqli_num_rows($req)!=0)
{
	$moyen.="$$";
	while($lg=mysqli_fetch_object($req))
	{
		$moyen.=$lg->idessai."|".$lg->fifo."|".$lg->badge."|".$lg->affaire."|".$lg->equipement;
		$moyen.="&&";
	}
}
$moyen = substr($moyen,0,strlen($moyen)-2);//supprime les 2 dernier caracteres inutiles (soit $$ soit &&)

echo $moyen;

mysqli_close($bdd);