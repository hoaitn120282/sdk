<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
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
                'name'=>'Customer name number '.$i,
                'description'=>'Customer description number '.$i,
                'created_at'=>new DateTime(),
                'updated_at'=>new DateTime(),
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1)
            ];
            DB::table('customers')->insert($fakeData);
            DB::unprepared('ALTER SEQUENCE customers_id_seq RESTART WITH 101;');
        }
    }
}