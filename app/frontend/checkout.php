<?php

/*************
* FRONTEND - CARRINHO DE COMPRAS
*************/

use \Hcode\Page;
use \Hcode\Model\Cart;
use \Hcode\Model\Product;
use \Hcode\Model\User;
use \Hcode\Model\Address;

// Página do checkout
$app->get("/checkout", function(){

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Address();

	$page = new Page();

	$page->setTpl("checkout", [
		'cart'=>$cart->getValues(),
		'address'=>$address->getValues()
	]);
});

?>