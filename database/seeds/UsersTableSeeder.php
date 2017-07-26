<?php

use Illuminate\Database\Seeder;

use App\User;
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
        User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@core.com',
            'phone' => '+6200000000000',
            'address' => 'Jakarta',
            'sex' => 'M',
            'role' => 'ADM',
            'password' => Hash::make('admin')
        ]);

        User::create([
            'name' => 'Krisan Alfa Timur',
            'email' => 'alfa@flipbox.co.id',
            'phone' => '+6289636782644',
            'address' => 'Bekasi',
            'sex' => 'M',
            'role' => 'ADM',
            'password' => Hash::make('admin')
        ]);

        User::create([
            'name' => 'Core User',
            'email' => 'user@core.com',
            'phone' => '+6211111111111',
            'address' => 'Bogor',
            'sex' => 'M',
            'role' => 'USR',
            'password' => Hash::make('user')
        ]);
    }
}
