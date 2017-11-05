<?php
	include ("funcions.inc");
	$link_db = connectar_db();
	$result = mysql_query("SELECT DATE_FORMAT(CURDATE(), '%d-%m-%Y') DA FROM DUAL", $link_db);
	$data_act = mysql_result($result, 0, "DA");

        array_pots(&$num_pcs, &$num_pc, &$desc_pc, &$num_prs, &$num_pr, &$desc_pr);
?>


<html>
<body>
<h1> Repartiment de saldo </h1>

<br>

<form METHOD = post ACTION = "alta_operacio_2.php" NAME = "enviar" Onsubmit = "return llista_operacions(this)">

	<div style="left: 10px;
                    top: 60px;
                    width: 1100px;
                    height: 50px;
                    position: absolute;
                    visibility: visible;
                    z-index:10;
                    font-size: 10pt;
                    font-family: Courier New;
                    font-weight: bold;
                    text-align: left;
                    border: 1px black solid;">

            <p style='position:absolute; left:20px; top:5px;'> Data : </p>
            <?php
                    echo "<input type='text' size=8 style='position:absolute; left:80px; top:12px; width:90px; heigth:25px;' name='data' value='".$data_act."' />";
            ?>
            
            <p style='position:absolute; left:200px; top:5px;'> Descripcio : </p>
            <textarea ROWS=1 COLS=50 NAME="descripcio" style='position:absolute; left:305px; top:10px; width:600px; height: 25px;' ></textarea>
            <input TYPE = "submit" style='position:absolute; left:940px; top:11px;' NAME = "boto1" VALUE = "ALTA OPERACIO">
        </div>

        <div style="left: 10px;
                    top: 120px;
                    width: 1100px;
                    height: 500px;
                    position: absolute;
                    visibility: visible;
                    z-index:10;
                    font-size: 10pt;
                    font-family: Courier New;
                    font-weight: bold;
                    text-align: left;
                    border: 1px black solid;">

            <?php

                $pos_left=25;
                $pos_top=10;

                for ($n=1; $n<=6; $n++) {

                    
                    echo "<p style='position:absolute; left:".$pos_left."px; top:".($pos_top+18)."px;'>".$n." - Import : </p>";
                    echo "<input type='text' size=6 style='position:absolute; left:".($pos_left+110)."px; top:".($pos_top+25)."px' name='import_".$n."'/>";

                    echo "<p style='position:absolute; left:".($pos_left+240)."px; top:".$pos_top."px;'> Pot comptable origen : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+430)."px; top:".($pos_top+5)."px;' name='pot_comptable_o_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_pcs; $i++) { echo "<option value=".$num_pc[$i].">".$num_pc[$i]." - ".$desc_pc[$i]."</option>"; }
                    echo "</select>";

                    echo "<p style='position:absolute; left:".($pos_left+650)."px; top:".$pos_top."px;'> Pot real origen : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+800)."px; top:".($pos_top+5)."px;' name='pot_real_o_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_prs; $i++) { echo "<option value=".$num_pr[$i].">".$num_pr[$i]." - ".$desc_pr[$i]."</option>"; }
                    echo "</select>";

                    $pos_top+=30;
                    echo "<p style='position:absolute; left:".($pos_left+248)."px; top:".$pos_top."px;'> Pot comptable desti : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+430)."px; top:".($pos_top+5)."px;' name='pot_comptable_d_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_pcs; $i++) { echo "<option value=".$num_pc[$i].">".$num_pc[$i]." - ".$desc_pc[$i]."</option>"; }
                    echo "</select>";

                    echo "<p style='position:absolute; left:".($pos_left+658)."px; top:".$pos_top."px;'> Pot real desti : </p>";
                    echo "<select style='position:absolute; left:".($pos_left+800)."px; top:".($pos_top+5)."px;' name='pot_real_d_".$n."'>";
                    echo "<option value=0></option>";
                    for ($i=1; $i<=$num_prs; $i++) { echo "<option value=".$num_pr[$i].">".$num_pr[$i]." - ".$desc_pr[$i]."</option>"; }
                    echo "</select>";

                    $pos_top+=50;

                }
            ?>

        </div>

</form>


</body>
</html>

