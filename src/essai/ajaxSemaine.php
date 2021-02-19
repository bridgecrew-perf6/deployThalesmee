<?php

	/*Récupére le dernier numéro de semaine d'une année
	* @param
	* annee : l'annee 
	* @return
	* le numéro de semaine
	*/
	function getLastWeek ($annee)
	{
		//La dernière semaine correspond au numéro de semaine du 28/12 de l'année
		return date("W", mktime(0,0,0,12,28,$annee));
	}

	/*Retourne la date du dernier jour de la semaine
	* @param
	* annee : l'année
	* semaine : la semaine
	* @return
	* la date du dernier jour
	*/
	function getSunday ($annee, $semaine)
	{
		$dimanche = new DateTime();
		$dimanche->setISOdate($annee, $semaine);
		$dimanche->add(new DateInterval('P6D'));
		return $dimanche->format('Y-m-d');
	}

	/*Calcul la semaine suivante de la dernière semaine d'un année
	* @param
	* annee : l'année
	* @return
	* "semaine1-nouvelle_année"
	*/
	function nextWeekLast ($annee)
	{
		$annee += 1; 
		return "1-".$annee;
	}

	/*Calcul la semaine suivante de la semaine 52
	* @param
	* annee : l'année
	* suiv : la semaine
	* @return
	* newWeekLst(annee) || "semaine53-l'année"
	* Tout dépend de la dernière semaine de l'année
	*/
	function nextWeekMaybeLast($annee, $sem)
	{
		//Récupération de la dernière semaine de l'année
		$lastWeek = getLastWeek (intval($annee));
		//Si la semaine et égale à la dernière semaine de l'année
		if ($lastWeek == $sem) return nextWeekLast (intval($annee));
		else return "53-".$annee;
	}

	/*Calcul la semaine seuivant d'un semaine quelconque
	* @param
	* annee : l'année
	* suiv : la semaine
	* @return
	* "nouvelle_semaine-année"
	*/
	function nextWeek ($annee, $suiv)
	{
		$val = $suiv+1;
		return $val."-".$annee;
	}

	/*Calcul la semaine précédente
	* @param
	* prec : la semaine
	* @return
	* la nouvelle semaine
	*/
	function prevWeek ($prec)
	{
		return $prec-1;
	}

	/*Calcul la semaine en cours
	* @return
	* "semaine_en_cours-année_en_cours"
	*/
	function actWeek ()
	{
		return date("W")."-".date("Y");
	}

	//On souhaite le dernier jour de la semaine
	if (isset($_GET["semaine"]) && isset($_GET["annee"])) echo getSunday($_GET["annee"], $_GET["semaine"]);

	//On souhaite connaître la semaine suivante
	elseif (isset($_GET["suiv"]) && isset($_GET["annee"]))
	{
		if ($_GET["suiv"] == "53") echo nextWeek($_GET["annee"]);
		elseif ($_GET["suiv"] == "52") echo nextWeekMaybeLast ($_GET["annee"], $_GET["suiv"]);
		else echo nextWeek($_GET["annee"], intval($_GET["suiv"]));

	//On souhaite connaître la semaine précédente
	}elseif (isset($_GET["prec"]) && isset($_GET["annee"]))
	{
		if ($_GET["prec"] != "1") echo prevWeek (intval($_GET["prec"]));
		else echo getLastWeek (intval($_GET["annee"])-1);

	//On souhaite connaître la semaine en cours
	}elseif (isset($_GET["cours"])) echo actWeek();

?>