<?php //fichier regroupant quelques fonctions en php utilisées par plusieurs pages differentes
function envoi_mail($obj, $dest, $emet, $cc, $corps,$codeService)
{
	//pour l'envoi du mail on utilise un logi dev par dsi pour l'envoi via ftp
	
	//créer la date d'emission (+code unique du fichier)
	$code=time();
	$dateEnv=date("d/m/Y H:i",$code);
	
	//on ajoute le code du service au code unique, car les trois mails pour les services peuvent etre généré tous en moins d'1s et auront donc le meme code
	$code.=$codeService;
	
	//encodage en Ansi, car outloock ne prend pas l'utf8 par défaut
	$corps= utf8_decode($corps);
	$ligneEmet=utf8_decode("Emetteur: $emet\r\n");
	$dateEnv=utf8_decode("Date d'émission: $dateEnv");
	$obj=utf8_decode($obj);
	
	$fp = fopen("../mail/corps.txt","w");
	fputs($fp, ".OB $obj\r\n.AD $dest\r\n"); //ecrit l'objet et le destinataire
	if($cc!="")
		fputs($fp, ".CC $cc\r\n"); // ecrit les cc
	fputs($fp, "$corps\r\n\r\n"); // ecrit le corps
	fputs($fp, $ligneEmet); // ecrit l'émetteur
	fputs($fp, $dateEnv); // ecrit la date d'envoi
	fclose($fp);	
	
	
	$fp = fopen("../mail/ordre.ftp","w");
	fputs($fp, "open tlfwrdp01\r\n");
	fputs($fp, "ftpnotes\r\n");
	fputs($fp, "profnote\r\n");
	fputs($fp, "put ./corps.txt ppbh.$emet.$code\r\n");
	fputs($fp, "quit");
	
	$path= realpath ('../mail'); //on recupere le chemin absolue du dossier mail (pas besoin de modifier le code si o, bouge l'appli)
	exec("cd $path && Execute.bat > NUL",$u,$res); //on execute un cd puis le fichier bat (le cd sert pour rechercher les autres fichier dans le dossier actuel mail)
	//echo shell_exec("cd $path && Execute.bat 2>&1 1> ../mail/log.txt");
	return $res;
	//return 0;
}

function samedi ($dateDeb, $dateFin, $duree)
{
	$timeDeb = explode (" ", $dateDeb);
	$date_debut = explode("-", $timeDeb[0]);
	$heure_debut = explode (":", $timeDeb[1])[0];
	$minutes_debut = explode (":", $timeDeb[1])[1];
	$samedi = 0;
	while (true)
	{
		if(strtotime($dateDeb) >= strtotime($dateFin)) return round($samedi/9,1);

		if ($heure_debut >= 17) $dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
		else {

			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." ".$heure_debut.":".$minutes_debut." +1 hours"));
			if (getdate(strtotime($dateDeb))["wday"] == 6) $samedi += 1;
		}

		$timeDeb = explode (" ", $dateDeb);
		$date_debut = explode("-", $timeDeb[0]);
		$heure_debut = explode (":", $timeDeb[1])[0];
		$minutes_debut = explode (":", $timeDeb[1])[1];
	}
}

function dateFin ($duree, $dateDeb, $samedi)
{
	$timeDeb = explode (" ", $dateDeb);
	$date_debut = explode("-", $timeDeb[0]);
	$horaire = explode (":", $timeDeb[1]);
	if (isset($horaire[1]))
	{
		$heure_debut = $horaire[0];
		$minutes_debut = $horaire[1];
	}else
	{
		$heure_debut = $horaire;
		$minutes_debut = "00";
	}
	
	for ($i = 0; $i < $duree; $i++)
	{
		if ($heure_debut >= 17) {

			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		//Samedi
		if (getdate(strtotime($dateDeb))["wday"] == 6 && $samedi ==0)
		{
			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		//Dimanche
		if (getdate(strtotime($dateDeb))["wday"] == 0)
		{
			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		
		$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." ".$heure_debut.":".$minutes_debut." +1 hours"));

		$timeDeb = explode (" ", $dateDeb);
		$date_debut = explode("-", $timeDeb[0]);
		$heure_debut = explode (":", $timeDeb[1])[0];
		$minutes_debut = explode (":", $timeDeb[1])[1];
		
	}
	return $dateDeb;
}

function dFin ($duree, $dateDeb, $samedi)
{
	$timeDeb = explode (" ", $dateDeb);
	$date_debut = explode("-", $timeDeb[0]);
	$horaire = explode (":", $timeDeb[1]);
	if (isset($horaire[1]))
	{
		$heure_debut = $horaire[0];
		$minutes_debut = $horaire[1];
	}else
	{
		$heure_debut = $horaire;
		$minutes_debut = "00";
	}
	
	for ($i = 0; $i < $duree; $i++)
	{
		if ($heure_debut > 17) {

			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		//Samedi
		if (getdate(strtotime($dateDeb))["wday"] == 6 && $samedi ==0)
		{
			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		//Dimanche
		if (getdate(strtotime($dateDeb))["wday"] == 0)
		{
			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
			$timeDeb = explode (" ", $dateDeb);
			$date_debut = explode("-", $timeDeb[0]);
			$heure_debut = explode (":", $timeDeb[1])[0];
			$minutes_debut = explode (":", $timeDeb[1])[1];
		}

		
		$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." ".$heure_debut.":".$minutes_debut." +1 hours"));

		$timeDeb = explode (" ", $dateDeb);
		$date_debut = explode("-", $timeDeb[0]);
		$heure_debut = explode (":", $timeDeb[1])[0];
		$minutes_debut = explode (":", $timeDeb[1])[1];
		
	}
	$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." ".$heure_debut.":".$minutes_debut." -1 day"));
	return $dateDeb;
}

function dureePrimavera ($dateDeb, $dateFin){

	$timeDeb = explode (" ", $dateDeb);
	$date_debut = explode("-", $timeDeb[0]);
	$heure_debut = explode (":", $timeDeb[1])[0];
	$minutes_debut = explode (":", $timeDeb[1])[1];
	$duree = 0;
	while (true)
	{
		if(strtotime($dateDeb) >= strtotime($dateFin)) return round($duree/9,1);

		if ($heure_debut >= 17) $dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." 08:00 +1 day"));
		else {

			$dateDeb = date("Y-m-d H:i", strtotime($date_debut[0]."-".$date_debut[1]."-".$date_debut[2]." ".$heure_debut.":".$minutes_debut." +1 hours"));

			if (getdate(strtotime($dateDeb))["wday"] != 0 && getdate(strtotime($dateDeb))["wday"] != 6) $duree += 1;
		}

		$timeDeb = explode (" ", $dateDeb);
		$date_debut = explode("-", $timeDeb[0]);
		$heure_debut = explode (":", $timeDeb[1])[0];
		$minutes_debut = explode (":", $timeDeb[1])[1];
	}
}

//Durée entre deux dates
function duree ($dateDeb, $dateFin){
	
	$diff = array();  		// Initialisation du retour
	$res = 0;
	
	while (true){
		
		//echo $dateDeb."<br/>";
		//echo $dateFin."<br/>";
		$dateDeb = str_replace('/','-',$dateDeb);
		$dateFin = str_replace('/','-',$dateFin);
		$heure = explode (" ", $dateDeb);
		$date_debut = explode("-", $heure[0]);
		$heure_debut = explode (":", $heure[1])[0];

		
		if (strtotime(str_replace('/','-',$dateDeb)) == strtotime(str_replace('/','-',$dateFin)))
		{
			$diff["heure"] = $res;
			$diff["minutes"] = 0;
			return $diff;
		
		}else if (strtotime(str_replace('/','-',$dateDeb)) > strtotime(str_replace('/','-',$dateFin))){
					
			$res -= 1;
			$minutes = 0;
			$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." -1 hours"));
			
			while (strtotime(str_replace('/','-',$dateDeb)) != strtotime(str_replace('/','-',$dateFin))){
				
				$heure = explode (" ", $dateDeb);
				$date_debut = explode("/", $heure[0]);
				
				$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 minutes"));
				$minutes += 1;
				
			}
			
			$diff["heure"] = $res;
			$diff["minutes"] = $minutes;
			return $diff;
			
		}else{
			
			//echo $heure_debut."</br>";
			//echo $heure_debut[1]."</br>";		
			if (intval($heure_debut) >= 8 && intval($heure_debut) < 17){
				
				$res += 1;
				//echo $res."<br/>";
				$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 hours"));
				
			}else {
				
				$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 hours"));
			}

		}
	
	}
}

//Durée entre deux dates sans we
function duree_we ($dateDeb, $dateFin){
	
	$diff = array();  		// Initialisation du retour
	$res = 0;
	
	while (true){
		
		//echo $dateDeb."<br/>";
		//echo $dateFin."<br/>";
		$dateDeb = str_replace('/','-',$dateDeb);
		$dateFin = str_replace('/','-',$dateFin);
		$heure = explode (" ", $dateDeb);
		$date_debut = explode("-", $heure[0]);
		$heure_debut = explode (":", $heure[1])[0];

		if (getdate(strtotime($dateDeb))["wday"] != 0 && getdate(strtotime($dateDeb))["wday"] != 6)
		{
			if (strtotime(str_replace('/','-',$dateDeb)) == strtotime(str_replace('/','-',$dateFin)))
			{
				$diff["heure"] = $res;
				$diff["minutes"] = 0;
				return $diff;
			
			}else if (strtotime(str_replace('/','-',$dateDeb)) > strtotime(str_replace('/','-',$dateFin))){
						
				$res -= 1;
				$minutes = 0;
				$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." -1 hours"));
				
				while (strtotime(str_replace('/','-',$dateDeb)) != strtotime(str_replace('/','-',$dateFin))){
					
					$heure = explode (" ", $dateDeb);
					$date_debut = explode("/", $heure[0]);
					
					$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 minutes"));
					$minutes += 1;
					
				}
				
				$diff["heure"] = $res;
				$diff["minutes"] = $minutes;
				return $diff;
				
			}else{
				
				//echo $heure_debut."</br>";
				//echo $heure_debut[1]."</br>";		
				if (intval($heure_debut) >= 8 && intval($heure_debut) < 17){
					
					$res += 1;
					//echo $res."<br/>";
					$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 hours"));
					
				}else {
					
					$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 hours"));
				}

			}
		}else
		{
			$dateDeb = date("d/m/Y H:i", strtotime($date_debut[2]."-".$date_debut[1]."-".$date_debut[0]." ".$heure[1]." +1 hours"));
		}
		
	
	}
}

function insertDOC_3IT($ref,$type,$issue,$rev,$tpdoc,$idDP,$bdd){

	$str="select idDoc from DOCUMENT_3IT where reference='$ref' and type_='$type' and issue='$issue' and rev='$rev' and idTypeDoc_TYPE_DOC='$tpdoc';";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		return '<div class="alert alert-danger"><strong>Erreur de récupération du doc 3it</strong></div>';
	if (mysqli_num_rows($req)==0){
		//le document n'est pas dans la base
		//on l y insere	
		
		$str="insert into DOCUMENT_3IT values (null,'$ref','$type','$issue','$rev','$tpdoc');";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			return '<div class="alert alert-danger"><strong>Erreur d\'insertion du doc 3it</strong></div>';
		else
			$idDoc=mysqli_insert_id($bdd);
	}else{
		//on recupere l identifiant du document
		$idDoc=(mysqli_fetch_object($req)->idDoc);
	}
	//le document est desormais dans la base
	
	//on insere le match dp/doc
	//l'update si key duplicate ne sert a rien mais standart sql -> "ignore" empeche de soulever une erreur
	$str="insert into referencerDoc values ($idDP,$idDoc) ON DUPLICATE KEY UPDATE idDP_DEMANDE_PROCEDURE=$idDP, idDoc_DOCUMENT_3IT=$idDoc;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		return '<div class="alert alert-danger"><strong>Erreur de d\'ajout du match dp/doc</strong></div>';
	return "";
}

function insertDOC_3IT_Art($ref,$type,$issue,$rev,$tpdoc,$iddp,$noArt,$com,$bdd){

	$str="select idDoc from DOCUMENT_3IT where reference='$ref' and type_='$type' and issue='$issue' and rev='$rev' and idTypeDoc_TYPE_DOC='$tpdoc';";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		return '<div class="alert alert-danger"><strong>Erreur de récupération du doc 3it</strong></div>';
	if (mysqli_num_rows($req)==0){
		//le document n'est pas dans la base
		//on l y insere	
		
		$str="insert into DOCUMENT_3IT values (null,'$ref','$type','$issue','$rev','$tpdoc');";
		$req=mysqli_query($bdd,$str);
		if(!$req)
			return '<div class="alert alert-danger"><strong>Erreur d\'insertion du doc 3it</strong></div>';
		else
			$idDoc=mysqli_insert_id($bdd);
	}else{		
			$idDoc=(mysqli_fetch_object($req)->idDoc);
	}
	
	
	//le document est desormais dans la base
	
	//on insere le match dp/doc/art
	//l'update si key duplicate ne sert a rien mais standart sql -> "ignore" empeche de soulever une erreur
	$str="insert into referencerDocArt values ($iddp,$idDoc,$noArt,'$com') ON DUPLICATE KEY UPDATE idDP_DEMANDE_PROCEDURE=$iddp, idDoc_DOCUMENT_3IT=$idDoc, noArticle_EQUIPEMENT_ART=$noArt, commentaire='$com';";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		return '<div class="alert alert-danger"><strong>Erreur de d\'ajout du match dp/doc/art</strong></div>';
	return "";
}

//Fonction qui renvoi qui renvoi true si Dp rapide, false sinon
function verifDPRapide($idDP,$bdd)
{
	// Ici pour chaque service on verifie que la procedure qui lui ai assigné est lié à un doc 3it 

	// pour cela on verifie que pour la demande de procedure, que chaque procedure coché ai bien un doc 3it fourni avec 
	$str="select noArticle_equipement_art from concernerart where iddp_demande_procedure=$idDP";
	$req=mysqli_query($bdd,$str);
	$rapide=true; //booleen de test et optimise la boucle -> dés que pour un article faux on arrete de chercher
	
	//on recupere les numéros des services a qui on a assigné une procédure pour cette demande
	$str="select distinct idservice_service from procedures where iddp_demande_procedure=$idDP";
	$reqLesproc=mysqli_query($bdd,$str);
	while($rapide && $resProc=mysqli_fetch_object($reqLesproc))
	{
		$numServ=$resProc->idservice_service;
		while($rapide && $lg=mysqli_fetch_object($req))
		{ 	
			if($numServ==1)//EMC
			{
				$no_Article=$lg->noArticle_equipement_art;
				//on recupere le nombre de procedure renséigner avec un doc art de type 31 EMC
				$str="select count(noArticle_equipement_art) as nb from referencerdocart
				where iddp_demande_procedure=$idDP
				and noArticle_equipement_art=$no_Article
				and iddoc_document_3it in
				(select iddoc from document_3it where idtypedoc_type_doc=31)";
				$req2=mysqli_query($bdd,$str);
				$lg2=mysqli_fetch_object($req2);
				if($lg2->nb==0) //si aucun doc lié
					$rapide=false;
			}
			elseif($numServ==2) //VIB
			{
				$no_Article=$lg->noArticle_equipement_art;
				//on recupere le nombre de procedure renséigner avec un doc art de type 33 VIB
				$str="select count(noArticle_equipement_art) as nb from referencerdocart
				where iddp_demande_procedure=$idDP
				and noArticle_equipement_art=$no_Article
				and iddoc_document_3it in
				(select iddoc from document_3it where idtypedoc_type_doc=32)";
				$req2=mysqli_query($bdd,$str);
				$lg2=mysqli_fetch_object($req2);
				if($lg2->nb==0)
					$rapide=false;
			
			}
			elseif($numServ==3) //VTH
			{
				$no_Article=$lg->noArticle_equipement_art;
				//on recupere le nombre de procedure renséigner avec un doc art de type 32 VTH
				$str="select count(noArticle_equipement_art) as nb from referencerdocart
				where iddp_demande_procedure=$idDP
				and noArticle_equipement_art=$no_Article
				and iddoc_document_3it in
				(select iddoc from document_3it where idtypedoc_type_doc=33)";
				$req2=mysqli_query($bdd,$str);
				$lg2=mysqli_fetch_object($req2);
				if($lg2->nb==0)
					$rapide=false;
			}
		}
	}
	return $rapide;
}

// cette fonction permet de creer et de telecharger un fichier .csv contenant les resultats d'une requete
// les resultats de la requete sont contenus dans le tableau $list passe en parametre
function exportCSV($list){
	
	//$list contient un tableau de donnees a afficher en csv
	header("Content-Type: application/force-download;charset=UTF-16LE");
	header('Content-Transfer-Encoding: binary'); 
	header("Content-disposition: attachment; filename=export.csv");
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");

	$fp = fopen('php://output', 'w+');// on crée un fichier dans le output du navigateur (sera automatiquement proposé au téléchargement et ne sera pas enregistrer sur le serveur
	$tot=count($list);
	for($i=0;$i<$tot;$i++) {
		$sous_tab=$list[$i];
		$nb=count($sous_tab);
		for($j=0;$j<$nb;$j++) //on converti toutes les données en UTF-16LE (format par defaut sous excel), sinon probleme d'affichage avec les accents
		{	
			if(isset($sous_tab[$j]))
				$sous_tab[$j]= mb_convert_encoding($sous_tab[$j], 'UTF-16LE', 'UTF-8');
		}	
		// on copie chaque ligne dans le fichier .csv
		fputcsv($fp, $sous_tab,";");
	}
	fclose($fp);//fermeture du fichier
}
//tronquer un texte si > a n caractere
function tronquer($texte,$n)
{
    if (strlen($texte) > $n)
    {    
        $texte = substr($texte, 0, $n);    
        $texte .= '...';
    }
    return $texte;
}
function dateSQLToFr($date)
{
	if( preg_match('`(\d{4})-(\d{1,2})-(\d{1,2})`' , $date)){
		$date=DateTime::CreateFromFormat('Y-m-d', $date);
		$dateForm=$date->format('d/m/Y');
	}
	else
		$dateForm="";
	return $dateForm;
}

function dateFrToSQL($date)
{
	if( preg_match('`(\d{1,2})/(\d{1,2})/(\d{4})`' , $date)){
		$date=DateTime::CreateFromFormat('d/m/Y', $date);
		$dateForm=$date->format('Y-m-d');
	}
	else
		$dateForm="";
	return $dateForm;
}


function dateFrToSQLDefault($date)
{
	if( preg_match('`(\d{1,2})/(\d{1,2})/(\d{4})`' , $date)){
		$date=DateTime::CreateFromFormat('d/m/Y', $date);
		$dateForm=$date->format('Y-m-d');
		$dateForm = $dateForm." 08:00:00";
	}
	else
		$dateForm="";
	return $dateForm;
}

function dateSQLToFrWithHours($date)
{
	if( preg_match('`(\d{4})-(\d{1,2})-(\d{1,2})`' , $date)){
		$date=DateTime::CreateFromFormat('Y-m-d H:i:s', $date);
		$dateForm=$date->format('d/m/Y H:i:s');
	}
	else
		$dateForm="";
	return $dateForm;
}

function dateSQLToFrWithoutMinutes($date)
{
	if( preg_match('`(\d{4})-(\d{1,2})-(\d{1,2})`' , $date)){
		$date=DateTime::CreateFromFormat('Y-m-d H:i:s', $date);
		$dateForm=$date->format('d/m/Y H:i');
	}
	else
		$dateForm="";
	return $dateForm;
}

function dateFrToSQLWithHours($date)
{
	if( preg_match('`(\d{1,2})/(\d{1,2})/(\d{4})`' , $date)){
		$date=DateTime::CreateFromFormat('d/m/Y H:i:s', $date);
		$dateForm=$date->format('Y-m-d H:i:s');
	}
	else
		$dateForm="";
	return $dateForm;
}

function multiexplode ($del, $str){
	
	$ready = str_replace ($del, $del[0], $str);
	$launch = explode ($del[0], $ready);
	return $launch;
}
