<?php

/*
* BACKEND - LOGIN
*/

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Login no Admin
$app->get('/admin/login', function()
{
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("login", [
        'error'=>User::getError()
    ]);
});

// Recebe dados do login
$app->post('/admin/login', function()
{
    try
    {
        User::login($_POST["login"], $_POST["password"]);
    }
    catch(Exception $e)
    {
        User::setError($e->getMessage());
    }

    header("Location: /admin");
    exit;
});

// Executa o logout
$app->get('/admin/logout', function()
{
    User::logout();

    header("Location: /admin/login");
    exit;
});

?>