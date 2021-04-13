$("#allC").change(function() {
	$("#tab .check").prop('checked', $(this).prop('checked'));
}); 


function validForm()
{
	var ok;
	var nbCoch=$("#tab .check:checked").length; //recup du nombre de case cochées
	if(nbCoch>0) //on verifi qu'au moins une case est coché
		ok=true;
	else
	{
		ok=false;
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-warning'><strong>Veuillez séléctionner au moins un instrument</strong></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	}
	return ok;
}