/*Change le valeur de la déviation ainsi que le couleur affichée
* @param
* type : niveau de déviation
*/
function changeDeviation(type, id)
{
	//Le type est null si aucun bouton n'est coché
	if (type != "null")
	{
		//Changement de la couleur suivant le niveau de déviation
		if (type == 0)$("#rouge"+id).css({backgroundColor : "red"});
		else if (type == 1){

			$("#orange"+id).css({backgroundColor : "orange"});
		} 
		else if (type == 2) $("#vert"+id).css({backgroundColor : "green"});
	}
}

var myIndex = 1;
var init = true;

/*Modification de la taille des zones de texte pour que l'affichage soit aligné
*/
function size()
{
	//Récupération de la taille des images +20 pour la marge entre elles
	var heightImage = $("#graphe1").height() + 20;
	//Récupération de la taille des en-têtes
	var heightLabel = $("h4").height();
	//Modification de la taille par le calcul
	$(".zone").height(heightImage * 2/3 - 3 * heightLabel - 10 + 1);
	$(".zone3").height((heightImage * 2/3 - 3 * heightLabel - 10 + 1)*3);
}

$(window).on('load', function(){
	size();
	var x = document.getElementsByClassName("carousel");
	console.log("Started...");
	for (i=0; i<x.length; i++)
	{
		x[i].className += "  fadein-animation";
	}
	$(".navbar").css("display", "none");
	carousel();
});

/*Gestion du diaporama
*/
function carousel()
{
	var i;
	//Récupeartion des éléments du diaporama
	var x = document.getElementsByClassName("carousel");

	for (i=0; i<x.length; i++)
	{
		//Suppression des animations
		x[i].classList.remove("fadeout-animation");
	}
	myIndex++;
	//Si l'indice dépasse remise à 1
	if (myIndex > x.length) myIndex = 1;
	//Affichage du graphique suivant
	x[myIndex-1].style.display = "block";
	//Recherche du graphique précedent
	if (myIndex-1 == 0)
	{
		//Ajout de l'animation de sortie
		x[5].className += " fadeout-animation";
		//Suppression de l'affichage
		x[4].style.display = "none";

	} 
	else if (myIndex-1 == 1)
	{
		//Ajout de l'animation de sortie
		x[0].className += " fadeout-animation";
		//Suppression de l'affichage
		x[5].style.display = "none";
	} 
	else if (myIndex-1 == 2)
	{
		//Ajout de l'animation de sortie
		x[1].className += " fadeout-animation";
		//Suppression de l'affichage
		x[0].style.display = "none";
	}
	else if (myIndex-1 == 3)
	{
		//Ajout de l'animation de sortie
		x[2].className += " fadeout-animation";
		//Suppression de l'affichage
		x[1].style.display = "none";
	} 
	else if (myIndex-1 == 4)
	{
		//Ajout de l'animation de sortie
		x[3].className += " fadeout-animation";
		//Suppression de l'affichage
		x[2].style.display = "none";
	} 
	else if (myIndex-1 == 5)
	{
		//Ajout de l'animation de sortie
		x[4].className += " fadeout-animation";
		//Suppression de l'affichage
		x[3].style.display = "none";
	} 

	//Répétition toute les 15 secondes
	if (init == true)
	{
		setTimeout(carousel, 0.1);
		if (myIndex-1 == 0) init = false;

	}else if (init == false)
	{
		setTimeout(carousel, 15000);
		init = "";
	}
}

$(window).mousemove(function(e)
{
	if (e.clientY < 100) $(".navbar").css("display", "block");
	else $(".navbar").css("display", "none");
})

$(window).resize(function(e){
	if (e.target == this) window.location.href =  window.location.href;
});	