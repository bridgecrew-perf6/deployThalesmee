function changeDate(fifo,idService)
{
	var service = idService;
	//supprime les anciens alert
	if (fifo == 0){
		
		var t=document.querySelectorAll('.alert'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
		for (var i=0;  i< t.length; i++)
			t[i].parentNode.removeChild(t[i]);
		var myDate = new Date(); //sert a contourner le cache pour recharger l'image
		var dateDeb=document.getElementById("dateDebNonFifo").value;
		var dateFin=document.getElementById("dateFinNonFifo").value;
		if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
		{
			dateDeb=dateDeb.split("/");
			dateDeb=dateDeb[2]+"-"+dateDeb[1]+"-"+dateDeb[0];
		
			dateFin=dateFin.split("/");
			dateFin=dateFin[2]+"-"+dateFin[1]+"-"+dateFin[0];

			//document.getElementById("rex_test").src="../graph/rex_fifo_test.php?idService=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			//document.getElementById("rex_of").src="../graph/rex_fifo_of.php?idService=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_av_NonFifo").src="../graph/attente_equip_av.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_av_NonFifo_PleinEcran").href="../graph/attente_equip_av.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			
			document.getElementById("rex_att_av_tous").src="../graph/attente_equip_av.php?idService="+service+"&fifo=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_av_tous_PleinEcran").href="../graph/attente_equip_av.php?idService="+service+"&fifo=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			
			document.getElementById("rex_att_fin_tous").src="../graph/attente_equip_fin.php?idService="+service+"&fifo=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_fin_tous_PleinEcran").href="../graph/attente_equip_fin.php?idService="+service+"&fifo=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			
			document.getElementById("rex_att_fin_NonFifo").src="../graph/attente_equip_fin.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_fin_NonFifo_PleinEcran").href="../graph/attente_equip_fin.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			
			
			document.getElementById("pdf_nonFifo").href="genPDFrexFifo.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin;
			document.getElementById("excel_nonFifo").href="rex_exportEx.php?idService="+service+"&fifo=0&dateDeb="+dateDeb+"&dateFin="+dateFin;
		}
		
	}else {
		var t=document.querySelectorAll('.alert'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
		for (var i=0;  i< t.length; i++)
			t[i].parentNode.removeChild(t[i]);
		var myDate = new Date(); //sert a contourner le cache pour recharger l'image
		var dateDeb=document.getElementById("dateDeb").value;
		var dateFin=document.getElementById("dateFin").value;
		if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
		{
			dateDeb=dateDeb.split("/");
			dateDeb=dateDeb[2]+"-"+dateDeb[1]+"-"+dateDeb[0];
		
			dateFin=dateFin.split("/");
			dateFin=dateFin[2]+"-"+dateFin[1]+"-"+dateFin[0];

			document.getElementById("rex_test").src="../graph/rex_fifo_test.php?idService="+service+"&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_test_PleinEcran").href="../graph/rex_fifo_test.php?idService="+service+"&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			//document.getElementById("rex_of").src="../graph/rex_fifo_of.php?idService=2&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_av").src="../graph/attente_equip_av.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_av_PleinEcran").href="../graph/attente_equip_av.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_fin").src="../graph/attente_equip_fin.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("rex_att_fin_PleinEcran").href="../graph/attente_equip_fin.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin+"&t="+myDate.getTime();
			document.getElementById("pdf_Fifo").href="genPDFrexFifo.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin;
			document.getElementById("excel_Fifo").href="rex_exportEx.php?idService="+service+"&fifo=1&dateDeb="+dateDeb+"&dateFin="+dateFin;
		}
	}
}

function verifDate(dateDeb,dateFin) //Cette fonction n'est executé que si les deux dates sont valides (isGoodDate avant dans le if), on peut donc assumer que l'on travaille avec des dates valides
{
	dateDeb=dateDeb.split('/');
	dateFin=dateFin.split('/');
	dateDeb=new Date(dateDeb[2], dateDeb[1]-1,dateDeb[0]);
	dateFin=new Date(dateFin[2], dateFin[1]-1,dateFin[0]);
	var dateAct= new Date();
	if(dateAct<dateDeb)
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date de début ne peut pas être supérieur à la date du jour </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
	
	if(dateDeb>dateFin)
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date de début ne peut pas être supérieur à la date de fin</strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
	return true;
	//if(dateDeb
}

function isGoodDate(mydate)
{
	
	var thedate, day, month, year;
	var onedate;
	var onetime;
 
	thedate=mydate.split('/');
	day = parseInt(thedate[0],10);	
	month = parseInt(thedate[1],10);	
	year = parseInt(thedate[2],10);
 
	if ((mydate.length != 10) || (thedate.length != 3) ||
		(isNaN(year)) || (isNaN(month)) || (isNaN(day)) ||
		(thedate[0].length < 2) || (thedate[1].length < 2) || (thedate[2].length < 4))
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date doit étre au format dd/mm/yyyy </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
			
 
	onedate = new Date(year, month-1, day);
	year = onedate.getFullYear();
 
	if ((onedate.getDate() != day) ||
		(onedate.getMonth() != month-1) ||
		(onedate.getFullYear() != year )) 
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date doit étre au format dd/mm/yyyy </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
			
	
	return true;
}
