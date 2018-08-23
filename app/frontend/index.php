<?php

/*************
* FRONTEND
*************/

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Home
$app->config('debug', true);

$app->get('/', function()
{
    $page = new Page();

    $page->setTpl("home");
});

?>