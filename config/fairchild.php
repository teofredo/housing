<?php

/**
* most of it are not used
* important notes only
*/

return [

	//order o generate month dues
	'month-due-codes' => [
		'water-bill', 
		'internet-fee', 
		'other-charges',
		'prev-balance',
		'penalty-non-payment',
		'adjustments',

		// not applicable, only be added during actual payment
		'penalty-late'
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