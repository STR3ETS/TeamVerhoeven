<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use App\Models\Order;
use App\Models\TrainingAssignment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PurgeExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:purge-expired {--dry-run : Toon wat zou worden verwijderd zonder daadwerkelijk te verwijderen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verwijder clients waarvan het abonnement meer dan 1 dag verlopen is';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $now = Carbon::now();
        $deletedCount = 0;
        $errors = [];

        $this->info('🔍 Zoeken naar verlopen abonnementen...');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODUS - Er wordt niets verwijderd');
        }

        // Haal alle clients op
        $clients = User::where('role', 'client')->get();

        foreach ($clients as $user) {
            $profile = ClientProfile::where('user_id', $user->id)->first();
            if (!$profile) {
                continue;
            }

            // Haal de laatste intake op met start_date
            $latestIntake = Intake::where('client_id', $user->id)
                ->whereNotNull('start_date')
                ->orderByDesc('start_date')
                ->first();

            if (!$latestIntake || !$latestIntake->start_date) {
                continue;
            }

            // Bereken einddatum
            $startDate = Carbon::parse($latestIntake->start_date);
            $periodWeeks = (int) ($profile->period_weeks ?? 12);
            $endDate = $startDate->copy()->addWeeks($periodWeeks);

            // Check of meer dan 1 dag verlopen (conservatieve afronding met floor)
            $daysExpiredRaw = $now->diffInDays($endDate, false); // negative als verlopen
            $daysExpired = (int) floor($daysExpiredRaw);

            if ($daysExpired < -1) {
                // Meer dan 1 dag verlopen
                $this->line("  📋 User #{$user->id} ({$user->email}): verlopen op {$endDate->format('d-m-Y')} ({$daysExpired} dagen)");

                if (!$dryRun) {
                    try {
                        $this->deleteUserCompletely($user->id);
                        $deletedCount++;
                        $this->info("     ✅ Verwijderd");
                    } catch (\Throwable $e) {
                        $errors[] = "User #{$user->id}: " . $e->getMessage();
                        $this->error("     ❌ Fout: " . $e->getMessage());
                    }
                } else {
                    $deletedCount++;
                    $this->info("     [DRY RUN] Zou worden verwijderd");
                }
            }
        }

        $this->newLine();
        
        if ($dryRun) {
            $this->info("🔢 Totaal {$deletedCount} users zouden worden verwijderd.");
        } else {
            $this->info("🔢 Totaal {$deletedCount} users verwijderd.");
        }

        if (count($errors) > 0) {
            $this->warn("⚠️  {count($errors)} fouten opgetreden:");
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        Log::info('[users:purge-expired] finished', [
            'deleted' => $deletedCount,
            'errors' => count($errors),
            'dry_run' => $dryRun,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Verwijder een user en alle gerelateerde data volledig.
     */
    private function deleteUserCompletely(int $userId): void
    {
        // 1. Annuleer Stripe subscription indien aanwezig
        $this->cancelStripeSubscription($userId);

        // 2. Verwijder alle gerelateerde data
        DB::transaction(function () use ($userId) {
            TrainingAssignment::where('user_id', $userId)->delete();
            Intake::where('client_id', $userId)->delete();
            Order::where('client_id', $userId)->delete();
            ClientProfile::where('user_id', $userId)->delete();

            $user = User::find($userId);
            if ($user) {
                $user->delete();
            }
        });

        Log::info('[users:purge-expired] user deleted', ['user_id' => $userId]);
    }

    /**
     * Annuleer Stripe subscription voor een user.
     */
    private function cancelStripeSubscription(int $userId): void
    {
        try {
            $order = Order::where('client_id', $userId)
                ->where('status', 'paid')
                ->whereNotNull('provider_ref')
                ->orderByDesc('id')
                ->first();

            if (!$order || !$order->provider_ref) {
                return;
            }

            $providerRef = $order->provider_ref;

            // Skip fake/access_key orders
            if (str_starts_with($providerRef, 'access_key_') || str_starts_with($providerRef, 'fake_')) {
                return;
            }

            $stripe = new StripeClient(config('services.stripe.secret'));

            try {
                $session = $stripe->checkout->sessions->retrieve($providerRef);
                $subscriptionId = $session->subscription ?? null;

                if ($subscriptionId) {
                    $stripe->subscriptions->cancel($subscriptionId);
                    Log::info('[users:purge-expired] stripe subscription canceled', [
                        'user_id' => $userId,
                        'subscription_id' => $subscriptionId,
                    ]);
                }
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Session niet gevonden - OK
            }
        } catch (\Throwable $e) {
            Log::warning('[users:purge-expired] stripe cancel failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
