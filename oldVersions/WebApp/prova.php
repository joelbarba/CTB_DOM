<html>
<head>
	<meta charset="UTF-8">
	<title> Història </title>
	<style>
	
		html,body {
			font:normal normal normal 10px/10px Verdana;
		}

		
		
	</style>

	<script language="Javascript">
	

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
function add_input() {
  // sets a new input element input, having the attributes: type=text si name=nume[]
  var new_input = document.createElement("input");
  new_input.setAttribute("type", "text");
  new_input.setAttribute("name", "nume[]");
  new_input.style.display = 'block';             // sets cssstyle display:block;

  // sets the objects for reference and parent
  var reper = document.getElementById('submit');
  var parinte = reper.parentNode;

  // Adds new_input
  parinte.insertBefore(new_input, reper);
}
        
	</script>


	
</head>

<body style="overflow: hidden; display: flex; flex-flow: row wrap; align-content:flex-start; ">

<form action="">
  <input type="text" name="nume[]" />
  <input type="submit" value="Submit" id="submit" /><br /><br />
  <input type="button" value="Add box" onclick="add_input()" />
</form>


		<form>
	
			<table>


				<tbody id="taula_nova_op">


					<tr >
						<td> <input style="width:  90px; text-align: center;" disabled="disabled" value="" /> </td>
						<td> <input style="width: 300px; text-align: left;"   value="" /> </td>
						<td> <input style="width:  80px; text-align: right;"  disabled="disabled" value="" /> </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>						
						<td style="width:  30px;" > </td>
					</tr>

					<tr id="tr_new_op_mov_1" >
						<td> <input style="width:  90px; text-align: center;" disabled="disabled" value="" /> </td>
						<td> <input style="width: 300px; text-align: left;"   value="" /> </td>
						<td> <input style="width:  80px; text-align: right;"  disabled="disabled" value="" /> </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>
						<td style="width: 100px;" > </td>
						<td> <input type="button" value="Add" onclick="add_mov_new_op();" />  </td>
					</tr>

				</tbody>
			</table> 

		</form>
		
</body>
</html>
