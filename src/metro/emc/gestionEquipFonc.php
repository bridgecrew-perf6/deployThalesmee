<?php
require('top.php');
require('../conf/connexion_param.php');
echo "<script>var menu=0;</script>";
if(isset($_GET["idEquipSupp"]))
{
	$idEquip=$_GET["idEquipSupp"];
	$str="delete from equipement_emc where idEquip='$idEquip';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert text-center alert-warning"><strong>Suppression impossible, l\'équipement est peut être utilisé par un instrument</strong></div>';
	else
		echo '<div class="alert text-center alert-success"><strong>Suppression effectuée</strong></div>';
	echo "<script>menu=1;</script>";
}
elseif(isset($_GET["idEquipModif"]))
{
	$idEquip=$_GET["idEquipModif"];
	
	$nomEquip=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["nomEquip"])));
	$str="update equipement_emc set nomEquip='$nomEquip' where idEquip='$idEquip';";
	$req=mysqli_query($bdd, $str);
	echo "<script>menu=1;</script>";
}
elseif(isset($_GET["nouvEquip"]))
{
	$nomEquip=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["nouvEquip"])));

	$str="insert into equipement_emc values(null,'$nomEquip');";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-error'><strong>Erreur d'ajout de l\'équipement</strong></div>";
	else
		echo "<div class='alert text-center alert-success'><strong>Ajout effectuée</strong></div>";
	echo "<script>menu=1;</script>";
}

elseif(isset($_GET["idFoncSupp"]))
{
	
	$idDes=$_GET["idFoncSupp"];
	$str="delete from designation_emc where idDes='$idDes';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert text-center alert-warning"><strong>Suppression impossible, la fonction est peut être utilisée par un instrument</strong></div>';
	else
		echo '<div class="alert text-center alert-success"><strong>Suppression effectuée</strong></div>';
	echo "<script>menu=2;</script>";
}
elseif(isset($_GET["idFoncModif"]))
{
	$idDes=$_GET["idFoncModif"];
	
	$fonction=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["fonction"])));
	$str="update designation_emc set fonction='$fonction' where idDes='$idDes';";
	$req=mysqli_query($bdd, $str);
	echo "<script>menu=2;</script>";
}
elseif(isset($_GET["nouvFonc"]))
{
	$fonction=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["nouvFonc"])));
	$equipFonc=$_GET["equipFonc"];
	$str="insert into designation_emc values(null,'$fonction','$equipFonc');";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-error'><strong>Erreur d'ajout de la localisation</strong></div>";
	else
		echo "<div class='alert text-center alert-success'><strong>Ajout effectuée</strong></div>";
	
	echo "<script>menu=2;</script>";
}

$str="select idEquip,nomEquip from equipement_emc";
$reqEquip=mysqli_query($bdd, $str);

$str="select idDes,fonction, nomEquip from designation_emc d, equipement_emc e
where e.idEquip=d.idEquip_equipement_emc";
$reqFonc=mysqli_query($bdd, $str);
if(!$reqEquip || !$reqFonc)
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des fonctions</strong></div>';
else
{
	$tabEquip=array();
	$i=0;
	while($lg=mysqli_fetch_object($reqEquip))
	{
		$tabEquip[$i]=$lg;
		$i++;
	}
		
?>
	<div class="container" id="menuP">
		<div class="page-header">
			<h2>Gestion des équipements/fonctions</h2>
		</div>
		<div class="text-center">
			<input type="button" id="btnGestEquip" value="Gestion des équipements" class="btn btn-primary btn-lg"/>
			<input type="button" id="btnGestFonc" value="Gestion des fonctions" class="btn btn-primary btn-lg"/>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
		</div>
	</div>
	<div class="container" id="menuE">
		<div class="page-header">
			<h2>Gestion des équipements</h2>
		</div>
		<p>
			<input id="btnajEquip" type="button" class="btn btn-primary btn-lg" value="Ajouter un équipement" onclick="ajouterEquip()" />
			<div id="ajEquip" style="display:none;" class="container theme-showcase" role="main">
				<h4>Ajouter un équipement</h4>
				<div class="jumbotron">
					<form method="GET" action="gestionEquipFonc.php" class="form-user" role="form">
						<p>
							<input id="nouvEquip" type="text" name="nouvEquip" class="form-control" placeholder="Nouvelle fonction" required />
						</p>
						<input type="submit" class="btn btn-primary btn-lg" value="Ajouter" />
						<input type="button" class="btn btn-primary btn-lg" value="Annuler" onclick="ajouterEquip()"/>
					</form>
				</div>
			</div>
		</p>
		<div class="container theme-showcase" role="main">
			<h4>Listes des equipements</h4>
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Equipement</th>
								<th style="text-align:right">Supprimer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Equipement</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								foreach($tabEquip as $lg)
								{
									$idEquip=$lg->idEquip;
									$nomEquip=$lg->nomEquip;
									
									echo "<tr>";
										echo '<td style="cursor:pointer;" onclick="modifEquip(this,\''.$idEquip.'\');">'.$nomEquip.'</td>';
										echo "<td onclick='confirmSupprE(\"$idEquip\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="text-center">
			<input type="button" value="Retour" class="btn btn-primary btn-lg" id="btnERetour"/>
		</div>
	</div><!-- /.container -->
	
	<div class="container" id="menuF">
		<div class="page-header">
			<h2>Gestion des fonctions</h2>
		</div>
		<p>
			<input id="btnajFonc" type="button" class="btn btn-primary btn-lg" value="Ajouter une fonction" onclick="ajouterFonc()" />
			<div id="ajFonc" style="display:none;" class="container theme-showcase" role="main">
				<h4>Ajouter une fonction</h4>
				<div class="jumbotron">
					<form method="GET" action="gestionEquipFonc.php" class="form-user" role="form">
						<p>
							<input id="nouvFonc" type="text" name="nouvFonc" class="form-control" placeholder="Nouvelle fonction" required />
							<select id="equipFonc" title="Equipement" class="form-control" name="equipFonc" required>
								<option value="" disabled selected>Equipement</option>
								<?php
									foreach($tabEquip as $lg)
									{
										$idEquip=$lg->idEquip;
										$nomEquip=$lg->nomEquip;
										echo '<option value="'.$lg->idEquip.'">'.$lg->nomEquip.'</option>';
									}
								?>
							</select>
							
						</p>
						<input type="submit" class="btn btn-primary btn-lg" value="Ajouter" />
						<input type="button" class="btn btn-primary btn-lg" value="Annuler" onclick="ajouterFonc()"/>
					</form>
				</div>
			</div>
		
		</p>
		<div class="container theme-showcase" role="main">
			<h4>Listes des fonctions</h4>
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri2">
						<thead>
							<tr >
								<th>Fonctions</th>
								<th>Equipement</th>
								<th style="text-align:right">Supprimer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Fonctions</th>
								<th>Equipement</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($reqFonc))
								{
									$idDes=$lg->idDes;
									$fonction=$lg->fonction;
									$nomEquip=$lg->nomEquip;
									echo "<tr>";
										echo '<td style="cursor:pointer;" onclick="modifFonc(this,\''.$idDes.'\');">'.$fonction.'</td>';
										echo '<td>'.$nomEquip.'</td>';
										echo "<td onclick='confirmSupprF(\"$idDes\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="text-center">
			<input type="button" value="Retour" class="btn btn-primary btn-lg" id="btnFRetour"/>
		</div>
	</div><!-- /.container -->
	
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
	<script type="text/javascript" charset="utf-8">
	var focus=false;
	function ajouterEquip() //fait apparaitre / cache le formulaire d'ajout d'un moyen
	{
		var form =document.getElementById('ajEquip');
		var btn =document.getElementById('btnajEquip');
		if(form.style.display == "none")
		{
			form.style.display = "";
			btn.style.display = "none";
			document.getElementById('nouvEquip').focus();
		}
		else{
			form.style.display = "none";
			btn.style.display = "";
		
		}
	}

	function confirmSupprE(idEquip)
	{
		if(confirm("Voulez vous vraiment supprimer cet équipement ?"))
			document.location.href='gestionEquipFonc.php?idEquipSupp='+idEquip;
	}

	function modifEquip(obj,idEquip)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html('<input type="text" id="modif" value="'+nom+'" class="form-control" placeholder="Nom de l\'équipement" />');
			$(obj).children().first().focus();
			
			$(obj).children().first().on("focusout",function(){
				document.location.href='gestionEquipFonc.php?idEquipModif='+idEquip+'&nomEquip='+document.getElementById("modif").value;
			});
			$(document).keypress(function(e) {
				if(e.which == 13) {
					document.location.href='gestionEquipFonc.php?idEquipModif='+idEquip+'&nomEquip='+document.getElementById("modif").value;
				}
			});
		}
	}
	
	function ajouterFonc() //fait apparaitre / cache le formulaire d'ajout d'un moyen
	{
		var form =document.getElementById('ajFonc');
		var btn =document.getElementById('btnajFonc');
		if(form.style.display == "none")
		{
			form.style.display = "";
			btn.style.display = "none";
			document.getElementById('nouvFonc').focus();
		}
		else{
			form.style.display = "none";
			btn.style.display = "";
		
		}
	}

	function confirmSupprF(idDes)
	{
		if(confirm("Voulez vous vraiment supprimer cette fonction ?"))
			document.location.href='gestionEquipFonc.php?idFoncSupp='+idDes;
	}

	function modifFonc(obj,idDes)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html('<input type="text" id="modif" value="'+nom+'" class="form-control" placeholder="Nom de la fonction" />');
			$(obj).children().first().focus();
			
			$(obj).children().first().on("focusout",function(){
				document.location.href='gestionEquipFonc.php?idFoncModif='+idDes+'&fonction='+document.getElementById("modif").value;
			});
			$(document).keypress(function(e) {
				if(e.which == 13) {
					document.location.href='gestionEquipFonc.php?idFoncModif='+idDes+'&fonction='+document.getElementById("modif").value;
				}
			});
		}
	}
	
	$(document).ready(function() {
		
		switch(menu)
		{
			case 0:
				$("#menuP").show();
				$("#menuE").hide();
				$("#menuF").hide();
			break;
			case 1:
				$("#menuP").hide();
				$("#menuE").show();
				$("#menuF").hide();
			break;
			case 2:
				$("#menuP").hide();
				$("#menuE").hide();
				$("#menuF").show();
			break;
		}
		
		$("#btnGestEquip").on('click', function(){
			$("#menuP").hide();
			$("#menuE").show();
		});
		$("#btnERetour").on('click', function(){
			$("#menuP").show();
			$("#menuE").hide();
		});
		
		$("#btnGestFonc").on('click', function(){
			$("#menuP").hide();
			$("#menuF").show();
		});
		$("#btnFRetour").on('click', function(){
			$("#menuP").show();
			$("#menuF").hide();
		});
		
		$('#tri').dataTable().columnFilter();
		$('#tri_filter input').attr("placeholder", "Rechercher");
		$('#tri_filter input').attr("class", "form-control");
		$('#tri_filter input').attr("style", "font-weight:normal;");
		$('#tri_length select').attr("class", "form-control");
		
		$('#tri2').dataTable().columnFilter();
		$('#tri2_filter input').attr("placeholder", "Rechercher");
		$('#tri2_filter input').attr("class", "form-control");
		$('#tri2_filter input').attr("style", "font-weight:normal;");
		$('#tri2_length select').attr("class", "form-control");
		
		//quand on change le type d'équipement, on propose les fonctions associés
		$('#equip').on('change', function() {
			var url="./ajaxModifInstru.php?equip="+this.value;
			$.getJSON( url, function(data) {
				//supprime les anciens option du select fonction
				$('#fonc option').filter(function() {
					return +this.value > 0;
				}).remove();
				//ajoute les option correspondant a l'equipement choisi
				$.each(data["equip"], function(key, value) {   
					$('#fonc')
					.append($("<option></option>")
					.attr("value",value["idDes"])
					.text(value["fonction"])); 
				});
			})
			.fail(function() {
				alert("Erreur Ajax");
			});
		});
	} );
	</script>
<?php
}
require('bottom.php');