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
    
    case 'cart':
        require_once("cart.php");
        break;
        
    case 'category':
        require_once("categories.php");
        break;
        
    case 'checkout':
        require_once("checkout.php");
        break;
        
    case 'login':
    case 'logout':
    case 'forgot':
        require_once("login.php");
        break;
    
    case 'product':
        require_once("products.php");
        break;
        
    case 'profile':
    case 'register':
        require_once("profile.php");
        break;
        
}

?>