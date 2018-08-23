<?php

/*************
* BACKEND
*************/

$uri = explode('?', substr($_SERVER['REQUEST_URI'], 1));
$uri = explode('/', $uri[0]);

// Escolhe qual rota seguir
switch ($uri[1]) {
    case '':
        require_once("home.php");
        break;
    
    case 'categories':
        require_once("categories.php");
        break;
    
    case 'forgot':
        require_once("forgot.php");
        break;
    
    case 'login':
    case 'logout':
        require_once("login.php");
        break;
    
    case 'users':
        require_once("users.php");
        break;
    
}

?>