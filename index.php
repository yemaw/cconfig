<?php

if($_GET['debug']){
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);
}

include_once('./cconfig.php');

$Config = new CConfig(
    isset($_GET['client']) ? $_GET['client'] : '',
    isset($_GET['version']) ? $_GET['version'] : '',
    isset($_GET['channel']) ? $_GET['channel'] : ''
);

$configs = $Config->get();
$configs = json_encode($configs);

header("Access-Control-Allow-Origin: *");
if($_GET['js_var']){ //Print as javascript variable
    header('Content-Type: application/javascript');
    $js_var = $_GET['js_var'];
    $configs = $js_var.'='.$configs;
} else {
    header('Content-Type: application/json');
}

echo $configs;
