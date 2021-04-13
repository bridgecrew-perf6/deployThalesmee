<?php 
require('top.php');
$idServ_Labo=$_SESSION['infoUser']['idService'];// service du labo

?>
	<div class="container">
		<div class="page-header">
			<h2>Liste des OF</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead >
							<tr>
								<th>N° OF</th>
								<th>Modèle</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot >
							<tr>
								<th>N° OF</th>
								<th>Modèle</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>		
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
	<script type="text/javascript" charset="utf-8">	
	$(document).ready(function(){
		$('#tri').dataTable( {
			"bServerSide": true,
			"bDestroy": true,
			"fnServerParams": function ( aoData ) {
			  aoData.push({ "name": "idServ_Labo", "value": "<?php echo $idServ_Labo; ?>" } );
			},
			"sAjaxSource": "../server_side/serv_side_list_of.php",
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				$(nRow).css('cursor', 'pointer');
				$(nRow).on('click', function () {
					document.location.href='detailsOf.php?noOf='+aData[0]+'';
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
require('bottom.php');
