//lance l'animation d'attente
lancerAnnim();
$(document).ready(function() {

	//Retourne la diffférence entre deux dates
	function dateDiff(date1, date2){
		var diff = {}                           // Initialisation du retour
		var tmp = date2 - date1;
		
		tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
		diff.sec = tmp % 60;                    // Extraction du nombre de secondes
		
		tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
		diff.min = tmp % 60;                    // Extraction du nombre de minutes
	 
		tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
		diff.hour = tmp % 24;                   // Extraction du nombre d'heures
		 
		tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
		diff.day = tmp;
		return diff;
	}
	//Fonction permettant d'afficher la couleur orange sur les boutons et indiquer à l'utilisateur où il se situe dans le calendrier
	function checkDate (){
		
		var url='focusDate.php';
		$.get( url, function(data) {
		
			data = JSON.parse(data);
			
			var date=getMonday(new Date());
			
			diff = dateDiff(date, ladate);
			if (7*parseInt(data['semaine']) + (parseInt(data['jour']) + (parseInt(data['mois']))) == 0){
				
				$("#sem_suiv").removeClass("backRed");
				$("#sem_prec").removeClass("backRed");
				$("#jour_prec").removeClass("backRed");
				$("#jour_suiv").removeClass("backRed");
				if (affichage == "mois"){
					
					$("#mois_prec").removeClass("backRed");
					$("#mois_suiv").removeClass("backRed");
				}

			}

			else if (7*parseInt(data['semaine']) + (parseInt(data['jour']) + (parseInt(data['mois']))) > 0){
				
				$("#sem_prec").addClass("backRed");
				$("#sem_suiv").removeClass("backRed");
				$("#jour_prec").addClass("backRed");
				$("#jour_suiv").removeClass("backRed");
				if (affichage == "mois"){
					
					$("#mois_prec").addClass("backRed");
					$("#mois_suiv").removeClass("backRed");
				}
				
				
			}else if (7*parseInt(data['semaine']) + (parseInt(data['jour']) + (parseInt(data['mois']))) < 0){
								
				$("#sem_suiv").addClass("backRed");
				$("#sem_prec").removeClass("backRed");
				$("#jour_prec").removeClass("backRed");
				$("#jour_suiv").addClass("backRed");
				if (affichage == "mois"){
					
					$("#mois_prec").removeClass("backRed");
					$("#mois_suiv").addClass("backRed");
				}
				
			}
				
		})
	}
	
	//Permet de trouver le numéro de la semaine en ayyany une année, un mois et un jour
	function DefSemaineNum(aaaa, mm, jj)
	{
		var MaDate = new Date(aaaa, mm, jj);
		var annee = MaDate.getFullYear();
		var NumSemaine = 0,//numéro de la semaine
		ListeMois = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		if (annee %4 == 0 && annee %100 !=0 || annee %400 == 0) {ListeMois[1]=29};
		var TotalJour=0;
		for(cpt=0; cpt<mm; cpt++){
			
			TotalJour+=ListeMois[cpt];
		}
		TotalJour+=jj;
		DebutAn = new Date(annee,0,1);
		var JourDebutAn;
		JourDebutAn=DebutAn.getDay();
		if(JourDebutAn==0){
			JourDebutAn=7
		};
		TotalJour-=8-JourDebutAn;
		NumSemaine = 1;
		NumSemaine+=Math.floor(TotalJour/7);
		if(TotalJour%7!=0){
			NumSemaine+=1
		};
		return(NumSemaine);
	}
	
	function ajLignes(data)
	{	
		for(var index in data)
		{
			var newRow = document.getElementById('tb_agend').insertRow(-1);
			var newCell=newRow.insertCell(-1);
			newCell.className="cellMoy";
			if (affichage == "mois"){
				
				newCell.colSpan = 3;
			}
			newCell.innerHTML = "<div class='conteneur'>"+index+"</div>";
			var moyen=data[index];
			var nbTest=Object.keys(moyen).length+1;
			newCell.rowSpan=nbTest;
			for(var i in moyen)
				ajLigneTest(moyen[i]);
			newRow.parentNode.lastChild.className="separation";
		}
	}
	
	function ajLigneTest(test)
	{
		var newRow = document.getElementById('tb_agend').insertRow(-1);

		//conversion de la date de debut sous le format JS
		var d = test["date_debut"].split(/[- :]/);
		if (d[3] == "00"){
			
			d[3] = "08";
		}
		var DateTestDebut = new Date(d[0], d[1]-1, d[2], d[3], d[4], 0);

		t = test["date_livraison"].split(/[- :]/);
		var DateLivraison = new Date(t[0], t[1]-1, t[2], t[3], t[4], 0);
		
		t = test["date_termine"].split(/[- :]/);
		var DateTermine = new Date(t[0], t[1]-1, t[2], t[3], t[4], 0);
		
		//conversion de la date de fin sous le format JS
		f = test["date_fin"].split(/[- :]/);
		var DateTestFin = new Date(f[0], f[1]-1, f[2], f[3], f[4], 0);
		
		//evite l'erreur si la date est nulle
		if (test["date_prevu"] != "null"){
			
			t = test["date_prevu"].split(/[- :]/);
			var DateDebutPrevu = new Date(t[0], t[1]-1, t[2], t[3], t[4], 0);

			
		}else 
			var DateDebutPrevu = "null";
		
		if (test["date_fin_prevu"] != "null"){
			
			t = test["date_fin_prevu"].split(/[- :]/);
			var DateFinPrevu = new Date(t[0], t[1]-1, t[2], t[3], t[4], 0);

			
		}else 
			var DateFinPrevu = "null";
		

		var diff;
		var date_parcourt=new Date(ladate.getTime());
		diff=dateDiff(ladate, DateTestDebut);
		var nbJDeb=diff.day;
		//nbJFin=DateTestFin.getDate()-ladate.getDate();
		diff=dateDiff(ladate, DateTestFin);
		var nbJFin=diff.day;
		var i=0
		

		//remplissage des cellules avant test
		while(i<nbJDeb)
		{
			var newCell=newRow.insertCell(-1);
			newCell.className="jours";
			
			if (affichage == "semaine"){
				
				var newCell2=newRow.insertCell(-1);
				newCell2.className+="jours";
			
			}
			
			if(contains(tabWeek,i+1))
			{
				newCell.className+=" cellWeekEnd";
				if (affichage == "semaine"){
				
					newCell2.className+=" cellWeekEnd";
				}
			}
			i++;
		}		
		i--;
		var nbJtest=nbJFin-i;

		//fonction qui ajoute le test
		
		if (affichage == "semaine"){
			
			var nbJour = 6;
			if(nbJtest+i+1>7) //on ajuste le nb de test en fonction du nb de jours qu'il reste à afficher
				nbJtest=7-i-1;
			
				
		}else {
			
			var nbJour = 30;
			if(nbJtest+i+1>30)
			//on ajuste le nb de test en fonction du nb de jours qu'il reste à afficher
				nbJtest=31-i-1;

		}
		
		ajBarTest(test,newRow,nbJtest,DateTestDebut,DateTestFin,nbJFin,DateLivraison,DateDebutPrevu, DateTermine, DateFinPrevu);
		
		i+=nbJtest;

		//remplissage des cellules après test
		while(i<nbJour)
		{
			var newCell=newRow.insertCell(-1);
			newCell.className="jours";
			
			if (affichage == "semaine"){
				
				var newCell2=newRow.insertCell(-1);
				newCell2.className+="jours";
			}
			
			if(contains(tabWeek,i+2))
			{
				newCell.className+=" cellWeekEnd";
				
				if (affichage == "semaine"){
					
					newCell2.className+=" cellWeekEnd";
				}
			}
			
			i++;
		}
	}
	
	function ajBarTest(test,newRow,nbJtest,DateTestDebut,DateTestFin,nbJFin,DateLivraison,DateDebutPrevu, DateTermine, DateFinPrevu)
	{	

		var heureDebut=DateTestDebut.getHours();
		var heureFin=DateTestFin.getHours();
		
		//cellule(s) test
		var idEtat=parseInt(test["idetat_etat"]);
		//couleur de la bar en fonction de l'etat
		var classBar; 
		var pastille="";
		switch (idEtat)
		{
			case 20:
				classBar='bar-plannifie';
				break;
			case 21:
				classBar='bar-reserve';
				break;
			case 22:
				classBar='bar-attente';
				break;
			case 23:
				classBar='bar-cours';
				//if(DateTestFin<date_act)
				//	pastille='<span class="pastilleOrange"></span>';
				break;	
			case 24:
				classBar='bar-fin';
				break;
			case 25:
				classBar='bar-retour';
				break;
			case 26:
				classBar='bar-validation';
				break;
			case 27:
				classBar='bar-maintenance';
				break;
		}
		
		var d = test["date_debut"].split(/[- :]/);
		
		var ecart = dateDiff(DateTestDebut,DateTestFin);
		if (affichage == "semaine"){
			
			var tailleBar=nbJtest*2;
			//si debut apres 13h on ajoute une cellule vide avant le test et on reduit la taille de la bar de 1
			if((DateTestDebut>=ladate)&& heureDebut>=13 && (heureFin>=13 || tailleBar>2))
			{
				if (!(ecart.min >= 0 && ecart.hour == 0 && ecart.day ==0 && d[3] < 14))
				{
					var newCell=newRow.insertCell(-1);
					newCell.className="jours";
					tailleBar--;
				}
				
			}
			
		}else {
			
			var tailleBar=nbJtest;
		}

		if(DateTestDebut == DateTestFin) var tailleBar=1;		
		
		var rouge;
		var orange;
		var retardME=false;
		/*
		if (idEtat >21){
			if (test["pastilleOrange"]==1){
			pastille='<span class="pastilleOrange"></span>';
		}*/
		
		//Si pas de date de début prevu -> pas de pastille

		if (DateDebutPrevu != "null" && test["pastilleOrange"]!=2){
			
			var tempsRetard = dateDiff(DateLivraison,DateDebutPrevu);
			//condition de retard
			if (idEtat > 21 && parseInt(tempsRetard.day) < 0 || (idEtat > 21 && parseInt(tempsRetard.day) == 0 && parseInt(tempsRetard.hour) < -2) || (idEtat > 21 && parseInt(tempsRetard.day) == 0 && parseInt(tempsRetard.hour)  == -2 && parseInt(tempsRetard.min) <0 ))
			{

				var url='majEssaiRetard.php?id='+test["idEssai"];
				$.get( url, function(data) {
					
				})
				.fail(function(data) {
					var child= document.createElement("div");
					child.className="text-center";
					child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 8</strong></div>";
					document.body.insertBefore(child, document.body.firstChild);
					window.scrollTo(0,0);
				});
				orange = true;
			}
		}
		
		//Mise à jour retardME
		if (DateFinPrevu != "null" && test["retardME"]!=2){

			var tempsRetard = dateDiff(DateTermine,DateFinPrevu);
			//condition de retard
			if (idEtat > 22 && parseInt(tempsRetard.day) < 0 || (idEtat > 22 && parseInt(tempsRetard.day) == 0 && parseInt(tempsRetard.hour) < 0) || (idEtat > 22 && parseInt(tempsRetard.day) == 0 && parseInt(tempsRetard.hour)  == 0 && parseInt(tempsRetard.min) <0 ))
			{

				var url='majEssaiRetard.php?retard=true&id='+test["idEssai"];
				$.get( url, function(data) {
					
				})
				.fail(function() {

					var child= document.createElement("div");
					child.className="text-center";
					child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 9</strong></div>";
					document.body.insertBefore(child, document.body.firstChild);
					window.scrollTo(0,0);
				});
				retardME = true;
			}
		}

		if (test["pastilleOrange"]==2) orange = false; //si la pastille a été ajouté manuellement
		if (test["retardME"]==2) orange = false; //si la pastille a été ajouté manuellement
		if (test["retardME"]==1) retardME=true;
		if (test["pastilleOrange"]==1) orange=true;
		if (test["anomalie"]==1) anomalie=true;		
		else anomalie=false;
		
		if((test["planifie"]==0)||(test["pastilleRouge"]==1))
		{
			var url='majEssaiRetard.php?idRouge='+test["idEssai"];
			$.get( url).fail(function(data) {

				var child= document.createElement("div");
				child.className="text-center";
				child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 10</strong></div>";
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			});
			rouge=true 
		}
		
		if (idEtat == 26) orange=false;
		if (idEtat <23) retardME=false;

		//condition d'affichage des pastilles
		if (!orange){
			if (retardME && !rouge) pastille='<span class="retardME">&#x26A0;</span>';
			else if (retardME && rouge){
				pastille='<span class="retardME">&#x26A0;</span>';
				pastille+='<span class="pastilleRouge"></span>';
				
			}else if (!retardME && rouge) pastille='<span class="pastilleRouge"></span>';

		}else if (orange && !rouge) pastille+='<span class="pastilleOrange"></span>';
		else if (orange && rouge){

			pastille+='<span class="pastilleRouge"></span>';
			pastille+='<span class="pastilleOrange"></span>';
		}

		var date_planifie = test["date_planifie"];
		var date_planifie_primavera = test["date_planifie_primavera"];
		var duree_planifie = test["duree_planifie"];
		var duree_planifie_primavera = test["duree_planifie_primavera"];
		var diff = false;

		if (date_planifie != "null" && date_planifie_primavera != "null" && duree_planifie != 0 && duree_planifie_primavera != 0)
		{
			if (date_planifie != date_planifie_primavera)
			{
				diff = true;
			}
		}

		//bar de test
		var newCellTest=newRow.insertCell(-1);
		newCellTest.className="jours";
		newCellTest.style.position = "relative";
		
		if (affichage == "semaine"){
			//si fin avant 13h on ajoute une cellule vide apres le test et on reduit la taille de la bar de 1
			if(nbJFin<7 && heureFin<=13)
			{
				var newCell2=newRow.insertCell(-1);
				newCell2.className="jours";
				tailleBar--;
			}
		}
		var title=test["of"];
		title += test["commentaire"];
		title +=test["descriptif"];

		var afEquip=test["affaire"]+" "+test["equipement"];

		if (diff) afEquip += ' <strong><=></strong>';

		if (title != ""){
			
			newCellTest.innerHTML="<div class='drag'><div id='1"+test["idEssai"]+"' class='progress essai "+classBar+"' >"+pastille+"</div><p id='"+test["idEssai"]+"' style='position:absolute; width:500px; top:50%; transform:translateY(-50%); left:0; z-index:1; cursor:pointer' onmouseout='cache("+test["idEssai"]+");' onmouseover='montre(event,"+test["idEssai"]+");'>"+afEquip+"</p><div class='infobulle' id=infobulle"+test["idEssai"]+">"+title+"</div></div>";
		}else{
			newCellTest.innerHTML="<div class='drag'><div id='1"+test["idEssai"]+"' class='progress essai "+classBar+"' >"+pastille+"</div><p id='"+test["idEssai"]+"' style='position:absolute; width:500px; top:50%; transform:translateY(-50%); left:0; z-index:1; cursor:pointer' onmouseout='cache("+test["idEssai"]+");' onmouseover='montre(event,"+test["idEssai"]+");'>"+afEquip+"</p></div>";
			
		}
		newCellTest.colSpan=tailleBar;
		$(newCellTest).dblclick(function(){
			document.location.href="detailsEssai.php?idEssai="+test["idEssai"];

		});
		
		var bar=$(newCellTest).children().first();
		var heightBar=bar.height();
		if (affichage == "semaine")
			var tailleCell=($('#agend').find('thead').find('tr').children().first().next().width()+1)/2; //+1 ajuste a l'exact
		else 
			var tailleCell=($('#agend').find('thead').find('tr').children().first().next().width()+1); //+1 ajuste a l'exact
		
		//Redimensionnement de la barre de test
		bar.resizable({
			maxHeight: heightBar,
			grid: tailleCell,
			stop: function( event, ui ){
				var date_parcourt=new Date(ladate.getTime());
				var decalage=Math.round((ui.size.width-ui.originalSize.width)/tailleCell);
				
				if (affichage == "semaine") nbJour = 7;
				else nbJour = 31;

				var dateFinSemaine = new Date(ladate);
				dateFinSemaine.setDate(dateFinSemaine.getDate() + nbJour);
				if(DateTestFin>dateFinSemaine)
				{
					date_parcourt=dateFinSemaine;
					decalage--;
				}
				else
				{
					date_parcourt=new Date(DateTestFin.getTime());
					if (affichage == "semaine") if(heureFin>=13) decalage++;
				}
				
				if (affichage == "semaine"){
					
					decalage=decalage/2;

					if(decalage%1!=0) date_parcourt.setHours(17);
					else date_parcourt.setHours(12);

				}else date_parcourt.setHours(17);

				date_parcourt.setMinutes(0);
				date_parcourt.setSeconds(0);

				if (date_parcourt.getDate()+decalage < 0) decalage--;

				date_parcourt.setDate(date_parcourt.getDate()+decalage);
				$("#dialText").html("Nouvelle date de fin: "+date_parcourt.toLocaleString()+" ?");
				//Fenetre de dialogue pour la validation
				$("#dialog").dialog({ 
					autoOpen: false,
					height: "auto",
					width: 350,
					modal: true,
					buttons: {
						"Valider": function() {
							majEssaiDateFin(test["idEssai"],date_parcourt.getTime());
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
							creerTab();
						}
					}
				});
				$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
				$("#dialog").dialog('open');				
			}
		});
		
		if (anomalie == true) $("#1"+test["idEssai"]).css("border", "2px solid #FA5858");
	}

	function frenchDate (date1)
	{
		var year = date1.getFullYear();
		var month = parseInt(date1.getMonth()) +1;
		if (month < 10) month = "0"+month;
		var day = parseInt(date1.getDate());
		if (day < 10) day = "0"+day;
		return year+"-"+month+"-"+day
	}
	
	function creerTab()
	{	
		clearTimeout(timer); //annule le dernier setTimeout selon les anciens parametres
		//supprime les précédents alert
		$(".alert").remove();
		$("#tab_att").hide();
		//requete pour les essai plannifié
		
		if (affichage == "semaine"){
			var url='ajaxEssai.php?date='+ladate.getTime()+'&typeEssai='+typeEssai+'&moyenSelect='+moyenSelect+'&labo='+labo;
			jQuery.getJSON( url, function(data) {
				//test si le numero entré n'est pas déja dans la liste

				if(data && Object.keys(data).length!=0)

					ajLignes(data);
				
				//ajoute les event drag & drop
				dragResize();
			})
			.fail(function(data) {
				var child= document.createElement("div");
				child.className="text-center";
				child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 1</strong></div>";
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			});
		} else {
			
			var url='ajaxEssaiMois.php?date='+ladate.getTime()+'&typeEssai='+typeEssai+'&moyenSelect='+moyenSelect+'&labo='+labo;

			jQuery.getJSON( url, function(data) {
				//test si le numero entré n'est pas déja dans la liste

				if(data && Object.keys(data).length!=0)

					ajLignes(data);
				
				//ajoute les event drag
				dragResize();
			})
			.fail(function(data) {
				var child= document.createElement("div");
				child.className="text-center";
				child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 2</strong></div>";
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			});
		}
		
		//requete pour les essai non plannifié
		url='ajaxEssai.php?labo='+labo;
		jQuery.getJSON( url, function(data) {
			//test si le numero entré n'est pas déja dans la liste
			if(data && Object.keys(data).length!=0)
				ajouterListeAtt(data);
			stopAnnim();
			
		})
		.fail(function(data) {
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 3</strong></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);
		});
		
		//on supprime toutes les lignes de tbody
		var myTBody = document.getElementById('tb_agend');
		myTBody.innerHTML="";
		
		myTBody = document.getElementById('tb_att');
		myTBody.innerHTML="";
		
		//on vide l'ancien tableau des week end 
		tabWeek=new Array();
		
		//supprime la coloration du jours actuel
		$('#jourAct').removeAttr('id');
		
		var titreAct=$('#agend').find('thead').find('tr').children().first().next(); //premier jours
		
		$("#infos_date").html(tab_mois[ladate.getMonth()]+" "+ladate.getFullYear());

		//Numéro de semaine
		var url='trouveNumDate.php?annee='+parseInt(ladate.getFullYear())+'&mois='+parseInt(ladate.getUTCMonth()+1)+'&jour='+parseInt(ladate.getUTCDate()+1);
		$.get( url, function(data) {
			$("#infos_date").html(tab_mois[ladate.getMonth()]+" "+ladate.getFullYear()+ "</br>" + "Semaine "+ data);
		})
		.fail(function(data) {
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 4</strong></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);
		});

		var date_parcourt=new Date(ladate.getTime());
		
		if (affichage == "semaine") var nbJour = 7;	
		else var nbJour = 31;
		
		for(var i=0; i<nbJour;i++)//on complete tous les jours
		{			
			
			if (affichage == "semaine"){
				
				titreAct.html( tab_jour[(date_parcourt.getDay())]+" "+date_parcourt.getDate() );
			}else {
				
				titreAct.html( tab_jour[(date_parcourt.getDay())].substr(0,1)+"</br> "+date_parcourt.getDate() );
			}
			
			
			if(date_parcourt.getTime()==date_act.getTime())
				titreAct.attr('id', 'jourAct');
			
			//si week end on ajoute l'index de la cellule dans le tableau du week end
			if(date_parcourt.getDay() == 6 || date_parcourt.getDay() == 0)
				tabWeek[tabWeek.length]=titreAct[0].cellIndex;
				
			date_parcourt.setDate(date_parcourt.getDate()+1);
			titreAct=titreAct.next();		
		}

		//Pourcentage de retard de test
		if (affichage == "semaine") var url='retardTest.php?affichage=semaine&labo='+labo+'&ladate='+frenchDate(ladate);
		else var url='retardTest.php?affichage=mois&labo='+labo+'&ladate='+frenchDate(ladate);

		$.get( url, function(data) {
			var titreAct=$('#agend').find('thead').find('tr').children().first().next(); //premier jours
			var retard = JSON.parse(data);
			for(var i=0; i<nbJour;i++)//on complete tous les jours
			{	
				if (i < retard.length) 
					if (retard[i] != "") 
						titreAct.append("<br>"+retard[i]+"%");
				
				titreAct=titreAct.next();			
			}
		})
		.fail(function(data) {
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 11</strong></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);
		});

		timer=setTimeout(function(){creerTab();}, 120000); //toutes les 2 minutes maj generale	
	}
	
	function ajouterListeAtt(data)
	{	
		var nb=data.length;
		for(var i in data)
		{
			var newRow;
			newRow=document.getElementById('tb_att').insertRow(-1); 
			newRow.id=data[i]["idEssai"];
			var newCell=newRow.insertCell(-1);
			newCell.innerHTML = data[i]["badge"]
			newCell=newRow.insertCell(-1);
			newCell.innerHTML = data[i]["affaire"]
			newCell=newRow.insertCell(-1);
			newCell.innerHTML = data[i]["equipement"]
			
			$(newRow).click(function() {
				document.location.href='detailsEssai.php?idEssai='+this.id;
			});
		}
		$("#tab_att").show();		
	}
	//Mise à jour de la date au redimenssionnementt
	function majEssaiDateDebut(idEssai,timeStamp)
	{

		jQuery.ajax({
			url:"AjaxMajEssai.php",
			type:"GET",
			data: 'idEssai='+idEssai+'&dateDebut='+timeStamp,
			
			success: function(data){
				console.log(data);
				creerTab();
			},
			error: function(data){
				var child= document.createElement("div");
				child.className="text-center";
				child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 5</strong></div>";
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			}
		});
	}
	//Maj de la date au drag and drop
	function majEssaiDateFin(idEssai,timeStamp)
	{
		jQuery.ajax({
			url:"AjaxMajEssai.php",
			type:"GET",
			data: 'idEssai='+idEssai+'&dateFin='+timeStamp,
			
			success: function(data){
				console.log(data);
				creerTab();
			},
			error: function(data){
				var child= document.createElement("div");
				child.className="text-center";
				child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 6</strong></div>";
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			}
		});
	}
	
	function contains(a, obj) { //fonction de recherche si elem dans un tableau, beaucoup plus rapide que toutes methodes pré-faites genre in_array
		var i = a.length;
		while (i--) {
		   if (a[i] === obj) {
			   return true;
		   }
		}
		return false;
	}
	//Fonction pour récuperre le lundi d'une date
	function getMonday(d) {
	  d = new Date(d);
	  var day = d.getDay(),
		  diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
	  return new Date(d.setDate(diff));
	}
	
	function setFiltreSession(nom,val){
		var dataString=nom+"="+val;
		$.ajax({
			type: "GET",
			url:"ajaxMajFiltre.php",
			data: dataString,
			dataType: 'json',
			cache: false
		});
	}
	
	//Fonction qui premet de réaliser un drag sur une barre d'essai
	function dragResize()
	{
		var bars=$("#agend").find('.drag');
		bars.draggable({
			axis: "x", //drag que sur l'axe x (horizontal)
			scroll: false,
			revert: false,
			start: function(e, ui) {
				var colspan=$(this).parent().attr('colspan');
				var tr=$(this).parent().parent();
				if(colspan>1)
				{
					$(this).parent().attr('colspan',1);
					
					for(var i=1;i<colspan;i++)
						$("<td class='jours'></td>").insertAfter($(this).parent());
				}
				setDropable(tr);
			}
		});
	}
	
	//Fonction qui permet de drop sur une ligne du tableau
	function setDropable(ligne)
	{
		ligne.find('.jours').droppable({
			drop: function(e, ui) {
				
				var date_parcourt=new Date(ladate.getTime());
				
				if (affichage == "semaine")
					var index=$(this).prevAll("td").length;
				else 
					var index=$(this).prevAll("td").length*2;
				if(index%2==1)//impair == aprem
				{
					date_parcourt.setHours(13);
				}
				else//matin
				{
					date_parcourt.setHours(8);
				}
				date_parcourt.setMinutes(0);
				date_parcourt.setSeconds(0);
				
				date_parcourt.setDate(date_parcourt.getDate()+index/2);
				$("#dialText").html("Nouvelle date de début: "+date_parcourt.toLocaleString()+" ?");
				$("#dialog").dialog({ 
					autoOpen: false,
					height: "auto",
					width: 350,
					modal: true,
					buttons: {
						"Valider": function() {
							
							if (affichage == "semaine")
								majEssaiDateDebut(ui.draggable.find("p").attr("id"),date_parcourt.getTime());
							else 
								majEssaiDateDebut(ui.draggable.find("p").attr("id"),date_parcourt.getTime()+1);
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
							creerTab();
						}
					}
				});
				$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
				$("#dialog").dialog('open');							
			}
		});
	}
	
	//pour le visuel
	$("#dialog").hide();
	
	var timer; //timer qui recrée le tableau (maj) toutes les x sec
	var timerGlob; //timer qui recharge la page
	var indexAct;
	
	var tab_jour=new Array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
	var tab_mois=new Array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
	var tab_mois_abr=new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug","Sep","Oct","Nov","Dec");
	var tabWeek;

	//La date doit être un lundi 
	var ladate=getMonday(new Date());
		
	//Permet de récuprere la date actuel où l'utilisateur se positionne
	var url='focusDate.php';
	$.get( url, function(data) {
		
		data = JSON.parse(data);	
		ladate.setDate(ladate.getDate()+(data['jour']));
		ladate.setDate(ladate.getDate()+7*(data['semaine']));
		ladate.setDate(ladate.getDate()+(data['mois']));
	})
	.done(function(){
		//Si tout s'est bien passé
		checkDate();
		creerTab();	
		
	})
	.fail(function(data) {
		//Si échec
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-danger'><strong>Erreur Ajax 7</strong></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	});
	
	//Initialisation de la date
	ladate.setSeconds(0);
	ladate.setMinutes(0);
	ladate.setHours(0);
	//ladate.setMilliseconds(0);
	
	var date_act=new Date();
	date_act.setSeconds(0);
	date_act.setMinutes(0);
	date_act.setHours(0);
	date_act.setMilliseconds(0);
	
	//multiselect
	$('#chkveg').multiselect({
		nonSelectedText: "Aucun",
		selectAllText: "Tout sélectionner",
		allSelectedText:"Tous",
		nSelectedText:"",
		numberDisplayed:0,
        includeSelectAllOption: true
    });
	var typeEssai=$('#chkveg').val();
	//Récuperation des types d'essais au changement
    $('#chkveg').change(function(){
		typeEssai=$('#chkveg').val();
		setFiltreSession("essai",typeEssai);
		creerTab();
    });
	
	//Récupération des moyens sélectionnés
	$('#moyenCheck').multiselect({
		nonSelectedText: "Aucun",
		selectAllText: "Tout sélectionner",
		allSelectedText:"Tous",
		nSelectedText:"",
		numberDisplayed:0,
        includeSelectAllOption: true
    });
	var moyenSelect=$('#moyenCheck').val();
	
	//Récupération des moyens au changement
    $('#moyenCheck').change(function(){
		moyenSelect=$('#moyenCheck').val();
		setFiltreSession("moyen",moyenSelect);
		creerTab();
    });
	
	//Evenement au clic sur le bouton semaine précedente
	$("#sem_prec").click(function(){
		var url='focusDate.php?semaine=0';
		$.get( url, function(data) {
			ladate.setDate(ladate.getDate()-7);
			checkDate();
			creerTab();
		});
	});
	
	//Evenement au clic sur le bouton semaine suivante
	$("#sem_suiv").click(function(){
		var url='focusDate.php?semaine=1';
		$.get( url, function(data) {
			ladate.setDate(ladate.getDate()+7);
			checkDate();
			creerTab();
		});
	});	
	
	//On affiche le bouton de navigation des jours seulement pour l'affichage hebdo
	if (affichage == "semaine"){
		//Evenement au clic sur le bouton jour suivant
		$("#jour_suiv").click(function(){
			var url='focusDate.php?jour=1';
			$.get( url, function(data) {
				ladate.setDate(ladate.getDate()+1);
				checkDate();
				creerTab();
			});
		});
		//Evenement au clic sur le bouton jour précédent
		$("#jour_prec").click(function(){
		
			var url='focusDate.php?jour=0';
			$.get( url, function(data) {
				ladate.setDate(ladate.getDate()-1);
				checkDate();
				creerTab();
			});
		});
		
	}
	
	//Si l'affichage est au mois, les boutonds de navigation entre les mois doivent apparaître
	//Si l'utilisateur clic sur le mois précedent et que le jour du mois est supérieur à 1
	//Alors ladate doit être le premier jour du mois
	//Sinon affichage du mois précédent
	if (affichage == "mois"){
		
		$("#mois_suiv").click(function(){
			var decalage = ladate.getDate();
			//Si la date en cours est le premier jour du mois
			if (decalage == 1){
				//On incémente la date d'un mois
				ladate.setMonth(ladate.getMonth()+1);
				//On retire un jour a la date
				ladate.setDate(ladate.getDate()-1);
				//Ainsi il est possible de connaître le nombre de jour dans le mois
				var jour = ladate.getDate();
				//Le nombre de jour de décalage ets le nombre de jour du mois
				decalage = jour;
				//On mets la date au bon jour
				ladate.setDate(ladate.getDate()+1);				

			}
			else {
				
				//On incémente la date d'un mois
				ladate.setMonth(ladate.getMonth()+1);
				//On met la date au premier jour du mois
				ladate.setDate(1);
				//ON enleve un jour
				ladate.setDate(ladate.getDate()-1);
				//On connait donc le nombre de jour dans le mois
				var jour = ladate.getDate();
				//Le décalage de jour est la différence entre le nombre total de jour dans le mois et le jour actuel
				decalage = jour - decalage;
				//Plus un pour avoir le premier jour
				decalage += 1;
				//On remet la date au bon jour
				ladate.setDate(ladate.getDate()+1);
				
			}
			var url='focusDate.php?mois=1&decalage='+decalage;
			$.get( url, function(data) {
				
				checkDate();
				creerTab();
				
			});
			
		});
		  
		$("#mois_prec").click(function(){
			
			var decalage = ladate.getDate();
			//Si la date est le premier jour du mois 
			if (decalage == 1){
				
				//On retire un jour à la date actuelle
				ladate.setDate(ladate.getDate()-1);
				//On connait donc le nombre de jour dans le mois précédent
				var jour = ladate.getDate();
				//Le décalage est donc égal au nombre de jour du mois précedent
				decalage = jour;
				ladate.setDate(1);

			}
			else {
				
				ladate.setDate(1);
				//Dans le cas où la date n'est pas le 1er jour du mois, le décalage est égale au nombre de jour. -1 pour arrivée au premier jour du mois
				decalage -= 1;
				
			}
			
			var url='focusDate.php?mois=0&decalage='+decalage;
			$.get( url, function(data) {
				
				checkDate();
				creerTab();
			});
		});
	}
	
	//Evenement au clic sur le bouton central. Permet de remettre la date a la date du jour
	$("#infos_date").click(function(){
		var url='../essai/focusDate.php?init=1';
		$.get( url, function(data) {
			ladate=getMonday(new Date());
			ladate.setSeconds(0);
			ladate.setMinutes(0);
			ladate.setHours(0);
			checkDate();
			creerTab();
		});
		
	});	
	
	timerGlob=setTimeout(function(){location.reload();}, 3600000); //toutes les heures rechargement integrale de la page;	
	
});
