<?php 
session_start();

if(isset($_SESSION["infoUser"]) && ($_SESSION['infoUser']['categUser']==1 || $_SESSION['infoUser']['categUser']==6) && isset($_GET["idService"]))
{
	// l utilisateur a bien ete identifie et l utilisateur a la droit d etre sur cette page
	require('../conf/connexionPDO_param.php'); 	// connexion a la base
	

	$condDate="";
	$idService=$_GET["idService"];
	
	if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
	{
		$fifo = $_GET["fifo"];
		$dateDeb=$_GET["dateDeb"];
		$dateFin=$_GET["dateFin"];
		$condDate="and et.dateEtat >=:dateDeb and et.dateEtat <=:dateFin";
		$condVar=array('idService' => $idService,'dateDeb' => $dateDeb,'dateFin' => $dateFin);
	}
	else
		$condVar=array('idService' => $idService);
	
	if ($fifo == 2){
		
		$str="select et.dateEtat, t.noOf_equipement_of, e.idEssai
		from essai e, etatessai et, tester t
		where et.idEssai_essai=e.idEssai
		and et.idEtat_etat=25
		and e.idService_service=:idService
		and t.idEssai_essai=e.idEssai
		$condDate
		order by et.dateEtat";
		
	}else{
		
		$str="select et.dateEtat, t.noOf_equipement_of, e.idEssai
		from essai e, etatessai et, tester t
		where et.idEssai_essai=e.idEssai
		and et.idEtat_etat=25
		and e.fifo=$fifo
		and e.idService_service=:idService
		and t.idEssai_essai=e.idEssai
		$condDate
		order by et.dateEtat";
		
	}

	$res=$dbh->prepare($str);
	$res->execute($condVar);
	
	$numSemPrec=0;
	$idEssaiPrec=0;
	$tabSem=array();
	$tabNbTest=array();
	$tabNbOF=array();
	
	$tabSem[0]=	mb_convert_encoding("Week", 'UTF-16LE', 'UTF-8');
	$tabNbTest[0]=mb_convert_encoding("Nb test réalisé", 'UTF-16LE', 'UTF-8');
	$tabNbOF[0]=mb_convert_encoding("Nb d'OF testés", 'UTF-16LE', 'UTF-8');
	
	$nbSem=1; //on commence a 1 à cause des titres
	while($lg=$res->fetch(PDO::FETCH_OBJ))
	{	
		$numSemAct=date("yW",(strtotime($lg->dateEtat)));
		$idEssaiAct=$lg->idEssai;
		if($numSemAct!=$numSemPrec)
		{
			$tabSem[$nbSem]=$numSemAct;
			$tabNbTest[$nbSem]=1;
			$tabNbTest[$nbSem]=1;
			$tabNbOF[$nbSem]=1;
			$numSemPrec=$numSemAct;
			$idEssaiPrec=$idEssaiAct;
			$nbSem++;
		}
		else
		{
			if($idEssaiPrec!=$idEssaiAct)
			{
				$tabNbTest[$nbSem-1]+=1;
				$idEssaiPrec=$idEssaiAct;
			}
			$tabNbOF[$nbSem-1]+=1;
		}
		
	}
	
	//tableau plus simple, on utilise pas la fonction export CSV

	header("Content-Type: application/force-download;charset=UTF-16LE");
	header('Content-Transfer-Encoding: binary'); 
	header("Content-disposition: attachment; filename=export.csv");
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");

	$fp = fopen('php://output', 'w+');// on crée un fichier dans le output du navigateur (sera automatiquement proposé au téléchargement et ne sera pas enregistrer sur le serveur
	
	fputcsv($fp, $tabSem,";");
	fputcsv($fp, $tabNbTest,";");
	fputcsv($fp, $tabNbOF,";");
	
	fclose($fp);//fermeture du fichier
	
	
}
else // acces interdit
	echo "<script>alert(\"Accès non autorisé.\");document.location.href=\"../index.php\";</script>";		

