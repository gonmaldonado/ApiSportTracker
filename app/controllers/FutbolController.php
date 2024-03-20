<?php

namespace App\Controllers;
use App\scraping\promiedos\CopadeLigaArg;

/**
 * This is the base controller for your Leaf MVC Project.
 * You can initialize packages or define methods here to use
 * them across all your other controllers which extend this one.
 */
class FutbolController extends Controller
{
    
    public function getTabla($id)
    {   
        $torneo = new CopadeLigaArg();     
        if($id != null)
        {
            // Recibir JSON
            $json =$torneo->getTablaCopadeLigaArg($id);
            // Decodificar JSON a arrays asociativo
            $data = json_decode($json, true);
            // Devolver la respuesta JSON
            return response()->json($data);
        }

    }
    public function getTablas()
    {
        $torneo = new CopadeLigaArg();
        $torneos = [];
    
        // Recibir los JSON
        $json1 = $torneo->getTablaCopadeLigaArg(1); 
        $json2 = $torneo->getTablaCopadeLigaArg(2); 
        // Decodificar los JSON a arrays asociativos
        $data1 = json_decode($json1, true);
        $data2 = json_decode($json2, true);
    
        $torneos[] = $data1;
        $torneos[] = $data2;
    
        // Devolver la respuesta JSON
        return response()->json($torneos);

    }
}
