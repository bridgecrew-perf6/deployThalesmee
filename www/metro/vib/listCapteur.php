<?php 
require("top.php");
?>
<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
<div class="container">
	<div class="page-header">
		<h2>Liste des capteurs</h2>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr>
					<th>Numéro</th>
					<th>Type</th>
					<th>Désignation</th>		
					<th>Fournisseur</th>
					<th>Modèle</th>
					<th>N°Série</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Numéro</th>
					<th>Type</th>
					<th>Désignation</th>		
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
	<div class="col-md-3"><button onclick="takeFilter()" id="export" class="btn btn-block btn-info btn-lg" >Excel</button></div>
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
<script type="text/javascript" charset="utf-8" src="../js/listInstru.js"></script>
<script>function getFilter(){<?php if(isset($_GET["filter"])) echo 'return "'.$_GET["filter"].'"'; else echo 'return ""';?>}</script>


<?php
require("bottom.php");