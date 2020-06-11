<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WaterRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('water_rates')->insert([
        	[
        		'min_fee' => 350,
	            'min_m3' => 0,
	            'max_m3' => 10,
	            'per_m3' => 0,
	            'created_at' => Carbon::now()
        	], [
	            'min_fee' => 0,
	            'min_m3' => 11,
	            'max_m3' => 20,
	            'per_m3' => 40,
	            'created_at' => Carbon::now()
        	]
        ]);
    }
}
