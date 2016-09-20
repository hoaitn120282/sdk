<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
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
                'name'=>'Region name number '.$i,
                'latitude'=>$i,
                'longitude'=>$i,
                'country_id'=>$i%3+1,
                'created_at'=>new DateTime(),
                'updated_at'=>new DateTime(),
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('regions')->insert($fakeData);
            DB::unprepared('ALTER SEQUENCE regions_id_seq RESTART WITH 101;');
        }
    }
}