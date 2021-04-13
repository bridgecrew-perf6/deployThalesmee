<?php
require("top.php");
require('bottom.php');
require('../conf/connexion_param.php');
$str ="SELECT `numInstru`, `numSerie`, `designation`,`modele`,`localisation`,`marque`,`date`,`motif` FROM `instrument_vib_suppr` ";
$req=mysqli_query($bdd, $str);
?>


<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<link href="../css/styleTable.css" rel="stylesheet">
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>


<div class="container">

<div class="page-header">
		<h2>Liste des instruments supprimés</h2>
		
	</div>

<div class="jumbotron">


<table id="dtBasicExample" class="table table-striped table-bordered table-sm myTable" cellspacing="0" width="100%">
  <thead>
    <tr class="header">
      <th class="th-sm">Numero
      </th>
      <th class="th-sm">Marque
      </th>
      <th class="th-sm">Modele
      </th>
      <th class="th-sm">Localisation
      </th>
      <th class="th-sm">Designation
      </th>
      <th class="th-sm">Serie
      </th>
	  </th>
      <th class="th-sm">Date
      </th>
	  </th>
      <th class="th-sm">Motif
      </th>
    </tr>
  </thead>
   <tbody>
   <?php 
		if(mysqli_num_rows($req) > 1)
		{
			while($lg=mysqli_fetch_object($req))
			{
				echo "<tr>
					<td>".$lg->numInstru."</td>
					<td>".$lg->marque."</td>
					<td>".$lg->modele."</td>
					<td>".$lg->localisation."</td>
					<td>".$lg->designation."</td>
					<td>".$lg->numSerie."</td>
					<td>".$lg->date."</td>
					<td>".$lg->motif."</td>
					</tr>";
			}
		}else if (mysqli_num_rows($req) == 1)
		{
			$lg=mysqli_fetch_object($req);
			echo "<tr>
					<td>".$lg->numInstru."</td>
					<td>".$lg->marque."</td>
					<td>".$lg->modele."</td>
					<td>".$lg->localisation."</td>
					<td>".$lg->designation."</td>
					<td>".$lg->numSerie."</td>
					<td>".$lg->date."</td>
					<td>".$lg->motif."</td>
				</tr>";
			
		}else {
			
			echo "<td colspan=8>Aucun instrument supprimé</td>";
		}
	?>	

		</tbody>
		<tfoot>
		<tr>
      <th>Numero
      </th>
      <th>Marque
      </th>
      <th>Modele
      </th>
      <th>Localisation
      </th>
      <th>Designation
      </th>
      <th>Serie
      </th>
	  </th>
      <th>Date
      </th>
	  </th>
      <th>Motif
      </th>
    </tr>
		</tfoot>
		</table>
	</div>
</div>
			<script>
	$(document).ready(function() {
    $('#dtBasicExample').DataTable( {
        "language": {
    "sProcessing":     "Traitement en cours...",
    "sSearch":         "Rechercher&nbsp;:",
    "sLengthMenu":     "_MENU_ ",
    "sInfo":           "Affichage de l'instrument _START_ &agrave; _END_ sur _TOTAL_ instruments",
    "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
    "sInfoPostFix":    "",
    "sLoadingRecords": "Chargement en cours...",
    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
    "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
    "oPaginate": {
        "sFirst":      "Premier",
        "sPrevious":   "Pr&eacute;c&eacute;dent",
        "sNext":       "Suivant",
        "sLast":       "Dernier"
    },
    "oAria": {
        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
    },
    "select": {
            "rows": {
                _: "%d lignes séléctionnées",
                0: "Aucune ligne séléctionnée",
                1: "1 ligne séléctionnée"
            } 
    }
}
    } );
} );
</script>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	