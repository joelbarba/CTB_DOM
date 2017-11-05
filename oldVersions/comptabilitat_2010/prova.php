

<html>

<head>
    <link href="estils.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<?
    $arxiu_o = "/etc/bind/named.conf";
    $arxiu_d = "/home/barba/fitxer2.txt";

    $includers[] = $arxiu_o;

    $res = llegir_arxiu($arxiu_o);
    $llista_inc = buscar_zones($res);

    echo "<p>S'han examinat els fitxers :</p>";
    echo "<ul>";
    foreach ($includers as $elem) { echo "<li>".$elem."</li>"; }
    echo "</ul>";
    echo "<br>";
    echo "<br>";

    echo "<p>S'han detectat els seguents dominis :</p>";
    echo "<ul>";
    foreach ($llista_inc as $elem) { echo "<li>".$elem."</li>"; }
    echo "</ul>";
    echo "<br>";
    echo "<br>";


    // Extreure arxiu de configuració
    foreach ($llista_inc as $elem) {

        //preg_match_all("/include *\".*\" *;/U", $elem, $llista_zone_files);
        preg_match_all("/file *\".*\" *;/U", $elem, $llista_zone_files);

        $arx=str_replace("file", "", $llista_zone_files[0][0]);
        $arx=str_replace('"', "", $arx);
        $arx=str_replace(';', "", $arx);
        $arx=trim($arx);

        //analitzar_zona($arx);

        echo "<li>".$arx."</li>";

    }

    echo "<br>";
    echo "<br>";

    analitzar_zona("/var/lib/bind/domini-prova.hosts");


    //print "<pre>"; print_r(buscar_zones($res)); print "</pre>\n";

    echo "Arxiu configuracio : <br><br>";
    //echo nl2br($res);

/*    $fp = fopen($arxiu_d, "w+");
    fwrite($fp, $texto);
    fclose($fp);*/




    // ---------------------------------------------------------------------- //


    // Analitza el fitxer de configuració d'una zona
    function analitzar_zona($fitxer_configuracio) {

        $text="";

        if (file_exists($fitxer_configuracio)) {

            $fp = fopen($fitxer_configuracio, "r");
            while (!feof($fp)) { $text .= fgets($fp, 1024); }
            fclose($fp);

            print nl2br($text);

            // Buscar ips
            preg_match_all("/\w*[ \t]*IN[ \t]*A[ \t]*[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}/m", $text, $llista_ips);
            //preg_match_all("/.*IN *A.*/m", $text, $llista_ips);
            print "<pre>"; print_r($llista_ips); print "</pre>\n";


        }


    }


    // Llegeix l'arxiu, busca tots els includes de les linies no comentades,
    // i explora'ls recursivament (profunditat màxima 10) reemplaçant la
    // clausula include pel contingut del fitxer
    // Evitar recursivitat ciclica (si un ja la processat el desestima)
    function llegir_arxiu($arxiu, $prof) {

        global $includers;
        $text="";

        if (file_exists($arxiu) && $prof < 10) {

            $fp = fopen($arxiu, "r");
            while (!feof($fp)) { $text .= fgets($fp, 1024); }
            fclose($fp);

            $llista_inc = buscar_includes($text);

            foreach ($llista_inc as $elem) {

                $arx2=str_replace("include", "", $elem);
                $arx2=str_replace('"', "", $arx2);
                $arx2=str_replace(';', "", $arx2);
                $arx2=trim($arx2);

                // Explora el include només si no esta a la llista dels ja explorats
                if (!in_array($arx2, $includers)) {

                    $includers[] = $arx2;
                    $text2 = llegir_arxiu($arx2, $prof + 1);
                    if ($text2 == "") { $includers = array_pop($includers); }   // Si no existeix, treu-lo de la llista
                    $text2=$elem."<br>------------- AFAGINT INCLUDE ----------<br>".$text2."<br>";
                    $text=str_replace($elem, $text2, $text);
                }
            }

        }

        return $text;

    }


    // Retorna un array amb totes les clausules include (efectives) trobades a $text
    function buscar_includes($text) {

        $text = codi_valid($text);

        // Buscar tots els includes
        preg_match_all("/include *\".*\" *;/U", $text, $llista_inc);

        /*
        if (preg_match_all("/include *\".*\" *;/U", $text, $coincidencias, PREG_OFFSET_CAPTURE)) {
            foreach ($coincidencias[0] as $coincide) {
                $llista_inc[] = $coincide[0];
            }
        }*/

        return $llista_inc[0];
    }


    // Retorna un array amb totes les clausules zone (efectives) trobades a $text
    function buscar_zones($text) {

        $text = codi_valid($text);

        // Buscar tots els includes
        preg_match_all("/zone *\".*\" *\{.*\}/Us", $text, $llista_inc);

        return $llista_inc[0];
    }



    // Retorna un text amb totes les linies valides (que no son espais o comencen per // o #)
    function codi_valid($text) {

        // Eliminar linies en blanc
        $text2="";
        $encontrado = preg_match_all("/.+/m", $text, $coincidencias);
        foreach ($coincidencias[0] as $linia) {
            $text2.=$linia.chr(10);
        }
        $text=$text2;

        // Eliminar linies comentades (amb //)
        $text2="";
        $encontrado = preg_match_all("/^( *)[^(( *)(\/\/))].*/m", $text, $coincidencias);
        foreach ($coincidencias[0] as $linia) {
            $text2.=$linia.chr(10);
        }
        $text=$text2;

        // Eliminar linies comentades (amb #)
        $text2="";
        $encontrado = preg_match_all("/^( *)[^(( *)(#))].*/m", $text, $coincidencias);
        foreach ($coincidencias[0] as $linia) {
            $text2.=$linia.chr(10);
        }
        $text=$text2;

        return $text;

    }



?>


</body>
</html>
