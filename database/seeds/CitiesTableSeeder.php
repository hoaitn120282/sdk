<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i<100; $i++){
            $fakeCity = [
                'id'=>$i,
                'name'=>'City number '.$i,
                'region_id'=>($i%2==0?1:2),
                'created_at'=>'2016-04-01 18:05:52',
                'updated_at'=>'2016-04-01 18:05:57',
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('cities')->insert($fakeCity);
            DB::unprepared('ALTER SEQUENCE cities_id_seq RESTART WITH 100;');
        }
    }
}
