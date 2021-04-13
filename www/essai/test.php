<?php

function ecart_mois ($date1, $date2){
	
	$moisCours = $date1[1];
	$anneCours = intval($date1[0]);
	if (intval($moisCours[0]) != 0){
		
		$moisCours = intval($moisCours);

	}else{
		$moisCours = intval($moisCours[1]);
	}
	
	$moisPrec = $date2[1];
	$anneePrec = intval($date2[0]);
	if (intval($moisCours[0]) != 0){
		
		$moisPrec = intval($moisPrec);

	}else{
		
		$moisPrec = intval($date2[1]);
	}
	
	if ($moisCours == $moisPrec && $anneCours == $anneePrec){
		
		return 0;
		
	}else if ($anneCours == $anneePrec && $moisCours > $moisPrec+1){
		
		$res = 0;
		while ($moisPrec+1 < $moisCours){
			
			$moisPrec++;
			$res ++;
		}
		return $res;
		
	}else if ($anneCours > $anneePrec){
		
		$res = 0;
		while ($anneePrec < $anneCours){
			
			$moisPrec++;
			$res ++;
			
			if ($moisPrec == 13){
				
				$moisPrec = 1;
				$anneePrec++;
			}
		}
		if ($moisCours > $moisPrec+1){
			
			while ($moisPrec+1 < $moisCours){
			
			$moisPrec++;
			$res ++;
			}
		}
		
		return $res;
		
	}else{
		
		return 0;
	}
	
}

function ecart_mois ($date1, $date2){
	
	$moisCours = $date1[1];
	$anneCours = intval($date1[0]);
	if (intval($moisCours[0]) != 0){
		$moisCours = intval($moisCours);
	}else{
		$moisCours = intval($moisCours[1]);
	}
	$moisPrec = $date2[1];
	$anneePrec = intval($date2[0]);
	if (intval($moisCours[0]) != 0){
		$moisPrec = intval($moisPrec);
	}else{
		$moisPrec = intval($date2[1]);
	}
	if ($moisCours == $moisPrec && $anneCours == $anneePrec){
		return 0;
	}else if ($anneCours == $anneePrec && $moisCours > $moisPrec+1){
		$res = 0;
		while ($moisPrec+1 < $moisCours){	
			$moisPrec++;
			$res ++;
		}
		return $res;
	}else if ($anneCours > $anneePrec){
		$res = 0;
		while ($anneePrec < $anneCours){
			$moisPrec++;
			$res ++;
			if ($moisPrec == 13){	
				$moisPrec = 1;
				$anneePrec++;
			}
		}
		if ($moisCours > $moisPrec+1){
			while ($moisPrec+1 < $moisCours){
			
			$moisPrec++;
			$res ++;
			}
		}
		return $res;
	}else{
		return 0;
	}
}

function multiexplode($delimiters, $string){
	
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode ($delimiters[0], $ready);
	return $launch;
}

$datePrec = multiexplode(array("-", " "), "2018-12-12");
$mois = multiexplode(array("-", " "), "2017-06-12");
ecart_mois($datePrec, $mois);


$ecart = ecart_mois($mois, $datePrec);
			for ($i=0; $i<$ecart;$i++){

?>