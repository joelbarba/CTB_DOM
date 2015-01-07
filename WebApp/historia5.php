<?php 

	include ("funcions.inc");

	$col_w1 = 20;
	$col_w2 = 90;
	$col_w3 = 300;
	$col_w4 = 80;
	$col_w5 = 105;
	$col_w6 = 150;
	$col_w7 = 80;
	
	array_comptes($codi_compte_real, $codi_compte_comptable);
	
?>

<html>
<head>
	<meta charset="UTF-8">
	<title> Història </title>
	<style>
	
		html,body,input,select{
			font:normal normal normal 10px/10px Verdana;
		}
		
		
		.capsalera_llista {
			border-style: none;
			border-left: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.fila_operacio {
			background-color: #f8f8f8;
			border-style: none;
			border-left: 1px solid #000000;
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
		.fila_moviment {
			background-color: #e2e2e2;
			border-style: none;
			border-top: 1px solid #bbbbbb;
			border-left: 1px solid #bbbbbb; 
    		padding-left: 5px; padding-right: 5px;
			font:normal normal normal 10px/10px Verdana;
		}
		
	</style>

	<script language="Javascript">

		var comptes_reals = new Array(0);
		var comptes_comptables = new Array(0);

		window.onload = function() {


			// Carregar els arrays amb els comptes reals i comptables des del servidor (PHP) al client (javascript)
			<?php
		
				for ($t = 0; $t < count($codi_compte_real); $t++) {
					echo "comptes_reals.push('". $codi_compte_real[$t] ."'); ";
				}
				
				/*
				codi_compte_comptable = 
				Array ( 
					[0] => Array ( 
						[cc1] => 1. General 
						[cc2] => Array ( 
							[0] => 1.1. Estalvi 
							[1] => 1.2. RBE 
							[2] => 1.3. Traspas R */ 				
				for ($t = 0; $t < count($codi_compte_comptable); $t++) {
					
					echo "comptes_comptables.push(new Array('". $codi_compte_comptable[$t]["cc1"] ."', new Array(0)));";
					
					for ($q = 0; $q < count($codi_compte_comptable[$t]["cc2"]); $q++) {
						echo "comptes_comptables[comptes_comptables.length - 1][1].push('". 
								$codi_compte_comptable[$t]["cc2"][$q] 
								."');";			
					}
				}
			
			?>
			// alert (comptes_reals.join("\n"));
			// alert (comptes_comptables.join("\n"));

			// Carregar inicialment els comptes de 1 
			canvi_sel2_new_op(1);
			// canvi_import_mov();
			
			// Inicialitzar data 1er moviment a data actual
			var date_act = new Date();
			var data_mov1 = document.getElementById('tr_new_op_mov_1').cells[0].firstElementChild; // setAttribute("defaultValue", today);
			data_mov1.valueAsDate = date_act;
		
		}

		// Reactualitzar el combo3 (compte comptable 2) en funció del valor del combo 2 (compte comptable 1)
		function canvi_sel2_new_op(num_mov) {

			var elemOP;
			var sel_cta2 = document.getElementById('sel2_new_op_mov_' + num_mov);
			var sel_cta3 = document.getElementById('sel3_new_op_mov_' + num_mov);
			
			// alert ("sel_cta2 = " + sel_cta2.value);
			// alert (comptes_comptables[sel_cta2.value][0]);
			// alert (comptes_comptables[sel_cta2.value][1].join(" - "));

			// Elimina tots els nodes actuals del select 3
			while (sel_cta3.firstChild) {
				sel_cta3.removeChild(sel_cta3.firstChild);
			}

			// Genera tots els comptes comptables (nivell2) al select3			
			for (var t = 0; t < comptes_comptables[sel_cta2.value][1].length; ++t) {
				// alert (comptes_comptables[sel_cta2.value][1][t]);
				elemOP = document.createElement("option");
				elemOP.setAttribute("value", t);
				elemOP.appendChild(document.createTextNode(comptes_comptables[sel_cta2.value][1][t]));
				sel_cta3.appendChild(elemOP);
			}

			
			
		}

		
		// KeyDown : Validar que només s'introdueixi un valor d'import correcte
		function validar_import() {
		
			var srcField = this.event.srcElement;
			var sKey = this.event.keyCode;
			document.title = " - " + sKey;
			
			var es_digit = false;
			if ((sKey >= 48 && sKey <= 57 && this.event.location == 0)
				||
				(sKey >= 96 && sKey <= 105 && this.event.location == 3))
				{ es_digit = true; }
			
			// Permetre només aquestes tecles
			if (!es_digit
				&& (sKey < 96 || sKey > 105) // numerical (pad)
				&& sKey != 16 // shift
				&& sKey != 188 // ,
				&& sKey != 189 // -
				&& sKey != 109 // -
				&& sKey != 190 // .
				&& sKey != 110 // .
				&& sKey != 46 // delete
				&& sKey !=  8 // back space
				&& sKey !=  9 // tab
				&& sKey != 35 // <- Ini
				&& sKey != 36 // <- Fin
				&& sKey != 37 // <- arrow 
				&& sKey != 39 // -> arrow
				&& (sKey != 67 || !this.event.ctrlKey) // -> Ctrl + C
				&& (sKey != 88 || !this.event.ctrlKey) // -> Ctrl + X
				&& (sKey != 86 || !this.event.ctrlKey) // -> Ctrl + V
				) {
				event.preventDefault();
				return false;				
			}
		

		
			// Només permet posar el '-' davant del número
			if ((sKey == 189 || sKey == 109) && srcField.value.indexOf("-") != -1) { event.preventDefault(); return false; }
			if ((sKey == 189 || sKey == 109) && srcField.selectionStart != 0) { srcField.setSelectionRange(0, 0); }
			
			// Només permetre un punt decimal
			if ((sKey == 188 || sKey == 190 || sKey == 110) && srcField.value.indexOf(".") != -1) { event.preventDefault(); return false; }
			
			// No permetre posar la coma (punt) més a enllà de 2 dígits a la dreta
			if ((sKey == 188 || sKey == 190 || sKey == 110) && srcField.selectionStart < srcField.value.length - 2) { event.preventDefault(); return false; }
			
			// Només permetre 2 dígits despés de la coma (punt)
			if (es_digit
				&& srcField.value.indexOf(".") != -1
				&& srcField.value.substring(srcField.value.indexOf(".") + 1).length >= 2
				&& srcField.selectionStart > srcField.value.indexOf(".")
				) {
				event.preventDefault(); return false;
			}
		
			// Eliminar 0's a l'esquerra abans de la coma (punt)
			if (es_digit && srcField.value == '0' && srcField.selectionStart > 0) { srcField.value = ''; }
			
			// No permetre posar dígits a l'esquerra del -
			if (es_digit && srcField.selectionStart <= srcField.value.indexOf("-")) { event.preventDefault(); return false; }
			
	
		}
		
		// KeyUp : Actualitzar el import total de la operació segons la suma dels seus moviments (quan el imput perd el focus i ha canviat)
		function canvi_import_mov() {

			var srcField = this.event.srcElement;
			// document.title += '+';
		
			// Si hi ha una ',' convertir-la en un '.'
			if (srcField.value.indexOf(",") != -1) {
				var caretPos = srcField.selectionStart;
				srcField.value = srcField.value.replace(',', '.');
				srcField.setSelectionRange(caretPos, caretPos);
			}
			
			// Si no hi ha valor, posar un 0
			if (srcField.value.length == 0) {
				srcField.value = 0;
			}

			var inputOp = document.getElementById('import_total_op');
			
			var tots_movs = document.getElementsByName('import_new_op_mov');
			var total = 0;
			for (var t = 0; t < tots_movs.length; t++) {
				if (tots_movs[t].value) {
					if (!isNaN(tots_movs[t].value)) {
						total += parseFloat(tots_movs[t].value);
					}
				}
			}

			inputOp.value = total.toFixed(2);
			// alert (total);
							
		}
				
		
		var num_new_mov = 1;
		
		// Crear nou moviment buit (a la llista temporal de operació)
		function add_mov_new_op(num_mov) {
			var elmTD;
			var elemText;
			var elemInput;
			var elemSelect;
			var elemOption;
			var TR_moviment;
			var TR_ult_mov;

			var elmTBODY = document.getElementById('taula_nova_op');
			
			
			// alert (elemInput.nodeName);
			
			// Habilitar botó Del del primer moviment			
			TR_moviment = elmTBODY.rows[1];
			elmTD = TR_moviment.cells[TR_moviment.cells.length - 2];
			elemInput = elmTD.firstElementChild;
			elemInput.removeAttribute("disabled");
			
			
			// Duplicar la fila a sota
			var TR_moviment_ori = document.getElementById("tr_new_op_mov_" + num_mov);
			TR_moviment = TR_moviment_ori.cloneNode(true);

			num_new_mov++;
			TR_moviment.setAttribute("id", "tr_new_op_mov_" + num_new_mov);
			
			// elemInput = TR_moviment.cells[0].firstElementChild;	// Data
			// elemInput.setAttribute("value", "");
			
			elemInput = TR_moviment.cells[1].firstElementChild;	// Descripció
			elemInput.setAttribute("value", "");

			elemInput = TR_moviment.cells[2].firstElementChild;	// Import
			elemInput.setAttribute("value", "");			

			elemInput = TR_moviment.cells[3].firstElementChild;	// Cta real
			elemInput.value = TR_moviment_ori.cells[3].firstElementChild.value;
			
			elemInput = TR_moviment.cells[4].firstElementChild;	// Cta ctble 1
			elemInput.setAttribute("id", "sel2_new_op_mov_" + num_new_mov);
			elemInput.setAttribute("onchange", "canvi_sel2_new_op(" + num_new_mov + ");");
			elemInput.value = TR_moviment_ori.cells[4].firstElementChild.value;

			elemInput = TR_moviment.cells[5].firstElementChild;	// Cta ctble 2
			elemInput.setAttribute("id", "sel3_new_op_mov_" + num_new_mov);
			elemInput.value = TR_moviment_ori.cells[5].firstElementChild.value;
			 
			
			elemInput = TR_moviment.cells[6].firstElementChild;	// boto Del
			elemInput.setAttribute("onclick", "del_mov_new_op(" + num_new_mov + ");");

			elemInput = TR_moviment.cells[7].firstElementChild; // Boto Add
			elemInput.setAttribute("onclick", "add_mov_new_op(" + num_new_mov + ");");


			TR_ult_mov = document.getElementById('tr_new_op_mov_' + num_mov);
			elmTBODY.insertBefore(TR_moviment, TR_ult_mov.nextSibling);

			canvi_sel2_new_op(num_new_mov);	// Carregar combo comptes comptables 2
		
		}
		

		// Eliminar moviment (a la llista temporal de operació)
		function del_mov_new_op(num_mov) {
		
			var elmTBODY = document.getElementById('taula_nova_op');
			var TR_moviment = document.getElementById('tr_new_op_mov_' + num_mov);
			
			elmTBODY.removeChild(TR_moviment);
			
			// Deshabilitar botó Del del primer moviment			
			if (elmTBODY.rows.length <= 2) {
				TR_moviment = elmTBODY.rows[1];
				elmTD = TR_moviment.cells[TR_moviment.cells.length - 2];
				elemInput = elmTD.firstElementChild;
				elemInput.setAttribute("disabled", "disabled");
			}
		
		}
		

		// Expandir o contraure les moviments de la operació a la llista
		function expand_op(id_op) {
			var Tbody_movs = document.getElementById('movs_op_' + id_op);
			var estil = window.getComputedStyle(Tbody_movs);
			var esveu = estil.getPropertyValue('display');
			
			if (esveu == 'none') {	Tbody_movs.style.display = 'table-row-group';
			} else {				Tbody_movs.style.display = 'none';
			}
			
		}


		function Alta_Operacio() {

			// alert ("Crear nova operacio");
			
			
			
			var xmlhttp;
			if (window.XMLHttpRequest) {	
				xmlhttp = new XMLHttpRequest();						// code for IE7+, Firefox, Chrome, Opera, Safari
	  		} else {
		  		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	// code for IE6, IE5
			}
	
			xmlhttp.onreadystatechange = function() {
	  			if (xmlhttp.readyState == 4) {
					// Tractament de la resposta
					alert (xmlhttp.responseText);
				}
			}

			var parametres = "";
			parametres += "descripcio=" + document.getElementById('descripcio_operacio_new').value;


			var TR_moviment = document.getElementById('tr_new_op_mov_1');
			var elmTD = TR_moviment.cells[0];
			var elemInput = elmTD.firstElementChild;
			var data_mov = elemInput.valueAsDate;
			/*
			alert (("00" + data_mov.getDate()).slice(-2) 
				+ '/' + ("00" + (data_mov.getMonth() + 1)).slice(-2) 
				+ '/' + ("0000" + data_mov.getFullYear()).slice(-4));
			*/

/*	
						<tr id="tr_new_op_mov_1" >
							<td> <input type="date" style="width: 125px; text-align: center;" value="14-02-2012" /> </td>
							<td> <input style="width: 300px; text-align: left;"   value="mov1" /> </td>
							<td> <input style="width:  80px; text-align: right;"  value="-24.98" onkeydown="validar_import();" onkeyup="canvi_import_mov();" name="import_new_op_mov"/> </td>
							<td> <select style="width: 130px; text-align: right; ">
								<?php
									for ($t = 0; $t < count($codi_compte_real); $t++) {
										echo '<option value="' . $t . '">'. $codi_compte_real[$t] . '</option>';
									}
								?>
							</select> </td>
*/













			
			xmlhttp.open("POST", "alta_operacio.php", true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(parametres);
			
			// xmlhttp.open("GET", "alta_operacio.php?descripcio=novaoperacio", true); xmlhttp.send();


			this.doNotSubmit();
			
		}

		
	</script>


	
</head>

<body style="display: flex; flex-flow: row wrap; align-content:flex-start; " >

	<?php
		// echo count ($codi_compte_real)-1;
		// print_r($codi_compte_real); 
		// print_r($codi_compte_comptable);

		// $variable_array = array('primer', 'segon', 'tercer');
		// var_dump($variable_array);
		// print_r($variable_array);
		
		// echo "<br/>";

		
		// print_r($codi_compte_comptable);

	/*
		$variable_array = array(
			"primer",
			"segon",
			array("3-1", "3-2", "3-3", "3-4")
		);
		print_r($variable_array); // Array ( [0] => primer [1] => segon [2] => Array ( [0] => 3-1 [1] => 3-2 [2] => 3-3 [3] => 3-4 ) ) 
		var_dump($variable_array{1});	 // string(5) "segon" 
		var_dump($variable_array[2][3]); // string(3) "3-4" 
		unset($variable_array[2][1]); 
		print_r($variable_array); // Array ( [0] => primer [1] => segon [2] => Array ( [0] => 3-1 [2] => 3-3 [3] => 3-4 ) ) 
*/
	?>
	
	<br/>

	<div id="nova_operacio" style="
		left: 5px; top: 5px;
		width: 95%;
		border: 1px solid #000000;
		background-color: #eeeeee;
		padding: 5px; 
		z-index:60;
		">

			<form name="form_new_op" action="#" >
		
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
							<th style="width: 125px;"> Data </th>
							<th style="width: 300px;"> Descripció </th>
							<th style="width:  80px;"> Import </th>
							<th style="width: 130px;"> Cta. Real </th>					
							<th style="width: 130px;"> Cta. Ctable 1 </th>
							<th style="width: 130px;"> Cta. Ctable 2 </th>
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
							<td> <input style="width: 125px; text-align: center;" disabled="disabled" value="" /> </td>
							<td> <input style="width: 300px; text-align: left;"   value="" id="descripcio_operacio_new" /> </td>
							<td> <input style="width:  80px; text-align: right;"  disabled="disabled" value="" id="import_total_op" /> </td>
							<td style="width: 130px;" > </td>
							<td style="width: 130px;" > </td>
							<td style="width: 130px;" > </td>						
							<td style="width:  30px;" > </td>
						</tr>
	
						<tr id="tr_new_op_mov_1" >
							<td> <input type="date" style="width: 125px; text-align: center;" /> </td>
							<td> <input style="width: 300px; text-align: left;"   value="mov1" /> </td>
							<td> <input style="width:  80px; text-align: right;"  value="-24.98" onkeydown="validar_import();" onkeyup="canvi_import_mov();" name="import_new_op_mov"/> </td>
							<td> <select style="width: 130px; text-align: right; ">
								<?php
									for ($t = 0; $t < count($codi_compte_real); $t++) {
										echo '<option value="' . $t . '">'. $codi_compte_real[$t] . '</option>';
									}
								?>
							</select> </td>
	
							<td> <select style="width: 130px; text-align: right;" id="sel2_new_op_mov_1" onchange="canvi_sel2_new_op(1);">
								<?php 
									for ($t = 0; $t < count($codi_compte_comptable); $t++) {
										echo '<option value="' . $t . '">'. $codi_compte_comptable[$t]["cc1"] . '</option>';
									}
								?>
							</select> </td>
	
							<td> <select style="width: 130px; text-align: right;" id="sel3_new_op_mov_1" >
								<?php 
									for ($t = 0; $t < count($codi_compte_comptable[4]["cc2"]); $t++) {
										echo '<option value="' . $t . '">'. $codi_compte_comptable[4]["cc2"][$t] . '</option>';
									}
								?>
							</select> </td>
							
							<td style="width:  30px; text-align: right; "> <input type="button" value="Del" onclick="del_mov_new_op(1);" disabled="disabled" />  </td>
							<td style="width:  30px; text-align: right; "> <input type="button" value="Add" onclick="add_mov_new_op(1);" />  </td>
						</tr>
	
	
	
					</tbody>
				</table> 
			
				<button style="margin-top: 5px;" onclick="Alta_Operacio();" > OK : Insertar operació i moviments </button>
			
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
					<th class="capsalera_llista" style="width: <?php echo $col_w1; ?>px; border-left-style: none;"> </th>
					<th class="capsalera_llista" style="width: <?php echo $col_w2; ?>px;"> Data </th>
					<th class="capsalera_llista" style="width: <?php echo $col_w3; ?>px;"> Descripció </th>
					<th class="capsalera_llista" style="width: <?php echo $col_w4; ?>px;"> Import </th>
					<th class="capsalera_llista" style="width: <?php echo $col_w5; ?>px;"> Cta. Real </th>					
					<th class="capsalera_llista" style="width: <?php echo $col_w6; ?>px;"> Cta. Ctable 1 </th>
					<th class="capsalera_llista" style="width: <?php echo $col_w7; ?>px;"> Cta. Ctable 2 </th>
					<th class="capsalera_llista" style="width: 06px;"> </th>
				</tr>
			</thead>

			<tbody id="taula_operacions" style="
				border-color: #000000;				
				border-style: none solid solid none;
				border-width: 1px;
				background-color: #ffffff;
				display:block;
				height:550px;
				overflow:scroll;
				">

				<?php // Generar totes les operacions
					
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
					while ($x < 30) {

						echo '
						<tr style="padding:0px; border-spacing: 0px; border: 1px solid #000000; border-left-style: none;">
							<td colspan="6" style="padding:0px; border-spacing: 0px; border-left-style: none;">
								<table name="taula_op" style="border-spacing: 0px; border-style: none;">

									<thead>
										<tr>
											<th class="fila_operacio" style="width: '.$col_w1.'px; border-left-style: none;" 
												onclick="expand_op('.pg_fetch_result($result, $x, "id_operacio").');"> '.
												pg_fetch_result($result, $x, "id_operacio").'
											</th>
											<th class="fila_operacio" style="width: '.$col_w2.'px; text-align: center;">
												'. pg_fetch_result($result, $x, "data_op") .'
											</th>
											<th class="fila_operacio" style="width: '.$col_w3.'px; text-align: left;  "> 
												'. pg_fetch_result($result, $x, "descripcio") .'
											</th>
											<th class="fila_operacio" style="width: '.$col_w4.'px; text-align: right; "> 
												'. pg_fetch_result($result, $x, "import") .'
											</th>
											<th class="fila_operacio" style="width: '.$col_w5.'px; text-align: left; "> </th>
											<th class="fila_operacio" style="width: '.$col_w6.'px; text-align: left; "> </th>
											<th class="fila_operacio" style="width: '.$col_w7.'px; text-align: left; "> </th>
										</tr>
									</thead>
									
									<tbody style="display: none;" id="movs_op_'.pg_fetch_result($result, $x, "id_operacio").'" >';
						// Aqui van tots els moviments

							$consulta2 = "
							select t1.data_moviment, 
							       t1.descripcio, 
								   t1.import, 
								   t1.codi_compte_real, 
								   t1.codi_compte_comptable, 
								   t2.codi_compte_comptable_pare
							  from moviments t1,
								   comptes_comptables t2
							 where t1.codi_compte_comptable = t2.codi_compte_comptable
							   and t1.id_operacio = ". pg_fetch_result($result, $x, "id_operacio") ."
							 order by t1.data_moviment, t1.id_moviment
							";
							$result2 = pg_query($dbconn, $consulta2);
							$y = 0;
							while ($y < pg_numrows($result2)) {
								echo '
								<tr>
									<td class="fila_moviment" style="width: '.$col_w1.'px; border-style: none;" 
										onclick="expand_op('.pg_fetch_result($result, $x, "id_operacio").');"> 
									</td>
									<td class="fila_moviment" style="width: '.$col_w2.'px; text-align: center; border-left-color: #000000;" >'.
										pg_fetch_result($result2, $y, "data_moviment")
									.'</td>
									<td class="fila_moviment" style="width: '.$col_w3.'px; text-align: left;  "> '.
										pg_fetch_result($result2, $y, "descripcio")
									.'</td>
									<td class="fila_moviment" style="width: '.$col_w4.'px; text-align: right; "> '.
										pg_fetch_result($result2, $y, "import")
									.'</td>
									<td class="fila_moviment" style="width: '.$col_w5.'px; text-align: left; "> '.
										pg_fetch_result($result2, $y, "codi_compte_real")
									.'</td>
									<td class="fila_moviment" style="width: '.$col_w6.'px; text-align: left; "> '.
										pg_fetch_result($result2, $y, "codi_compte_comptable")
									.'</td>
									<td class="fila_moviment" style="width: '.$col_w7.'px; text-align: left; "> '.
										pg_fetch_result($result2, $y, "codi_compte_comptable_pare")
									.'</td>
								</tr>';
								$y++;
							}

						
						// Tancar tr operacio
						echo '
									</tbody>

								</table>
							</td>
						</tr>';
						$x++;
					}				
					
					pg_close($dbconn);
				
				?>

			</tbody>
		</table> 

	</div>


</body>
</html>
