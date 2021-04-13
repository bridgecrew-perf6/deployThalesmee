<?php
require('top.php');
require('../conf/connexion_param.php');
if(isset($_GET["idLocalSupp"]))
{
	
	$idLocal=$_GET["idLocalSupp"];
	$str="delete from localisation where idLocal='$idLocal';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert text-center alert-warning"><strong>Suppression impossible, la localisation est peut être utilisée par un instrument</strong></div>';
	else
		echo '<div class="alert text-center alert-success"><strong>Suppression effectuée</strong></div>';
}
elseif(isset($_GET["idLocalModif"]))
{
	$idLocal=$_GET["idLocalModif"];
	
	if(isset($_GET["nomLocal"])) //modif du nom
	{
		$nomLocal=$_GET["nomLocal"];
		$str="update localisation set nomLocal='$nomLocal' where idLocal='$idLocal';";
		$req=mysqli_query($bdd, $str);
	}
	else //modif du labo
	{
		$idLabo=$_GET["idLabo"];
		$str="update localisation set idLabo_labo='$idLabo' where idLocal='$idLocal';";
		$req=mysqli_query($bdd, $str);
	}
	
}
elseif(isset($_GET["nouvLocal"]))
{
	$nomLocal=$_GET["nouvLocal"];
	if(isset($_GET["idLabo"]))
	{
		$idLabo=$_GET["idLabo"];
		$str="insert into localisation values(null,'$nomLocal','$idLabo');";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			echo "<div class='alert alert-error'><strong>Erreur d'ajout de la localisation</strong></div>";
		else
			echo "<div class='alert text-center alert-success'><strong>Ajout effectuée</strong></div>";
	}
	else
		echo "<div class='alert text-center alert-warning'><strong>Veuillez choisir un labo</strong></div>";
	
}

$str="select idLocal,nomLocal,idLabo,nomLabo from localisation l, labo la
where l.idLabo_labo=la.idLabo";
$req=mysqli_query($bdd, $str);
if(!$req)
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des localisations</strong></div>';
else
{
	$str="select idlabo,nomLabo from labo";
	$reqLabo=mysqli_query($bdd, $str);
?>
	<div class="container">
		<div class="page-header">
			<h2>Gestion des localisations</h2>
		</div>
		<p>
			<input id="btnajLocal" type="button" class="btn btn-primary btn-lg" value="Ajouter une localisation" onclick="ajouterLocal()" />
			<div id="ajLocal" style="display:none;" class="container theme-showcase" role="main">
				<h4>Ajouter une localisation</h4>
				<div class="jumbotron">
					<form method="GET" action="gestionLocal.php" class="form-user" role="form">
						<p>
							<input id="nouvLocal" type="text" name="nouvLocal" class="form-control" placeholder="Nouvelle localisation" required />
							<select id="labo" title="Laboratoire" class="form-control" name="idLabo">
								<option value="-1" disabled selected>Choisir le laboratoire</option>
								<?php
								while($lg=mysqli_fetch_object($reqLabo))
									echo '<option value="'.$lg->idlabo.'">'.$lg->nomLabo.'</option>';
								?>
							</select>
						</p>
						<input type="submit" class="btn btn-primary btn-lg" value="Ajouter" />
						<input type="button" class="btn btn-primary btn-lg" value="Annuler" onclick="ajouterLocal()"/>
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
								<th>Localisation</th>
								<th>Labo</th>
								<th style="text-align:right">Supprimer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Localisation</th>
								<th>Labo</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idLocal=$lg->idLocal;
									$nomLocal=$lg->nomLocal;
									$idLabo=$lg->idLabo;
									$nomLabo=$lg->nomLabo;
									
									echo "<tr>";
										echo "<td style='cursor:pointer;' onclick='modifLocal(this,\"$idLocal\");'>$nomLocal</td>";
										echo "<td style='cursor:pointer;' onclick='modifLabo(this,\"$idLocal\",\"$idLabo\");'>$nomLabo</td>";
										echo "<td onclick='confirmSuppr(\"$idLocal\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
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
	function ajouterLocal() //fait apparaitre / cache le formulaire d'ajout d'un moyen
	{
		var form =document.getElementById('ajLocal');
		var btn =document.getElementById('btnajLocal');
		if(form.style.display == "none")
		{
			form.style.display = "";
			btn.style.display = "none";
			document.getElementById('nouvLocal').focus();
		}
		else{
			form.style.display = "none";
			btn.style.display = "";
		
		}
	}

	function confirmSuppr(idLocal)
	{
		if(confirm("Voulez vous vraiment supprimer ce moyen ?"))
			document.location.href='gestionLocal.php?idLocalSupp='+idLocal;
	}

	function modifLocal(obj,idLocal)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html("<input type='text' id='modif' value='"+nom+"' class='form-control' placeholder='Nom de la localisation' />");
			$(obj).children().first().focus();
			
			$(obj).children().first().on("focusout",function(){
				document.location.href='gestionLocal.php?idLocalModif='+idLocal+'&nomLocal='+document.getElementById("modif").value;
			});
			$(document).keypress(function(e) {
				if(e.which == 13) {
					document.location.href='gestionLocal.php?idLocalModif='+idLocal+'&nomLocal='+document.getElementById("modif").value;
				}
			});
		}
	}
	
	function modifLabo(obj,idLocal,idLabo)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html($("#labo").clone());
			var list=$(obj).children().first();
			list.val(idLabo);
			list.focus();
			$(obj).children().first().on("focusout",function(){
				if(list.val()!=null)
					document.location.href='gestionLocal.php?idLocalModif='+idLocal+'&idLabo='+list.val();
				else
				{
					list.focus();
					alert("Veuillez choisir un labo");
				}
			});
			
			$(document).keypress(function(e) {
				if(e.which == 13) {
					if(list.val()!=null)
						document.location.href='gestionLocal.php?idLocalModif='+idLocal+'&idLabo='+list.val();
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