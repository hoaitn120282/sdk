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
        $fakeOrg = [
            'id'=>1,
            'name'=>'Developer',
            'description'=>'This is the role for developer',
            'is_active'=>true,
            'type'=>'role',
            'parent_id'=>null,
            'created_at'=>new DateTime(),
            'updated_at'=>new DateTime(),
            'created_by'=>1,
            'updated_by'=>1,
            'lft'=>1,
            'rgt'=>2
        ];
        DB::table('roles')->insert($fakeOrg);
        
        DB::unprepared('ALTER SEQUENCE roles_id_seq RESTART WITH 2;');
    }
}
