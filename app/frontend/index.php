<?php

/*************
* FRONTEND
*************/
$uri = explode('?', substr($_SERVER['REQUEST_URI'], 1));
$uri = explode('/', $uri[0]);

// Escolhe qual rota seguir
switch ($uri[0]) {
    case '':
        require_once("home.php");
        break;
    
    case 'category':
        require_once("categories.php");
        break;
        
}

?>