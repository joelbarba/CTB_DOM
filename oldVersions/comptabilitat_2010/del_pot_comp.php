<?php
	include ("funcions.inc");

	$link_db = connectar_db();
	
	$sentencia = "DELETE FROM POTS_COMPTABLES WHERE NUM = ".$_GET['num'];
	mysql_db_query("comptabilitat", $sentencia);
	$desc_error = mysql_error();
	
	mysql_close($link_db);

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
			echo "S'ha eliminat correctament el pot num. ".$_REQUEST[num];
		} else {
			echo "Error, no s'ha pogut inserir el pot. <br> <br>";
			echo $sentencia;
			echo "<br> <br>";
			echo $desc_error;
		}
	?>

	<br> <br>
	<a href='llista_pots.php'> Tornar a la llista de pots </a>

</body>
</html>
