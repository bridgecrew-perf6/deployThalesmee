/*Change le valeur de la déviation ainsi que le couleur affichée
* @param
* type : niveau de déviation
*/
function changeDeviation(type)
{
	//Remise à zéro de la couleur
	$(".bouton").css({backgroundColor : "white"});
	//Le type est null si aucun bouton n'est coché
	if (type != "null")
	{
		//Ajout du paramètre pour l'enregistrement en base de données
		$("#form").attr('action','publication.php?deviation='+type);
		//Changement de la couleur suivant le niveau de déviation
		if (type == 0)$("#rouge").css({backgroundColor : "red"});
		else if (type == 1) $("#orange").css({backgroundColor : "orange"});
		else if (type == 2) $("#vert").css({backgroundColor : "green"});
	}
}

/*Modifie les urls
* @param
* id : identifiant du graphe à modifier
* annee : nouvelle année
* fin : nouvelle date de fin
*/
function setUrl (id, annee, fin)
{
	//Récupération de l'attribut src actuel
	var url = $(id).attr("src");
	//Découpage
	var tab = url.split("&");
	//Remplissage avec les nouvelles valeurs
	var new_url = tab[0]+"&"+tab[1]+"&dateDeb="+annee+"-01-01 00:00:00&dateFin="+fin+" 23:59:00";
	var nbParam = 4;
	//Ajout des paramètre restants
	while (nbParam < tab.length)
	{
		new_url+="&"+tab[nbParam];
		nbParam ++;
	}
	//Ajout du nouvel attribut
	$(id).attr("src",new_url);
}

/*Change l'url des graphes suivant la date et la semaine choisie par l'utilisateur
*/
function changeUrl()
{
	//Récupération des données saisies
	var semaine = $("#semaine").val();
	var annee = $("#annee").val();
	//Création de l'url pour la requête AJAX
	$url = "../essai/ajaxSemaine.php?semaine="+semaine+"&annee="+annee;
	$.get($url, function(data){
		//Récupération des paramètres dans le nouveau contexte
		var semaine = $("#semaine").val();
		var annee = $("#annee").val();
		//Modification des urls
		setUrl ("#graphe1", annee, data);
		setUrl ("#graphe2", annee, data);
		//Changement du texte correspondant aux nouveaux graphes
		changeText(semaine, annee);
	})
}

/*Changement du texte correspondant aux nouveaux graphes
* semaine : semaine choisie
* annee : annee choisie
*/
function changeText (semaine, annee)
{
	//Création de l'url pour la requête AJAX
	$url = "../essai/ajaxPublication.php?semaine="+semaine+"&annee="+annee;
	$.get($url, function(data){
		var data = JSON.parse(data);
		//Modification du contenu des balises
		$("#mesure").html(data["mesure"]);
		$("#analyse").html(data["analyse"]);
		$("#fait").html(data["fait"]);
		//Changement de la valeur de la déviation
		changeDeviation(data["deviation"])
	})
}

/*Modification de l'année par l'année précédente
*/
function annee_prec()
{
	$("#annee").val($("#annee").val()-1);
	//Changement de l'url des graphes
	changeUrl();
}

/*Modfication de l'année par l'année en cours
*/
function annee_cours()
{
	var date = new Date();
	$("#annee").val(date.getFullYear());
	//Changement de l'url des graphes
	changeUrl();
}

/*Modification de l'année par l'année en cours
*/
function annee_suiv()
{
	$("#annee").val(parseInt($("#annee").val())+1);
	//Changement de l'url des graphes
	changeUrl();
}

/*Modification de la semaine par la semaine suivante
*/
function semaineSuivante ()
{
	//Récupération des données saisies
	var semaine = $("#semaine").val();
	var annee = $("#annee").val();
	//Création de l'url pour la requête AJAX
	$url = "./ajaxSemaine.php?suiv="+semaine+"&annee="+annee;
	$.get($url, function(data){
		//Découpage du résultat pour avoir la semaine et l'année
		var res = data.split("-");
		//Modification des valeurs
		$("#semaine").val(res[0]);
		$("#annee").val(res[1]);
		//Changement de l'url des graphes
		changeUrl();
	})
}

/*Modification de la semaine par la semaine en cours
*/
function semaineEnCours ()
{
	//Création de l'url pour la requête AJAX
	$url = "./ajaxSemaine.php?cours=1";
	$.get($url, function(data){
		//Découpage du résultat pour avoir la semaine et l'année
		var res = data.split("-");
		//Modification des valeurs
		$("#semaine").val(res[0]);
		$("#annee").val(res[1]);
		//Changement de l'url des graphes
		changeUrl();
	})
}

/*Modification de la semaine par la semaine précédente
*/
function semainePrecedente ()
{
	//Récupération des données saisies
	var semaine = $("#semaine").val();
	var annee = $("#annee").val();
	//Si la semaine est à 1 => changement de l'année par une de moins
	if (semaine == 1) $("#annee").val($("#annee").val()-1);
	$url = "./ajaxSemaine.php?prec="+semaine+"&annee="+annee;
	$.get($url, function(data){
		//Modification de la valeur
		$("#semaine").val(data);
		//Changement de l'url des graphes
		changeUrl();
	})
}

/*Modification de la taille des zones de texte pour que l'affichage soit aligné
*/
function size()
{
	//Récupération de la taille des images +20 pour la marge entre elles
	var heightImage = $("#graphe1").height() + 20;
	//Récupération de la taille des en-têtes
	var heightLabel = $("h4").height();
	//Modification de la taille par le calcul
	$(".zone").height(heightImage * 2/3 - 3 * heightLabel - 10);
}

$(window).on('load', function(){
	size();
});

changeUrl();
