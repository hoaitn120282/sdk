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
                'short_name'=>'Customer short name number '.$i,
                'reg_number'=>'Customer reg number number '.$i,
                'reg_date'=>new DateTime(),
                'tax_number'=>'Customer tax number number '.$i,
                'reg_address'=>'Customer reg address number '.$i,
                'email'=>'Customer email number '.$i,
                'phone'=>'Customer phone number '.$i,
                'fax'=>'Customer fax number '.$i,
                'website'=>'Customer website number '.$i,
                'logo'=>'Customer logo number '.$i,
                'billing_to'=>'Customer billing to number '.$i,
                'billing_address'=>'Customer billing address number '.$i,
                'billing_email'=>'Customer billing email number '.$i,
                'country_id'=>$i%3+1,
                'customer_source_id'=>$i%3+1,
                'business_domain_id'=>$i%3+1,
                'verified'=>($i%2==0?true:false),
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