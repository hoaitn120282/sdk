<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $step = 0;
        for ($i = 1; $i<100; $i++){
            $fakeData = [
                'id'=>$i,
                'name'=>'Role name number '.$i,
                'description'=>'Role description number '.$i,
                'is_active'=>($i%2==0?true:false),
                'type'=>'Role type number '.$i,
                'created_at'=>new DateTime(),
                'updated_at'=>new DateTime(),
                'created_by'=>($i%3+1),
                'updated_by'=>($i%3+1),
                'parent_id'=>null,
                'lft'=>$i+$step,
                'rgt'=>$i+$step+1
            ];
        $step++;
            DB::table('roles')->insert($fakeData);
            DB::unprepared('ALTER SEQUENCE roles_id_seq RESTART WITH 101;');
        }
    }
}