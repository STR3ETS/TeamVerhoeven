<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder voor test clients met verschillende abonnement statussen.
 * 
 * Test scenario's:
 * 1. Actief abonnement (>7 dagen over) - geen popup
 * 2. Bijna verlopen (<=7 dagen over) - waarschuwing popup met "Begrepen" knop
 * 3. Verlopen abonnement - popup met verlengen/verwijderen knoppen (niet wegklikbaar)
 * 
 * Alle clients hebben wachtwoord: test1234
 * 
 * Run met: php artisan db:seed --class=TestClientsSeeder
 */
class TestClientsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating test clients for subscription expiry testing...');
        $this->command->newLine();

        $testClients = [
            // 1. Actief abonnement - nog 10 weken over (geen popup)
            [
                'email' => 'client.active@test.com',
                'name' => 'Test Active',
                'weeks_ago' => 2, // Start 2 weken geleden, 12 weken totaal = 10 weken over
                'period_weeks' => 12,
                'description' => 'Actief - 10 weken over (geen popup)',
            ],
            // 2. Bijna verlopen - exact 7 dagen over (waarschuwing popup)
            [
                'email' => 'client.expiring7@test.com',
                'name' => 'Test Expiring 7 Days',
                'weeks_ago' => 11, // Start 11 weken geleden = 1 week over = 7 dagen
                'period_weeks' => 12,
                'description' => 'Bijna verlopen - 7 dagen (waarschuwing popup)',
            ],
            // 3. Bijna verlopen - 3 dagen over (waarschuwing popup)
            [
                'email' => 'client.expiring3@test.com',
                'name' => 'Test Expiring 3 Days',
                'weeks_ago' => 12,
                'period_weeks' => 12,
                'days_offset' => 3, // 3 dagen over
                'description' => 'Bijna verlopen - 3 dagen (waarschuwing popup)',
            ],
            // 4. Vandaag verlopend - 0 dagen over (waarschuwing popup)
            [
                'email' => 'client.expiringtoday@test.com',
                'name' => 'Test Expiring Today',
                'weeks_ago' => 12,
                'period_weeks' => 12,
                'days_offset' => 0, // Vandaag
                'description' => 'Vandaag verlopend - 0 dagen (waarschuwing popup)',
            ],
            // 5. Gisteren verlopen - verlopen popup
            [
                'email' => 'client.expired1@test.com',
                'name' => 'Test Expired 1 Day',
                'weeks_ago' => 12,
                'period_weeks' => 12,
                'days_offset' => -1, // Gisteren verlopen
                'description' => 'Verlopen - 1 dag geleden (verlopen popup)',
            ],
            // 6. Week verlopen - verlopen popup
            [
                'email' => 'client.expired7@test.com',
                'name' => 'Test Expired 1 Week',
                'weeks_ago' => 13, // 12 weken pakket, 13 weken geleden = 1 week verlopen
                'period_weeks' => 12,
                'description' => 'Verlopen - 1 week geleden (verlopen popup)',
            ],
            // 7. Lang pakket - net gestart (geen popup)
            [
                'email' => 'client.new@test.com',
                'name' => 'Test New Client',
                'weeks_ago' => 0, // Vandaag gestart
                'period_weeks' => 24,
                'description' => 'Nieuw - net gestart (geen popup)',
            ],
        ];

        foreach ($testClients as $clientData) {
            $this->createTestClient($clientData);
        }

        $this->command->newLine();
        $this->command->info('Test clients created successfully!');
        $this->command->newLine();
        $this->command->info('Login credentials:');
        $this->command->line('  Email: any of the above emails');
        $this->command->line('  Password: test1234');
    }

    private function createTestClient(array $data): void
    {
        // Calculate start date
        $startDate = Carbon::now()->subWeeks($data['weeks_ago']);
        
        // Adjust for specific days offset if set
        if (isset($data['days_offset'])) {
            // Calculate: we want end_date to be today + days_offset
            // end_date = start_date + period_weeks
            // So: start_date = today + days_offset - period_weeks
            $endDate = Carbon::now()->addDays($data['days_offset']);
            $startDate = $endDate->copy()->subWeeks($data['period_weeks']);
        }

        // Delete existing user if exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            Intake::where('client_id', $existingUser->id)->delete();
            ClientProfile::where('user_id', $existingUser->id)->delete();
            $existingUser->delete();
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('test1234'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        // Create complete client profile (all required fields filled to skip intake)
        ClientProfile::create([
            'user_id' => $user->id,
            'coach_id' => null,
            'birthdate' => Carbon::now()->subYears(30),
            'gender' => 'male',
            'phone_e164' => '+31612345678',
            'address' => [
                'street' => 'Teststraat',
                'house_number' => '123',
                'postcode' => '1234AB',
                'city' => 'Amsterdam',
            ],
            'coach_preference' => 'no_preference',
            'height_cm' => 180,
            'weight_kg' => 75.0,
            'goals' => ['general_fitness', 'weight_loss'],
            'frequency' => [
                'sessions_per_week' => 3,
                'minutes_per_session' => 60,
            ],
            'goal' => [
                'distance' => '10k',
                'time_hms' => '00:50:00',
                'date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
            ],
            'test_12min' => [
                'meters' => 2800,
            ],
            'test_5k' => '00:25:00',
            'heartrate' => [
                'max' => 185,
                'rest' => 60,
            ],
            'period_weeks' => $data['period_weeks'],
            'background' => 'Test client for subscription expiry testing',
        ]);

        // Create intake record with start_date
        Intake::create([
            'client_id' => $user->id,
            'status' => 'completed',
            'payload' => [
                'package' => 'pakket_a',
                'duration_weeks' => $data['period_weeks'],
                'contact' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ],
            ],
            'start_date' => $startDate,
            'completed_at' => $startDate,
        ]);

        // Calculate and display expiry info
        $endDate = $startDate->copy()->addWeeks($data['period_weeks']);
        $daysUntil = (int) floor(Carbon::now()->diffInDays($endDate, false));
        
        if ($daysUntil > 7) {
            $status = '✅ ACTIEF';
            $popup = 'Geen popup';
        } elseif ($daysUntil >= 0) {
            $status = '⚠️ BIJNA VERLOPEN';
            $popup = 'Waarschuwing popup';
        } else {
            $status = '❌ VERLOPEN';
            $popup = 'Verlopen popup';
        }

        $this->command->line(sprintf(
            '  %s | %s | End: %s | Days: %d | %s',
            $data['email'],
            $status,
            $endDate->format('d-m-Y'),
            $daysUntil,
            $popup
        ));
    }
}
