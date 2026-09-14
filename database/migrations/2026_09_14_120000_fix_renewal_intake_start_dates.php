<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Datacorrectie: bij verlengingen in maart–mei 2026 is per ongeluk een
     * extra intake mét een eigen start_date aangemaakt. Omdat period_weeks
     * bij een verlenging wordt OPGETELD in het profiel, hoort de einddatum
     * altijd vanaf de oorspronkelijke startdatum gerekend te worden. De
     * extra start_date liet de eerste periode dubbel tellen, waardoor de
     * verleng-popup bij deze klanten nooit (of veel te laat) verscheen.
     *
     * We zetten de start_date van die renewal-intakes op NULL; de originele
     * intake blijft het anker. De WHERE-condities matchen exact op de
     * bekende rijen zodat deze migratie nergens anders iets kan raken.
     *
     * Betreft:
     * - intake 217 (client 7,  Dave Jannenga,   verlenging 23-03-2026)
     * - intake 234 (client 29, Isaak Loupatty,  verlenging 13-04-2026)
     * - intake 268 (client 34, Team Anergy,     verlenging 18-05-2026)
     * - intake 269 (client 34, Team Anergy,     verlenging 18-05-2026)
     */
    public function up(): void
    {
        $renewalIntakes = [
            ['id' => 217, 'client_id' => 7,  'start_date' => '2026-03-23'],
            ['id' => 234, 'client_id' => 29, 'start_date' => '2026-04-13'],
            ['id' => 268, 'client_id' => 34, 'start_date' => '2026-05-18'],
            ['id' => 269, 'client_id' => 34, 'start_date' => '2026-05-18'],
        ];

        foreach ($renewalIntakes as $row) {
            DB::table('intakes')
                ->where('id', $row['id'])
                ->where('client_id', $row['client_id'])
                ->whereDate('start_date', $row['start_date'])
                ->update(['start_date' => null]);
        }
    }

    /**
     * Bewust geen down(): de oude start_dates waren foutieve data.
     */
    public function down(): void
    {
        // niets terugdraaien
    }
};
