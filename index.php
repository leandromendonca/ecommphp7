<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$uri = explode('?', substr($_SERVER['REQUEST_URI'], 1));
$uri = explode('/', $uri[0]);

// Escolhe qual rota seguir
switch ($uri[0]) {
    case 'admin':
        require_once("app/admin/index.php");
        break;
    
    default:
        require_once("app/frontend/index.php");
        break;
}

/*************
* RENDER
*************/

$app->run();

?>