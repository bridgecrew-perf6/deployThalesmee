<?php
require('top.php');

if(!isset($_GET["idEssai"]))
		echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	require('../conf/connexion_param.php'); //connexion a la bdd
	require('../fonction.php');
	$idEssai=$_GET["idEssai"];
	
	$str="SELECT et.idEtat_ETAT, e.date_debut FROM essai e, etatEssai et
	where idEssai =$idEssai
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";
	$req=mysqli_query($bdd, $str);
	echo mysqli_error($bdd);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération de l'état</strong></div>";
	else{
		$lg=mysqli_fetch_object($req);
		$idEtat=1+$lg->idEtat_ETAT;
		$ok=true;
		//par defaut redirection ver index.php
		$lien="index.php";
		if($idEtat==22)
		{
			if($_GET["fifo"]==1){
				
				$str="select date_debut ,date_fin from essai where idEssai=$idEssai;";
				$req=mysqli_query($bdd, $str);
				$lg=mysqli_fetch_object($req);
				$date_debut=$lg->date_debut;
				$date_fin=$lg->date_fin;
				$diff=strtotime ($date_fin)-strtotime($date_debut);
				$nvDateDebut= date('Y-m-d H:i');
				$nvDateFin=date('Y-m-d H:i',strtotime($nvDateDebut)+$diff);
				$duree = dureePrimavera($nvDateDebut, $nvDateFin);
			
				
				//Update date_debut avec valeur reel + decalage de la date de fin
				$str="UPDATE essai SET fifo=1, date_debut ='$nvDateDebut', date_fin='$nvDateFin', duree_actuelle = $duree WHERE idEssai =$idEssai;";
				$req=mysqli_query($bdd, $str);
				
			}
			else
			{
				$str="UPDATE essai SET fifo='0' WHERE idEssai =$idEssai;";
				$req=mysqli_query($bdd, $str);
			}
			
			//si l'essai à x jours de retard, on le traite comme un essai non plannifié
			$date = new DateTime($lg->date_debut);
			$date->add(new DateInterval('P3D'));
			
			if($date<new DateTime())
			{
				//$str="update essai set planifie='0' where idEssai='$idEssai'";
				//$req=mysqli_query($bdd, $str);
				/*
				$str="delete from etatEssai where idEssai_ESSAI='$idEssai' and idEtat_ETAT <=21";
				$req=mysqli_query($bdd, $str);
				*/
			}
			$lien="renseignerEssai.php?idEssai=".$idEssai;
			
		}
		elseif($idEtat==23)
		{
			if(!isset($_GET["idMoyen"]))
			{
				echo "<div class='alert alert-danger'><strong>Erreur de récupération du moyen</strong></div>";
				$ok=false;
			}
			elseif($_GET["idMoyen"]!=0) //si moyen ==0 on a deja attibué le moyen
			{
				$idMoyen=$_GET["idMoyen"];
								
				//on update le moyen pour l'essai
				$str="UPDATE essai set idMoyen_moyen=$idMoyen where idEssai=$idEssai;";
				$req=mysqli_query($bdd,$str);
				if(!$req){
					echo '<div class="alert alert-danger"><strong>Erreur de l\'ajout du test entre l\'of crée et l\'essai</strong></div>';
				}	
			}
			
			if (isset ($_GET["nom"])){
				
				$nom=$_GET["nom"];
				$str="insert into vibtesterpar values('$idEssai','$nom')";
				$req=@mysqli_query($bdd, $str);

			}
			
			$str="select date_debut ,date_fin from essai where idEssai=$idEssai;";
			$req=mysqli_query($bdd, $str);
			$lg=mysqli_fetch_object($req);
			$date_debut=$lg->date_debut;
			$date_fin=$lg->date_fin;
			//$diff=strtotime ($date_fin)-strtotime($date_debut);
			$nvDateDebut= date('Y-m-d H:i');
			//$nvDateFin=date('Y-m-d H:i',strtotime($nvDateDebut)+$diff);
			
			$diff = duree(date('d/m/Y H:i',strtotime($date_debut)), date('d/m/Y H:i',strtotime($date_fin)));
			$nbHeure = intval($diff["heure"]);
			$date_fin = $nvDateDebut;
			
			while ($nbHeure != 0){
				
				$heure= explode (" ", $date_fin);
				$date_fin = explode("-", $heure[0]);
				$heure_fin = explode (":", $heure[1])[0];
				
				if (intval($heure_fin) >= 8 && intval($heure_fin) < 17){
						
					$date_fin = date("Y-m-d H:i", strtotime($date_fin[0]."-".$date_fin[1]."-".$date_fin[2]." ".$heure[1]." +1 hours"));
					$nbHeure -= 1;
					
				}else {
					
					$date_fin = date("Y-m-d H:i", strtotime($date_fin[0]."-".$date_fin[1]."-".$date_fin[2]." ".$heure[1]." +1 hours"));
				}
				
				
			}

			$duree = dureePrimavera($nvDateDebut, $date_fin);

			//Update date_debut avec valeur reel + decalage de la date de fin
			$str="UPDATE essai SET date_debut ='$nvDateDebut', date_fin='$date_fin', duree_actuelle = $duree WHERE idEssai =$idEssai;";
			$req=mysqli_query($bdd, $str);
			
		}
		elseif($idEtat==24) //si idEtat==24 (Essai terminé) maj date fin
		{
			$str="SELECT date_debut, date_fin from essai where idEssai=$idEssai;";
			$req=mysqli_query($bdd, $str);
			$lg=mysqli_fetch_object($req);
			$date_fin=new DateTime($lg->date_fin);
			if($date_fin<new DateTime())
			{
				$str="UPDATE essai SET retard_interne='1' WHERE idEssai =$idEssai;";
				$req=mysqli_query($bdd, $str);
			}

			$str="UPDATE essai set date_fin=now() where idEssai=$idEssai;";
			$req=mysqli_query($bdd, $str);

			$str="SELECT date_debut, date_fin from essai where idEssai=$idEssai;";
			$req=mysqli_query($bdd, $str);
			$lg=mysqli_fetch_object($req);
			//Calcul de la durée de l'essai selon la référence de Primavera
			$duree = dureePrimavera(date('Y-m-d H:i',strtotime($lg->date_debut)), date('Y-m-d H:i',strtotime($lg->date_fin)));

			//Changement de la durée actuelle de l'essai
			$str="UPDATE essai set duree_actuelle=$duree where idEssai=$idEssai;";
			$req=mysqli_query($bdd, $str);
		}
		if($ok)
		{
			$str="INSERT into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,$idEtat);";
			$req=mysqli_query($bdd, $str);
			if(!$req)
				echo "<div class='alert alert-danger'><strong>Erreur de changement d'état</strong></div>";
			else{	
				echo "<script>document.location.href='".$lien."'</script>";

			}
		}
	}	
}
require('bottom.php');
