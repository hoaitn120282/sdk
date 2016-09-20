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
            $fakeRegion = [
                'id'=>$i,
                'name'=>'Region number '.$i,
                'country_id'=>($i%2==0?242:236),
                'created_at'=>'2016-04-01 18:05:52',
                'updated_at'=>'2016-04-01 18:05:57',
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('regions')->insert($fakeRegion);
            DB::unprepared('ALTER SEQUENCE regions_id_seq RESTART WITH 100;');
        }
    }
}
