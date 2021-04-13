<?php
/*
class sql
{
	private $connexion_sql;
	
	function __construct()
	{
		$this->connexion_bdd = new PDO('sqlsrv:Server=\\Tlpibt02;Database=Hybrides', 'smt', 'smtsmt');

		// Fixe les options d'erreur (ici nous utiliserons les exceptions)
		$this->connexion_bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function requete($requete)
	{
		$prepare = $this->connexion_bdd->prepare($requete);
		$prepare->execute();
		
		return $prepare;
	}
}

$sql = new sql();

$req = $sql->requete('SELECT * FROM applications');
while ($r = $req->fetch())
{
	print_r($r);
}
*/

/*
$serverName = "172.16.2.8"; //serverName\instanceName
$connectionInfo = array( "Database"=>"Alice", "UID"=>"smt", "PWD"=>"smtsmtsmt", "CharacterSet" =>"UTF-8");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connexion établie.<br />";
}else{
     echo "La connexion n'a pu être établie.<br />";
     die( print_r( sqlsrv_errors(), true));
}
*/
/*
$cnx = new PDO("odbc:Driver={SQL Native Client};Server=172.16.2.8;Database=Alice; 
Uid=smt;Pwd=smtsmtsmt;"); 
*/
/*
try {

    $DBH = new PDO("sqlsrv:host=172.16.2.8;dbname=Alice", 'smt', 'smtsmtsmt');

} catch (PDOException $e) {

    echo $e->getMessage();
}
*/

	try{
		//$hostname = "Tlpibt02";            //host
		$hostname = "Tlipep01";            //host
		$dbname = "Alice";            //db name
		//$dbname = "Hybrides";            //db name
		$username = "smt";            // username like 'sa'
		$pw = "smt";                // password for the user
		$dbh = new PDO ("sqlsrv:server=$hostname;database=$dbname","$username","$pw");
		
	} catch (PDOException $e) {
		echo "Failed to get DB handle: " . $e->getMessage() . "\n";
		exit;
	}
	
	//$sql="Select * from INFORMATION_SCHEMA.TABLES";
	//$sql="Select * from [Commun].[Table Configuration a Appliquer]";
	$sql="select *  from [Table Article] a, [Table des OF] o where o.[OF]='PD0102' and a.[n°dossier]=o.[article]";
	//$sql="select [OF],[N_doc],[IndiceM_Ed], [Indicem_Rev], [Type_Doc] from [Commun].[Table Configuration a Appliquer] where [OF]='PD0102'";
	$t=$dbh->prepare($sql);
	$t->execute();
	//echo "\nPDOStatement::errorCode():\n";

	print_r( $t->errorInfo());
	//echo $t->fetchColumn();
	
	foreach($dbh->query($sql) as $row)
	{
		print_r($row);
	
	}


/*
 try {
    $dbname = "Alice";
    $serverName = "172.16.2.135";  
    $username = "smt";
    $pw = "smtsmtsmt"; 
    $dbh = new PDO ("sqlsrv:server=$serverName;Database=$dbname","$username","$pw");
   
 } 
    catch (PDOException $e) {
    print "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
    }  
*/	
/*
try{
        $bdd=new PDO('mssql:host=SERVEUR-SQLEXPRESS;dbname=bdd','usr','***');
    }catch(Exception $e){
        echo $e->getMessage() ;
    }
*/

/*
// Le serveur est au format : <hôte>\<nom d'instance> ou
// <serveur>,<port> quand on utilise un port différent de celui par défaut
$server = '172.16.2.8';

// Connexion à MSSQL
$link = mssql_connect($server, 'smt', 'smtsmtsmt');

if(!$link) {
    die('Erreur de connexion à MSSQL');
}
*/


//Tlpibt02 -> 172.16.2.135

//Tlipep01 -> 172.16.2.165
