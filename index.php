<?php 

// Inicializa a sessão do usuário
session_start();

// Carrega autoloaders das classes
require_once("vendor" . DIRECTORY_SEPARATOR . "autoload.php");

// Carrega biblioteca de funções
require_once("app" . DIRECTORY_SEPARATOR . "functions.php");

// Inicializa o Slim Framework
use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

// Escolhe qual rota seguir
$uri = explode('?', substr($_SERVER['REQUEST_URI'], 1));
$uri = explode('/', $uri[0]);

switch ($uri[0]) {
    case 'admin':
        require_once("app" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "index.php");
        break;
    
    default:
        require_once("app" . DIRECTORY_SEPARATOR . "frontend" . DIRECTORY_SEPARATOR . "index.php");
        break;
}

// Renderiza a página
$app->run();

?>