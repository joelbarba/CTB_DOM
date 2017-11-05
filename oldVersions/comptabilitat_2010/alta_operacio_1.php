<?php
	include ("funcions.inc");
	$link_db = connectar_db();
	$result = mysql_query("SELECT DATE_FORMAT(CURDATE(), '%d-%m-%Y') DA FROM DUAL", $link_db);
	$data_act = mysql_result($result, 0, "DA");

        array_pots(&$num_pcs, &$num_pc, &$desc_pc, &$num_prs, &$num_pr, &$desc_pr);

        $num_moviments=$_REQUEST[num_movs];
        if ($num_moviments<1) { $num_moviments=1; }
        if ($num_moviments>10) { $num_moviments=10; }
?>


<html>
<body>
<h1> Alta nova operacio </h1>

<br>

<?php
    echo "<form METHOD = post ACTION = 'alta_operacio_2.php?num_movs=".$num_moviments."' NAME = 'enviar' Onsubmit = 'return llista_operacions(this)'>";
	
            echo "Data : <input type='text' size=8 style='font-size: 14pt;' name='data' value='".$data_act."' />     ";
	?>
		
	Referencia : <input type="text" size=4 style='font-size: 14pt;' name="referencia"/> <br><br>
	Descripcio : <br> <textarea ROWS=3 COLS=50 NAME="descripcio" style='font-size: 14pt;' ></textarea> <br>
	<br><br>


        <div style="left: 0px;
                    top: 260px;
                    width: 1100px;
                    height: 0px;
                    position: absolute;
                    visibility: visible;
                    z-index:10;
                    font-size: 10pt;
                    font-family: Courier New;
                    font-weight: bold;
                    text-align: left;">

            <?php

                $pos_left=15;
                $pos_top=10;

                for ($n=1; $n<=$num_moviments; $n++) {

                    echo "<p style='position:absolute; left:".$pos_left."px; top:".($pos_top+0)."px;'>".$n." - Import : </p>";
                    echo "<input type='text' size=6 style='position:absolute; left:".($pos_left+105)."px; top:".($pos_top+5)."px' name='import_".$n."'/>";

                    echo "<p style='position:absolute; left:".($pos_left+220)."px; top:".$pos_top."px;'> Pot comptable : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+350)."px; top:".($pos_top+5)."px;' name='pot_comptable_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_pcs; $i++) { echo "<option value=".$num_pc[$i].">".$num_pc[$i]." - ".$desc_pc[$i]."</option>"; }
                    echo "</select>";

                    echo "<p style='position:absolute; left:".($pos_left+560)."px; top:".$pos_top."px;'> Pot real : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+650)."px; top:".($pos_top+5)."px;' name='pot_real_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_prs; $i++) { echo "<option value=".$num_pr[$i].">".$num_pr[$i]." - ".$desc_pr[$i]."</option>"; }
                    echo "</select>";

                    if ($n == $num_moviments) {
                        echo "<a href='alta_operacio_1.php?num_movs=".($num_moviments + 1)."'>";
                        echo "<p style='position:absolute; left:".($pos_left+920)."px; top:".$pos_top."px;'> + moviment </p>";
                        echo "</a>";
                    }

                    $pos_top+=40;

                }
            ?>

        </div>




	<br><br><br>
	<input TYPE = "submit" style='position:absolute; left:610px; top: 215px; font-size: 14pt;' NAME = "boto1" VALUE = "ALTA OPERACIO">
</form>


</body>
</html>

