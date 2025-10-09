<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;

class CoachPlanningController extends Controller
{
    /**
     * Maak pagina + laad catalogus uit TXT naar $library
     */
    public function create(User $client)
    {
        $library = $this->loadLibraryFromTxt(); // array met items
        return view('coach.planning.create', compact('client', 'library'));
    }

    /**
     * PER-WEEK GENEREREN
     * Verwacht: week_number, week_start(YYYY-MM-DD, maandag), extra_input_bot (optioneel)
     * Retourneert: { ok: true, week: {...} }
     */
    public function generate(Request $request, User $client)
    {
        $profile = $client->clientProfile;
        abort_unless($profile, 404, 'Profiel niet gevonden');

        $periodWeeks = (int) ($profile->period_weeks ?? 12);

        $weekNumber = (int) $request->integer('week_number', 1);
        abort_if($weekNumber < 1 || $weekNumber > $periodWeeks, 422, 'Ongeldig weeknummer');

        $weekStart = Carbon::parse($request->input('week_start'));
        abort_if(!$weekStart->isMonday(), 422, 'Weekstart moet een maandag zijn.');
        abort_if($weekStart->isBefore(Carbon::today()), 422, 'Weekstart mag niet in het verleden liggen.');

        $notes = trim((string) $request->input('extra_input_bot', ''));

        // ===== Laad catalogus en bouw lib_id-enum =====
        $catalog = $this->loadLibraryFromTxt(); // zelfde bron als frontend
        if (empty($catalog)) abort(500, 'Lege catalogus');
        $libIdEnum = array_values(array_map(fn($it) => $it['id'], $catalog));

        // ===== Schema voor ÉÉN week =====
        $blockItem = [
            'type'                 => 'object',
            'additionalProperties' => false,
            'required'             => ['phase','title','duration_min','notes'],
            'properties'           => [
                'phase'        => ['type'=>'string','enum'=>['warmup','activation','mobilisation','main','finisher','cooldown']],
                'title'        => ['type'=>'string'],
                'duration_min' => ['type'=>'integer','minimum'=>3,'maximum'=>90],
                'notes'        => ['type'=>'string'],
            ],
        ];
        $sessionItem = [
            'type'                 => 'object',
            'additionalProperties' => false,
            'required'             => ['day','lib_id','title','type','duration_min','notes','blocks'],
            'properties'           => [
                'day'          => ['type'=>'string','enum'=>['mon','tue','wed','thu','fri','sat','sun']],
                // Cruciaal: lib_id MOET uit catalogus komen
                'lib_id'       => ['type'=>'string','enum'=>$libIdEnum],
                'title'        => ['type'=>'string'],
                'type'         => ['type'=>'string'], // vrij (we valideren via lib_id)
                'duration_min' => ['type'=>'integer','minimum'=>10,'maximum'=>180],
                'notes'        => ['type'=>'string'],
                'blocks'       => ['type'=>'array','items'=>$blockItem],
            ],
        ];
        $benchmarkItem = [
            'type'=>'object','additionalProperties'=>false,
            'required'=>['kind','target'],
            'properties'=>[
                'kind'=>['type'=>'string','enum'=>['cooper_12min','hyrox_sim','run_5k']],
                'target'=>['type'=>'string'],
            ],
        ];
        $schema = [
            'type'                 => 'object',
            'additionalProperties' => false,
            'required'             => ['week'],
            'properties'           => [
                'week' => [
                    'type'=>'object','additionalProperties'=>false,
                    'required'=>['week_number','start','focus','sessions','benchmarks'],
                    'properties'=>[
                        'week_number'=>['type'=>'integer','enum'=>[$weekNumber]],
                        'start'=>['type'=>'string','pattern'=>'^\d{4}-\d{2}-\d{2}$'],
                        'focus'=>['type'=>'string'],
                        'sessions'=>['type'=>'array','items'=>$sessionItem],
                        'benchmarks'=>['type'=>'array','items'=>$benchmarkItem],
                    ],
                ],
            ],
        ];

        // Tekstbestanden voor extra context (optioneel)
        $planOutline = trim(@file_get_contents(public_path('prompts/opbouw_trainingen_12_en_24_wekenoverzicht.txt'))) ?: '';

        // Bouw een compacte tabel van catalogus voor de prompt
        $catalogTable = $this->catalogForPrompt($catalog);

        $system = "Je bent een coach. Programmeer HYROX/loop-trainingen met progressieve opbouw.
- Houd je strikt aan het JSON-schema.
- Elke sessie bevat minimaal 'warmup' aan het begin en 'cooldown' aan het einde.
- Gebruik waar passend 'activation' en/of 'mobilisation' vóór 'main'.
- Sessie-duur = som van block-durations.
" . ($planOutline ? "\n=== OUTPUT-RICHTLIJNEN ===\n{$planOutline}\n" : '');

        $user = "Genereer een planning voor week {$weekNumber} met start {$weekStart->toDateString()} (maandag).
KIES UITSLUITEND sessies uit onderstaande catalogus door het corresponderende 'lib_id' te gebruiken.
Kopieer de bijbehorende 'title' en 'type' van dat catalog-item; 'duration_min' mag je licht bijstellen indien logisch.
Gebruik per sessie altijd blocks met minimaal 'warmup' en 'cooldown' (en waar passend 'activation'/'mobilisation'/'main').

=== TRAINING-CATALOGUS (id • group • type • title • duur • notes) ===
{$catalogTable}

Lever uitsluitend JSON volgens schema (geen extra tekst).";

        $payload = [
            'model' => 'gpt-4.1-mini', // of 'gpt-4o-mini'
            'input' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user . ($notes ? "\n\n=== EXTRA INSTRUCTIES ===\n{$notes}" : '')],
            ],
            'text' => [
                'format' => [
                    'type'   => 'json_schema',
                    'name'   => 'SingleWeek',
                    'strict' => true,
                    'schema' => $schema,
                ],
            ],
        ];

        try {
            $resp = Http::withToken(env('OPENAI_API_KEY'))
                ->asJson()
                ->connectTimeout(15)
                ->timeout(90)
                ->retry(2, 1000, fn($e)=>$e instanceof ConnectionException, throw:false)
                ->post('https://api.openai.com/v1/responses', $payload);

            if (!$resp->ok()) {
                \Log::warning('OpenAI non-ok', ['status'=>$resp->status(),'body'=>$resp->body()]);
                abort(502, 'OpenAI: '.$resp->body());
            }
        } catch (\Throwable $e) {
            \Log::error('OpenAI request failed', ['msg'=>$e->getMessage(), 'class'=>get_class($e)]);
            abort(502, 'OpenAI timeout/verbinding: '.$e->getMessage());
        }

        $body = $resp->json();
        $jsonText = data_get($body, 'output_text')
            ?? data_get($body, 'output.0.content.0.text')
            ?? data_get($body, 'choices.0.message.content');

        $out = json_decode($jsonText ?? '', true);
        abort_unless(is_array($out) && isset($out['week']), 502, 'Geen geldige week ontvangen.');

        // Veiligheidsnet: zorg dat iedere sessie nog steeds bestaat in catalogus
        $week = $out['week'];
        foreach (($week['sessions'] ?? []) as &$s) {
            $id = $s['lib_id'] ?? null;
            $match = $id ? collect($catalog)->firstWhere('id', $id) : null;
            if ($match) {
                // overschrijf titel/type/notes uit catalogus om drift te voorkomen
                $s['title'] = $match['title'];
                $s['type']  = $match['type'];
                $s['notes'] = $s['notes'] ?? $match['notes'];
            }
        }

        return response()->json(['ok' => true, 'week' => $week]);
    }

    /**
     * Laad & parse catalogus uit TXT naar array items:
     * [id, group, type, title, duration_min, notes]
     */
    private function loadLibraryFromTxt(): array
    {
        $txt = @file_get_contents(public_path('prompts/totaal_overzicht_trainingen_ingedeeld.txt'));
        if (!$txt) return [];

        $lines = preg_split('/\R/u', $txt);
        $out = [];
        $i = 1;

        foreach ($lines as $raw) {
            $line = trim($raw);
            if ($line === '' || strpos($line, '===') !== false) continue; // skip headers/separators

            // probeer duur te vinden
            $duration = null;
            if (preg_match('/(\d{1,3})\s*min/i', $line, $m)) {
                $duration = (int) $m[1];
            }

            // optional notes na " - " of " — "
            $titlePart = $line;
            $notes = '';
            if (preg_match('/^(.*?)[\-\—]\s*(.+)$/u', $line, $m)) {
                $titlePart = trim($m[1]);
                $notes = trim($m[2]);
            }

            // grove groepsdetectie
            $lower = Str::lower($line);
            $group = 'misc';
            if (str_contains($lower, 'run'))        $group = 'run';
            elseif (str_contains($lower, 'strength') || str_contains($lower, 'kracht')) $group = 'strength';
            elseif (str_contains($lower, 'erg') || str_contains($lower, 'row') || str_contains($lower, 'bike')) $group = 'erg';
            elseif (str_contains($lower, 'core'))   $group = 'core';
            elseif (str_contains($lower, 'recover') || str_contains($lower, 'herstel')) $group = 'recovery';
            elseif (str_contains($lower, 'hyrox'))  $group = 'hyrox';

            $type = Str::slug(Str::limit($titlePart, 40, ''), '_') ?: 'custom';
            $out[] = [
                'id'           => 'lib_' . str_pad((string)$i++, 4, '0', STR_PAD_LEFT),
                'group'        => $group,
                'type'         => $type,
                'title'        => $titlePart,
                'duration_min' => $duration ?? 45,
                'notes'        => $notes,
            ];
        }
        return $out;
    }

    /**
     * Maak compacte tabelstring voor de prompt
     */
    private function catalogForPrompt(array $catalog): string
    {
        $lines = [];
        foreach ($catalog as $c) {
            $lines[] = "{$c['id']} • {$c['group']} • {$c['type']} • {$c['title']} • {$c['duration_min']} min • {$c['notes']}";
        }
        return implode("\n", $lines);
    }
}
