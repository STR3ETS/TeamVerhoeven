<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CoachProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CoachesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = [
            ['name' => 'Eline', 'email' => 'eline@example.com'],
            ['name' => 'Nicky', 'email' => 'nicky@example.com'],
            ['name' => 'Roy',   'email' => 'roy@example.com'],
            ['name' => 'Roy',   'email' => 'raphael@eazyonline.nl'],
        ];

        foreach ($coaches as $c) {
            /** @var \App\Models\User $user */
            $user = User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name'              => $c['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'coach',
                    'email_verified_at' => now(),
                    'remember_token'    => Str::random(10),
                ]
            );

            CoachProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'avatar_url'  => null,
                    'bio'         => null,
                    'specialties' => [],
                    'is_active'   => true,
                ]
            );
        }
    }
}
