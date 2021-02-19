<?php
//etablie une connexion a la base alice
try{
	$hostname = getenv("DATABASE");            //host
	$dbname = "bdmee_prod";            //db name
	$username = "mee";            // username like 'sa'
	$pw = "pipo";                // password for the user
	$dbh = new PDO ("mysql:host=$hostname;dbname=$dbname;","$username","$pw",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$dbh->exec("SET NAMES utf8");
	$dbh->exec("SET CHARACTER SET utf8");
	
} catch (PDOException $e) {
	echo "Failed to get DB handle: " . $e->getMessage() . "\n";
	exit;
}
