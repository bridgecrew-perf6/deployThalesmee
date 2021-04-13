<?php
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   GPL v2 or BSD (3-point)
 
 * modify by Nicolas Boyer
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 
 
 */
 
require("../conf/connexion_param.php");
//recupération des param
$idEtat=$_GET["idEtat"];
$idServ_Labo=$_GET["idServ_Labo"];

$aColumns = array( 'idEssai', 'idTachePrim', 'affaire', 'nomMoyen', 'equipement','badge', 'dateEtat', 'of', 'nomEtat');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "idEssai";

/* DB table to use */
$sTable = "etat, essai e left join etatessai et on et.idEssai_ESSAI = e.idEssai left join tester t on t.idEssai_ESSAI = e.idEssai left join moyen m on idMoyen = e.idMoyen_MOYEN ";

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = "LIMIT ".mysqli_real_escape_string($bdd, $_GET['iDisplayStart'] ).", ".
		mysqli_real_escape_string($bdd, $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = "ORDER BY  ";
	$îSorting=intval( $_GET['iSortingCols'] );
	for ( $i=0 ; $i<$îSorting ; $i++ )
	{
		if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				".mysqli_real_escape_string($bdd, $_GET['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == "ORDER BY" )
	{
		$sOrder = "";
	}
}

/*
/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$nbcol=count($aColumns);
$having = "";
if ( $_GET['sSearch'] != "" )
{
	$having = "HAVING (";
	
	for ( $i=0 ; $i<$nbcol; $i++ )
	{
		$having .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($bdd, $_GET['sSearch'] )."%' OR ";
	}
	$having = substr_replace( $having, "", -3 );
	$having .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<$nbcol ; $i++ )
{
	if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
	{
		if ( $having == "" )
		{
			$having = "HAVING ";
		}
		else
		{
			$having .= " AND ";
		}
		$having .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($bdd,$_GET['sSearch_'.$i])."%' ";
	}
}

//condition where en plus

$sWhere="where et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEtat_ETAT in ($idEtat)
	and idEtat = idEtat_ETAT
	and e.idService_SERVICE=$idServ_Labo
	group by idEssai";

/*
 * SQL queries
 * Get data to display
 */
 /*
$sQuery = "
	SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
	FROM   $sTable
	$sWhere
	$sOrder
	$sLimit
";
$rResult = mysqli_query( $bdd,$sQuery );
*/
$sQuery = "
	SELECT SQL_CALC_FOUND_ROWS idEssai, idTachePrim, affaire, nomMoyen, equipement, badge, dateEtat, GROUP_CONCAT(noOF_EQUIPEMENT_OF) as of, nomEtat
	from $sTable
	$sWhere
	$having
	$sOrder
	$sLimit
";

$rResult = mysqli_query( $bdd,$sQuery );
/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS()
";
$rResultFilterTotal = mysqli_query( $bdd,$sQuery) ;
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];



/*
 * Output
 */
$output = array(
	"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iFilteredTotal, //afin d'enlever un affichage supérflu, on lui donne la meme valeur que iTotalDisplayRecords
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => array()
);

while ( $aRow = mysqli_fetch_array( $rResult ) )
{
	$row = array();
	$idEssai = $aRow[ "idEssai"];
	for ( $i=0 ; $i<$nbcol ; $i++ )
	{
		
		$row[] = $aRow[ $aColumns[$i] ];
	}
	
	$output['aaData'][] = $row;
}

echo json_encode( $output );
