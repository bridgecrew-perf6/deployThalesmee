function redirection(){
	
	document.location.href="index.php";
}

swal({
	
	title : "Informations validées",
	text : "Redirection dans quelques instants",
	icon : "success"
	
});
setTimeout(redirection, 1250);