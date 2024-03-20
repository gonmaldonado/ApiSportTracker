<?php

app()->get('/futbol/copaDeLaLigaARG', 'FutbolController@getTablas');
app()->get('/futbol/copaDeLaLigaARG/{id}', 'FutbolController@getTabla');
