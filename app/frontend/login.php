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

    $page->setTpl("login", [
    	'error'=>User::getError(),
    	'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : []
    ]);
});

// Recebe dados do login
$app->post('/login', function()
{
	try
	{
	    User::login($_POST["login"], $_POST["password"]);
	}
	catch(Exception $e)
	{
		User::setError($e->getMessage());
	}

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

// Esqueceu a senha
$app->get('/forgot', function()
{
    $page = new Page();

    $page->setTpl("forgot");
});

// Recebe os dados do formulário de esquecimento
$app->post('/forgot', function()
{
    $user = User::getForgot($_POST["email"], false);

    header("Location: /forgot/sent");
    exit;
});

// Página de confirmação de envio do reset de senha
$app->get("/forgot/sent", function()
{
    $page = new Page();

    $page->setTpl("forgot-sent");
});

// Página para digitação de nova senha
$app->get("/forgot/reset", function()
{
    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new page();

    $page->setTpl("forgot-reset", array(
        "name"=>$user["desperson"],
        "code"=>$_GET["code"]
    ));
});

// Recebe o reset de senha
$app->post("/forgot/reset", function()
{
    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = User::getPasswordHash($_POST["password"]);

    $user->setPassword($password);

    $page = new page();

    $page->setTpl("forgot-reset-success");

});

?>