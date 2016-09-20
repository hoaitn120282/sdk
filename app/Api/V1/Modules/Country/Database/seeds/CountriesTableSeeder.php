<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i<100; $i++){
            $fakeData = [
                'id'=>$i,
                'name'=>'Country name number '.$i,
                'alpha2'=>'Country alpha2 number '.$i,
                'alpha3'=>'Country alpha3 number '.$i,
                'latitude'=>$i,
                'longitude'=>$i,
                'created_at'=>new DateTime(),
                'updated_at'=>new DateTime(),
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('countries')->insert($fakeData);
            DB::unprepared('ALTER SEQUENCE countries_id_seq RESTART WITH 101;');
        }
    }
}