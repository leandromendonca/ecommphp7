<?php

/*************
* FRONTEND - PROFILE DO CLIENTE
*************/

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Address;

// Página do registro do cliente
$app->post("/register", function(){

	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '')
	{
		User::setError("Preencha o seu nome.");

		header('Location: /login');
		exit;
	}

	if (!isset($_POST['email']) || $_POST['email'] == '')
	{
		User::setError("Preencha o seu e-mail.");

		header('Location: /login');
		exit;
	}

	if (!isset($_POST['password']) || $_POST['password'] == '')
	{
		User::setError("Preencha o sua senha.");

		header('Location: /login');
		exit;
	}

	if (User::checkLoginExist($_POST['email']) === true)
	{
		User::setError("Este endereço de e-mail já está cadastrado.");

		header('Location: /login');
		exit;
	}

	$user = new User();

	$user->setData([
		'inadmin' => 0,
		'deslogin' => $_POST['email'],
		'desperson' => $_POST['name'],
		'desemail' => $_POST['email'],
		'despassword' => $_POST['password'],
		'nrphone' => $_POST['phone']
	]);

	$user->save();

	User::login($_POST['email'], $_POST['password']);

	header('Location: /checkout');
	exit;
});

// Página da conta do cliente
$app->get("/profile", function(){

	User::verifyLogin(false);

    $iduser = User::getFromSession()->getiduser();

    $user = new User();

    $user->get((int)$iduser);

	$page = new Page();

	$page->setTpl("profile", [
		'user'=>$user->getValues(),
		'profileError'=>User::getError(),
		'profileMsg'=>User::getSuccess()
	]);
});

// Salva conta do cliente
$app->post("/profile", function(){

	User::verifyLogin(false);

	if (!isset($_POST['desperson']) || $_POST['desperson'] == '')
	{
		User::setError("Preencha o seu nome.");

		header('Location: /profile');
		exit;
	}

	if (!isset($_POST['desemail']) || $_POST['desemail'] == '')
	{
		User::setError("Preencha o seu e-mail.");

		header('Location: /profile');
		exit;
	}

    $iduser = User::getFromSession()->getiduser();

    $user = new User();

    $user->get((int)$iduser);

    if ($_POST['desemail'] !== $user->getdesemail())
    {
		if (User::checkLoginExist($_POST['desemail']) === true)
		{
			User::setError("Este endereço de e-mail já está cadastrado.");

			header('Location: /profile');
			exit;
		}
	}

    $_POST['inadmin'] = $user->getinadmin();
    $_POST['despassword'] = $user->getdespassword();
    $_POST['deslogin'] =$_POST['desemail'];

    $user->setData($_POST);

    $user->update();

    User::setSuccess("Dados alterados com sucesso!");

    header('Location: /profile');
    exit;
});
?>