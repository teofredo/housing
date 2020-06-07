<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{
	DB,
	Carbon
};

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('fees')->insert([
	    	[
	    		'code' => 'association_dues',
	    		'name' => 'association dues',
	    		'fee' => 600,
	    		'other_fee' => 0,
	    		'description' => null,
	    		'deleble' => 0
	    	],
	    	[
	    		'code' => 'other',
	    		'name' => 'other',
	    		'fee' => 0,
	    		'other_fee' => 1,
	    		'description' => 'please specify',
	    		'deleble' => 0
	    	],
	    	[
	    		'code' => 'pro_rated',
	    		'name' => 'pro-rated',
	    		'fee' => 0,
	    		'other_fee' => 1,
	    		'description' => 'internet pro-rated',
	    		'deleble' => 0
	    	],
	    	[
	    		'code' => 'pre_termination_fee',
	    		'name' => 'pre-termination fee',
	    		'fee' => 0,
	    		'other_fee' => 1,
	    		'description' => 'internet plan cancellation during lockin period',
	    		'deleble' => 0
	    	],
	    	[
	    		'code' => 'installation_fee',
	    		'name' => 'installation fee',
	    		'fee' => 1000,
	    		'other_fee' => 1,
	    		'description' => 'internet plan',
	    		'deleble' => 0
	    	],
	    	[
	    		'code' => 'downgrade_fee',
	    		'name' => 'downgrade fee',
	    		'fee' => 2500,
	    		'other_fee' => 1,
	    		'description' => 'internet plan',
	    		'deleble' => 0
	    	]
    	]);
    }
}
