
swal({
	
	title : "Informations validées",
	text : "Redirection dans quelques instants",
	icon : "error"
	
}).then ((value)=> {
	
	document.location.href="../essai/indicateurs.php";
	
});
