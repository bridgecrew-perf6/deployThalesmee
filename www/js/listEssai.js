//Masquage des erreurs
$("#erreur").hide();

//Gestion de la checkbox TOUS pour cocher/décocher tous
$("#tous").click(function(){

	var filtre = $("input[type=checkbox]");
	if ($(this).prop("checked")){
		filtre.prop('checked', true);
	}else
	{
		filtre.prop('checked', false);
	}
})

/* Fonction permettant de verifier qu'au moins un case est coché
@return Booleen désignant si oui ou non une case est coché
*/
function verif(){
	
	var check = false
	$("input[type=checkbox]").each(function(){
		
		if ($(this).prop("checked")){
			
			check = true
		}		
	})
	if (check == false){
		
		$("#erreur").show();
	}

	return check;
}

/* Fonction d'initialisation des case à cocher
*/
function init()
{
	var filtre = $("input[type=checkbox]");
	if ($("#tous").prop("checked")){
		filtre.prop('checked', true);
	}

	if ($(this).prop("checked")){
		
		$("#tous").prop("checked", true);
	}
}

/* Permet de gérer le décochage de la case TOUS si une case n'est pas coché et 
inversement si toutes les cases sont cochées la case TOUS sera coché pour garder la cohérence
*/
$(".filtre").click(function(){

	var filtre = true;

	$(".filtre").each(function()
	{
		if (!$(this).prop("checked")){
		
			filtre = false;
			return true;
		}
	})

	console.log(filtre);

	if (filtre)
	{
		$("#tous").prop("checked", true);

	}else
	{
		$("#tous").prop("checked", false);
	}
})



