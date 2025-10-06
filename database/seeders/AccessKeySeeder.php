<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccessKey;
use Illuminate\Support\Str;

class AccessKeySeeder extends Seeder
{
    public function run(): void
    {
        AccessKey::updateOrCreate(
            ['key' => 'KEY-BASIS-12-'.Str::random(12)],
            ['package' => 'pakket_a', 'duration_weeks' => 12, 'active' => true]
        );
        AccessKey::updateOrCreate(
            ['key' => 'KEY-CHASING-12-'.Str::random(12)],
            ['package' => 'pakket_b', 'duration_weeks' => 12, 'active' => true]
        );
        AccessKey::updateOrCreate(
            ['key' => 'KEY-ELITE-12-'.Str::random(12)],
            ['package' => 'pakket_c', 'duration_weeks' => 12, 'active' => true]
        );
        AccessKey::updateOrCreate(
            ['key' => 'KEY-BASIS-24-'.Str::random(12)],
            ['package' => 'pakket_a', 'duration_weeks' => 24, 'active' => true]
        );
        AccessKey::updateOrCreate(
            ['key' => 'KEY-CHASING-24-'.Str::random(12)],
            ['package' => 'pakket_b', 'duration_weeks' => 24, 'active' => true]
        );
        AccessKey::updateOrCreate(
            ['key' => 'KEY-ELITE-24-'.Str::random(12)],
            ['package' => 'pakket_c', 'duration_weeks' => 24, 'active' => true]
        );
    }
}
