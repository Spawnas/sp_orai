<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="description" content="The HTML5 Herald">
  <meta name="author" content="SitePoint">
  <title>nothing</title>

 
</head>

<body>
<?php

function connect_to_db()
{
	// server name
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "gedo";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	return $conn;
}

$miestai = array('Vilnius', 'Kaunas', 'Klaipeda', 'Siauliai', 'Panevezys');
$db_conn = connect_to_db ();
$content = file_get_contents('http://old.meteo.lt/oru_prognoze.php');

preg_match_all("/(\d+\s)[°C]/", $content, $temperatura);
preg_match_all("/\d+[-]\d+[-]\d+/", $content, $data);
preg_match_all('/oplm_reisk.*?gif" alt="(.*?)"/s', $content, $oras);


//print_r ($temperatura);


$dienu_sk = 1;
$data_temp = $data[0][1];
$dien = 2;

while ($data_temp != $data[0][$dien])
{
	$dien++;
	$dienu_sk++; 
}


$a = 0;
$b = $dienu_sk;
$c = 1;

for ($i = 0; $i < 5; $i++)
{
	
	while ($a < $b)
	{
		$sql = "SELECT data, miestas  FROM orai WHERE data = '" . $data[0][$c] . "' AND miestas = '" . $miestai[$i] ."';";
		$res = $db_conn->query($sql);
		//print_r ($res);
		//echo "<br>" . $data[0][$c] . "<br>";
		
		if ($res->num_rows == 0) 
		{
			$sql = "INSERT INTO orai(data, miestas, paros_metas, temperatura, dangus) VALUES ('" . $data[0][$c] . "'
			, '" . $miestai[$i] . "' , '0' , '" . $temperatura[1][$a] . "', '" . $oras[1][$a] . "'), ('" . $data[0][$c] . "'
			, '" . $miestai[$i] . "' , '1' , '" . $temperatura[1][$a + $dienu_sk] . "', '" . $oras[1][$a + $dienu_sk] . "')";
			$db_conn->query($sql);
			
		}
		$c++;
		$a++;
	}
	$c = 1;
	$a += $dienu_sk;
	$b += ($dienu_sk * 2);
}

$sql = "SELECT data, miestas, paros_metas, temperatura, dangus FROM orai";
$result = $db_conn->query($sql);

while($row = $result->fetch_assoc()) 
	{
	echo $row["data"]. "  " . $row["miestas"]. " " . $row["paros_metas"] . " " . $row["temperatura"] . $row["dangus"] . "<br>";
    }
//print_r ($oras);
//echo "<br> ". $dienu_sk ." <br>";
//print_r ($content);
//print_r ($data_temp);

?>
</body>
</html>
