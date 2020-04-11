<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{
	DB,
	Carbon
};

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->insert([
        	[
        		'key' => 'payment-due', 
        		'value' => 'END-OF-MONTH',
        		'comment' => 'last day of the month'
        	],

        	[
        		'key' => 'cut-off',
        		'value' => 25,
        		'comment' => 'day of the month, internet cut-off'
        	],

        	[
        		'key' => 'internet-plan-lockin',
        		'value' => 12,
        		'comment' => 'no of months'
        	],

        	[
        		'key' => 'notice-of-disconnection',
        		'value' => 3,
        		'comment' => 'consecutive unpaid payments'
        	],

            [
                'key' => 'generator-lock',
                'value' => 0,
                'comment' => 'must be enabled to generate month dues and disable CRUD'
            ],

            [
                'key' => 'pro-rated',
                'value' => 15,
                'comment' => 'collect pro rated on due date if it exceeds 15 days'
            ],

            [
                'key' => 'penalty-non-payment',
                'value' => 3,
                'comment' => '(percentage)penalty for non payment'
            ],

            [
                'key' => 'penalty-late',
                'value' => 3,
                'comment' => '(percentage)penalty for late'
            ]
        ]);
    }
}
