<?php

/*************
* FRONTEND - CATEGORIAS
*************/

use \Hcode\Page;
use \Hcode\Model\Product;

$app->get("/product/:desurl", function($desurl)
{
	$product = new Product();

	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()
	]);
});

?>