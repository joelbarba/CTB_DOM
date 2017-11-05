<html>
<body>
<h1> Alta d'un nou pot real </h1>

<br> <br>

<FORM METHOD = post ACTION = "alta_pot_real_2.php" NAME = "enviar" Onsubmit = "return llista_pots_reals(this)">
	Nom del pot :
	<INPUT type = "text" NAME = "nom" VALUE="nou pot" >
	<BR> Node arrel: 
	<INPUT NAME = "arrel" TYPE = "checkbox">
	<BR> <BR>
	Port 1: 
	<SELECT NAME="port1">
		<OPTION> </OPTION>
		<? if ((pg_numrows($registre)) > 0) {
			for ($i=0; $i<(pg_numrows($registre)); $i++) {
				$val = pg_fetch_array($registre, $i);
				echo "<OPTION> - ".$val[nom]." </OPTION>";
			}
		}
		if ((pg_numrows($registre2)) > 0) {
			for ($i=0; $i<(pg_numrows($registre2)); $i++) {
				$val = pg_fetch_array($registre2, $i);
				echo "<OPTION> + ".$val[nom]." </OPTION>";
			}
		} ?>
	</SELECT>


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

