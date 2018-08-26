<?php

/*
* BACKEND - CATEGORIAS
*/

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

// Página de categorias
$app->get("/admin/categories", function()
{
    User::verifyLogin();

    $categories = Category::listAll();

    $page = new pageAdmin();

    $page->setTpl("categories", [
        'categories'=>$categories
    ]);
});

// Cria categoria
$app->get("/admin/categories/create", function()
{
    User::verifyLogin();

    $page = new pageAdmin();

    $page->setTpl("categories-create");
});

// Recebe a criação da categoria
$app->post("/admin/categories/create", function()
{
    User::verifyLogin();

    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;
});

// Apaga categoria
$app->get("/admin/categories/:idcategory/delete", function($idcategory)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $category->delete();

    header("Location: /admin/categories");
    exit;
});

// Edita categoria
$app->get("/admin/categories/:idcategory", function($idcategory)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $page = new pageAdmin();

    $page->setTpl("categories-update", [
        'category'=>$category->getValues()
    ]);
});

// Recebe a edição da categoria
$app->post("/admin/categories/:idcategory", function($idcategory)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;
});

// Relação de categorias com produtos
$app->get("/admin/categories/:idcategory/products", function($idcategory)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $page = new pageAdmin();

    $page->setTpl("categories-products", [
        'category'=>$category->getValues(),
        'productsRelated'=>$category->getProducts(true),
        'productsNotRelated'=>$category->getProducts(false)
    ]);
});

// Adiciona o produto na categoria
$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $product = new Product();

    $product->get((int)$idproduct);

    $category->addProduct($product);

    header("Location: /admin/categories/".$idcategory."/products");
    exit;
});

// Remove o produto da categoria
$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct)
{
    User::verifyLogin();

    $category = new Category();

    $category->get((int)$idcategory);

    $product = new Product();

    $product->get((int)$idproduct);

    $category->removeProduct($product);

    header("Location: /admin/categories/".$idcategory."/products");
    exit;
});


?>