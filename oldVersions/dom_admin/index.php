<?php
    include ("funcions.inc");
    connectar_db();
?>

<html>
<body>
<h1> Login </h1>

<br> <br>

<FORM METHOD = post ACTION = "alta_pot_real_2.php" NAME = "enviar" Onsubmit = "return llista_pots_reals(this)">
	Nom del pot :
	<INPUT type = "text" NAME = "nom" VALUE="nou pot" >
	<BR> Node arrel:
	<INPUT NAME = "arrel" TYPE = "checkbox">
	<BR> <BR>
	Port 1:

	<BR>
	<BR>
	Introdueix els comentaris del concentrador:
	<BR>
	<TEXTAREA ROWS=5 COLS=30 NAME = "text">Comentaris</TEXTAREA>
	<BR>
	<CENTER>
	<INPUT TYPE = "submit" NAME = "boto1" VALUE = "Enviar">
	</CENTER>
</FORM>



</body>
</html>