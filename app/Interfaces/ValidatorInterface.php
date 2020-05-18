<?php
namespace App\Interfaces;

interface ValidatorInterface
{
	public function getRules();
	public function overrideRules();
}