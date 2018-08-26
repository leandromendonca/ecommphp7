<?php

/*************
* FRONTEND - HOME
*************/

use \Hcode\Page;
use \Hcode\Model\Product;

// Home
$app->get('/', function()
{
	$products = Product::listAll();

    $page = new Page();

    $page->setTpl("home", [
    	'products'=>Product::checkList($products)
    ]);
});

?>