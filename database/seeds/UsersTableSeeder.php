<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $key = \Illuminate\Support\Facades\Config::get('app.key');

        $fakeUser = [
            'id'=>1,
            'name'=>'QSoft VietNam',
            'email'=>'admin@qsoftvietnam.com',
            'password'=>\Illuminate\Support\Facades\Hash::make('admin'),
            'active'=>true,
            'created_at'=>'2016-01-19 18:05:52',
            'updated_at'=>'2016-01-19 18:05:57',
            'created_by'=>1,
            'updated_by'=>1,
            'secret_key'=>QSoftvn\Helper\Helper::simpleEncrypt('admin@qsoftvietnam.com')
        ];
        DB::table('users')->insert($fakeUser);
        $fakeUser = [
            'id'=>2,
            'name'=>'Member 1',
            'email'=>'member1@qsoftvietnam.com',
            'password'=>\Illuminate\Support\Facades\Hash::make('admin'),
            'active'=>true,
            'created_at'=>'2016-01-19 18:05:52',
            'updated_at'=>'2016-01-19 18:05:57',
            'created_by'=>1,
            'updated_by'=>1
        ];

        DB::table('users')->insert($fakeUser);

        $fakeUser = [
            'id'=>3,
            'name'=>'Member 2',
            'email'=>'member2@qsoftvietnam.com',
            'password'=>\Illuminate\Support\Facades\Hash::make('admin'),
            'active'=>true,
            'created_at'=>'2016-01-19 18:05:52',
            'updated_at'=>'2016-01-19 18:05:57',
            'created_by'=>1,
            'updated_by'=>1
        ];

        DB::table('users')->insert($fakeUser);

        $fakeClientInfo = [
            'id'=>1,
            'secret'=>'123',
            'name'=>'test',
            'created_at'=>'2016-01-19 18:05:52',
            'updated_at'=>'2016-01-19 18:05:57'];

        DB::table('oauth_clients')->insert($fakeClientInfo);
        DB::unprepared('ALTER SEQUENCE users_id_seq RESTART WITH 4;');
    }
}
