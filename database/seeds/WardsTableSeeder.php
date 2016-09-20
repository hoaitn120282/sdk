<?php

use Illuminate\Database\Seeder;

class WardsTableSeeder extends Seeder
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
                'name'=>'Ward name number '.$i,
                'latitude'=>$i,
                'longitude'=>$i,
                'district_id'=>$i%3+1,
                'created_at'=>new DateTime(),
                'updated_at'=>new DateTime(),
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('wards')->insert($fakeData);
            DB::unprepared('ALTER SEQUENCE wards_id_seq RESTART WITH 100;');
        }
    }
}