<?php

function formatNumber(float $value)
{
	return number_format($vlnumber, 2, ",", ".");
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

?>