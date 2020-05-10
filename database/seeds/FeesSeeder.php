<?php

use Illuminate\Database\Seeder;

class FeesSeeder extends Seeder
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
        		'code' => 'association-dues',
        		'name' => 'association dues',
        		'fee' => 600,
        		'other_fee' => 0,
        		'deleble' => 0,
        		'description' => 'fees for 24 hrs security, CCTV, street lights, garbage collection, villas street cleaner'
        	],

        	[
        		'code' => 'other',
        		'name' => 'other',
        		'fee' => 0,
        		'other_fee' => 1,
        		'deleble' => 0,
        		'description' => 'to be specified'
        	],

        	[
        		'code' => 'pro-rated',
        		'name' => 'pro-rated',
        		'fee' => 0,
        		'other_fee' => 1,
        		'deleble' => 0,
        		'description' => 'internet fee pro rata'
        	]
        ]);
    }
}
