<?php

/*************
* FRONTEND - CARRINHO DE COMPRAS
*************/

use \Hcode\Page;
use \Hcode\Model\Cart;
use \Hcode\Model\Product;

// Página do carrinho
$app->get('/cart', function()
{
	$cart = Cart::getFromSession();

    $page = new Page();

    $page->setTpl("cart", [
    	'cart'=>$cart->getValues(),
    	'products'=>$cart->getProducts(),
    	'totals'=>$cart->getProductsTotals(),
    	'error'=>Cart::getMsgError()
    ]);
});

// Adiciona uma unidade do produto ao carrinho
$app->get('/cart/:idproduct/add', function($idproduct)
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;

	for ($i = 0; $i < $qtd; $i++)
	{
		$cart->addProduct($product);
	}

	header("Location: /cart");
	exit;
});

// Remove uma unidade do produto ao carrinho
$app->get('/cart/:idproduct/minus', function($idproduct)
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart");
	exit;
});

// Remove o produto do carrinho
$app->get('/cart/:idproduct/remove', function($idproduct)
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart");
	exit;
});

// Calcula o frete de acordo com o CEP
$app->post('/cart/freight', function()
{
	$cart = Cart::getFromSession();

	$cart->setFreight($_POST['zipcode']);

	header("Location: /cart");
	exit;
});

?>