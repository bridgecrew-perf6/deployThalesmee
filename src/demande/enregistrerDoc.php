<?php 

function clean_file_name($file)
{
	// nettoyage du nom de fichier
	$file= preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($file, ENT_QUOTES, 'UTF-8'));
	$file = str_replace(' ', '_',$file);
	return $file;
}


//cette page enregistre un document
//le retour de cette page commence par un 1 si erreur, par un 0 sinon, permet de detecter les erreurs et de renvoyer des données si il n'y en a pas (l'id du nouveau doc)
if(isset($_POST["tpdoc"]))
{
	if(isset($_POST["ref"]) && isset($_POST["issue"]) &&  isset($_POST["rev"]) && isset($_POST["plateforme"])) //on verifie la reception des parametres
	{
		require('../conf/connexion_param.php'); //connexion a la bdd
		require('../conf/enregistrerDoc_param.php');
		$chemin=give_me_link_DocClient();// Repertoire ou sera stocke le document
		
		$tpdoc=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["tpdoc"])));
		$ref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["ref"])));
		$issue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issue"])));
		$rev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["rev"])));
		$plateforme=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["plateforme"])));
		
		if($tpdoc!= "" && $ref!="" && $issue && $plateforme) //on teste si les champs obligatoires ne sont pas vide
		{
			// Testons si le fichier a bien ete envoye et s'il n'y a pas d'erreur
			if (isset($_FILES['monfichier']) AND $_FILES['monfichier']['error'] == 0)
			{
				// Testons si le fichier n'est pas trop gros
				if ($_FILES['monfichier']['size'] <= 8000000)
				{
					// Testons si l'extension est autorisee
					$infosfichier = pathinfo($_FILES['monfichier']['name']);
					$extension_upload = strtolower($infosfichier['extension']);
					$extensions_autorisees = array('pdf', 'doc', 'docx');
					if (in_array($extension_upload, $extensions_autorisees))
					{
						// on enregistre les info dans la base
						$name=$_FILES['monfichier']['name'];
						$name=clean_file_name($name); //on netoie le nom du fichier (pas d'espace ou d'accents)
						$tp=$_POST["tpdoc"];
						$ref=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["ref"])));
						$issue=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["issue"])));
						$rev=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["rev"])));
						$plateforme=strtoupper(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["plateforme"])));
						
						require('../conf/connexion_param.php'); 	//Connexion bdd
						
						//on verifie que le document n est pas deja dans la base
						$str="select idSpec from SPEC_CLIENT where reference='$ref' and issue='$issue' and rev='$rev';";
						$req=mysqli_query($bdd,$str);
						if(!$req)
							echo "1Erreur de verification du document";
						else
						{
							if(mysqli_num_rows($req)!=0){
								//le document est deja dans la base
								echo "1Enregistrement annulé. Ce document est déjà dans la base.";
							}else{
								//on ajoute l'id du doc a son nom pour éviter les risques d'écrasement des fichiers si ils ont le meme nom
								$str="select max(idSpec) as id from SPEC_CLIENT;";
								$req=mysqli_query($bdd,$str);
								$id=1+mysqli_fetch_object($req)->id;
								//le document n est pas dans la base, on l y enregistre
								// On peut valider le fichier et le stocker definitivement
								if(!move_uploaded_file($_FILES['monfichier']['tmp_name'], $chemin.$id.$name)) //si echec
									echo "1Erreur d'enregistrement du fichier sur le serveur";
								else
								{			
									//insertion dans la base
									$str="insert into SPEC_CLIENT values ($id,'$name','$ref','$issue','$rev','$plateforme',$tp);";
									$req=mysqli_query($bdd,$str);
			
									if(!$req)
										echo "1Erreur d'ajout du doc dans la base de données";
									else //tout c'est bien passé
										echo "0".$id;
									
								}
							}
						}
					}else
						echo "1Erreur : le document à télécharger doit être un .pdf , .doc ou .docx .";
				}else
					echo "1Erreur : la taille du document à télécharger doit être < 8M.";
			}else
				echo "1Sélectionnez le fichier à envoyer";
		}
		else
			echo "1Veuillez remplir les champs réference, issue et plateforme";	
	}	
	else
		echo "1Erreur de reception des parametres";
}
else
	echo "1Veuillez choisir un type de document";
?>