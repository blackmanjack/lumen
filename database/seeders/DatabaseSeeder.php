<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        User::create([
            'username' => 'admin',
            'email' => 'lumenpostgresql@gmail.com',
            'password' => Hash::make('adminLumen22'),
            'is_admin' => true,
            'status' => true,
            'token' => base64_encode(Str::random(32))   
        ]);
    }
}
