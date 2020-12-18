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
	 
	require("../../conf/connexion_param.php");
	require("../../fonction.php");
	//recupération de la selection du mois
	$dDeb=dateFrToSQL($_GET["dDeb"]);
	$dFin=dateFrToSQL($_GET["dFin"]);
	
	$aColumns = array( 'numInstru', 'nomDes', 'nomStatut', 'marque', 'modele','numSerie', 'date_futureInt', 'nomLocal');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "numInstru";

	
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
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$nbcol=count($aColumns);
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		
		for ( $i=0 ; $i<$nbcol; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($bdd, $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<$nbcol ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($bdd,$_GET['sSearch_'.$i])."%' ";
		}
	}
	
	//condition where en plus
	if($sWhere != "")
	{
		$condWhere1="and ";
		$condWhere2="and ";
		$condWhereUnion="and ";
	}
	else
	{
		$condWhere1="where ";
		$condWhere2="where ";
		$condWhereUnion="where ";
	}
	$condWhereUnion="i.idStatut_statut=s.idStatut and i.idStatut_statut!=4
	and i.date_futureInt >= '$dDeb' and i.date_futureInt <= '$dFin'";
	$condWhere1.="i.numInstru = iv.numInstru_instrument and ".$condWhereUnion;
	$condWhere2.="i.numInstru = ivc.numInstru_instrument and ".$condWhereUnion;
	
	
	//Union All evite le travail de trie/suppression de doublon -> gain de perf
	$sQuery = "
		(SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		from statut s, instrument_vib iv, instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		$sWhere
		$condWhere1)
		UNION ALL
		(SELECT ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM statut s, instrument_vib_capteur ivc, instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		$sWhere
		$condWhere2)	
		$sOrder
		$sLimit
	";
	$rResult = mysqli_query( $bdd,$sQuery );
	

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
		for ( $i=0 ; $i<$nbcol ; $i++ )
		{
			
			if(($i==6) && $aRow[ $aColumns[$i] ]!="")
				$aRow[ $aColumns[$i] ]=date('d/m/Y',strtotime($aRow[ $aColumns[$i] ]));
			
			$row[] = $aRow[ $aColumns[$i] ];
			
		}
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
