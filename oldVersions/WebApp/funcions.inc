
<?php

	
	function array_comptes(&$codi_compte_real, &$codi_compte_comptable) {

		$dbconn = pg_connect("host=localhost dbname=db_comptabilitat user=barba password=barba0001")
		or die('No s\'ha pogut connectar : ' . pg_last_error());	

		$result = pg_query($dbconn, 'select * from comptes_reals order by codi_compte_real');
		for ($t = 0; $t < pg_numrows($result); $t++) {
			$codi_compte_real[$t] = pg_fetch_result($result, $t, "codi_compte_real");
		}

		$codi_compte_comptable = array();

		$result = pg_query($dbconn, 'select * from comptes_comptables where codi_compte_comptable_pare is null order by codi_compte_comptable');
		for ($t = 0; $t < pg_numrows($result); $t++) {
			$codi_compte_comptable1[$t] = pg_fetch_result($result, $t, "codi_compte_comptable");

			$codi = pg_fetch_result($result, $t, "codi_compte_comptable");
			
			$codi_compte_comptable[] = array("cc1" => $codi, "cc2" => array() );
			
			$result2 = pg_query($dbconn, 'select * from comptes_comptables where codi_compte_comptable_pare = \'' . $codi . '\' order by codi_compte_comptable');
			for ($q = 0; $q < pg_numrows($result2); $q++) {
				$codi_compte_comptable[count($codi_compte_comptable) - 1]["cc2"][] = pg_fetch_result($result2, $q, "codi_compte_comptable");
			}
		}
		
		
		pg_close($dbconn);
	}
/*
		codi_compte_comptable = 
		Array ( 
			[0] => Array ( 
				[cc1] => 1. General 
				[cc2] => Array ( 
					[0] => 1.1. Estalvi 
					[1] => 1.2. RBE 
					[2] => 1.3. Traspas R 
					) 
				) 
			[1] => Array ( 
				[cc1] => 2. Ingressos 
				[cc2] => Array ( 
					[0] => 2.1. Atmira Nomina 
					[1] => 2.2. Atmira SNC 
					[2] => 2.3. Atmira Hores extres 
					[3] => 2.4. Atmira Dietes 
					[4] => 2.5. Interessos bancaris 
					[5] => 2.6. Regals 
					[6] => 2.7. Altres 
					) 
				) 
			[2] => Array ( 
				[cc1] => 3. Prestecs 
				[cc2] => Array ( 
					[0] => 3.1. Ivan-1 
					[1] => 3.2. Marc-1 
					) 
				) 
			[3] => Array ( 
				[cc1] => 4. Vivenda 
				[cc2] => Array ( 
					[0] => 4.1. Lloguer st. Gaieta 
					[1] => 4.2. Seguro pis 
					[2] => 4.3. Fiança 
					[3] => 4.4. Obres 
					) 
				) 
			[4] => Array ( 
				[cc1] => 5. Serveis 
				[cc2] => Array ( 
					[0] => 5.10. Tintoreria 
					[1] => 5.11. Tren 
					[2] => 5.12. Esmorzar 
					[3] => 5.1. Electricitat 
					[4] => 5.2. Gas 
					[5] => 5.3. Aigua 
					[6] => 5.4. Internet 
					[7] => 5.5. Mòbil 
					[8] => 5.6. CCOO 
					[9] => 5.7. Centre Excurcionista 
					[10] => 5.8. Seguro FEEC 
					[11] => 5.9. Creu roja 
					) 
				) 
			[5] => Array ( 
				[cc1] => 6. Cotxe 
				[cc2] => Array ( 
					[0] => 6.1. Gasolina 
					[1] => 6.2. Seguro cotxe 
					[2] => 6.3. Impost circulació 
					[3] => 6.4. Quota Teletac 
					[4] => 6.5. Peatges 
					[5] => 6.6. Reparacions 
					) 
				) 
			[6] => Array ( 
				[cc1] => 7. Material 
				[cc2] => Array ( 
					[0] => 7.1. Roba 
					[1] => 7.2. Mobiliari Pis 
					[2] => 7.3. Tecnologia 
					[3] => 7.4. Btt & Run 
					[4] => 7.5. Cultura 
					[5] => 7.6. Crema grans 
					[6] => 7.7. Finesterida 
					[7] => 7.8. Altres 
					) 
				) 
			[7] => Array ( 
				[cc1] => 8. Menjar 
				[cc2] => Array ( 
					[0] => 8.1. Compra menor 
					[1] => 8.2. Compra major 
					[2] => 8.3. Fruiteria 
					[3] => 8.4. Pa entrepans 
					) 
				) 
			[8] => Array ( 
				[cc1] => 9. Gastos varis 
				[cc2] => Array ( 
					[0] => 9.1. Altres 
					[1] => 9.2. Restaurants 
					[2] => 9.3. Vacances 
					[3] => 9.4. Multes 
					[4] => 9.5. Despedida Eriko 
					[5] => 9.6. Despedida Sanlle
					) 
				)
			) 

*/


	
	
?>
