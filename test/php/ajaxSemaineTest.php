<?php
	
	include("../../essai/ajaxSemaine.php");

	assert_options(ASSERT_ACTIVE, 1);

	function my_assert_handler($file, $line, $code)
	{
		echo "<hr>Echec de l'essertion :
			File ".$file."<br />
			Line ".$line."<br />";
	}

	assert_options(ASSERT_CALLBACK, "my_assert_handler");

	//getLastWeek ($annee)
	assert(getLastWeek (2015) == 53);
	assert(getLastWeek (2017) == 52);
	assert(getLastWeek (2019) == 52);

	//getSunday ($annee, $semaine)
	assert(getSunday (2019, 45) == "2019-11-10");
	assert(getSunday (2015, 53) == "2016-01-03");
	assert(getSunday (2016, 52) == "2017-01-01");

	//nextWeekLast ($annee)
	assert(nextWeekLast (2019) == "1-2020");
	assert(nextWeekLast (2015) == "1-2016");
	assert(nextWeekLast (2017) == "1-2018");

	//nextWeekMaybeLast($annee, $sem)
	assert(nextWeekMaybeLast(2019, 52) == "1-2020");
	assert(nextWeekMaybeLast(2015, 53) == "1-2016");
	assert(nextWeekMaybeLast(2015, 52) == "53-2015");

	//nextWeek ($annee, $suiv)
	assert(nextWeek (2019, 16)== "17-2019");
	assert(nextWeek (2015, 1)== "2-2015");
	assert(nextWeek (2010, 49)== "50-2010");

	//prevWeek ($prec)
	assert(prevWeek (16)== "15");
	assert(prevWeek (53)== "52");
	assert(prevWeek (52)== "51");

	//actWeek ()
	assert(actWeek() == "45-2019");
?>