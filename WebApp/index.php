<html>
<body>

<p>
<?php

	// if (extension_loaded('pgsql')) { echo "SI"; } else { echo "NO"; } 
	// echo "<br/>";
	// print_r(get_loaded_extensions());

	$dbconn = pg_connect("host=localhost dbname=db_comptabilitat user=barba password=barba0001")   or die('Could not connect: ' . pg_last_error());

	echo "Conexio correcte a ".pg_dbname();
	echo "<br/>";


	$res = pg_query($db, "select * from comptes_comptables where codi = '2. Ingressos'");
	$val = pg_fetch_result($res, 1, 0);
	echo $val;
	echo "<br/><br/>";


	$result = pg_query("SELECT codi, descripcio FROM comptes_comptables") or die('Query failed: ' . pg_last_error());


	while ($row = pg_fetch_row($result)) {
		echo "Codi: $row[0]   Desc: $row[1]";
		echo "<br/>\n";
	}

	
/*
	echo "<table >\n";


	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    echo "\t<tr>\n";

	    foreach ($line as $col_value) { echo "\t\t<td>$col_value</td>\n"; }
	    echo "\t</tr>\n";
	}

	echo "</table>\n";

*/

	pg_close($dbconn);

	// phpinfo();

?>
</p>


<p> de moment funciona </p>

</body>
</html>


