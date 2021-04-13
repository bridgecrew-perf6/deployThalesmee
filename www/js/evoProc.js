//on cache les elements

document.getElementById('f2').style.display = "none";
document.getElementById('emc').style.display = "none";
document.getElementById('vib').style.display = "none";
document.getElementById('vth').style.display = "none";
document.getElementById('btnRetour').style.display = "none";
document.getElementById('btnSubmit').style.display = "none";


function evoDP(idDP) //ajoute la deuxieme partie de la page evolution d'une dp
{
	document.getElementById('btnRetour').style.display = "";
	document.getElementById('f1').style.display = "none";
	document.getElementById('f2').style.display = "";
	document.getElementById('titref2').innerHTML="Demande de procédure n°"+idDP+"";
	//on ajoute l'id de la dp au champs hidden, permet de recuperer cette valeur sur la page de traitement
	document.getElementById('idDP').value=idDP;
	
}


function ajoutEMC() //fonction qui ajoute la partie EMC
{
	if(document.getElementById('emc').style.display=="none")
	{
		document.getElementById('emc').style.display = "";
		//le fait de disabled ou non ces champs permet d'eviter un bug connu sur plusieurs navigateur:
		//Si un champs est required, meme s'il est hidden il bloquera un submit
		document.getElementById('refEMC').disabled=false;
		document.getElementById('issEMC').disabled=false;
	}
	else
	{
		document.getElementById('emc').style.display = "none";
		document.getElementById('refEMC').disabled=true;		
		document.getElementById('issEMC').disabled=true;		
	}
	displaySubmit();
}

function ajoutVIB() //fonction qui ajoute la partie vib
{
	if(document.getElementById('vib').style.display=="none")
	{
		document.getElementById('vib').style.display = "";
		document.getElementById('refVIB').disabled=false;
		document.getElementById('issVIB').disabled=false;
	}
	else
	{
		document.getElementById('vib').style.display = "none";
		document.getElementById('refVIB').disabled=true;
		document.getElementById('issVIB').disabled=true;	
	}
	displaySubmit();
}

function ajoutVTH() //fonction qui ajoute la partie EMC
{
	if(document.getElementById('vth').style.display=="none")
	{
		document.getElementById('vth').style.display = "";
		document.getElementById('refVTH').disabled=false;
		document.getElementById('issVTH').disabled=false;
	}
	else
	{
		document.getElementById('vth').style.display = "none";
		document.getElementById('refVTH').disabled=true;
		document.getElementById('issVTH').disabled=true;
	}
	displaySubmit();
}

function retourChoix()
{
	document.getElementById('choix_1').checked=false;
	document.getElementById('choix_2').checked=false;
	document.getElementById('choix_3').checked=false;
	document.getElementById('btnSubmit').style.display = "none";
	document.getElementById('btnRetour').style.display = "none";
	document.getElementById('f2').style.display = "none";
	document.getElementById('emc').style.display = "none";
	document.getElementById('vib').style.display = "none";
	document.getElementById('vth').style.display = "none";
	
	
	document.getElementById('f1').style.display = "";

}

function displaySubmit()
{
	//si les trois cases sont décocher on ne fait pas apparaitre le bouton valider
	if(document.getElementById('emc').style.display == "none" 
	&& document.getElementById('vib').style.display == "none" 
	&& document.getElementById('vth').style.display == "none")
		document.getElementById('btnSubmit').style.display = "none";
	else
		document.getElementById('btnSubmit').style.display = "";
}


