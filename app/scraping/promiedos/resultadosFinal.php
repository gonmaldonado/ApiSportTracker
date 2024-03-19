<?php

include('../simple_html_dom.php');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://www.promiedos.com.ar/ayer');
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);


$domResult = new simple_html_dom();
$domResult->load($result);


foreach($domResult->find('div#fixturein') as $torneos) 
{
    $competencia;
    $data ;
    foreach($torneos->find("//tr[@class='tituloin']") as $torneo) 
    {
        $competencia = $torneo->plaintext;
        echo '<h1>' . $competencia . ' </h1><br>';
    }

    foreach($torneos->find('tr[name=nvp]') as $partido) {
        if (strpos($partido->plaintext, 'Final') !== false ||strpos($partido->plaintext, 'Susp.') !== false ) {
        $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box','Susp.', 'Final','Ida:','-'), '', $partido->plaintext));
        echo '<h3>' . $dato_partidos . ' </h3><br>';
        $data = extraerDatosPartido($dato_partidos,$competencia);
        print_r($data);
        }else{
            $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box', 'Final','Ida:','-'), '', $partido->plaintext));
            if (strpos($dato_partidos, "'") !== false && strpos($dato_partidos, ";") !== false && preg_match("/[0-9]/", $dato_partidos)){
                echo '<h3>' . $dato_partidos . ' </h3><br>';
                print_r(goles($data['goles_local'],$data['goles_visitante'], $partido->plaintext)) ;
            
            }
        }
    }       
    
}

function extraerDatosPartido($texto,$competencia) {
    $patron1 = "/^([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//sin penales
    $patron2 = "/^([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//con penales

    // Realizar la coincidencia utilizando expresiones regulares
    if (preg_match($patron2, $texto, $coincidencias)) {
        // Extraer los datos del partido
        $local = $coincidencias[1];
        $goles_local = $coincidencias[2];
        $penales_local = $coincidencias[3];
        $goles_visitante = $coincidencias[4];
        $penales_visitante = $coincidencias[5];
        $visitante = $coincidencias[6];
        // Construir y devolver los datos del partido
        return array(
            "fecha"=> date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d")))),
            "competencia"=>$competencia,
            "local" => $local,
            "goles_local" => $goles_local,
            "goles_visitante" => $goles_visitante,
            "visitante" => $visitante,
            "resutado" => $goles_local . " - " . $goles_visitante,
            "penales" => $penales_local . " - " . $penales_visitante,
        );
    }
    else{
        if (preg_match($patron1, $texto, $coincidencias)) {
            // Extraer los datos del partido
            $local = $coincidencias[1];
            $goles_local = $coincidencias[2];
            $goles_visitante = $coincidencias[3];
            $visitante = $coincidencias[4];
            // Construir y devolver los datos del partido
            return array(
                "fecha"=> date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d")))),
                "competencia"=>$competencia,
                "local" => $local,
                "goles_local" => $goles_local,
                "goles_visitante" => $goles_visitante,
                "visitante" => $visitante,
                "resutado" => $goles_local . " - " . $goles_visitante,
                "penales" => false,
            );
        }else {
            // No se encontraron suficientes partes para representar un partido válido
            return false;}
    }
        
}

function goles ($goles_local,$goles_visitante,$texto)
{ 
    $golesLocal="";
    $golesVisitante="";
    $golesTotales = $goles_local +$goles_visitante;
    if($golesTotales > 0)
    {
    $goles = explode(";", $texto);

        if($goles_local > 0){
            for ($i = 0; $i <= $goles_local-1 ; $i++) {
                $golesLocal = $golesLocal . $goles[$i].";";
            }
        } 

        if($goles_visitante > 0){
            for ($i = $goles_local; $i <= $golesTotales-1 ; $i++) {
                $golesVisitante = $golesVisitante . $goles[$i].";";
            }
        } 
        return array(
            "golesLocal"=>rtrim($golesLocal, ';'),
            "golesVisitante"=>rtrim($golesVisitante, ';')
        );

    }else{
        return false;
    }

    
}

?>
