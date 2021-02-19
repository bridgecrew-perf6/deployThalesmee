var num="";
jQuery(document).keypress(function(touche){ // ecoute l'evenement keyPress
	var tag = touche.target.tagName.toLowerCase(); //on recupere le focus actuel
    if (tag != 'input' && tag != 'textarea')  //on test que ce n'est pas un input ou un textarea
	{
		var codeTouche = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés	
		if (codeTouche!=116 && codeTouche!=27) //f5 && echap
		{
			if(codeTouche==13 && num!="") //enter 
			{
				var url="./ajaxRechInstru.php?num="+num;
				jQuery.getJSON( url, function(data) {
						document.location.href="./detailsInstru.php?numInstru="+data["numInstru"];
					})
					.fail(function() {
						alert("Instrument inconnue");
					});
				num="";	
			}
			else
			{
				var lettre=String.fromCharCode(codeTouche);
				num+=lettre;
			}
		}
	}
});