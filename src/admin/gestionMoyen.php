<?php
require('top.php');
require('../conf/connexion_param.php');
if(isset($_GET["idMoyenSupp"]))
{
	
	$idMoyen=$_GET["idMoyenSupp"];
	$str="delete from moyen where idMoyen='$idMoyen';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert text-center alert-warning"><strong>Suppression impossible, la moyen est peut être utilisée par un instrument</strong></div>';
	else
		echo '<div class="alert text-center alert-success"><strong>Suppression effectuée</strong></div>';
}
elseif(isset($_GET["idMoyenModif"]))
{
	$idMoyen=$_GET["idMoyenModif"];
	
	if(isset($_GET["nomMoyen"])) //modif du nom
	{
		$numMoyen=$_GET["numMoyen"];
		$nomMoyen=$_GET["nomMoyen"];
		$coloneMoyen="nomMoyen";
		if($numMoyen!=1)
			$coloneMoyen.=$numMoyen;
		
		$str="update moyen set $coloneMoyen='$nomMoyen' where idMoyen='$idMoyen';";
		$req=mysqli_query($bdd, $str);
	}
	else //modif du labo
	{
		$idService=$_GET["idService"];
		$str="update moyen set idService_service='$idService' where idMoyen='$idMoyen';";
		$req=mysqli_query($bdd, $str);
	}
	
}
elseif(isset($_GET["nouvMoyen"]))
{
	$nomMoyen=$_GET["nouvMoyen"];
	if(isset($_GET["idService"]))
	{
		$idService=$_GET["idService"];
		$str="insert into moyen values(null,'$nomMoyen',null,null,null,null,'$idService');";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			echo "<div class='alert alert-error'><strong>Erreur d'ajout du moyen</strong></div>";
		else
			echo "<div class='alert text-center alert-success'><strong>Ajout effectuée</strong></div>";
	}
	else
		echo "<div class='alert text-center alert-warning'><strong>Veuillez choisir un labo</strong></div>";
	
}

$str="select idMoyen,nomMoyen,nomMoyen2,nomMoyen3,nomMoyen4,nomMoyen5,idService,nomService from moyen m, service s
where m.idService_service=s.idService";
$req=mysqli_query($bdd, $str);
if(!$req)
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des moyen</strong></div>';
else
{
	$str="select idService,nomService from service";
	$reqLabo=mysqli_query($bdd, $str);
?>
	<div class="container">
		<div class="page-header">
			<h2>Gestion des moyens</h2>
		</div>
		<p>
			<input id="btnajMoyen" type="button" class="btn btn-primary btn-lg" value="Ajouter un moyen" onclick="ajouterMoyen()" />
			<div id="ajMoyen" style="display:none;" class="container theme-showcase" role="main">
				<h4>Ajouter un moyen</h4>
				<div class="jumbotron">
					<form method="GET" action="gestionMoyen.php" class="form-user" role="form">
						<p>
							<input id="nouvMoyen" type="text" name="nouvMoyen" class="form-control" placeholder="Nouveau moyen" required />
							<select id="labo" title="Laboratoire" class="form-control" name="idService">
								<option value="-1" disabled selected>Choisir le laboratoire</option>
								<?php
								while($lg=mysqli_fetch_object($reqLabo))
									echo '<option value="'.$lg->idService.'">'.$lg->nomService.'</option>';
								?>
							</select>
						</p>
						<input type="submit" class="btn btn-primary btn-lg" value="Ajouter" />
						<input type="button" class="btn btn-primary btn-lg" value="Annuler" onclick="ajouterMoyen()"/>
					</form>
				</div>
			</div>
		
		</p>
		<div class="container theme-showcase" role="main">
			<h4>Listes des moyens</h4>
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Nom du moyen</th>
								<th>Nom alternatif 2</th>
								<th>Nom alternatif 3</th>
								<th>Nom alternatif 4</th>
								<th>Nom alternatif 5</th>
								
								<th>Labo</th>
								<th style="text-align:right">Supprimer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Nom du moyen</th>
								<th>Nom alternatif 2</th>
								<th>Nom alternatif 3</th>
								<th>Nom alternatif 4</th>
								<th>Nom alternatif 5</th>
								
								<th>Labo</th>
								
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idMoyen=$lg->idMoyen;
									$nomMoyen=$lg->nomMoyen;
									$nomMoyen2=$lg->nomMoyen2;
									$nomMoyen3=$lg->nomMoyen3;
									$nomMoyen4=$lg->nomMoyen4;
									$nomMoyen5=$lg->nomMoyen5;
									$idService=$lg->idService;
									$nomService=$lg->nomService;
									
									echo "<tr>";
										echo "<td style='cursor:pointer;' onclick='modifMoyen(this,\"$idMoyen\",1);'>$nomMoyen</td>";
										echo "<td style='cursor:pointer;' onclick='modifMoyen(this,\"$idMoyen\",2);'>$nomMoyen2</td>";
										echo "<td style='cursor:pointer;' onclick='modifMoyen(this,\"$idMoyen\",3);'>$nomMoyen3</td>";
										echo "<td style='cursor:pointer;' onclick='modifMoyen(this,\"$idMoyen\",4);'>$nomMoyen4</td>";
										echo "<td style='cursor:pointer;' onclick='modifMoyen(this,\"$idMoyen\",5);'>$nomMoyen5</td>";
										echo "<td style='cursor:pointer;' onclick='modifLabo(this,\"$idMoyen\",\"$idService\");'>$nomService</td>";
										echo "<td onclick='confirmSuppr(\"$idMoyen\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div><!-- /.container -->
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
	<script type="text/javascript" charset="utf-8">
	var focus=false;
	function ajouterMoyen() //fait apparaitre / cache le formulaire d'ajout d'un moyen
	{
		var form =document.getElementById('ajMoyen');
		var btn =document.getElementById('btnajMoyen');
		if(form.style.display == "none")
		{
			form.style.display = "";
			btn.style.display = "none";
			document.getElementById('nouvMoyen').focus();
		}
		else{
			form.style.display = "none";
			btn.style.display = "";
		
		}
	}

	function confirmSuppr(idMoyen)
	{
		if(confirm("Voulez vous vraiment supprimer ce moyen ?"))
			document.location.href='gestionMoyen.php?idMoyenSupp='+idMoyen;
	}

	function modifMoyen(obj,idMoyen,numMoyen)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html("<input type='text' id='modif' value='"+nom+"' class='form-control' placeholder='Nom de la moyen' />");
			$(obj).children().first().focus();
			
			$(obj).children().first().on("focusout",function(){
				document.location.href='gestionMoyen.php?idMoyenModif='+idMoyen+'&numMoyen='+numMoyen+'&nomMoyen='+document.getElementById("modif").value;
			});
			$(document).keypress(function(e) {
				if(e.which == 13) {
					document.location.href='gestionMoyen.php?idMoyenModif='+idMoyen+'&numMoyen='+numMoyen+'&nomMoyen='+document.getElementById("modif").value;
				}
			});
		}
	}
	
	function modifLabo(obj,idMoyen,idService)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html($("#labo").clone());
			var list=$(obj).children().first();
			list.val(idService);
			list.focus();
			$(obj).children().first().on("focusout",function(){
				if(list.val()!=null)
					document.location.href='gestionMoyen.php?idMoyenModif='+idMoyen+'&idService='+list.val();
				else
				{
					list.focus();
					alert("Veuillez choisir un labo");
				}
			});
			
			$(document).keypress(function(e) {
				if(e.which == 13) {
					if(list.val()!=null)
						document.location.href='gestionMoyen.php?idMoyenModif='+idMoyen+'&idService='+list.val();
					else
					{
						list.focus();
						alert("Veuillez choisir un labo");
					}
				}
			});
		}
	}

	$(document).ready(function() {
		
		$('#tri').dataTable().columnFilter();
		$('#tri_filter input').attr("placeholder", "Rechercher");
		$('#tri_filter input').attr("class", "form-control");
		$('#tri_filter input').attr("style", "font-weight:normal;");
		$('#tri_length select').attr("class", "form-control");
	} );
	</script>
<?php
}
require('bottom.php');