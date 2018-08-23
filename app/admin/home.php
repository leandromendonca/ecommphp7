<?php
/*************
* BACKEND - HOME
*************/

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Home do Admin
$app->get('/admin', function()
{
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");
});

?>