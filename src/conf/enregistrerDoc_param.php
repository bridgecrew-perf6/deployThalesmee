<?php
	function give_me_link_DocClient(){
		//le nom du dossier doit se terminer par \\  car le '\' est un caractere d echappement donc a mettre en double
		// exemple : $chemin="U:\StockageDOC\\";
		$chemin="C:\HOST\doc\\";
		return $chemin;
	}

//on ne ferme pas une page php par "? >" 
//cela peut entrainer une generation d'espace blanc en html, et dans ce cas fait planter les generations de graphiques