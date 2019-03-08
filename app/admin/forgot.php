<?php

/*
* BACKEND - RESET DE SENHAS
*/

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Esqueceu a senha
$app->get('/admin/forgot', function()
{
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot");
});

// Recebe os dados do formulário de esquecimento
$app->post('/admin/forgot', function()
{
    $user = User::getForgot($_POST["email"]);

    header("Location: /admin/forgot/sent");
    exit;
});

// Página de confirmação de envio do reset de senha
$app->get("/admin/forgot/sent", function()
{
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-sent");
});

// Página para digitação de nova senha
$app->get("/admin/forgot/reset", function()
{
    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new pageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset", array(
        "name"=>$user["desperson"],
        "code"=>$_GET["code"]
    ));
});

// Recebe o reset de senha
$app->post("/admin/forgot/reset", function()
{
    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = User::getPasswordHash($_POST["password"]);

    $user->setPassword($password);

    $page = new pageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset-success");

});

?>