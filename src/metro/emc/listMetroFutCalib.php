<?php
require("top.php");
?>
<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
<div class="container">
	<div class="page-header">
		<h2>Futures calibrations</h2>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr >
					<th>Jours restants</th>
					<th>Date FI</th>
					<th>Equipement</th>
					<th>Fonction</th>
					<th>Numéro</th>
				</tr>
			</thead>
			<tfoot>
				<tr >
					<th>Jours restants</th>
					<th>Date FI</th>
					<th>Equipement</th>
					<th>Fonction</th>
					<th>Numéro</th>
				</tr>
			</tfoot>
			<tbody>
			</tbody>
		</table>
	</div>
	<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
</div>
<div id="dialog" title="Calibration">
	<div id="messMetro"></div>	
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
<script type="text/javascript" language="javascript" src="../js/listMetro.js"></script>
<script>
	$(document).ready(function(){
		initListMetroFutCalib("emc");
	});
</script>
<?php
require("bottom.php");