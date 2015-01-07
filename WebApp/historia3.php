<html>

MODDDD

<head>
	<meta charset="UTF-8">
	<title> Història </title>
	<style>
	
		html,body {
			font:normal normal normal 10px/10px Verdana;
		}
		
		
		.capsalera_llista {
			border: 1px solid #000000;
			border-style: none;
			border-left: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.fila_operacio {
			border: 1px solid #000000;
			background-color: #f8f8f8;
			border-style: none;
			border-left: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.fila_moviment {
			border: 1px solid #000000;
			background-color: #e2e2e2;
			border-style: none;
			border-top: 1px solid #bbbbbb;
			border-left: 1px solid #bbbbbb; 
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.col_mov1 { width:  10px; border-style: none; }
		.col_mov2 { width:  10px; border-style: none; }
		.col_mov3 { width:  20px; border-style: none; }
		.col_mov4 { width:  90px; text-align: center; border-left-color: #000000;}
		.col_mov5 { width: 300px; text-align: left;   }
		.col_mov6 { width: 120px; text-align: right;  }
		
		
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

		function add_mov_new_op() {
			var elmTD;
			var elemText;
			
			var elmTBODY = document.getElementById('taula_nova_op');
			var TR_ult_mov = document.getElementById('tr_new_op_mov_1');
			var TR_moviment = document.createElement('tr');

			elmTD = TR_moviment.insertCell(0);
			elmTD.appendChild(document.createTextNode("yyy"));
			elmTD.setAttribute("colSpan", "7");
			
			elmTBODY.insertBefore(TR_moviment, TR_ult_mov.nextSibling);
			
		}

        
	</script>


	
</head>

<body style="overflow: hidden; display: flex; flex-flow: row wrap; align-content:flex-start; ">



	<div id="nova_operacio" style="
		left: 5px; top: 5px;
		width: 95%;
		border: 1px solid #000000;
		background-color: #eeeeee;
		padding: 5px; 
		z-index:60;
		">

		<form>
		
			<table style="
				table-layout: fixed;
				border-collapse: collapse;
				font:normal normal normal 10px/10px Verdana;
				">

				<thead style="
					position:relative;
					display:block; 
					">
					<tr>
						<th style="width:  90px;"> Data </th>
						<th style="width: 300px;"> Descripció </th>
						<th style="width:  80px;"> Import </th>
						<th style="width: 100px;"> Cta. Real </th>					
						<th style="width: 100px;"> Cta. Ctable 1 </th>
						<th style="width: 100px;"> Cta. Ctable 2 </th>
						<th style="width:  30px;"> </th>					
						<th style="width:  06px;"> </th>
					</tr>
				</thead>

				<tbody id="taula_nova_op" style="
					border-color: #000000;				
					border-style: none;
					border-width: 1px;
					background-color: #ffffff;
					display:block;
					overflow:auto;
					">


					<tr >
						<td> <input style="width:  90px; text-align: center;" disabled="disabled" value="14-02-2012" /> </td>
						<td> <input style="width: 300px; text-align: left;"   value="mov1" /> </td>
						<td> <input style="width:  80px; text-align: right;"  disabled="disabled" value="-24.98" /> </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>						
						<td style="width:  30px;" > </td>
					</tr>

					<tr id="tr_new_op_mov_1" >
						<td> <input style="width:  90px; text-align: center;" value="14-02-2012" /> </td>
						<td> <input style="width: 300px; text-align: left;"   value="mov1" /> </td>
						<td> <input style="width:  80px; text-align: right;"  value="-24.98" /> </td>
						<td> <select style="width: 100px; text-align: right; ">
								<option value="volvo">Volvo</option>
								<option value="saab">Saab</option>
								<option value="mercedes">Mercedes</option>
								<option value="audi">Audi</option>
						</select> </td>

						<td> <select style="width: 100px; text-align: right; ">
								<option value="volvo">Volvo</option>
								<option value="saab">Saab</option>
								<option value="mercedes">Mercedes</option>
								<option value="audi">Audi</option>
						</select> </td>

						<td> <select style="width: 100px; text-align: right; ">
								<option value="volvo">Volvo</option>
								<option value="saab">Saab</option>
								<option value="mercedes">Mercedes</option>
								<option value="audi">Audi</option>
						</select> </td>
						
						<td style="width:  30px; text-align: right; "> <button onclick="add_mov_new_op();"> Add </button> </td>
					</tr>



				</tbody>
			</table> 
		
			<button> OK : Insertar operació i moviments </button>
		</form>
		
	</div>	
	
	
	<div id="llista" style="
		margin-top: 5px;
		width: 95%;
		border: 1px solid #000000;
		background-color: #eeeeee;
		padding: 5px; 
		z-index:10;
		">

		<table style="
			table-layout: fixed;
			border: 1px solid #000000;
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
					<th class="capsalera_llista" style="width:  20px; border-left-style: none;"> </th>
					<th class="capsalera_llista" style="width:  90px;"> Data </th>
					<th class="capsalera_llista" style="width: 300px;"> Descripció </th>
					<th class="capsalera_llista" style="width:  80px;"> Import </th>
					<th class="capsalera_llista" style="width: 100px;"> Cta. Real </th>					
					<th class="capsalera_llista" style="width: 100px;"> Cta. Ctable 1 </th>
					<th class="capsalera_llista" style="width: 100px;"> Cta. Ctable 2 </th>
					<th class="capsalera_llista" style="width:  06px;"> </th>
				</tr>
			</thead>

			<tbody id="taula_operacions" style="
				border-color: #000000;				
				border-style: none solid solid none;
				border-width: 1px;
				background-color: #ffffff;
				display:block;
				height:500px;
				overflow:scroll;
				">


				<tr style="padding:0px; border-spacing: 0px; border: 1px solid #000000; border-left-style: none;">
					<td colspan="6" style="padding:0px; border-spacing: 0px; border-left-style: none;">
						<table name="taula_op" style="border-spacing: 0px; border-style: none;">

							<thead>
								<tr>
									<th class="fila_operacio" style="width:  20px; border-left-style: none;"> </th>
									<th class="fila_operacio" style="width:  90px; text-align: center;"> 06-08-2014 </th>
									<th class="fila_operacio" style="width: 300px; text-align: left;  "> operació 1 </th>
									<th class="fila_operacio" style="width:  80px; text-align: right; "> 534.42 </th>
									<th class="fila_operacio" style="width: 100px; text-align: right; "> </th>
									<th class="fila_operacio" style="width: 100px; text-align: right; "> </th>
									<th class="fila_operacio" style="width: 100px; text-align: right; "> </th>

								</tr>
							</thead>

							<tbody style="display: inerhit;">
								<tr >
									<td class="fila_moviment" style="width:  20px; border-style: none; "> </td>
									<td class="fila_moviment" style="width:  90px; text-align: center; border-left-color: #000000;" > 14-02-2012 </td>
									<td class="fila_moviment" style="width: 300px; text-align: left;  "> mov1 </td>
									<td class="fila_moviment" style="width:  80px; text-align: right; "> -24.98 </td>
									<td class="fila_moviment" style="width: 100px; text-align: right;  "> 7. Traspas C </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5.4. Internet </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5. Serveis </td>
								</tr>
								<tr >
									<td class="fila_moviment" style="width:  20px; border-style: none; "> </td>
									<td class="fila_moviment" style="width:  90px; text-align: center; border-left-color: #000000;" > 14-02-2012 </td>
									<td class="fila_moviment" style="width: 300px; text-align: left;  "> mov1 </td>
									<td class="fila_moviment" style="width:  80px; text-align: right; "> -24.98 </td>
									<td class="fila_moviment" style="width: 100px; text-align: right;  "> 7. Traspas C </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5.4. Internet </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5. Serveis </td>
								</tr>
								<tr >
									<td class="fila_moviment" style="width:  20px; border-style: none; "> </td>
									<td class="fila_moviment" style="width:  90px; text-align: center; border-left-color: #000000;" > 14-02-2012 </td>
									<td class="fila_moviment" style="width: 300px; text-align: left;  "> mov1 </td>
									<td class="fila_moviment" style="width:  80px; text-align: right; "> -24.98 </td>
									<td class="fila_moviment" style="width: 100px; text-align: right;  "> 7. Traspas C </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5.4. Internet </td>
									<td class="fila_moviment" style="width: 100px; text-align: right; "> 5. Serveis </td>
								</tr>

							</tbody>

						</table>
					</td>
				</tr>




			</tbody>
		</table> 
		
		
	</div>	
	



</body>
</html>
