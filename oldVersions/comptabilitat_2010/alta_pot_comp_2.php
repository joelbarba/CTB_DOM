<?php
	include ("funcions.inc");

	$link_db = connectar_db();

	$result = mysql_query("SELECT IFNULL(MAX(NUM), 0) + 1 AS NP FROM POTS_COMPTABLES", $link_db);
	$num_pot = mysql_result($result, 0, "NP");

	$sentencia = "INSERT INTO POTS_COMPTABLES VALUES (".$num_pot.", '".$_REQUEST[descripcio]."')";

	mysql_db_query("comptabilitat", $sentencia);
	$desc_error = mysql_error();

?>
<script language="JavaScript" type="text/javascript">
	var pagina="llista_pots.php"
	function redireccionar() {
		location.href=pagina
	} 
	<?php
		if ($desc_error == '') { echo 'setTimeout ("redireccionar()", 500);'; }
	?>
</script>
<html>
<body>
<?php
	if ($desc_error == '') {	
		echo "S'ha inserit correctament el pot num. ".$num_pot." amb la descripcio: ". $_REQUEST[descripcio];
	} else {
		echo "Error, no s'ha pogut inserir el pot. <br> <br>";
		echo $sentencia;
		echo "<br> <br>";
		echo $desc_error;
	}

	mysql_close($link_db);

?>
	<br> <br>
	<a href='llista_pots.php'> Tornar a la llista de pots </a>

</body>
</html>
