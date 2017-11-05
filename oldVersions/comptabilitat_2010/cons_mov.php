<?php

	include ("funcions.inc");

	$link_db = connectar_db();

	$result = mysql_query("SELECT COUNT(*) NM FROM MOVIMENTS WHERE NUM_OPERACIO = ".$_REQUEST[num_op], $link_db);
        $num_movs = mysql_result($result, 0, "NM");
        mysql_free_result($result);

        $resultat=$num_movs.",";

        $sentencia = "
SELECT T1.NUM_MOVIMENT NUM_MOVIMENT,
       T1.IMPORT IMPORT,
       IF(T1.POT_COMPTABLE = 1, T1.IMPORT, 0) PCV1,
       IF(T1.POT_COMPTABLE = 2, T1.IMPORT, 0) PCV2,
       IF(T1.POT_COMPTABLE = 3, T1.IMPORT, 0) PCV3,
       IF(T1.POT_COMPTABLE = 4, T1.IMPORT, 0) PCV4,
       IF(T1.POT_COMPTABLE = 5, T1.IMPORT, 0) PCV5,
       IF(T1.POT_COMPTABLE = 6, T1.IMPORT, 0) PCV6,
       IF(T1.POT_COMPTABLE = 7, T1.IMPORT, 0) PCV7,
       IF(T1.POT_COMPTABLE = 8, T1.IMPORT, 0) PCV8,
       IF(T1.POT_COMPTABLE = 9, T1.IMPORT, 0) PCV9,
       IF(T1.POT_REAL = 1, T1.IMPORT, 0) PRV1,
       IF(T1.POT_REAL = 2, T1.IMPORT, 0) PRV2,
       IF(T1.POT_REAL = 3, T1.IMPORT, 0) PRV3,
       IF(T1.POT_REAL = 4, T1.IMPORT, 0) PRV4,
       IF(T1.POT_REAL = 5, T1.IMPORT, 0) PRV5,
       SUM(IF(T2.POT_COMPTABLE = 1, T2.IMPORT, 0)) PCA1,
       SUM(IF(T2.POT_COMPTABLE = 2, T2.IMPORT, 0)) PCA2,
       SUM(IF(T2.POT_COMPTABLE = 3, T2.IMPORT, 0)) PCA3,
       SUM(IF(T2.POT_COMPTABLE = 4, T2.IMPORT, 0)) PCA4,
       SUM(IF(T2.POT_COMPTABLE = 5, T2.IMPORT, 0)) PCA5,
       SUM(IF(T2.POT_COMPTABLE = 6, T2.IMPORT, 0)) PCA6,
       SUM(IF(T2.POT_COMPTABLE = 7, T2.IMPORT, 0)) PCA7,
       SUM(IF(T2.POT_COMPTABLE = 8, T2.IMPORT, 0)) PCA8,
       SUM(IF(T2.POT_COMPTABLE = 9, T2.IMPORT, 0)) PCA9,
       SUM(IF(T2.POT_REAL = 1, T2.IMPORT, 0)) PRA1,
       SUM(IF(T2.POT_REAL = 2, T2.IMPORT, 0)) PRA2,
       SUM(IF(T2.POT_REAL = 3, T2.IMPORT, 0)) PRA3,
       SUM(IF(T2.POT_REAL = 4, T2.IMPORT, 0)) PRA4,
       SUM(IF(T2.POT_REAL = 5, T2.IMPORT, 0)) PRA5
  FROM MOVIMENTS T1,
       MOVIMENTS T2
 WHERE T1.NUM_MOVIMENT >= T2.NUM_MOVIMENT
   AND T1.NUM_OPERACIO = ".$_REQUEST[num_op]."
 GROUP BY T1.NUM_MOVIMENT, T1.IMPORT
 ORDER BY T1.NUM_MOVIMENT
";
        $result = mysql_db_query("comptabilitat", $sentencia);
        while($row = mysql_fetch_array($result)) {

            $resultat .= $row["NUM_MOVIMENT"].",";
            $resultat .= $row["IMPORT"].",";

            for ($i=1; $i<=9; $i++) { $resultat .= $row["PCV".$i].","; }
            for ($i=1; $i<=5; $i++) { $resultat .= $row["PRV".$i].","; }
            for ($i=1; $i<=9; $i++) { $resultat .= $row["PCA".$i].","; }
            for ($i=1; $i<=5; $i++) { $resultat .= $row["PRA".$i].","; }

        }
        $resultat .= $_REQUEST[num_op];
        mysql_free_result($result);

        echo $resultat;

	mysql_close($link_db);

?>
