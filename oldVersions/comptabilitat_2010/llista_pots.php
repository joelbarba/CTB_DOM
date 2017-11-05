<?php
	include ("funcions.inc");
	$link_db = connectar_db();
?>

<html>
<body>

<h1> Llista pots reals : </h1>
<a href='alta_pot_real_1.php'> Alta pot real </a>
<br> <br>

<table cellspacing="3" border="1">
<tr>
<td> <b> NUM </b> </td>
<td> <b> DESCRIPCIO </b> </td>
</tr>

<?php
	$result = mysql_db_query("comptabilitat", "select * from POTS_REALS order by NUM");
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td> ".$row["NUM"]." </td>";
		echo "<td> ".$row["DESCRIPCIO"]." </td>";
		echo "<td> <a href='del_pot_real.php?num=".$row["NUM"]."'> eliminar </a> </td>";
		echo "<td> <a href='mod_pot_real_1.php?num=".$row["NUM"]."'> modificar </a> </td>";
		echo "</tr>";
	}
?>
</table>

<br> <br>

<h1> Llista pots comptables : </h1>
<a href='alta_pot_comp_1.php'> Alta pot comptable </a>
<br> <br>

<table cellspacing="3" border="1">
<tr>
<td> <b> NUM </b> </td>
<td> <b> DESCRIPCIO </b> </td>
</tr>
<?php
	$result = mysql_db_query("comptabilitat", "select * from POTS_COMPTABLES order by NUM");
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td> ".$row["NUM"]." </td>";
		echo "<td> ".$row["DESCRIPCIO"]." </td>";
		echo "<td> <a href='del_pot_comp.php?num=".$row["NUM"]."'> eliminar </a> </td>";
		echo "<td> <a href='mod_pot_comp_1.php?num=".$row["NUM"]."'> modificar </a> </td>";
		echo "</tr>";
	}
?>

</table>

</body>
</html>

<?php
	mysql_free_result($result);
	mysql_close($link_db);
?>


