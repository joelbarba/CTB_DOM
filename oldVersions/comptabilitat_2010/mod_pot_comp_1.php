<?php
	include ("funcions.inc");

	$link_db = connectar_db();
	$result = mysql_query("SELECT * FROM POTS_COMPTABLES WHERE NUM = ".$_GET['num'], $link_db);
?>

<html>
<body>
<h1> Modificar pot comptable </h1>
<br> <br>
<FORM METHOD = post ACTION = "mod_pot_comp_2.php" NAME = "enviar" Onsubmit = "return llista_pots(this)">
	Descripcio del pot : 
	<BR>

	<?php
		echo "<TEXTAREA ROWS=5 COLS=50 NAME='descripcio'>".mysql_result($result, 0, "DESCRIPCIO")."</TEXTAREA>";
		echo "<input type='hidden' name='num' value=".$_GET['num'].">";

		mysql_free_result($result);
		mysql_close($link_db);
	?>
	
	<INPUT TYPE = "submit" NAME = "boto1" VALUE = "OK">
</FORM>



</body>
</html>

