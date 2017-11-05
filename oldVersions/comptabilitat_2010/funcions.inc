
<?php
	function connectar_db() {

		$link_db = mysql_connect("localhost:3306", "usuari1", "prvcompta04");
		mysql_select_db("comptabilitat", $link_db);
		return $link_db;

	}

        function array_pots(&$num_pcs, &$num_pc, &$desc_pc, &$num_prs, &$num_pr, &$desc_pr) {
            // Recuperar tots els numeros de pots comptables
            $sent_pc = "SELECT NUM, DESCRIPCIO FROM POTS_COMPTABLES ORDER BY NUM";
            $res_pc = mysql_db_query("comptabilitat", $sent_pc);
            $num_pcs = 0;
            while($row = mysql_fetch_array($res_pc)) { 
                $num_pcs++;
                $num_pc[$num_pcs] = $row["NUM"];
                $desc_pc[$num_pcs] = $row["DESCRIPCIO"]; 
            }
            mysql_free_result($res_pc);

            // Recuperar tots els numeros de pots reals
            $sent_pr = "SELECT NUM, DESCRIPCIO FROM POTS_REALS ORDER BY NUM";
            $res_pr = mysql_db_query("comptabilitat", $sent_pr);
            $num_prs = 0;
            while($row = mysql_fetch_array($res_pr)) { 
                $num_prs++;
                $num_pr[$num_prs] = $row["NUM"];
                $desc_pr[$num_prs] = $row["DESCRIPCIO"];
            }
            mysql_free_result($res_pr);
        }
?>
