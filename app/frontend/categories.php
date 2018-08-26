<?php

/*************
* FRONTEND - CATEGORIAS
*************/

use \Hcode\Page;
use \Hcode\Model\Category;

// Categorias
$app->get('/category/:idcategory', function($idcategory)
{
	$category = new Category();

	$category->get((int)$idcategory);
    $page = new Page();

    $page->setTpl("category", [
    	'category'=>$category->getValues(),
    	'products'=>[]
    ]);
});

?>