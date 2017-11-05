
<?php
	function connectar_db() {

		$link_db = mysql_connect("localhost:3306", "da_usr", "d0ublepurpul3");
		mysql_select_db("dom_admin", $link_db);
		return $link_db;

	}

?>
