<?php

$astronjson = file_get_contents(PATH_ROOT.'tmp/database/app.json');
$astronjson = json_decode($astronjson, true);

$dir = PATH_ROOT."src/entity/";
$isDevMode = false;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'host'     => $astronjson['database']['host'],
    'user'     => $astronjson['database']['username'],
    'password' => $astronjson['database']['password'],
    'dbname'   => $astronjson['database']['database']
);