<?php
	include ("funcions.inc");
	$link_db = connectar_db();
?>

<html>
<body>
<h1> Detall operacio </h1>

<?php 
	$result = mysql_query("SELECT * FROM OPERACIONS WHERE NUM = ".$_GET['num'], $link_db);
	echo "Numero d'operacio  : ".mysql_result($result, 0, "NUM")."<br>";
	echo "Data de l'operacio : ".mysql_result($result, 0, "DATA")."<br>";
	echo "Any-mes comptable  : ".mysql_result($result, 0, "ANYMES")."<br>";
	echo "Descripcio         : ".mysql_result($result, 0, "DESCRIPCIO")."<br>";
	echo "Referencia         : ".mysql_result($result, 0, "REFERENCIA")."<br>";
	mysql_free_result($result);
	
	$result = mysql_query("SELECT SUM(IMPORT) TOTAL FROM MOVIMENTS WHERE NUM_OPERACIO = ".$_GET['num'], $link_db);
	echo "Import total       : ".mysql_result($result, 0, "TOTAL")."<br>";
	mysql_free_result($result);
?> 

<br><br>

<table cellspacing="3" border="1">
<tr>
<td> <b> NUM </b> </td>
<td> <b> POT REAL </b> </td>
<td> <b> POT COMPTABLE </b> </td>
<td> <b> IMPORT </b> </td>
</tr>

<?php

	$sentencia = "SELECT T1.NUM,
                        T1.IMPORT,
                        T1.POT_REAL,
                        T2.DESCRIPCIO D1,
                        T1.POT_COMPTABLE,
                        T3.DESCRIPCIO D2
                   FROM MOVIMENTS T1,
                        POTS_REALS T2,
                        POTS_COMPTABLES T3
                  WHERE T1.NUM_OPERACIO  = ".$_GET['num']."
                    AND T1.POT_REAL      = T2.NUM
                    AND T1.POT_COMPTABLE = T3.NUM
                  ORDER BY T1.NUM";

	$result = mysql_db_query("comptabilitat", $sentencia);
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td> ".$row["NUM"]." </td>";
		echo "<td> ".$row["POT_REAL"]." - ".$row["D1"]." </td>";
		echo "<td> ".$row["POT_COMPTABLE"]." - ".$row["D2"]." </td>";
		echo "<td> ".$row["IMPORT"]." </td>";
		echo "</tr>";
	}
	mysql_free_result($result);
?>
</table>
</body>
</html>

<?php	
	mysql_close($link_db);
?>

