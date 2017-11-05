<?php
	include ("funcions.inc");
	$link_db = connectar_db();
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <link href="estils.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<p style='font-size: 16pt; font-family: Verdana; font-weight: bold'>Historia completa</p>

<br>

<div style='position: absolute; border: 1px black solid;'>

<div class='llista1' style='left: 0px; position: relative;'>

	<table id='taula1' cellspacing="3" border="0">
	<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
		<th width="40"> NUM </th>
                <th width="130"> DATA </th>
                <th width="350"> DESCRIPCIO </th>
                <th width="80"> IMPORT </th>
	</tr>

	<?php

		$sentencia = "SELECT T1.NUM,
	                        DATE_FORMAT(T1.DATA, '%d-%m-%Y') DATA,
	                        T1.DESCRIPCIO,
	                        IFNULL(T2.TOTAL, 0) TOTAL
	                   FROM OPERACIONS T1
	                        LEFT OUTER JOIN
	                        (SELECT NUM_OPERACIO,
	                                SUM(IMPORT) TOTAL
	                           FROM MOVIMENTS
	                          GROUP BY NUM_OPERACIO) T2
	                     ON T2.NUM_OPERACIO = T1.NUM
	                  ORDER BY T1.NUM DESC LIMIT 0, 30";

		$result = mysql_db_query("comptabilitat", $sentencia);
		$pijama=0;
		while($row = mysql_fetch_array($result)) {

			if ($pijama == 0) { echo "<tr style='background-color: #ffffff;>'"; $pijama=1; }
			else {              echo "<tr style='background-color: #eeeeee;>'"; $pijama=0; }

			echo "<td align='center'> <a href='gestio_operacio.php?num=".$row["NUM"]."'> ".$row["NUM"]." </a> </td>";
			echo "<td align='center'> ".$row["DATA"]." </td>";
			echo "<td align='left'> ".$row["DESCRIPCIO"]." </td>";
			echo "<td align='right' style='";
			if ($row["TOTAL"] < 0) { echo " color:red; "; }
			if ($row["TOTAL"] > 0) { echo " color:green; "; }
			echo "'> ".$row["TOTAL"]." </td>";
			echo "</tr>";
		}
		mysql_free_result($result);
	?>
	</table>
</div>


<div class='llista' style='float: left; position: relative; padding-left: 30px;'>

	<table cellspacing="3" border="0" width=800>
	<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
		<?php
			$sentencia = "SELECT NUM FROM POTS_COMPTABLES ORDER BY NUM";
			$result = mysql_db_query("comptabilitat", $sentencia);
			while($row = mysql_fetch_array($result)) {
				echo "<td width=10%> PC-".$row["NUM"]." </td>";
			}
		?>
	</tr>

	<?php
		$sentencia = "SELECT NUM FROM OPERACIONS ORDER BY NUM DESC LIMIT 0, 30";
		$result = mysql_db_query("comptabilitat", $sentencia);

		$pijama=0;
		while($row = mysql_fetch_array($result)) {

			if ($pijama == 0) { echo "<tr style='background-color: #ffffff;>'"; $pijama=1; }
			else {              echo "<tr style='background-color: #eeeeee;>'"; $pijama=0; }

			$sentencia = "SELECT NUM FROM POTS_COMPTABLES ORDER BY NUM";
			$result2 = mysql_db_query("comptabilitat", $sentencia);
			while($row2 = mysql_fetch_array($result2)) {

				$sentencia = "SELECT IFNULL(SUM(IMPORT), 0) TOTAL
                            FROM MOVIMENTS
                           WHERE NUM_OPERACIO = ".$row[NUM]."
                             AND POT_COMPTABLE = ".$row2[NUM];
				$result3 = mysql_query($sentencia, $link_db);
				$import = mysql_result($result3, 0, "TOTAL");
				mysql_free_result($result3);

				echo "<td width=10% align='right'";
				if ($import < 0) { echo " style='color:red;'"; }
				if ($import > 0) { echo " style='color:green;'"; }
				echo "'> ".$import." </td>";

			}
			mysql_free_result($result2);

			echo "</tr>";

		}
		mysql_free_result($result);
	?>
	</table>
</div>


<div class='llista' style='float: left; position: relative; padding-left: 30px;'>

	<table cellspacing="3" border="0" width=800>
	<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
		<?php
			$sentencia = "SELECT NUM FROM POTS_COMPTABLES ORDER BY NUM";
			$result = mysql_db_query("comptabilitat", $sentencia);
			while($row = mysql_fetch_array($result)) {
				echo "<td width=10%> PC-".$row["NUM"]." </td>";
			}
		?>
	</tr>

	<?php
		$sentencia = "SELECT NUM FROM OPERACIONS ORDER BY NUM DESC LIMIT 0, 30";
		$result = mysql_db_query("comptabilitat", $sentencia);

		$pijama=0;
		while($row = mysql_fetch_array($result)) {

			if ($pijama == 0) { echo "<tr style='background-color: #ffffff;>'"; $pijama=1; }
			else {              echo "<tr style='background-color: #eeeeee;>'"; $pijama=0; }

			$sentencia = "SELECT NUM FROM POTS_COMPTABLES ORDER BY NUM";
			$result2 = mysql_db_query("comptabilitat", $sentencia);
			while($row2 = mysql_fetch_array($result2)) {

				$sentencia = "SELECT IFNULL(SUM(IMPORT), 0) TOTAL
                            FROM MOVIMENTS
                           WHERE NUM_OPERACIO <= ".$row[NUM]."
                             AND POT_COMPTABLE = ".$row2[NUM];
				$result3 = mysql_query($sentencia, $link_db);
				$import = mysql_result($result3, 0, "TOTAL");
				mysql_free_result($result3);

				echo "<td width=10% align='right'";
				if ($import < 0) { echo " style='color:red;'"; }
				if ($import > 0) { echo " style='color:green;'"; }
				echo "'> ".$import." </td>";

			}
			mysql_free_result($result2);

			echo "</tr>";

		}
		mysql_free_result($result);
	?>
	</table>
</div>

</div>

</body>
</html>

<?php
	mysql_close($link_db);
?>

