<?php

/*
* BACKEND - PRODUTOS
*/

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

// Página de produtos
$app->get("/admin/products", function()
{
    User::verifyLogin();

    $products = Product::listAll();

    $page = new pageAdmin();

    $page->setTpl("products", [
        'products'=>$products
    ]);
});

// Cria produto
$app->get("/admin/products/create", function()
{
    User::verifyLogin();

    $page = new pageAdmin();

    $page->setTpl("products-create");
});

// Recebe a criação do produto
$app->post("/admin/products/create", function()
{
    User::verifyLogin();

    $product = new Product();

    $product->setData($_POST);

    $product->save();

    header("Location: /admin/products");
    exit;
});

// Apaga produto
$app->get("/admin/products/:idproduct/delete", function($idproduct)
{
    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $product->delete();

    header("Location: /admin/products");
    exit;
});

// Edita produto
$app->get("/admin/products/:idproduct", function($idproduct)
{
    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $page = new pageAdmin();

    $page->setTpl("products-update", [
        'product'=>$product->getValues()
    ]);
});

// Recebe a edição do produto
$app->post("/admin/products/:idproduct", function($idproduct)
{
    User::verifyLogin();

    $product = new Product();

    $product->get((int)$idproduct);

    $product->setData($_POST);

    $product->save();

    $product->setPhoto($_FILES["file"]);

    header("Location: /admin/products");
    exit;
});

?>