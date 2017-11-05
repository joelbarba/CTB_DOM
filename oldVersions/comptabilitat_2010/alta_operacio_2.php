<?php

	include ("funcions.inc");
                
	$link_db = connectar_db();

	// Insereix la operacio
	$result = mysql_query("SELECT IFNULL(MAX(NUM), 0) + 1 AS NO FROM OPERACIONS", $link_db);
	$num_op = mysql_result($result, 0, "NO");


	if ($_REQUEST[referencia] == "") { $ref = "NULL"; } else { $ref = $_REQUEST[referencia]; }
	
        $sentencia = "INSERT INTO OPERACIONS VALUES (".
                                         $num_op.", '".
                                         $_REQUEST[descripcio]."', STR_TO_DATE('".
                                         $_REQUEST[data]."', '%d-%m-%Y'), ".
                                         $ref.")";

        mysql_db_query("comptabilitat", $sentencia);
        $desc_error = mysql_error();

	// Insereix els moviments
        if ($desc_error == "") {
            for ($i = 1; $i <= $_REQUEST[num_movs]; $i++) {

                if ($_REQUEST["import_".$i] != "") {
		
                    $result = mysql_query("SELECT IFNULL(MAX(NUM), 0) + 1 AS NM FROM MOVIMENTS", $link_db);
                    $num_mov = mysql_result($result, 0, "NM");

                    $sentencia = "INSERT INTO MOVIMENTS VALUES (".
                                                $num_mov.", REPLACE('".
                                                $_REQUEST["import_".$i]."', ',', '.'), ".
                                                $_REQUEST["pot_real_".$i].", ".
                                                $_REQUEST["pot_comptable_".$i].", ".
                                                $num_op.")";

                    mysql_db_query("comptabilitat", $sentencia);
                    $desc_error = mysql_error();
                }
            }
	}

	mysql_close($link_db);

?>

<script language="JavaScript" type="text/javascript">
	var pagina="llista_pots.php"
	function redireccionar() {
		location.href=pagina
	} 
	<?php
		//if ($desc_error == '') { echo 'setTimeout ("redireccionar()", 500);'; }
	?>
</script>
<html>
<body>
<?php
        
	if ($desc_error == '') {	
		echo "S'ha inserit correctament l'operació ". $_REQUEST[descripcio];
                
	} else {
		echo "Error, no s'ha pogut inserir el pot. <br> <br>";
		echo $sentencia;
		echo "<br> <br>";
		echo $desc_error;
	}

?>
	<br> <br>
	<a href='llista_operacions.php'> Tornar a la llista d'operacions </a>

</body>
</html>
