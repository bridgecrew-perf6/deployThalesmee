<?php 
require("top.php");
?>
<div class="container">
	<div class="page-header">
		<h2>Liste des instruments</h2>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr >
					<th>Numéro</th>
					<th>Désignation</th>
					<th>Statut</th>
					<th>Fournisseur</th>
					<th>Modèle</th>
					<th>N°Série</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</thead>
			<tfoot>
				<tr >
					<th>Numéro</th>
					<th>Désignation</th>
					<th>Statut</th>
					<th>Fournisseur</th>
					<th>Modèle</th>
					<th>N°Série</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</tfoot>
			<tbody>
				
			</tbody>
		</table>
	</div>
	<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
</div>
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
<script type="text/javascript" charset="utf-8">
	
	$(document).ready(function(){
		$('#tri').dataTable( {
			"bServerSide": true,
			"sAjaxSource": "../server_side/vib/servSide_listInstruNonDispo_vib.php",
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				$(nRow).css('cursor', 'pointer');
				
				$(nRow).on('click', function () {
				document.location.href='detailsInstru.php?numInstru='+aData[0]+'';
				
			});
			}
		} ).columnFilter();
		$('#tri_filter input').attr("placeholder", "Rechercher");
		$('#tri_filter input').attr("class", "form-control");
		$('#tri_filter input').attr("style", "font-weight:normal;");
		$('#tri_length select').attr("class", "form-control");
		
		
	});
</script>
<?php
require("bottom.php");	
