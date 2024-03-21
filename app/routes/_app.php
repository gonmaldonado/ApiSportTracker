<?php

app()->get('/futbol/copaDeLaLigaARG/posiciones', 'CopaDeLigaArgController@getTablas');
app()->get('/futbol/copaDeLaLigaARG/posiciones/{id}', 'CopaDeLigaArgController@getTabla');
