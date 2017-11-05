<html>
<body>
<h1> AFEGIR NOVA OPERACIO </h1>


<form id="autform" action="autuser.asp" method="post">
<div>
<fieldset>
<legend> Dades operacio : </legend>
 Data operacio     : <input type="text" name="data" value="13/07/2010" /> <br>
 Any mes comptable : <input type="text" name="anymes" value="2010/07" /> <br>
 Referencia        : <input type="text" name="referencia" value="" /> <br>
 Descripcio        : <input type="text" name="descripcio" value="" /> <br>
<input type="button" value="Entrar" />
</fieldset>
</div>
</form>


<?php
/*
	$link = mysql_connect("localhost:3306", "usuari1", "cocaina");
	mysql_select_db("comptabilitat", $link);

	$result = mysql_db_query("comptabilitat","select * from OPERACIONS");
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td> ".$row["NUM"]." </td>";
		echo "<td> ".$row["DESCRIPCIO"]." </td>";
		echo "</tr>";
	}
	mysql_free_result($result);
	mysql_close($link);
*/
?>

</body>
</html>

