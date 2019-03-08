<?php

/*
* FRONTEND - LOGIN
*/

use \Hcode\Page;
use \Hcode\Model\User;

// Login no Admin
$app->get('/login', function()
{
    $page = new Page();

    $page->setTpl("login");
});

// Recebe dados do login
$app->post('/login', function()
{
    User::login($_POST["login"], $_POST["password"]);

    header("Location: /checkout");
    exit;
});

// Executa o logout
$app->get('/logout', function()
{
    User::logout();

    header("Location: /login");
    exit;
});

?>