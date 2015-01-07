<html>
<head>
	<meta charset="UTF-8">
	<title> Història de tot</title>
	<style>
	
		html,body {
			height:100%;
			font:normal normal normal 10px/10px Verdana;
		}
		
		th, td {
    		border: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.fila_operacio {
			border-style: none;
			border-right: 1px solid #000000;
			background-color: #f8f8f8;
		}
		
		.fila_moviment {
			background-color: #e2e2e2;
			border-color: #bbbbbb;
			border-style: none;
			border-right-style: solid;
			
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


			
				<tr class="fila_operacio" id="fila_num_0">
					<td class="fila_operacio" > 0 </td>
					<td class="fila_operacio"> 1 </td>
					<td onclick="expandir_op(0,1);" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> 0 </td>
					<td style="width:  90px; text-align: center; border-left-style: none; "> 05-02-2009 </td>
					<td class="fila_operacio" style="width: 300px; text-align: left;">  operacio de prova 2 </td>
					<td class="fila_operacio" style="width: 120px; text-align: right;"> -47.29 </td>
				</tr>

				<tr class="fila_operacio" id="fila_num_1">
					<td class="fila_operacio" > 0 </td>
					<td class="fila_operacio"> 1 </td>
					<td onclick="expandir_op(0,1);" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> 0 </td>
					<td style="width:  90px; text-align: center; border-left-style: none; "> 18-02-2009 </td>
					<td class="fila_operacio" style="width: 300px; text-align: left;">  operacio de prova 3 </td>
					<td class="fila_operacio" style="width: 120px; text-align: right;"> -89.12 </td>
				</tr>

				<tr class="fila_operacio" id="fila_num_2">
					<td class="fila_operacio" > 0 </td>
					<td class="fila_operacio"> 1 </td>
					<td onclick="expandir_op(0,1);" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> 0 </td>
					<td style="width:  90px; text-align: center; border-left-style: none; "> 15-03-2009 </td>
					<td class="fila_operacio" style="width: 300px; text-align: left;">  operacio de prova 4 </td>
					<td class="fila_operacio" style="width: 120px; text-align: right;"> 130.45 </td>
				</tr>					

				
				<tr class="fila_operacio" id="fila_num_20">
					<td colspan="6" style="padding:0px; border-spacing: 0px;">
						<table name="taula_op" style="border-spacing: 0px; border-style: none;">

							<tr class="fila_operacio" id="fila_num_1" style="padding:0px; border-spacing: 0px; border-style: none;">
								<td class="fila_operacio" style="width:  20px;" > 0 </td>
								<td class="fila_operacio" style="width:  20px;"> 1 </td>
								<td onclick="expandir_op(0,1);" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> 0 </td>
								<td class="fila_operacio" style="width:  90px; text-align: center;"> 18-02-2009 </td>
								<td class="fila_operacio" style="width: 300px; text-align: left;">  operacio de prova 3 </td>
								<td class="fila_operacio" style="width: 120px; text-align: right; border-right-style: none;"> -89.12 </td>
							</tr>

							<tr style="padding:0px; border-spacing: 0px; border-style: none; ">
								<td colspan="6" style="border-style: none; padding: 0px; ">
									<table name="taula_mov" style="border-spacing: 0px; border-style: none;">
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
									</table>
								</td>
							</tr>


						</table>
					</td>
				</tr>				


				<tr class="fila_operacio" id="fila_num_20">
					<td colspan="6" style="padding:0px; border-spacing: 0px;">
						<table name="taula_op" style="border-spacing: 0px; border-style: none;">

							<tr class="fila_operacio" id="fila_num_1" style="padding:0px; border-spacing: 0px; border-style: none;">
								<td class="fila_operacio" style="width:  20px;" > 0 </td>
								<td class="fila_operacio" style="width:  20px;"> 1 </td>
								<td onclick="expandir_op(0,1);" style="width:  20px; border-bottom-style: none; " class="fila_operacio"> 0 </td>
								<td class="fila_operacio" style="width:  90px; text-align: center;"> 18-02-2009 </td>
								<td class="fila_operacio" style="width: 300px; text-align: left;">  operacio de prova 3 </td>
								<td class="fila_operacio" style="width: 120px; text-align: right; border-right-style: none;"> -89.12 </td>
							</tr>

							<tr style="padding:0px; border-spacing: 0px; border-style: none; ">
								<td colspan="6" style="border-style: none; padding: 0px; ">
									<table name="taula_mov" style="border-spacing: 0px; border-style: none;">
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
										<tr class="fila_moviment" style="padding:0px; border-spacing: 0px; border-style: none;">
											<td class="fila_operacio" style="width:  82px; "> </td>
											<td class="fila_moviment" style="width:  90px; text-align: center; border-top: 1px solid #bbbbbb;"> 18-02-2009 </td>
											<td class="fila_moviment" style="width: 300px; text-align: left;   border-top: 1px solid #bbbbbb;">  moviment 1 </td>
											<td class="fila_moviment" style="width: 120px; text-align: right;  border-top: 1px solid #bbbbbb; border-right-style: none;"> -89.12 </td>
										</tr>
									</table>
								</td>
							</tr>


						</table>
					</td>
				</tr>	
				
				
			</tbody>
		</table> 

	</div>


</body>
</html>
