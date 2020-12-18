<?php
session_start();
//on test que l'utilisateur a le droit d'etre sur la page -> deja log
if(isset($_SESSION["infoUser"]))
{
	//Traitement du formulaire
	if (isset($_POST['ancienPwd'])){ // le formulaire a ete envoye
		require('conf/connexion_param.php'); 

		//Recuperation des donnees de l utilisateur 
		$login=$_SESSION["infoUser"]["login"];
		$idEmp=$_SESSION["infoUser"]["idEmp"];
		$ancienPwd=htmlspecialchars($_POST['ancienPwd']);
		$pwd=htmlspecialchars($_POST['pwd']);
		$confPwd=htmlspecialchars($_POST['confPwd']); //meme si la verif est faite en JavaScript, on reverifie quand meme en php
		
		if($pwd==$confPwd)
		{	
			require('conf/salt.php');
			
			$ancienPwd=$PREFIXE_SALT.sha1($ancienPwd);
			
			
			
			//test si l'utilisateur existe et recuperation des données
			$str="select idUser from UTILISATEUR u where u.logUser='$login' and u.pwdUser='$ancienPwd' and idEmp_EMPLOYE='$idEmp';";
			$req=@mysqli_query($bdd, $str);
			if(!$req) //une erreur dans la requete renvera false
				echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des utilisateurs</strong></div>';
			else
			{
				if(mysqli_num_rows($req)==1)
				{
					$pwd=$PREFIXE_SALT.sha1($pwd); //hashage du mdp
					
					$str="update utilisateur set pwdUser='$pwd' where logUser='$login' and idEmp_EMPLOYE='$idEmp';";
					$req=@mysqli_query($bdd, $str);
					if(!$req) //une erreur dans la requete renvera false
						echo '<div class="alert alert-danger"><strong>Erreur de modification du mot de passe</strong></div>';
					else{ //redirection dans 2s vers la page index qui s'occupera de rediriger vers le bon endroit
						echo "<center><div class='alert alert-success'><strong>La modification a bien été éffectué, redirection dans 2s</strong></div></center>";
						echo "<script>window.setTimeout(\"location=('index.php');\",2000);</script>";
					}
				}
				else //on affiche qu'il y a echec de connexion
					echo '<center><div class="alert alert-warning"><strong>Ancien mot de passe incorrect</strong></div></center>';
			}
		}
		else
			echo '<center><div class="alert alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques</strong></div></center>';
	}
	?>

	<!DOCTYPE html>
	<html lang="fr">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
	 
		<title>Modifier mon mot de passe</title>
		<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
		<!-- Bootstrap core CSS -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="css/starter-template.css" rel="stylesheet">
		

		 <!--[if lt IE 9]>
		<script>
		/* Placeholders.js v3.0.2 -> ajoute le support du placeholder pour IE sans effet pour ceux qui le supporte deja */
	(function(t){"use strict";function e(t,e,r){return t.addEventListener?t.addEventListener(e,r,!1):t.attachEvent?t.attachEvent("on"+e,r):void 0}function r(t,e){var r,n;for(r=0,n=t.length;n>r;r++)if(t[r]===e)return!0;return!1}function n(t,e){var r;t.createTextRange?(r=t.createTextRange(),r.move("character",e),r.select()):t.selectionStart&&(t.focus(),t.setSelectionRange(e,e))}function a(t,e){try{return t.type=e,!0}catch(r){return!1}}t.Placeholders={Utils:{addEventListener:e,inArray:r,moveCaret:n,changeType:a}}})(this),function(t){"use strict";function e(){}function r(){try{return document.activeElement}catch(t){}}function n(t,e){var r,n,a=!!e&&t.value!==e,u=t.value===t.getAttribute(V);return(a||u)&&"true"===t.getAttribute(D)?(t.removeAttribute(D),t.value=t.value.replace(t.getAttribute(V),""),t.className=t.className.replace(R,""),n=t.getAttribute(F),parseInt(n,10)>=0&&(t.setAttribute("maxLength",n),t.removeAttribute(F)),r=t.getAttribute(P),r&&(t.type=r),!0):!1}function a(t){var e,r,n=t.getAttribute(V);return""===t.value&&n?(t.setAttribute(D,"true"),t.value=n,t.className+=" "+I,r=t.getAttribute(F),r||(t.setAttribute(F,t.maxLength),t.removeAttribute("maxLength")),e=t.getAttribute(P),e?t.type="text":"password"===t.type&&M.changeType(t,"text")&&t.setAttribute(P,"password"),!0):!1}function u(t,e){var r,n,a,u,i,l,o;if(t&&t.getAttribute(V))e(t);else for(a=t?t.getElementsByTagName("input"):b,u=t?t.getElementsByTagName("textarea"):f,r=a?a.length:0,n=u?u.length:0,o=0,l=r+n;l>o;o++)i=r>o?a[o]:u[o-r],e(i)}function i(t){u(t,n)}function l(t){u(t,a)}function o(t){return function(){m&&t.value===t.getAttribute(V)&&"true"===t.getAttribute(D)?M.moveCaret(t,0):n(t)}}function c(t){return function(){a(t)}}function s(t){return function(e){return A=t.value,"true"===t.getAttribute(D)&&A===t.getAttribute(V)&&M.inArray(C,e.keyCode)?(e.preventDefault&&e.preventDefault(),!1):void 0}}function d(t){return function(){n(t,A),""===t.value&&(t.blur(),M.moveCaret(t,0))}}function g(t){return function(){t===r()&&t.value===t.getAttribute(V)&&"true"===t.getAttribute(D)&&M.moveCaret(t,0)}}function v(t){return function(){i(t)}}function p(t){t.form&&(T=t.form,"string"==typeof T&&(T=document.getElementById(T)),T.getAttribute(U)||(M.addEventListener(T,"submit",v(T)),T.setAttribute(U,"true"))),M.addEventListener(t,"focus",o(t)),M.addEventListener(t,"blur",c(t)),m&&(M.addEventListener(t,"keydown",s(t)),M.addEventListener(t,"keyup",d(t)),M.addEventListener(t,"click",g(t))),t.setAttribute(j,"true"),t.setAttribute(V,x),(m||t!==r())&&a(t)}var b,f,m,h,A,y,E,x,L,T,N,S,w,B=["text","search","url","tel","email","password","number","textarea"],C=[27,33,34,35,36,37,38,39,40,8,46],k="#7a7a7a",I="placeholdersjs",R=RegExp("(?:^|\\s)"+I+"(?!\\S)"),V="data-placeholder-value",D="data-placeholder-active",P="data-placeholder-type",U="data-placeholder-submit",j="data-placeholder-bound",q="data-placeholder-focus",z="data-placeholder-live",F="data-placeholder-maxlength",G=document.createElement("input"),H=document.getElementsByTagName("head")[0],J=document.documentElement,K=t.Placeholders,M=K.Utils;if(K.nativeSupport=void 0!==G.placeholder,!K.nativeSupport){for(b=document.getElementsByTagName("input"),f=document.getElementsByTagName("textarea"),m="false"===J.getAttribute(q),h="false"!==J.getAttribute(z),y=document.createElement("style"),y.type="text/css",E=document.createTextNode("."+I+" { color:"+k+"; }"),y.styleSheet?y.styleSheet.cssText=E.nodeValue:y.appendChild(E),H.insertBefore(y,H.firstChild),w=0,S=b.length+f.length;S>w;w++)N=b.length>w?b[w]:f[w-b.length],x=N.attributes.placeholder,x&&(x=x.nodeValue,x&&M.inArray(B,N.type)&&p(N));L=setInterval(function(){for(w=0,S=b.length+f.length;S>w;w++)N=b.length>w?b[w]:f[w-b.length],x=N.attributes.placeholder,x?(x=x.nodeValue,x&&M.inArray(B,N.type)&&(N.getAttribute(j)||p(N),(x!==N.getAttribute(V)||"password"===N.type&&!N.getAttribute(P))&&("password"===N.type&&!N.getAttribute(P)&&M.changeType(N,"text")&&N.setAttribute(P,"password"),N.value===N.getAttribute(V)&&(N.value=x),N.setAttribute(V,x)))):N.getAttribute(D)&&(n(N),N.removeAttribute(V));h||clearInterval(L)},100)}M.addEventListener(t,"beforeunload",function(){K.disable()}),K.disable=K.nativeSupport?e:i,K.enable=K.nativeSupport?e:l}(this);
		</script>
		<![endif]-->
		<!-- Just for debugging purposes. Don't actually copy this line! -->
		<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<script src="bootstrap/js/jquery.min.js"></script>
	   </head>

	  <body id='cont' style="background-color:#eef">
	  <center>
		<div class="container">
		<div class="page-header">
			<h2>Modifier mon mot de passe</h2>
		</div>
		
		<form onsubmit="return testPwd()" method="post" action="modifMonPwd.php" class="form-user" role="form">
			<input oncopy="return false" oncut="return false" title="Ancien mot de passe" name="ancienPwd" type="password" class="form-control" placeholder="Ancien mot de passe" required autofocus />
			<input oncopy="return false" oncut="return false" id="pwd" title="Nouveau mot de passe" name="pwd" type="password" class="form-control" placeholder="Nouveau mot de passe" required />
			<input oncopy="return false" oncut="return false" id="confPwd" title="Confirmer nouveau mot de passe" name="confPwd" type="password" class="form-control" placeholder="Confirmer nouveau mot de passe" required />
			<br/>
			<input type="submit" class="btn btn-lg btn-primary " value="Valider" />
			<input type="button" class="btn btn-lg btn-primary " value="Annuler" onclick="document.location.href='index.php'" />
		</form>
	
		  
		  <script>
		  //verifi que le mdp et sa confirmation son identique, indique sinon et n'envoie pas la requete au serveur
		  function testPwd(){
				if(document.getElementById('confPwd').value == document.getElementById('pwd').value)
					return true;
				else{
					var child= document.createElement("center");
					child.innerHTML='<div class="alert alert-warning"><strong>Le mot de passe et la confirmation doivent êtres identiques</strong></div>';
					document.body.insertBefore(child, document.body.firstChild);
					return false;
				}
		  }
		  </script>

		</div> <!-- /container -->
	   </center>

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
	  </body>
	</html>
<?php
}
else
	header("Location: index.php");
?>
