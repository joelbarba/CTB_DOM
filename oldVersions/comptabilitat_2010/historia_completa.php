<?php
	include ("funcions.inc");
	$link_db = connectar_db();

	// Recuperar tots els numeros de pots comptables
	$sent_pc = "SELECT NUM, DESCRIPCIO FROM POTS_COMPTABLES ORDER BY NUM";
	$res_pc = mysql_db_query("comptabilitat", $sent_pc);
	$num_pcs = 0;
	while($row = mysql_fetch_array($res_pc)) { $llista_pc[$num_pcs++] = $row["NUM"]; }
	mysql_free_result($res_pc);

	// Recuperar tots els numeros de pots reals
	$sent_pr = "SELECT NUM, DESCRIPCIO FROM POTS_REALS ORDER BY NUM";
	$res_pr = mysql_db_query("comptabilitat", $sent_pr);
	$num_prs = 0;
	while($row = mysql_fetch_array($res_pr)) { $llista_pr[$num_prs++] = $row["NUM"]; }
	mysql_free_result($res_pr);

	$tam_cel_imp = 50;
        $tam_cap_1 = ($tam_cel_imp*($num_pcs)) + (($num_pcs-1)*4) + 10;
        $tam_cap_2 = ($tam_cel_imp*($num_prs)) + (($num_prs-1)*4) + 6;
        $tam_cap_3 = ($tam_cel_imp*($num_pcs+$num_prs)) + (($num_pcs+$num_prs+1)*4) + 16;

?>

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <link href="estils.css" rel="stylesheet" type="text/css"/>
    <script language="JavaScript" type="text/javascript">

        var ultima_operacio=0;
        var fila_sel;

        function nuevoAjax(){
                var xmlhttp=false;
                try {
                        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                        try {
                                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch (E) {
                                xmlhttp = false;
                        }
                }

                if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
                        xmlhttp = new XMLHttpRequest();
                }
                return xmlhttp;
        }


        function crearEvent(elemento, evento, funcion) {
              if (elemento.addEventListener) {
                    elemento.addEventListener(evento, funcion, false);
              } else {
                    elemento.attachEvent("on" + evento, funcion);
              }
        }

        function eliminarEvent(element, event, funcio) {
            if (element.removeEventListener) { //todos los navegadores excepto IE
                element.removeEventListener(event, funcio, false);
            } else if (element.detachEvent) {
                element.detachEvent("on"+event, funcio);
            }
        }

        // Insereix l'element nou inmediatament despres del element de referencia
        function insertAfter(element_ref, element_nou){
            if(element_ref.nextSibling){
                element_ref.parentNode.insertBefore(element_nou ,element_ref.nextSibling);
            } else {
                element_ref.parentNode.appendChild(element_nou);
            }
        }

        document.getElementsByClass = function(className) {
         var all = document.all ? document.all : document.getElementsByTagName('*');
         var elements = new Array();
         for (var e = 0; e < all.length; e++)
          if (all[e].className == className)
           elements[elements.length] = all[e];
         return elements;
        }



        function afagir_operacions(num_ops) {

          <?php echo "var pots_c=".$num_pcs.";";
                echo "var pots_r=".$num_prs.";";
                echo "var anymes='".$_REQUEST[anymes]."';";
                echo "var ordre='".$_REQUEST[ordre]."';";
                ?>

                ajax=nuevoAjax();
                ajax.open("POST", "cons_op.php", true);
                ajax.onreadystatechange=function(num_ops) {
                        if (ajax.readyState==4) {
                            operacions = ajax.responseText.split(",");

                            // El contingut de operacions[] recuperat és :
                            //  0: número d'operacions retornades (files)
                            //  1: -1 si no queden mes operacions a retornar
                            // per cada fila (el caracter @ indica final de fila) :
                            //  2: numero d'operacio
                            //  3: data (formatada)
                            //  4: descripcio
                            //  5: referencia
                            //  6: import

                            //alert (ajax.responseText);

                            ref_mov=2;
                            var elmTBODY = document.getElementById('CuerpoTabla');

                            for (i=1;i<=operacions[0];i++) {

                                  elmTR = document.createElement('tr');
                                  elmTBODY.appendChild(elmTR);

                                  // Columna oculta que indica l'expansió de l'operació (-1=no expandida)
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(-1);
                                  elmTD.style.display="none";
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  // Numero operacio
                                  ultima_operacio=operacions[ref_mov++];
                                  crearEvent(elmTR, "click", function() { clicar_operacio(this); });
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(ultima_operacio);
                                  elmTD.setAttribute("align","center");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  // Data operacio
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(operacions[ref_mov++]);
                                  elmTD.setAttribute("align","center");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);

                                  // Descripcio operacio
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(operacions[ref_mov++]);
                                  elmTD.setAttribute("align","left");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  // Import operacio
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(operacions[ref_mov++]);
                                  elmTD.setAttribute("align","right");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_c;j++) { elmTR.appendChild(afegir_cela(operacions[ref_mov++], true, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_r;j++) { elmTR.appendChild(afegir_cela(operacions[ref_mov++], true, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_c;j++) { elmTR.appendChild(afegir_cela(operacions[ref_mov++], false, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_r;j++) { elmTR.appendChild(afegir_cela(operacions[ref_mov++], false, false)); }

                                  if (operacions[ref_mov] != "@" && i == 1) { alert ("atencio, s'esta carregant contingut desplaçat"); }
                                  ref_mov++;
                            }

                            e=document.getElementById('mes_operacions');
                            e.scrollIntoView(true);

                            if (operacions[1] == -1) {
                                //eliminarEvent(e, "click", function () { alert("eliminat"); });
                                e.parentNode.removeChild(e);
                                //e.innerHTML = "final de la llista";
                            }
                            pintar_pijama();
                        }
                }
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                params = "max_ops="+num_ops;
                params += "&anymes="+anymes;
                params += "&ordre="+ordre;
                params += "&num_op="+ultima_operacio;

                //alert (params);
                ajax.send(params);

        }

        function expandir_moviments(fila_operacio) {

          <?php echo "var pots_c=".$num_pcs.";";
                echo "var pots_r=".$num_prs.";"; ?>

                num_op = fila_operacio.getElementsByTagName('td')[1].innerHTML;
                
                ajax=nuevoAjax();
                ajax.open("POST", "cons_mov.php", true);
                ajax.onreadystatechange=function() {
                        if (ajax.readyState==4) {
                            moviments = ajax.responseText.split(",");

                            // El contingut de moviments[] recuperat és :
                            //  0: número de moviments (files)
                            // per cada fila :
                            //  1: numero del moviment
                            //  2: import
                            //  variacions pots comptables
                            //  variacions pots reals
                            //  acumulats pots comptables
                            //  acumulats pots reals

                            ref_mov=1;
                            fila_operacio.getElementsByTagName('td')[0].innerHTML = moviments[0];
                            fila = fila_operacio;
                            
                            //var elmTBODY = document.getElementById('CuerpoTabla');

                            for (i=1;i<=moviments[0];i++) {
                                  //elmTR = elmTBODY.insertRow(num_fila + i);
                                  //num_col=0;

                                  elmTR = document.createElement('tr');
                                  elmTR.setAttribute("style","background-color:yellow");

                                  if (i==1) {
                                      //elmTD = elmTR.insertCell(num_col++);
                                      elmTD = document.createElement('td');
                                      elmTD.setAttribute("rowSpan",moviments[0]);
                                      elmTD.setAttribute("style","background-color:white");
                                      elmText = document.createTextNode("");
                                      elmTD.appendChild(elmText);
                                      elmTR.appendChild(elmTD);
                                  }

                                  //elmTD = elmTR.insertCell(num_col++);
                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(moviments[ref_mov++]);
                                  elmTD.setAttribute("colSpan","2");
                                  elmTD.setAttribute("align","right");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  elmTD = document.createElement('td');
                                  elmText = document.createTextNode(moviments[ref_mov++]);
                                  elmTD.setAttribute("align","right");
                                  elmTD.appendChild(elmText);
                                  elmTR.appendChild(elmTD);


                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_c;j++) { elmTR.appendChild(afegir_cela(moviments[ref_mov++], true, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_r;j++) { elmTR.appendChild(afegir_cela(moviments[ref_mov++], true, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_c;j++) { elmTR.appendChild(afegir_cela(moviments[ref_mov++], true, false)); }
                                  elmTR.appendChild(afegir_cela("", true, true));
                                  for (j=1;j<=pots_r;j++) { elmTR.appendChild(afegir_cela(moviments[ref_mov++], true, false)); }

                                  insertAfter(fila, elmTR);
                                  fila = elmTR;
                            }

                        }
                }
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.send("num_op="+num_op);
        }

        function contraure_moviments() {
          /*var elmTBODY = document.getElementById('CuerpoTabla');
          for (i=num_movs;i>0;i--) {
              //elmTBODY.deleteRow(fila_expand + 1);
          }*/
        }

        function afegir_cela(valor, nozero, nofons) {
              elmTD = document.createElement('td');
              if (valor == 0 && nozero) { valor = ''; }
              elmText = document.createTextNode(valor);
              elmTD.setAttribute("align","right");
              if (nofons) { elmTD.setAttribute("style","background-color: white"); }
              elmTD.appendChild(elmText);
              return elmTD;
        }

        function clicar_operacio(fila) {
            tab=document.getElementById('CuerpoTabla');
            movs_expand = (fila.getElementsByTagName('td')[0]).innerHTML;

            if (movs_expand == -1) {
                fila.style.background = '#cccc00';
                expandir_moviments(fila);
            } else {
                for (i=0; ele=(fila.parentNode).getElementsByTagName('tr')[i]; i++) {
                    if (ele == fila) {
                        for (j=1;j<=movs_expand;j++) {
                            seg=tab.getElementsByTagName('tr')[i+1];
                            tab.removeChild(seg);
                        }
                        break;
                    }
                }
                (fila.getElementsByTagName('td')[0]).innerHTML = -1;
                canviar_color_fila(fila, -1);
            }
        }

        function canviar_color_fila(fila, fondo) {
            //document.title = (fila.getElementsByTagName('td')[0]).innerHTML;
            if ((fila.getElementsByTagName('td')[0]).innerHTML == -1) {
                if (fondo == -1) { fila.style.background = '#ffffaa'; }
                if (fondo ==  0) { fila.style.background = '#ffffff'; }
                if (fondo ==  1) { fila.style.background = '#eeeeee'; }
            }
        }

        function pintar_pijama() {
            tab=document.getElementById('CuerpoTabla');
            for (i=0; ele=tab.getElementsByTagName('tr')[i]; i++) {
                if (((ele.getElementsByTagName('td')[0]).innerHTML) == -1) {
                    fondo = (i % 2 == 0) ? '#ffffff' : '#eeeeee';
                    ele.style.background = fondo;
                    ele.onmouseover = function() { canviar_color_fila(this, -1); }
                    if (i % 2 == 0) { ele.onmouseout = function() { canviar_color_fila(this, 0); } }
                    else {            ele.onmouseout = function() { canviar_color_fila(this, 1); } }
                }
            }
        }

        function filtrar(anymes) {
            elem=document.getElementById('filtre');
            elem2=document.getElementById('filtre2');
            //alert (elem.options[elem.selectedIndex].value);
            window.location='historia_completa.php?num_vis=30&anymes='+elem.options[elem.selectedIndex].value+'&ordre='+elem2.options[elem2.selectedIndex].value;
        }

        function mostrar_ocultar_col(visible) {
            dis = visible ? '' : 'none';
            vis = visible ? 'collapse' : 'visible';
            var1 = visible ? 10 : -10;

            document.getElementById('grup1').style.visibility = vis;


            tab=document.getElementById('capsalera');
            fila = tab.getElementsByTagName('tr')[1];
            fila.getElementsByTagName('th')[2].colSpan+=var1;

            fila = tab.getElementsByTagName('tr')[2];
            fila.getElementsByTagName('th')[0].style.display = dis;
            fila.getElementsByTagName('th')[1].style.display = dis;

            fila = tab.getElementsByTagName('tr')[3];

/*
            for (i=5; i<=14; i++) { fila.getElementsByTagName('th')[i].style.display = dis; }

            tab=document.getElementById('CuerpoTabla');

            for (i=0; ele=tab.getElementsByTagName('tr')[i]; i++) {
                for (j=5; j<=14; j++) { ele.getElementsByTagName('td')[j].style.display=dis; }
            }*/

        }


        window.onload = function() {
            //cap=document.getElementById("cap302").colSpan="9";
            <?php echo "afagir_operacions(".$_REQUEST[num_vis].")"; ?>
        }

</script>

</head>

<body>


<br>

<div style="position: absolute; left: 10px; top: 10px; border-style: solid; border-width: 1px; padding: 5px;">
    <?php 

        echo "<p> Filtre any mes : ";
        echo "<select id='filtre' onChange='filtrar()'>";
        echo "<option";
        if ($_REQUEST[anymes] == '999999') { echo " selected='true'"; }
        echo " value=999999> Tots </option>";
            
        $sentencia2 = "
SELECT DATE_FORMAT(DATA, '%Y%m') D1,
       DATE_FORMAT(DATA, '%Y %m') D2
  FROM OPERACIONS
 GROUP BY DATE_FORMAT(DATA, '%Y%m')
 ORDER BY DATE_FORMAT(DATA, '%Y%m') DESC";

        $result = mysql_db_query("comptabilitat", $sentencia2);
        while($row = mysql_fetch_array($result)) {
            echo "<option ";
            if ($row["D1"] == $_REQUEST[anymes]) { echo "selected='true'"; }
            echo "value=".$row["D1"]."> ".$row["D2"]."</option>";
        }
        mysql_free_result($result);        

        echo "</select>";

        echo " Ordre : ";
        echo "<select id='filtre2' onChange='filtrar()'>";

        echo "<option";
        if ($_REQUEST[ordre] == 'A') { echo " selected='true'"; }
        echo " value='A'> primer -> ultim </option>";

        echo "<option";
        if ($_REQUEST[ordre] == 'D') { echo " selected='true'"; }
        echo " value='D'> ultim -> primer </option>";

        echo "</select>";

        echo "<input type='button' name='af3' value='mostrar' onClick='mostrar_ocultar_col(true);'>";
        echo "<input type='button' name='af3' value='ocultar' onClick='mostrar_ocultar_col(false);'>";
        echo "</p>";

        ?>
</div>


<div id="contenidor1">
    <table class="taula_cont_1" width="1880px" style="position: absolute; left:0px; top: 37px;">
        <!--<caption><p class="titol">Historia completa</p></caption>-->

        <colgroup width="30px"></colgroup>
        <colgroup width="90px"></colgroup>
        <colgroup width="230px"></colgroup>
        <colgroup width="50px"></colgroup>
        <colgroup width="20px"></colgroup>
        <colgroup id="grup1" span=9 width="50px"></colgroup>
        <colgroup width="20px"></colgroup>
        <colgroup id="grup2" span=5 width="50px"></colgroup>
        <colgroup width="20px"></colgroup>
        <colgroup id="grup1" span=9 width="50px"></colgroup>
        <colgroup width="20px"></colgroup>
        <colgroup id="grup2" span=5 width="50px"></colgroup>

        <thead id="capsalera">
        <tr>
            <th id="cap101" width="1880px" colspan="36">
                <p class="titol">Historia completa</p>
            </th>
        </tr>
        <tr>
            <th id="cap201" rowspan="2" colspan="4" class="titol" style="font-size: 12px;">OPERACIONS</th>
            <th></th>
            <th id="cap202" colspan="15" class="titol" style="font-size: 12px;">VARIACIO</th>
            <th></th>
            <th id="cap203" colspan="15" class="titol" style="font-size: 12px;">ACUMULAT</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="9" class="titol" style="font-size: 12px;"> Comptables </th>
            <th></th>
            <th colspan="5" class="titol" style="font-size: 12px;"> Reals </th>
            <th></th>
            <th colspan="9" class="titol" style="font-size: 12px;" onClick='ocultar()'> Comptables </th>
            <th></th>
            <th colspan="5" class="titol" style="font-size: 12px;" onClick='mostrar()'> Reals </th>
        </tr>
        <tr style="background-color:#aaaaff;">
            <th>Num</th>
            <th>Data</th>
            <th>Desc</th>
            <th>Import</th>

            <?php


                echo "<span id='caps_pc'>";
                echo "<th width='20px' style='background-color:#ffffff;'></th>";
                for ($i=1;$i<=$num_pcs;$i++) { echo "<th width='50px'>".$llista_pc[$i-1]."</th>"; }
                echo "</span>";

                echo "<th width='20px' style='background-color:#ffffff;'></th>";
                for ($i=1;$i<=$num_prs;$i++) { echo "<th width='50px'>".$llista_pr[$i-1]."</th>"; }
                echo "<th width='20px' style='background-color:#ffffff;'></th>";
                for ($i=1;$i<=$num_pcs;$i++) { echo "<th width='50px'>".$llista_pc[$i-1]."</th>"; }
                echo "<th width='20px' style='background-color:#ffffff;'></th>";
                for ($i=1;$i<=$num_prs;$i++) { echo "<th width='50px'>".$llista_pr[$i-1]."</th>"; }
                
           
            ?>

        </tr>
        </thead>

        <tbody id="CuerpoTabla">
        </tbody>

        <tfoot id='mes_operacions'>
             <tr> <td colspan=36>
                 <a onclick="afagir_operacions(20);"> + operacions </a>
             </td> </tr>
        </tfoot>

    </table>

</div>


</body>
</html>

<?php
	mysql_close($link_db);
?>

