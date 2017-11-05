<?php
	include ("funcions.inc");
	$link_db = connectar_db();
?>

<html>
<body>

<div style="left: 30px; top: 600px; position: absolute; visibility: visible; z-index:10; font-size: 14pt; font-family: courier new;"> 
	<a href='llista_pots.php'> Gestio pots </a> <br><br>
	<a href='repartiment_1.php'> Repartiment </a> <br><br>
</div>

<div style="left: 300px; top: 600px; position: absolute; visibility: visible; z-index:10; font-size: 14pt; font-family: courier new;"> 
	<a href='historia_completa.php?num_vis=30&anymes=999999'> Historia completa </a> <br><br>
	<a href='alta_operacio_1.php?num_movs=1'> Alta nova operacio </a>
</div>


<div style="left: 30px; 
            top: 100px; 
            position: absolute; 
            visibility: visible; 
            z-index:10; 
            font-size: 14pt; 
            font-family: courier new; 
            text-align: center;
            border: 1px black solid;"> 

	<p style='font-size: 14pt; font-weight: bold'>Imports actuals</p> 
	<table cellspacing="3" border="0">
		<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
			<td> POT COMPTABLE </td>
			<td> IMPORT </td>
			<td> +/- MES </td>
		</tr>
	
		<?php
			$sentencia = "SELECT T2.NUM, 
			                     T2.DESCRIPCIO, 
		                        SUM(IF(POT_COMPTABLE=T2.NUM, IMPORT, 0)) TOTAL
		                   FROM MOVIMENTS T1,
		                        POTS_COMPTABLES T2
		                  WHERE T1.NUM_OPERACIO IN (SELECT NUM FROM OPERACIONS WHERE DATA <= NOW())
		                  GROUP BY T2.NUM
		                  ORDER BY T2.NUM";
			$result = mysql_db_query("comptabilitat", $sentencia);
			while($row = mysql_fetch_array($result)) {
				echo "<tr>";
				
				$sentencia = "SELECT IFNULL(SUM(T1.IMPORT), 0) TOT_MES_ANT
                            FROM MOVIMENTS T1,
                                 OPERACIONS T2
                           WHERE T1.NUM_OPERACIO = T2.NUM
                              AND T1.POT_COMPTABLE = ".$row[NUM]."
                              AND T2.ANYMES = DATE_FORMAT(NOW(), '%Y%m')";
   
				$result2 = mysql_query($sentencia, $link_db);
				$tot_mes_ant = mysql_result($result2, 0, "TOT_MES_ANT");				
				
				echo "<td style='width: 20em;'> ".$row["NUM"]." - ".$row["DESCRIPCIO"]." </td>";

				echo "<td align='right' style='width: 6em;";
				if ($row["TOTAL"] < 0) { echo " color:red; "; } 
				if ($row["TOTAL"] > 0) { echo " color:green; "; }
				echo "'> ".$row["TOTAL"]." </td>";

				echo "<td align='right' style='width: 6em;";
				if ($tot_mes_ant < 0) { echo " color:red; "; }
				if ($tot_mes_ant > 0) { echo " color:green; "; }
				echo "'> ".$tot_mes_ant." </td>";
				
				echo "</tr>";
			}
		?>

		<tr><td></td></tr>

		<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
			<td> POT REAL </td>
			<td> IMPORT </td>
			<td> +/- MES </td>
		</tr>
	
		<?php
		
			$sentencia = "SELECT T2.NUM, 
			                     T2.DESCRIPCIO, 
		                        SUM(IF(POT_COMPTABLE=T2.NUM, IMPORT, 0)) TOTAL
		                   FROM MOVIMENTS T1,
		                        POTS_REALS T2
		                  WHERE T1.NUM_OPERACIO IN (SELECT NUM FROM OPERACIONS WHERE DATA <= NOW())
		                  GROUP BY T2.NUM
		                  ORDER BY T2.NUM";
			$result = mysql_db_query("comptabilitat", $sentencia);
			while($row = mysql_fetch_array($result)) {
				echo "<tr>";

				$sentencia = "SELECT IFNULL(SUM(T1.IMPORT), 0) TOT_MES_ANT
                            FROM MOVIMENTS T1,
                                 OPERACIONS T2
                           WHERE T1.NUM_OPERACIO = T2.NUM
                              AND T1.POT_REAL = ".$row[NUM]."
                              AND T2.ANYMES = DATE_FORMAT(NOW(), '%Y%m')";
   
				$result2 = mysql_query($sentencia, $link_db);
				$tot_mes_ant = mysql_result($result2, 0, "TOT_MES_ANT");				
				
				echo "<td style='width: 20em;'> ".$row["NUM"]." - ".$row["DESCRIPCIO"]." </td>";

				echo "<td align='right' style='width: 6em;";
				if ($row["TOTAL"] < 0) { echo " color:red; "; } 
				if ($row["TOTAL"] > 0) { echo " color:green; "; }
				echo "'> ".$row["TOTAL"]." </td>";

				echo "<td align='right' style='width: 6em;";
				if ($tot_mes_ant < 0) { echo " color:red; "; }
				if ($tot_mes_ant > 0) { echo " color:green; "; }
				echo "'> ".$tot_mes_ant." </td>";

				echo "</tr>";
			}
		?>

	</table>

</div>


<div style="left: 600px; 
            top: 100px; 
            position: absolute; 
            visibility: visible; 
            z-index:10; 
            font-size: 14pt; 
            font-family: courier new; 
            text-align: center;
            border: 1px black solid"> 

	<p style='font-size: 14pt; font-weight: bold'>Ultimes operacions</p>
	<table cellspacing="3" border="0">
	<tr style="background-color: #bbbbff; text-align: center; font-weight: bold">
		<td> NUM </td>
		<td> DATA </td>
		<td> DESCRIPCIO </td>
		<td> IMPORT </td>
	</tr>
	
	<?php
	
		$sentencia = "SELECT T1.NUM, 
	                        DATE_FORMAT(T1.DATA, '%d-%m-%Y') DATA, 
	                        T1.DESCRIPCIO,
	                        T2.TOTAL TOTAL
	                   FROM OPERACIONS T1,
	                        (SELECT NUM_OPERACIO,
	                                SUM(IMPORT) TOTAL
	                           FROM MOVIMENTS
	                          GROUP BY NUM_OPERACIO) T2
	                  WHERE T2.NUM_OPERACIO = T1.NUM
	                  ORDER BY T1.NUM DESC
	                  LIMIT 0, 20";
	
		$result = mysql_db_query("comptabilitat", $sentencia);
		$pijama=0;
		while($row = mysql_fetch_array($result)) {

			if ($pijama == 0) { echo "<tr style='background-color: #ffffff;>'"; $pijama=1; }
			else {              echo "<tr style='background-color: #eeeeee;>'"; $pijama=0; }
			 
			echo "<td align='center' style='width: 4em;'> <a href='gestio_operacio.php?num=".$row["NUM"]."'> ".$row["NUM"]." </a> </td>";
			echo "<td align='center' style='width: 8em;'> ".$row["DATA"]." </td>";
			echo "<td align='left' style='width: 25em;'> ".$row["DESCRIPCIO"]." </td>";
			echo "<td align='right' style='width: 6em;";
			if ($row["TOTAL"] < 0) { echo " color:red; "; }
			if ($row["TOTAL"] > 0) { echo " color:green; "; }
			echo "'> ".$row["TOTAL"]." </td>";
			echo "</tr>";
		}
		mysql_free_result($result);
	?>
	</table>
</div>



</body>
</html>

<?php
	mysql_close($link_db);
?>


