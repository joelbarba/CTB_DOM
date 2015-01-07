<html>
<head>
	<meta charset="UTF-8">
	<title> Història </title>
	<style>
	
		html,body {
			height:100%;
			font:normal normal normal 10px/10px Verdana;
		}
		
		th, td {
    		border: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
		}
		
		.fila_operacio {
			background-color: #f8f8f8;
		}
		
		.fila_moviment {
			background-color: #e2e2e2;
			border-color: #bbbbbb;
			border-bottom-style: none;
			
		}
		
	</style>

	<script language="Javascript">
	
		function expandir_op(num_reg, id_operacio) {

			var xmlhttp;
			
			if (window.XMLHttpRequest) {	xmlhttp = new XMLHttpRequest();			// code for IE7+, Firefox, Chrome, Opera, Safari
	  		} else {						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	// code for IE6, IE5
			}
			
			xmlhttp.onreadystatechange = function() { 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					// alert ("Resposta rebuda");
					expandir_op_resp(num_reg, id_operacio, xmlhttp.responseText);
				}
			}
		
			// alert ("Cridant a cons_mov.php");
			xmlhttp.open("GET", "cons_mov.php?id_operacio=" + id_operacio, true);
			xmlhttp.send();
		}
		
		
		function expandir_op_resp(num_reg, id_operacio, resultat) {

			var elmTBODY = document.getElementById('taula_operacions');
			var TR_operacio;
			var TR_moviment;
			var TR_ult_mov;
			var elmTD;
			var elemText;

			// alert ("Executant funcio resposta");
			
			var moviments = resultat.split(",");
			alert (moviments);
			
			TR_operacio = document.getElementById('fila_num_' + num_reg);
			elmTD = TR_operacio.firstChild;
			TR_ult_mov = TR_operacio;
			
			var ref_mov = 1;
			for (t = 1; t <= moviments[0]; t++) {

				TR_moviment = document.createElement('tr');
				
				elmTD = TR_moviment.insertCell(0);
				elmTD.setAttribute("colSpan", "3");
				elmTD.setAttribute("style", "border-style:none solid none solid;");
				
				elmTD = TR_moviment.insertCell(1);
				elmTD.setAttribute("align", "center");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode(moviments[ref_mov++]));

				elmTD = TR_moviment.insertCell(2);
				elmTD.setAttribute("align", "left");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode(moviments[ref_mov++]));

				elmTD = TR_moviment.insertCell(3);
				elmTD.setAttribute("align", "right");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode(moviments[ref_mov++]));
				
				elmTBODY.insertBefore(TR_moviment, TR_ult_mov.nextSibling);
				TR_ult_mov = TR_moviment;			

			}
			
			/*
            TR_operacio = document.getElementById('fila_num_' + num_reg);
            elmTD = TR_operacio.firstChild;
            elemText = elmTD.firstChild;
            // alert (elemText.textContent);
            
			TR_ult_mov = TR_operacio;
			
			for (t = 1; t <= 8; t++) {
			
				TR_moviment = document.createElement('tr');
				
				elmTD = TR_moviment.insertCell(0);
				elmTD.setAttribute("colSpan", "3");
				elmTD.setAttribute("style", "border-style:none solid none solid;");
				
				
				elmTD = TR_moviment.insertCell(1);
				elmTD.setAttribute("align", "center");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode('2012-04-15'));

				elmTD = TR_moviment.insertCell(2);
				elmTD.setAttribute("align", "left");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode('Desc mov' + t));

				elmTD = TR_moviment.insertCell(3);
				elmTD.setAttribute("align", "right");
				elmTD.setAttribute("class", "fila_moviment");
				elmTD.appendChild(document.createTextNode('-54.19'));
				
				
				elmTBODY.insertBefore(TR_moviment, TR_ult_mov.nextSibling);
				TR_ult_mov = TR_moviment;			
			}
*/
		}



        
	</script>


	
</head>

<body style="overflow: hidden; ">

	<div id="capsalera" style="
		position: fixed; top: 5px; left: 5px;
		width: 98%; height: 100px;
		border: 1px solid #000000;
		background-color: #dddddd; 
		z-index:50;
		">
	</div>

	<div id="llista" style="
		position: absolute; left: 5px; top: 110px;
		width: 95%;
		border: 1px solid #000000;
		background-color: #eeeeee;
		padding: 5px; 
		z-index:10;
		">

		<table style="
			table-layout: fixed;
			border-collapse: collapse;
			border-spacing: 2px;
			font-family: Courier New; font-size: 11px;
			">

			<thead style="
				background-color: #88ddee;
				position:relative;
				display:block; 
				">
				<tr>
					<th style="width: 10px;"> </th>
					<th style="width: 10px;"> </th>
					<th style="width: 20px; border-right-style: none;"> </th>
					<th style="width: 90px; border-left-style: none; "> Data </th>
					<th style="width: 300px;"> Descripció </th>
					<th style="width: 120px;"> Import </th>
					<th style="width: 06px;"> </th>
				</tr>
			</thead>

			<tbody id="taula_operacions" style="
				border-color: #000000;				
				border-style: none solid solid none;
				border-width: 1px;
				background-color: #ffffff;
				display:block;
				height:600px;
				overflow:auto;
				">

				<?php 
				
				$dbconn = pg_connect("host=localhost dbname=db_comptabilitat user=barba password=barba0001")
							or die('No s\'ha pogut connectar : ' . pg_last_error());
				
				$consulta = "
					select t1.id_operacio 		as id_operacio, 
						   count(*) 			as total_movs, 
						   min(data_moviment) 	as data_op, 
						   t1.descripcio		as descripcio, 
						   sum(t2.import) 		as import
					  from operacions t1,
						   moviments  t2
					 where t1.id_operacio = t2.id_operacio
					 group by t1.id_operacio, t1.descripcio
					 order by min(data_moviment), t1.id_operacio
				";
				$result = pg_query($dbconn, $consulta);
				$x = 0;
				// while ($x < pg_numrows($result)) {
				while ($x < 80) {
					echo '<tr class="fila_operacio" id="fila_num_'.$x.'">';
					echo '<td class="fila_operacio"> 0 </td>';
					echo '<td class="fila_operacio"> '.pg_fetch_result($result, $x, "id_operacio").' </td>';

					echo '<td onclick="expandir_op('
							.$x.', '
							.pg_fetch_result($result, $x, "id_operacio")
							.');" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> '.$x.' </td>';

			        echo '<td style="width:  90px; text-align: center; border-left-style: none; ">'.
								pg_fetch_result($result, $x, "data_op").'</td>';
					echo '<td class="fila_operacio" style="width: 300px; text-align: left;">  '.pg_fetch_result($result, $x, "descripcio").' </td>';
					echo '<td class="fila_operacio" style="width: 120px; text-align: right;"> '.pg_fetch_result($result, $x, "import").' </td>';
			        echo '</tr>';
		        
			        $x++;
				}				
				
				pg_close($dbconn);
				
				?>

			</tbody>
		</table> 

	</div>


</body>
</html>
