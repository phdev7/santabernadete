<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@santabernadete.bymovve.com');
        $password = env('ADMIN_PASSWORD', 'admin123456');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador',
                'password' => Hash::make($password),
            ],
        );
    }
}
