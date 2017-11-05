<?php
	include ("funcions.inc");
	$link_db = connectar_db();
?>

<html>
<body>
<h1> Llista d'operacions </h1>

<a href='alta_operacio_1.php'> Alta operacio </a>

<font face="courier new">

<br><br>

<table cellspacing="3" border="1">
<tr>
<td> <b> NUM </b> </td>
<td> <b> DATA </b> </td>
<td> <b> MES </b> </td>
<td> <b> DESCRIPCIO </b> </td>
<td> <b> REF. </b> </td>
<td> <b> MOVS. </b> </td>
<td> <b> IMPORT </b> </td>
</tr>

<?php

	$sentencia = "SELECT T1.NUM, 
                        T1.DATA,
                        T1.ANYMES,
                        T1.DESCRIPCIO,
                        T1.REFERENCIA,
                        T2.NUM_MOVS,
                        T2.TOTAL
                   FROM OPERACIONS T1,
                        (SELECT NUM_OPERACIO, 
                        		  COUNT(*) NUM_MOVS,
                                SUM(IMPORT) TOTAL
                           FROM MOVIMENTS
                          GROUP BY NUM_OPERACIO) T2
                  WHERE T2.NUM_OPERACIO = T1.NUM
                  ORDER BY T1.NUM DESC";

	$result = mysql_db_query("comptabilitat", $sentencia);
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td> <a href='gestio_operacio.php?num=".$row["NUM"]."'> ".$row["NUM"]." </a> </td>";
		echo "<td> ".$row["DATA"]." </td>";
		echo "<td> ".$row["ANYMES"]." </td>";
		echo "<td> ".$row["DESCRIPCIO"]." </td>";
		echo "<td> ".$row["REFERENCIA"]." </td>";
		echo "<td> ".$row["NUM_MOVS"]." </td>";
		echo "<td> ".$row["TOTAL"]." </td>";
		echo "</tr>";
	}
	mysql_free_result($result);
?>
</table>

</font>

</body>
</html>

<?php	
	mysql_close($link_db);
?>

