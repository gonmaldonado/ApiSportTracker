<?php

include('../simple_html_dom.php');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://www.promiedos.com.ar/');
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
    //TITULOS
    foreach($torneos->find("//tr[@class='tituloin']") as $torneo) 
    {
        $competencia = $torneo->plaintext;
        echo '<h1>' . $competencia . ' </h1><br>';
        
    }
    // PARTIDOS EN CURSO
    foreach($torneos->find('tr[name=vp]') as $partido) {
        if (preg_match("/\d+'/", $partido->plaintext) || (strpos($partido->plaintext, 'E. T.') !== false)) {
            $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box','Final','Ida:','-'), '', $partido->plaintext));
             if(strpos($dato_partidos, ";") == false){
                echo '<h3>' . $dato_partidos . ' </h3><br>';
                $dato_partidos = trim(str_ireplace(array('E. T.'), '', $dato_partidos));
                $data = extraerDatosPartidoEnCurso($dato_partidos,$competencia);
                print_r($data);
             }else{
                echo '<h3>' . $dato_partidos . ' </h3><br>';
                 print_r(goles($data['goles_local'],$data['goles_visitante'], $partido->plaintext)) ;

             }
        }
    }
    //PARTIDOS FINALIZADOS
    foreach($torneos->find('tr[name=nvp]') as $partido) {
        if (strpos($partido->plaintext, 'Final') !== false) {
        $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box', 'Final','Ida:','-'), '', $partido->plaintext));
        echo '<h3>' . $dato_partidos . ' </h3><br>';
        $data = extraerDatosPartidoFinal($dato_partidos,$competencia);
        print_r($data);
        }else{
            $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box', 'Final','Ida:','-'), '', $partido->plaintext));
            if (strpos($dato_partidos, "'") !== false && strpos($dato_partidos, ";") !== false && preg_match("/[0-9]/", $dato_partidos)){
                echo '<h3>' . $dato_partidos . ' </h3><br>';
                print_r(goles($data['goles_local'],$data['goles_visitante'], $partido->plaintext)) ;           
            }
        }
    }
    //PARTIDOS DE HOY
    foreach($torneos->find('tr[name=nvp]') as $partido) {
        if (preg_match("/^\d+:\d+/",$partido->plaintext)) {
        $dato_partidos = trim(str_ireplace(array('add_boxvideo_library','ondemand_video','add_box','Ida:','-'), '', $partido->plaintext));
        echo '<h3>' . $dato_partidos . ' </h3><br>';
        $data = extraerDatosPartidoHoy($dato_partidos,$competencia);
        print_r($data);
         } 
    }
    
}

function extraerDatosPartidoHoy($texto,$competencia) 
{

    $patron1 = "/^(\d+:\d+)\s+(.+?)\s+(.+)$/";

    if (preg_match($patron1, $texto, $coincidencias)) {
        $horario =$coincidencias[1];
        $local = $coincidencias[2];
        $visitante = $coincidencias[3];

        return array(
            "fecha"=> date("Y-m-d"),
            "competencia"=>$competencia,
            "horario"=>$horario,
            "local" => $local,
            "visitante" => $visitante,
        );
    }
    else 
    {
        return false;
    }
}
    function extraerDatosPartidoEnCurso($texto,$competencia) {
        $patron1 = "/^(\d+') ([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/u";//##'
        $patron2 = "/^(\d+[+]*\d*')\s+([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//90+##'
        $patron3 = "/^([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//E. T. le envio Local golesL golesV Visitante, sin E. T.

        if (preg_match($patron1, $texto, $coincidencias)) {
            $minutos =$coincidencias[1];
            $local = $coincidencias[2];
            $goles_local = $coincidencias[3];
            $goles_visitante = $coincidencias[4];
            $visitante = $coincidencias[5];
            
            return array(
                "fecha"=> date("Y-m-d"),
                "competencia"=>$competencia,
                "minutos"=>$minutos,
                "local" => $local,
                "goles_local" => $goles_local,
                "goles_visitante" => $goles_visitante,
                "visitante" => $visitante,
                "resutado" => $goles_local . " - " . $goles_visitante,
            );
        }
        else
        { 
            if (preg_match($patron2, $texto, $coincidencias)) 
            {              
                $minutos =$coincidencias[1];
                $local = $coincidencias[2];
                $goles_local = $coincidencias[3];
                $goles_visitante = $coincidencias[4];
                $visitante = $coincidencias[5];
   
                return array(
                    "fecha"=> date("Y-m-d"),
                    "competencia"=>$competencia,
                    "minutos"=>$minutos,
                    "local" => $local,
                    "goles_local" => $goles_local,
                    "goles_visitante" => $goles_visitante,
                    "visitante" => $visitante,
                    "resutado" => $goles_local . " - " . $goles_visitante,
                );
            }   
            else
            {
                if (preg_match($patron3, $texto, $coincidencias)) 
                {
                    $minutos ="E.T.";
                    $local = $coincidencias[1];
                    $goles_local = $coincidencias[2];
                    $goles_visitante = $coincidencias[3];
                    $visitante = $coincidencias[4];

                    return array(
                        "fecha"=> date("Y-m-d"),
                        "competencia"=>$competencia,
                        "minutos"=>$minutos,
                        "local" => $local,
                        "goles_local" => $goles_local,
                        "goles_visitante" => $goles_visitante,
                        "visitante" => $visitante,
                        "resutado" => $goles_local . " - " . $goles_visitante,
                    );
                } 
                else
                {
                    return false;
                }
            } 

        }

    }

    function extraerDatosPartidoFinal($texto,$competencia) 
    {
        $patron1 = "/^([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//Partido finalizado sin penales
        $patron2 = "/^([\p{L}().\s]+?)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+([\p{L}().\s]+)$/";//Partido finalizado con penales

        if (preg_match($patron2, $texto, $coincidencias)) {
            
            $local = $coincidencias[1];
            $goles_local = $coincidencias[2];
            $penales_local = $coincidencias[3];
            $goles_visitante = $coincidencias[4];
            $penales_visitante = $coincidencias[5];
            $visitante = $coincidencias[6];
            
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
                $local = $coincidencias[1];
                $goles_local = $coincidencias[2];
                $goles_visitante = $coincidencias[3];
                $visitante = $coincidencias[4];

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
            }
            else 
            {
                return false;
            }
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
/*
COMENTARIOS:
PromiedoResultado contempla con resultados:
Final-->OK
##'-->OK
90+#'-->OK
E. T.-->OK
TODO para otras variantes no contempladas.
*/

?>
