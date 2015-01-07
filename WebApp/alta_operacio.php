<?php

	// Crear una nova operacio
	
	$dbconn = pg_connect("host=localhost dbname=db_comptabilitat user=barba password=barba0001")
	or die('No s\'ha pogut connectar : ' . pg_last_error());	

	$sentencia = "
		insert into operacions
		select coalesce(max(id_operacio), 0) + 1, $1, 'N'
		from operacions
		";

	$result = pg_prepare($dbconn, "my_query", $sentencia);

	$result = pg_execute($dbconn, "my_query", array($_REQUEST['descripcio']));
	// $result = pg_execute($dbconn, "my_query", array("novaaaaaaaa3"._REQUEST[descripcio] ));
		
	pg_close($dbconn);
	

	// echo "_SERVER['REQUEST_METHOD'] = " . $_SERVER['REQUEST_METHOD'];
	// echo "_POST = " . $_POST['descripcio'];
	echo "_REQUEST[descripcio] = " . $_REQUEST['descripcio'];
	
	echo $sentencia;

?>