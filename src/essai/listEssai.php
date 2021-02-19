<?php 
require('top.php');
require('../conf/connexion_param.php'); 
$saisie = false;
$idEtat = "";
$libele_etat = array();

$str = "SELECT idEtat, nomEtat FROM etat WHERE idEtat >= 20 AND idEtat < 30";
$req = mysqli_query($bdd, $str);
while ($lg=mysqli_fetch_object($req))
{
	if (isset($_POST[str_replace(" ", "", $lg->nomEtat)]))
	{
		$saisie = true;
		$idEtat.=$lg->idEtat.",";
		array_push($libele_etat, str_replace(" ", "", $lg->nomEtat));
	}
}

if (!$saisie)
{
	$idEtat="20,21,22,23,24,25,26,27";

}else
{
	//On enlève le dernier caractère qui est une virgule
	$idEtat = substr($idEtat,0,-1);
	if ($idEtat == "20,21,22,23,24,25,26,27") $saisie = !$saisie;
}

$idServ_Labo=$_SESSION['infoUser']['idService'];

?>
<style>
	.form-control{
		width : calc(100% - 10px);
	}
</style>
<div class="container-fluid theme-showcase" role="main">
	<div class="page-header">
		<h2>Liste des essais</h2>
	</div>
	<div class="jumbotron">
		<form id= "form" method="post" action="listEssai.php" onsubmit="return verif()">
			<div id = "erreur" class='alert alert-danger'><strong>Veuillez remplir le champ ci-dessous</strong></div>
			<div class="row" id="filtre" style="margin-bottom:10px; text-align:left;">
				<div class="col-md-3"><label class="checkbox-inline"><input id="tous" type="checkbox" name="Tous" value="1" <?php if (!$saisie) echo 'checked' ?>>&nbsp;Tous</label></div>

				<?php
					$str = "SELECT idEtat, nomEtat FROM etat WHERE idEtat >= 20 AND idEtat < 30";
					$req = mysqli_query($bdd, $str);
					while($lg=mysqli_fetch_object($req))
					{
				?>
						<div class="col-md-3">
						<label class="checkbox-inline"><input class="filtre" type="checkbox" name="<?php echo str_replace(" ", "", $lg->nomEtat); ?>" value="1" <?php if (in_array(str_replace(" ", "", $lg->nomEtat), $libele_etat)) echo 'checked' ?>><span 	  class="<?php echo 'carre bar-'.$lg->idEtat ?>"></span>&nbsp;<?php echo $lg->nomEtat ?></label>
						</div>
				<?php
					}
				?>
			</div>
			<div class="text-center" style="margin-top:30px;">
				<input type="submit" class="btn btn-block btn-primary" value="Rechercher" />
			</div>
		</form>
	</div>
	<div class="jumbotron">
		<div class="table-responsive">
			<table class="table table-striped table-tri" id="tri">
				<thead>
					<tr>
						<th>N° essai</th>
						<th>Primavera</th>
						<th>Affaire</th>
						<th>Moyen</th>
						<th>Équipement</th>
						<th>Badge</th>
						<th>Date</th>
						<th>Liste des OF</th>
						<th>Etat</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
					<tr>
						<th>N° essai</th>
						<th>Primavera</th>
						<th>Affaire</th>
						<th>Moyen</th>
						<th>Équipement</th>
						<th>Badge</th>
						<th>Date</th>
						<th>Liste des OF</th>
						<th>Etat</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>	
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
<script type="text/javascript" language="javascript" src="../js/listEssai.js"></script>	
<script type="text/javascript" charset="utf-8">	
	$.get("../server_side/serv_side_listEssai.php?idEtat=<?php echo $idEtat; ?>&idServ_Labo=<?php echo $idServ_Labo; ?>", function(data)
	{
		console.log(data);
	} )
	$(document).ready(function(){
		$('#tri').dataTable( {
			"bServerSide": true,
			"bDestroy": true,
			"aaSorting": [[6,'desc']],
			"sAjaxSource": "../server_side/serv_side_listEssai.php",
			"fnServerParams": function ( aoData ) {
			  aoData.push( { "name": "idEtat", "value": "<?php echo $idEtat; ?>" },{ "name": "idServ_Labo", "value": "<?php echo $idServ_Labo; ?>" } );
			},
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				$(nRow).css('cursor', 'pointer');
				
				$(nRow).on('click', function () {
					document.location.href='detailsEssai.php?idEssai='+aData[0]+'';
				});
			}
		} ).columnFilter({
			sPlaceHolder : "head:after"
		});
		$('#tri_filter input').attr("placeholder", "Rechercher");
		$('#tri_filter input').attr("class", "form-control");
		$('#tri_filter input').attr("style", "font-weight:normal;");
		$('#tri_length select').attr("class", "form-control");

		init();

	});
</script>
<?php
require('bottom.php');
