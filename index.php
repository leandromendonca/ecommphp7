<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

/*************
* FRONTEND
*************/

$app->config('debug', true);

$app->get('/', function()
{
    $page = new Page();

    $page->setTpl("home");
});

/*************
* BACKEND
*************/

$app->get('/admin', function()
{
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");
});

/*
* LOGIN
*/

// Login no Admin
$app->get('/admin/login', function()
{
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("login");
});

// Recebe dados do login
$app->post('/admin/login', function()
{
    User::login($_POST["login"], $_POST["password"]);

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

/*
* USUÁRIOS
*/

// Lista de usuários
$app->get('/admin/users', function()
{
    User::verifyLogin();

    $users = User::listAll();
    
    $page = new PageAdmin();

    $page->setTpl("users", array(
        "users"=>$users
    ));
});

// Cadastra usuário
$app->get('/admin/users/create', function()
{
    User::verifyLogin();
    
    $page = new PageAdmin();

    $page->setTpl("users-create");
});

// Recebe dados do cadastro
$app->post('/admin/users/create', function()
{
    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;
});

// Apaga usuário
$app->get('/admin/users/:iduser/delete', function($iduser)
{
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;
});

// Edita usuário
$app->get('/admin/users/:iduser', function($iduser)
{
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);
    
    $page = new PageAdmin();

    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
});

// Recebe dados da modificação do usuário e grava no banco
$app->post('/admin/users/:iduser', function($iduser)
{
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});

/*
* SENHAS
*/

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

// Tela de confirmação de envio do reset de senha
$app->get("/admin/forgot/sent", function()
{
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-sent");
});

// Tela para digitação de nova senha
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

$app->post("/admin/forgot/reset", function()
{
    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
        "cost"=>12
    ]);

    $user->setPassword($password);

    $page = new pageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset-success");

});

/*************
* RENDER
*************/

$app->run();

?>