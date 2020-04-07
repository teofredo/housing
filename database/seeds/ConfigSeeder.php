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
        		'value' => json_encode([28,29,30,31]),
        		'comment' => 'last day of the month'
        	],

        	[
        		'key' => 'cut-off',
        		'value' => 25,
        		'comment' => 'day of the month'
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
                'value' => 0
            ]
        ]);
    }
}
