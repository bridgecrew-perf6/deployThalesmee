<?php
//etablie une connexion a la base alice
try{
	$hostname = "Tlipep01";            //host
	$dbname = "Alice";            //db name
	$username = "smt";            // username like 'sa'
	$pw = "smt";                // password for the user
	$dbh = new PDO ("sqlsrv:server=$hostname;database=$dbname;","$username","$pw");
	$dbh->exec("SET NAMES utf8");
	$dbh->exec("SET CHARACTER SET utf8");
	
} catch (PDOException $e) {
	echo "Failed to get DB handle: " . $e->getMessage() . "\n";
	exit;
}
