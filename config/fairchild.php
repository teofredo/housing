<?php

/**
* most of it are not used
* important notes only
*/

return [

	//order to generate month dues
	'month-due-codes' => [
		'water', 
		'internet', 
		'other_charges',
		'prev_balance',
		'penalty',
		'adjustments',

		// not applicable, only be added during actual payment
		'penalty_ate'
	],

	'payment-dues' => [
		'START_OF_MONTH',
		'END_OF_MONTH',
		'HALF_OF_MONTH'
	],
	

	'queue_names' => [
		'commands' // for artisan console
	]
];	