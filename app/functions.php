<?php

use \Hcode\Model\User;

//
// Funções de formatação
//
function formatNumber(float $value)
{
	return number_format($value, 2, ",", ".");
}

function formatPrice(float $value)
{
	return "R$" . number_format($value, 2, ",", ".");
}

function formatValueToDecimal($value)
{
	$value = str_replace('.', '', $value);

	return (float)str_replace(',', '.', $value);
}

//
// Funções de usuários
//
function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);
}

function getUserName()
{
	return User::getUserName();
}

?>