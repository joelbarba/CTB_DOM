<?php 


$dbconn = pg_connect("host=localhost dbname=db_comptabilitat user=barba password=barba0001")
			or die('No s\'ha pogut connectar : ' . pg_last_error());

			
$consulta = "
select data_moviment, descripcio, import
  from moviments
 where id_operacio = ".$_REQUEST[id_operacio];
 
$result = pg_query($dbconn, $consulta);
$rows = pg_num_rows($result);

$retorn = $rows.",";


$x = 0;
while ($x < $rows) {
	$retorn .= pg_fetch_result($result, $x, "data_moviment").",";
	$retorn .= pg_fetch_result($result, $x, "descripcio").",";
	$retorn .= pg_fetch_result($result, $x, "import").",";
	$x++;
}				



echo $retorn;

pg_close($dbconn);

?>
