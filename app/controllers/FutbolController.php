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
    public function index()
    {
        $torneo = new CopadeLigaArg(); // Crear una instancia de la clase
        echo $torneo->getTablaCopadeLigaArg(1);

    }
}
