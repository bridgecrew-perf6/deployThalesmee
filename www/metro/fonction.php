<?php
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

function uploadPhoto($numInstru,$fichier)
{ 
	$chemin="C:\\Serveur\\metro\\photoInstrument\\";
	$fait = true;
	// Testons si le fichier a bien ete envoye et s'il n'y a pas d'erreur
	if (isset($fichier) AND $fichier['error'] == 0)
	{
		// Testons si le fichier n'est pas trop gros
		if ($fichier['size'] <= 8000000)
		{
			// Testons si l'extension est autorisee
			$infosfichier = pathinfo($fichier['name']);
			$extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('jpg');
			if (in_array($extension_upload, $extensions_autorisees))
			{	
				$name=$numInstru;	
				$name = str_replace("/", "", $name);				
				if(!move_uploaded_file($fichier['tmp_name'], $chemin.$name.".jpg")){ //si echec
					echo "Erreur d'enregistrement du fichier sur le serveur";
					return false;
				}else return true;
			}else
				echo "Erreur : le fichier à télécharger doit être un .jpg";
				return false;
		}else
			echo "Erreur : la taille du fichier à télécharger doit être < 8M.";
			return false;
	}
	
}

function uploadFicheTech($numInstru,$fichier)
{ 
	$chemin="C:\\Serveur\\www\\metro\\ficheTech\\";
	// Testons si le fichier a bien ete envoye et s'il n'y a pas d'erreur
	if (isset($fichier) AND $fichier['error'] == 0)
	{
		// Testons si le fichier n'est pas trop gros
		if ($fichier['size'] <= 8000000)
		{
			// Testons si l'extension est autorisee
			$infosfichier = pathinfo($fichier['name']);
			$extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('pdf');
			if (in_array($extension_upload, $extensions_autorisees))
			{
				$name=$numInstru;
				$name = str_replace("/", "", $name);
				if(!move_uploaded_file($fichier['tmp_name'], $chemin.$name.".pdf")) //si echec
					echo "Erreur d'enregistrement du fichier sur le serveur";
			
			}else
				echo "Erreur : le fichier à télécharger doit être un .pdf";
		}else
			echo "Erreur : la taille du fichier à télécharger doit être < 8M.";
	}
}

function uploadInstruCertif($numInstru,$fichier,$bdd)
{ 
	$chemin="C:\\Serveur\\www\\metro\\certificats\\";
	// Testons si le fichier a bien ete envoye et s'il n'y a pas d'erreur
	if (isset($fichier) AND $fichier['error'] == 0)
	{
		// Testons si le fichier n'est pas trop gros
		if ($fichier['size'] <= 8000000)
		{
			// Testons si l'extension est autorisee
			$infosfichier = pathinfo($fichier['name']);
			$extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('pdf');
			$nameClean=mysqli_real_escape_string($bdd,$fichier['name']);
			if (in_array($extension_upload, $extensions_autorisees))
			{
				$str = "SELECT numInstru_INSTRUMENT, certificat FROM certificat_instrument WHERE numInstru_INSTRUMENT = '$numInstru'";
				$req = mysqli_query($bdd, $str);
				if (mysqli_num_rows($req) != 0)
				{

					$lg=mysqli_fetch_object($req);
					if($lg->certificat!= 'NULL')
					{
						$lien=$chemin.$lg->certificat;
						if (file_exists($lien)) {	
							@unlink($lien);
						}
					}

					$str="UPDATE certificat_instrument set certificat='".$nameClean."'  
					where numInstru_INSTRUMENT='$numInstru'";
					$req=mysqli_query($bdd, $str);

				}else
				{
					$str="INSERT INTO certificat_instrument VALUES ( '$numInstru','".$nameClean."')";
					$req=mysqli_query($bdd, $str);
				}

				
				// on enregistre les info dans la base						
				if(!move_uploaded_file($fichier['tmp_name'], $chemin.$fichier['name'])) //si echec
					echo "Erreur d'enregistrement du fichier sur le serveur";

			}else
				echo "Erreur : le fichier à télécharger doit être un .pdf";
		}else
			echo "Erreur : la taille du fichier à télécharger doit être < 8M.";
	}
}

function uploadCertif($numInstru,$fichier,$bdd)
{ 
	$chemin="C:\\Serveur\\www\\metro\\certificats\\";
	// Testons si le fichier a bien ete envoye et s'il n'y a pas d'erreur
	if (isset($fichier) AND $fichier['error'] == 0)
	{
		// Testons si le fichier n'est pas trop gros
		if ($fichier['size'] <= 8000000)
		{
			// Testons si l'extension est autorisee
			$infosfichier = pathinfo($fichier['name']);
			$extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('pdf');
			$nameClean=mysqli_real_escape_string($bdd,$fichier['name']);
			if (in_array($extension_upload, $extensions_autorisees))
			{
				//suppression de l'ancien certificat
				$str="SELECT certificat from instrument_vib_capteur
				where numInstru_instrument='$numInstru'";
				$req=mysqli_query($bdd, $str);
				$lg=mysqli_fetch_object($req);
				if($lg->certificat!= 'NULL')
				{
					$lien=$chemin.$lg->certificat;
					if (file_exists($lien)) {	
						@unlink($lien);
					}
				}
				// on enregistre les info dans la base						
				if(!move_uploaded_file($fichier['tmp_name'], $chemin.$fichier['name'])) //si echec
					echo "Erreur d'enregistrement du fichier sur le serveur";
				else
				{
					$str="UPDATE instrument_vib_capteur set certificat='".$nameClean."'  
					where numInstru_instrument='$numInstru'";
					$req=mysqli_query($bdd, $str);
				
				}
			}else
				echo "Erreur : le fichier à télécharger doit être un .pdf";
		}else
			echo "Erreur : la taille du fichier à télécharger doit être < 8M.";
	}
}
function repertoirePhoto()
{
	return "../photoInstrument/";
}

function repertoireCertificat()
{
	return "../certificats/";
}

function repertoireficheTech()
{
	return "../ficheTech/";
}