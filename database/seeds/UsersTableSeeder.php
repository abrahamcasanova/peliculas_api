<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'admin';
        $user->email = 'abrahamcasanovac@outlook.com';
        $user->password =  Hash::make('admin123456');
        $user->save();
        $user->roles()->attach(Role::where('name', 'admin')->first());

        $user = new User();
        $user->name = 'user';
        $user->email = 'pruebas2021@example.com';
        $user->password =  Hash::make('pruebas2021');
        $user->save();
        $user->roles()->attach(Role::where('name', 'user')->first());

    }
}
