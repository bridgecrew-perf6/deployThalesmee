<div class="container">
	<div class="page-header">
		<h2>Calibration du 
		<select id="mois" title="Mois séléctionné" >
			<option value="0" >mois précédent</option>
			<option value="1" selected>mois courant</option>
			<option value="2" >mois suivant</option>
		</select>
		</h2>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr >
					<th>Numéro</th>
					<th>Désignation</th>
					<th>Statut</th>
					<th>État</th>
					<th>Date DI</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</thead>
			<tfoot>
				<tr >
					<th>Numéro</th>
					<th>Désignation</th>
					<th>Statut</th>
					<th>État</th>
					<th>Date DI</th>
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
		
		
		loadDatatable();
		
		$("#mois").change(function() {
			loadDatatable();
		});
		
		function loadDatatable()
		{
			$('#tri').dataTable( {
				"bServerSide": true,
				"bDestroy": true,
				"sAjaxSource": "../server_side/vth/servSide_infoFutCalib_vth.php",
				 "aaSorting": [[6,'asc']],
				"fnServerParams": function ( aoData ) {
				  aoData.push( { "name": "select", "value": $("#mois").val() } );
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
		
	});
</script>	
