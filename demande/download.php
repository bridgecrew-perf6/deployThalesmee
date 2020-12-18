<?php
if (isset($_GET['link']) && isset($_GET["nomOr"])){
	// on a le fichier a telecharger
	
	$Fichier_a_telecharger=$_GET['link'];
	$nomOriginal=$_GET['nomOr'];
	require('../conf/enregistrerDoc_param.php');
	
	$chemin=give_me_link_DocClient();

	switch(strrchr(basename($Fichier_a_telecharger), ".")) {
		// on essaie de reconnaitre l'extension pour que le téléchargement 
		//corresponde au type de fichier afin d'éviter les erreurs de corruptions
		
		case ".gz": $type = "application/x-gzip"; break;
		case ".tgz": $type = "application/x-gzip"; break;
		case ".zip": $type = "application/zip"; break;
		case ".pdf": $type = "application/pdf"; break;
		case ".xls": $type = "application/xls"; break;
		case ".xlsx": $type = "application/xlsx"; break;
		case ".png": $type = "image/png"; break;
		case ".gif": $type = "image/gif"; break;
		case ".jpg": $type = "image/jpeg"; break;
		case ".txt": $type = "text/plain"; break;
		case ".htm": $type = "text/html"; break;
		case ".html": $type = "text/html"; break;
		case ".doc": $type = "application/doc"; break;
		case ".docx": $type = "application/docx"; break;
		case ".odt": $type = "application/odt"; break;
		case ".ppt": $type = "application/ppt"; break;
		case ".pptx": $type = "application/pptx"; break;
		case ".odp": $type = "application/odp"; break;
		case ".xlt": $type = "application/xlt"; break;
		case ".csv": $type = "application/csv"; break;
		default: $type = "application/octet-stream"; break;
	}

	//Telechargement
	header("Content-disposition: attachment; filename=$nomOriginal");
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: $type\n"); // Surtout ne pas enlever le \n
	header("Content-Length: ".filesize($chemin . $Fichier_a_telecharger));
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
	readfile($chemin.$Fichier_a_telecharger);

}else{
	//erreur
	echo "<script>alert(\"Erreur recuperation nom du fichier.\");document.location.href=\"index.php\";</script>";
}
?>