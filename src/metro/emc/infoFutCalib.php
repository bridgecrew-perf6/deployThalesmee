<?php 
require("top.php");
$date = new DateTime();
$dateDeb=$date->format('01/m/Y');
$dateFin=$date->format('t/m/Y');
?>
<link href="../calendrier/calendrier.css" rel="stylesheet" />
<div class="container">
	<div class="page-header">
		<h2 class="form-inline">Calibration du 
		<input placeholder="Date de début" value="<?php echo $dateDeb; ?>" type="text"  id="dDeb" class="calendrier form-control " size="8" />
		au
		<input placeholder="Date de fin" value="<?php echo $dateFin; ?>" type="text"  id="dFin" class="calendrier form-control" size="8" />
		</h2>
		<input type="button" id="valD" value="Valider" class="btn btn-primary btn-lg"/>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr >
					<th>Numéro</th>
					<th>Equipement</th>
					<th>Fonction</th>
					<th>Fabricant</th>
					<th>Modéle</th>
					<th>Caractéristiques</th>
					<th>Date FI</th>
					<th>Statut</th>
					<th>T | I</th>
				</tr>
			</thead>
			<tfoot>
				<tr >
					<th>Numéro</th>
					<th>Equipement</th>
					<th>Fonction</th>
					<th>Fabricant</th>
					<th>Modèle</th>
					<th>Caractéristiques</th>
					<th>Date FI</th>
					<th>Statut</th>
					<th>T | I</th>
				</tr>
			</tfoot>
			<tbody>
				
			</tbody>
		</table>
	</div>
	<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	<input type="button" value="Excel" class="btn btn-primary btn-lg pull-right" id="export"/>
</div>
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script type="text/javascript" charset="utf-8">
	
	$(document).ready(function(){
		loadDatatable();
		
		$("#valD").on('click',function() {
			loadDatatable();
		});
		
		
		function loadDatatable()
		{
			$('#tri').dataTable( {
				"bServerSide": true,
				"bDestroy": true,
				"sAjaxSource": "../server_side/emc/servSide_infoFutCalib_emc.php",
				 "aaSorting": [[6,'asc']],
				"fnServerParams": function ( aoData ) {
				  aoData.push( { "name": "dDeb", "value": $("#dDeb").val() },{ "name": "dFin", "value": $("#dFin").val() } );
				},
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
			
		}
		
		$("#export").on('click', function () {
			document.location.href='exportFutCalib.php?dDeb='+$("#dDeb").val()+'&dFin='+$("#dFin").val();
		});
	});
</script>
<?php
require("bottom.php");

