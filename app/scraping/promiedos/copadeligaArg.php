<?php
namespace App\scraping\promiedos;
include(__DIR__ . '/../simple_html_dom.php');
class CopadeLigaArg{

function getTablaCopadeLigaArg ($zona)
{
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://www.promiedos.com.ar/copadeliga');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    $domResult = new simple_html_dom();
    $domResult->load($result);

    $zonas = array(
        1 => array(
            'zona' => 'Zona A',
            'clase' => 'tablesorter1'
        ),
        2 => array(
            'zona' => 'Zona B',
            'clase' => 'tablesorter2'
        )
    );

    foreach($domResult->find('div[id=tablapts]') as $t) 
{
    foreach($t->find('table#posiciones.' .$zonas[$zona]['clase']) as $z) 
    {

        foreach ($z->find('tbody') as $data) {
            $datos = array();
            foreach ($data->find('tr') as $fila) {
                $posicion = $fila->find('td', 0)->plaintext;   
                $equipo = $fila->find('td', 1)->plaintext;
                $puntos = $fila->find('td', 2)->plaintext;
                $jugados= $fila->find('td', 3)->plaintext;
                $ganados = $fila->find('td', 4)->plaintext;
                $empatados = $fila->find('td', 5)->plaintext;
                $perdidos = $fila->find('td', 6)->plaintext;
                $gf = $fila->find('td', 7)->plaintext;
                $gc = $fila->find('td', 8)->plaintext;
                $dif = $fila->find('td', 9)->plaintext;
            
                $datos[] = array(
                        'posicion'=>$posicion,  
                        'equipo'=>$equipo,
                        'puntos'=>$puntos,
                        'jugados'=>$jugados,
                        'ganados'=>$ganados,
                        'empatados' =>$empatados,
                        'perdidos'=>$perdidos,
                        'gf'=>$gf,
                        'gc'=>$gc,
                        'dif'=>$dif 
                );
            }
         }
         $return = array(
            'tabla'=>$zonas[$zona]['zona'],
            'data'=>$datos
         );

        return json_encode($return);
    }
    }


}     
}
    
// //ZONA A
// echo getTablaCopadeLigaArg (1);
// //ZONA B
// echo getTablaCopadeLigaArg (2);


?>
