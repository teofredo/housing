<?php

//custom helper functions

function pr(array $data)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function vd($data)
{
	var_dump($data);
}