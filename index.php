<?php

// Provjeri je li postavljena varijabla rt; kopiraj ju u $route
if (isset($_GET['rt']))
    $route = $_GET['rt'];
else
    $route = 'rooms';

// Ako je $route == 'con/act', onda rastavi na $controllerName='con', $action='act'
$parts = explode('/', $route);

$controllerName = $parts[0] . 'Controller';
if (isset($parts[1]))
    $action = $parts[1];
else
    $action = 'index';

// Controller $controllerName se nalazi poddirektoriju controller
$controllerFileName = 'controller/' . $controllerName . '.php';

// Includeaj tu datoteku
if (!file_exists($controllerFileName)) {
    $controllerName = '_404Controller';
    $controllerFileName = 'controller/' . $controllerName . '.php';
}

require_once $controllerFileName;

// Stvori pripadni kontroler
$con = new $controllerName;

// Ako u njemu nema tražene akcije, stavi da se traži akcija index
if (!method_exists($con, $action))
    $action = 'index';

// Pozovi odgovarajuću akciju

$args = [];
//odabiru se dijelovi query stringa koji dolaze nakon dijela koji određuje koju 
//akciju pozvati te se ti dijelovi prosljeđuju akciji kao parametri 
foreach ($parts as $part_key => $part_value){
    if($part_key> 1){
        array_push($args, $part_value);
    }
}
//da bi mogli funkciji (akciji) proslijediti parametre kao Array; 
$re =  call_user_func_array(array($con, $action), $args);
;
?>
