<?php

	include ("funcions.inc");

	$link_db = connectar_db();

        $filtre_operacio="";
        if ($_REQUEST[ordre] == 'D') { 
            if ($_REQUEST[num_op] > 0) { $filtre_operacio = " AND T1.NUM_OPERACIO < ".$_REQUEST[num_op]; }
            $ordre='DESC';
        } else  {
            $filtre_operacio = " AND T1.NUM_OPERACIO > ".$_REQUEST[num_op];
            $ordre='';
        }

        $sentencia = "
SELECT COUNT(*) NM
  FROM OPERACIONS T1
 WHERE (DATE_FORMAT(T1.DATA, '%Y%m') = '".$_REQUEST[anymes]."'
    OR '999999' = '".$_REQUEST[anymes]."')
     ".$filtre_operacio;

	$result = mysql_query($sentencia, $link_db);
        $num_ops = mysql_result($result, 0, "NM");
        mysql_free_result($result);


        if ($num_ops > $_REQUEST[max_ops]) {
            $num_ops = $_REQUEST[max_ops];
            $resultat=$num_ops.",0,";
        } else {
            $resultat=$num_ops.",-1,";
        }

       $sentencia = "
SELECT T1.NUM_OPERACIO NUM_OPERACIO,
       DATE_FORMAT(T1.DATA, '%d-%m-%Y') DATA,
       T1.DESCRIPCIO DESCRIPCIO,
       T1.REFERENCIA REFERENCIA,
       IFNULL(T2.TOTAL, 0) IMPORT
  FROM OPERACIONS T1
       LEFT OUTER JOIN
       (SELECT NUM_OPERACIO,
               SUM(IMPORT) TOTAL
          FROM MOVIMENTS
         GROUP BY NUM_OPERACIO) T2
    ON T2.NUM_OPERACIO = T1.NUM_OPERACIO
 WHERE (DATE_FORMAT(T1.DATA, '%Y%m') = '".$_REQUEST[anymes]."' OR '999999' = '".$_REQUEST[anymes]."')
     ".$filtre_operacio."
ORDER BY T1.NUM_OPERACIO ".$ordre."
 LIMIT 0, ".$num_ops;






        $result = mysql_db_query("comptabilitat", $sentencia);
        while($row = mysql_fetch_array($result)) {

            $resultat .= $row["NUM_OPERACIO"].",";
            $resultat .= $row["DATA"].",";
            $resultat .= $row["DESCRIPCIO"].",";
            //$resultat .= $row["REFERENCIA"].",";
            $resultat .= $row["IMPORT"].",";


            // Variacions i acumulats
            $sentencia_2 = "
SELECT 1 P, T2.NUM, SUM(IF(T1.POT_COMPTABLE = T2.NUM, T1.IMPORT, 0)) IMPORT
  FROM MOVIMENTS T1,
       POTS_COMPTABLES T2
 WHERE T1.NUM_OPERACIO = ".$row["NUM_OPERACIO"]."
 GROUP BY T2.NUM
 UNION ALL
SELECT 2 P, T2.NUM, SUM(IF(T1.POT_REAL = T2.NUM, T1.IMPORT, 0)) IMPORT
  FROM MOVIMENTS T1,
       POTS_REALS T2
 WHERE T1.NUM_OPERACIO = ".$row["NUM_OPERACIO"]."
 GROUP BY T2.NUM
 UNION ALL
SELECT 3 P, T2.NUM, SUM(IF(T1.POT_COMPTABLE = T2.NUM, T1.IMPORT, 0)) IMPORT
  FROM MOVIMENTS T1,
       POTS_COMPTABLES T2
 WHERE T1.NUM_OPERACIO <= ".$row["NUM_OPERACIO"]."
 GROUP BY T2.NUM
 UNION ALL
SELECT 4 P, T2.NUM, SUM(IF(T1.POT_REAL = T2.NUM, T1.IMPORT, 0)) IMPORT
  FROM MOVIMENTS T1,
       POTS_REALS T2
 WHERE T1.NUM_OPERACIO <= ".$row["NUM_OPERACIO"]."
 GROUP BY T2.NUM
 ORDER BY P, NUM";

            $result_2 = mysql_db_query("comptabilitat", $sentencia_2);
            while($row_2 = mysql_fetch_array($result_2)) {
                $resultat .= $row_2["IMPORT"].",";
            }
            mysql_free_result($result_2);
            $resultat .= "@,";

        }

        mysql_free_result($result);
        echo $resultat;
	mysql_close($link_db);


/*
        $sentencia = "
SELECT T3.NUM NUM_OP,
       DATE_FORMAT(T3.DATA, '%d-%m-%Y') DATA,
       T3.DESCRIPCIO DES,
       T3.REFERENCIA REF,
       T1.IMPORT IMPORT,
       T1.PCV1, T1.PCV2, T1.PCV3, T1.PCV4, T1.PCV5, T1.PCV6, T1.PCV7, T1.PCV8, T1.PCV9,
       T1.PRV1, T1.PRV2, T1.PRV3, T1.PRV4, T1.PRV5,
       SUM(T2.PCV1) PCA1, SUM(T2.PCV2) PCA2, SUM(T2.PCV3) PCA3, SUM(T2.PCV4) PCA4, SUM(T2.PCV5) PCA5,
       SUM(T2.PCV6) PCA6, SUM(T2.PCV7) PCA7, SUM(T2.PCV8) PCA8, SUM(T2.PCV9) PCA9,
       SUM(T2.PRV1) PRA1, SUM(T2.PRV2) PRA2, SUM(T2.PRV3) PRA3, SUM(T2.PRV4) PRA4, SUM(T2.PRV5) PRA5
  FROM (SELECT NUM_OPERACIO,
               SUM(IMPORT) IMPORT,
               SUM(IF(POT_COMPTABLE = 1, IMPORT, 0)) PCV1,
               SUM(IF(POT_COMPTABLE = 2, IMPORT, 0)) PCV2,
               SUM(IF(POT_COMPTABLE = 3, IMPORT, 0)) PCV3,
               SUM(IF(POT_COMPTABLE = 4, IMPORT, 0)) PCV4,
               SUM(IF(POT_COMPTABLE = 5, IMPORT, 0)) PCV5,
               SUM(IF(POT_COMPTABLE = 6, IMPORT, 0)) PCV6,
               SUM(IF(POT_COMPTABLE = 7, IMPORT, 0)) PCV7,
               SUM(IF(POT_COMPTABLE = 8, IMPORT, 0)) PCV8,
               SUM(IF(POT_COMPTABLE = 9, IMPORT, 0)) PCV9,
               SUM(IF(POT_REAL = 1, IMPORT, 0)) PRV1,
               SUM(IF(POT_REAL = 2, IMPORT, 0)) PRV2,
               SUM(IF(POT_REAL = 3, IMPORT, 0)) PRV3,
               SUM(IF(POT_REAL = 4, IMPORT, 0)) PRV4,
               SUM(IF(POT_REAL = 5, IMPORT, 0)) PRV5
          FROM MOVIMENTS
         GROUP BY NUM_OPERACIO) T1,
       (SELECT NUM_OPERACIO,
               SUM(IMPORT) IMPORT,
               SUM(IF(POT_COMPTABLE = 1, IMPORT, 0)) PCV1,
               SUM(IF(POT_COMPTABLE = 2, IMPORT, 0)) PCV2,
               SUM(IF(POT_COMPTABLE = 3, IMPORT, 0)) PCV3,
               SUM(IF(POT_COMPTABLE = 4, IMPORT, 0)) PCV4,
               SUM(IF(POT_COMPTABLE = 5, IMPORT, 0)) PCV5,
               SUM(IF(POT_COMPTABLE = 6, IMPORT, 0)) PCV6,
               SUM(IF(POT_COMPTABLE = 7, IMPORT, 0)) PCV7,
               SUM(IF(POT_COMPTABLE = 8, IMPORT, 0)) PCV8,
               SUM(IF(POT_COMPTABLE = 9, IMPORT, 0)) PCV9,
               SUM(IF(POT_REAL = 1, IMPORT, 0)) PRV1,
               SUM(IF(POT_REAL = 2, IMPORT, 0)) PRV2,
               SUM(IF(POT_REAL = 3, IMPORT, 0)) PRV3,
               SUM(IF(POT_REAL = 4, IMPORT, 0)) PRV4,
               SUM(IF(POT_REAL = 5, IMPORT, 0)) PRV5
          FROM MOVIMENTS
         GROUP BY NUM_OPERACIO) T2,
       OPERACIONS T3
 WHERE T1.NUM_OPERACIO >= T2.NUM_OPERACIO
   AND T1.NUM_OPERACIO
   AND T1.NUM_OPERACIO = T3.NUM
   AND (DATE_FORMAT(T3.DATA, '%Y%m') = '".$_REQUEST[anymes]."' OR '999999' = '".$_REQUEST[anymes]."')
   ".$filtre_operacio."
 GROUP BY T1.NUM_OPERACIO
 ORDER BY T1.NUM_OPERACIO
 LIMIT 0, ".$num_ops;

        $result = mysql_db_query("comptabilitat", $sentencia);
        while($row = mysql_fetch_array($result)) {

            $resultat .= $row["NUM_OP"].",";
            $resultat .= $row["DATA"].",";
            $resultat .= $row["DES"].",";
            //$resultat .= $row["REF"].",";
            $resultat .= $row["IMPORT"].",";

            for ($i=1; $i<=9; $i++) { $resultat .= $row["PCV".$i].","; }
            for ($i=1; $i<=5; $i++) { $resultat .= $row["PRV".$i].","; }
            for ($i=1; $i<=9; $i++) { $resultat .= $row["PCA".$i].","; }
            for ($i=1; $i<=5; $i++) { $resultat .= $row["PRA".$i].","; }

        }

        mysql_free_result($result);
        echo $resultat;
	mysql_close($link_db);

*/



?>
