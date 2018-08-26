<?php

/*************
* FRONTEND - CATEGORIAS
*************/

use \Hcode\Page;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

// Categorias
$app->get('/category/:idcategory', function($idcategory)
{
	$pageNum = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($pageNum);

	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++)
	{
		array_push($pages, [
			'link'=>'/category/' . $idcategory . '?page=' . $i,
			'page'=>$i
		]);
	}

    $page = new Page();

    $page->setTpl("category", [
    	'category'=>$category->getValues(),
    	'products'=>$pagination["data"],
    	'pages'=>$pages
    ]);
});

?>