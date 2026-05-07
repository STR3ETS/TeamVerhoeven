<?php

namespace Database\Seeders;

use App\Models\TrainingPlanTemplate;
use App\Models\TrainingPlanTemplateItem;
use Illuminate\Database\Seeder;

class TrainingPlanTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedKnee45Days();
        $this->seedKnee6Days();
        $this->seedGeneral45Days();
        $this->seedGeneral6Days();
    }

    /**
     * Knie-blessure traject — 4-5 dagen gevuld — 12 weken
     *
     * Gemapped vanuit PDF: "Opbouw basis schema vanuit knie herstel 4-5 dagen gevuld"
     * Card IDs verwijzen naar training_cards in de database.
     *
     * Mapping formaat: [week, day, sort_order, training_card_id]
     */
    private function seedKnee45Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'knee_4_5_days'],
            [
                'name' => 'Knie-blessure traject (4-5 dagen)',
                'injury_type' => 'knee',
                'max_days' => 5,
                'weeks' => 12,
            ]
        );

        // Verwijder bestaande items bij re-seed
        $template->items()->delete();

        // Card ID referenties:
        // 176 = Warming-up Algemeen
        // 177 = Warming-up Hardlopen / Cardio
        // 178 = Recovery 30 min run
        // 180 = Recovery 50 min run
        // 181 = Recovery 60 min run
        // 182 = Recovery 30 min bike
        // 183 = Recovery 40 min bike
        // 184 = Recovery 60 min bike
        // 190 = Endurance 40 min zone 2
        // 191 = Endurance 50 min zone 2
        // 202 = Endurance zone 2 assbike/row/ski
        // 203 = Endurance zone 2 assbike/row/ski 15
        // 204 = Endurance zone 2 assbike/row/ski 20
        // 206 = Endurance zone 2 ski 3km
        // 209 = Endurance zone 2 row
        // 211 = Endurance zone 2 assbike 20min
        // 214 = Endurance zone 2 bike 30min
        // 215 = Endurance zone 2+ bike 45min
        // 245 = Treshold ski/row ladder
        // 249 = Threshold 4x1200m
        // 251 = Interval 8x200
        // 282 = Interval 5x1200 +kracht
        // 310 = Comprimised Strength endurance 4*10min push pull
        // 314 = Comprimised Strength Endurance 15min run strenght 15min run
        // 315 = Strength Endurance hyrox specific 5r 20hh
        // 318 = Strength Endurance chipper 200 benen
        // 327 = Strength Endurance 5*20 upper body push pull
        // 331 = Strength Endurance wall ball sled
        // 332 = Strength Endurance circuit b
        // 338 = Strength Endurance benen schouder combi
        // 339 = Strength Endurance Wall Ball - Finisher A
        // 340 = Strength hypertrofie ladder
        // 350 = Cooling Down
        // 351 = Isometrische warming-up + knieherstel Variant A
        // 352 = Isometrische warming-up + knieherstel Variant B
        // 359 = Warming-up 5 min cardio + dynamic
        // 360 = Cooling Down Cardio + stretch
        // 361 = Core Stability 3x20 sec circuit
        // 363 = HYROX Sim 50%
        // 370 = Strength Endurance Farm carry + sled pull (4-6 rds ASAP)
        // 374 = Strength Endurance BBJ + push press
        // 390 = High-Intensity Interval EMOM 32-40min ergs + bike
        // 406 = Strength Training HYROX Specific barbell full body
        // 407 = Warming-up bike & foam roll
        // 408 = Endurance Training zone 2 (ergs, sled, run)
        // 412 = interval 8x2min zone 4 / 2 min zone 1
        // 419 = Strength Endurance Wall Ball - Finisher B
        // 420 = Strength Endurance Wall Ball - Finisher C
        // 428 = Endurance zone 2 - 2x 10 min (opbouw)
        // 431 = Threshold zone 3 run - 2x 10 min (opbouw)
        // 432 = Threshold zone 3 run - 3x 10 min (opbouw)
        // 433 = Endurance zone 2 - 6x 1 min (opbouw)
        // 434 = Endurance zone 2 - 10 x 1 min (opbouw)
        // 435 = Endurance zone 2 - 8 x 90 sec (opbouw)
        // 436 = Strength Endurance Wall ball Squats - Finisher A
        // 439 = Strength Endurance Burpees - Finisher A
        // 441 = Strength Endurance Sled + Lunges (3-5 rondes)
        // 443 = Stretch & mobiliteit
        // 449 = interval 400-100-400-200
        // 454 = Warming-up Basic (vanuit blessure)
        // 463 = ski test 2 km
        // 469 = core stability dynamic 3*30/10

        $items = [
            // ==================== WEEK 1 ====================
            // Dag 1: Cardio ergs + stretch
            [1, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [1, 'mon', 2, 202],  // Endurance zone 2 assbike/row/ski 10 min
            [1, 'mon', 3, 443],  // Stretch & mobiliteit

            // Dag 2: Endurance hardlopen opbouw
            [1, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [1, 'tue', 2, 433],  // Endurance zone 2 hardlopen 6x60 sec opbouw
            [1, 'tue', 3, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [1, 'tue', 4, 350],  // Cooling down

            // Dag 3: Knieherstel
            [1, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // Dag 4: Strength upper body
            [1, 'thu', 1, 359],  // Warming up dynamisch
            [1, 'thu', 2, 327],  // Strength endurance circuit upper body
            [1, 'thu', 3, 350],  // Cooling down

            // ==================== WEEK 2 ====================
            // Dag 1: Cardio ergs 15 min + stretch
            [2, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [2, 'mon', 2, 203],  // Endurance zone 2 assbike/row/ski 15 min
            [2, 'mon', 3, 443],  // Stretch & mobiliteit

            // Dag 2: Endurance hardlopen opbouw
            [2, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [2, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [2, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec opbouw
            [2, 'tue', 4, 350],  // Cooling down

            // Dag 3: Knieherstel
            [2, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // Dag 4: Strength sled + explosief
            [2, 'thu', 1, 359],  // Warming up dynamisch
            [2, 'thu', 2, 370],  // Strength Endurance Farm carry + sled pull
            [2, 'thu', 3, 374],  // Strength Endurance BBJ + push press
            [2, 'thu', 4, 350],  // Cooling down

            // ==================== WEEK 3 ====================
            // Dag 1: Cardio ergs 20 min + stretch
            [3, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [3, 'mon', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [3, 'mon', 3, 443],  // Stretch & mobiliteit

            // Dag 2: Endurance hardlopen opbouw
            [3, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [3, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [3, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec opbouw
            [3, 'tue', 4, 350],  // Cooling down

            // Dag 3: Knieherstel
            [3, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // Dag 4: Ski + strength circuit + finisher
            [3, 'thu', 1, 359],  // Warming up dynamisch
            [3, 'thu', 2, 206],  // SkiErg zone 2 3 kilometer
            [3, 'thu', 3, 332],  // Strength endurance circuit B total body
            [3, 'thu', 4, 436],  // Strength Endurance wall ball squats finisher A
            [3, 'thu', 5, 443],  // Stretch & mobiliteit

            // ==================== WEEK 4 ====================
            // Dag 1: Threshold ski
            [4, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [4, 'mon', 2, 245],  // Threshold ski/row ladder zone 3
            [4, 'mon', 3, 443],  // Stretch & mobiliteit

            // Dag 2: Endurance hardlopen opbouw
            [4, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [4, 'tue', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec opbouw
            [4, 'tue', 3, 428],  // Endurance zone 2 hardlopen 2x10 min opbouw
            [4, 'tue', 4, 350],  // Cooling down

            // Dag 3: Knieherstel variant B
            [4, 'wed', 1, 352],  // Isometrische warming-up + knieherstel Variant B

            // Dag 4: Bike + strength finishers
            [4, 'thu', 1, 454],  // Warming up (vanuit herstel)
            [4, 'thu', 2, 214],  // Endurance zone 2 bike 30 min (PDF: 40 min, closest match)
            [4, 'thu', 3, 439],  // Strength endurance burpees finisher A
            [4, 'thu', 4, 436],  // Strength Endurance wall ball squats finisher A
            [4, 'thu', 5, 443],  // Stretch & mobiliteit

            // ==================== WEEK 5 ====================
            // Dag 1: HIIT EMOM
            [5, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [5, 'mon', 2, 390],  // High-Intensity Interval EMOM 32-40min ergs + bike
            [5, 'mon', 3, 443],  // Stretch & mobiliteit

            // Dag 2: Knieherstel
            [5, 'tue', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // Dag 3: Endurance + threshold hardlopen
            [5, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [5, 'wed', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec opbouw
            [5, 'wed', 3, 431],  // Threshold zone 3 hardlopen 2x10 min opbouw
            [5, 'wed', 4, 350],  // Cooling down

            // Dag 4: Strength chipper + finisher
            [5, 'thu', 1, 359],  // Warming up dynamisch
            [5, 'thu', 2, 318],  // Strength endurance chipper
            [5, 'thu', 3, 339],  // Strength Endurance wall ball finisher A
            [5, 'thu', 4, 350],  // Cooling down

            // ==================== WEEK 6 (Testweek) ====================
            // Dag 1: Cooper test
            [6, 'mon', 1, 176],  // Warming up algemeen
            [6, 'mon', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [6, 'mon', 3, 364],  // 12 min looptest (coopertest)
            [6, 'mon', 4, 350],  // Cooling down

            // Dag 2: Recovery + knieherstel
            [6, 'tue', 1, 182],  // Recovery bike zone 1 30 min
            [6, 'tue', 2, 352],  // Isometrische warming-up + knieherstel Variant B
            [6, 'tue', 3, 361],  // Core Stability 3x20 sec circuit

            // Dag 3: Bike + strength
            [6, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [6, 'wed', 2, 214],  // Endurance zone 2 bike 30 min
            [6, 'wed', 3, 339],  // Strength endurance wall ball finisher A
            [6, 'wed', 4, 443],  // Stretch & mobiliteit

            // Dag 4: Hypertrofie
            [6, 'thu', 1, 359],  // Warming up dynamisch
            [6, 'thu', 2, 340],  // Strength hypertrofie krachtcircuit
            [6, 'thu', 3, 350],  // Cooling down

            // Dag 5: Recovery run + threshold
            [6, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [6, 'fri', 2, 178],  // Recovery hardlopen 30 min (PDF: 20 min, closest match)
            [6, 'fri', 3, 431],  // Threshold zone 3 hardlopen 2x10 min opbouw
            [6, 'fri', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Dag 1: Endurance + threshold hardlopen
            [7, 'mon', 1, 176],  // Warming up algemeen
            [7, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min opbouw
            [7, 'mon', 3, 432],  // Threshold zone 3 hardlopen 3x10 min
            [7, 'mon', 4, 350],  // Cooling down

            // Dag 2: Bike endurance
            [7, 'tue', 1, 407],  // Warming up bike & foam roll
            [7, 'tue', 2, 215],  // Endurance zone 2+ bike 45 min
            [7, 'tue', 3, 350],  // Cooling down

            // Dag 3: Knieherstel + core
            [7, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [7, 'wed', 2, 361],  // Core Stability 3x20 sec circuit

            // Dag 4: Strength sled + wall ball
            [7, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'thu', 2, 441],  // Strength Endurance Sled + Lunges (3-5 rondes)
            [7, 'thu', 3, 331],  // Strength Endurance wall ball sled
            [7, 'thu', 4, 350],  // Cooling down

            // Dag 5: Zone 2 run
            [7, 'fri', 1, 177],  // Warming up hardlopen
            [7, 'fri', 2, 190],  // Zone 2 run 40 min

            // ==================== WEEK 8 ====================
            // Dag 1: Endurance + interval
            [8, 'mon', 1, 176],  // Warming up algemeen
            [8, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min opbouw
            [8, 'mon', 3, 251],  // Interval 8x200
            [8, 'mon', 4, 350],  // Cooling down

            // Dag 2: Ergs endurance
            [8, 'tue', 1, 407],  // Warming up bike & foam roll
            [8, 'tue', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [8, 'tue', 3, 350],  // Cooling down

            // Dag 3: Knieherstel + core
            [8, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [8, 'wed', 2, 361],  // Core Stability 3x20 sec circuit

            // Dag 4: HYROX specific + finisher
            [8, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'thu', 2, 315],  // Strength Endurance hyrox specific 5r 20hh
            [8, 'thu', 3, 419],  // Strength Endurance wall ball finisher B
            [8, 'thu', 4, 350],  // Cooling down

            // Dag 5: Run + row
            [8, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'fri', 2, 190],  // Run zone 2 40 min
            [8, 'fri', 3, 209],  // Endurance zone 2 row (PDF: 5 km, closest generic)
            [8, 'fri', 4, 350],  // Cooling down

            // ==================== WEEK 9 ====================
            // Dag 1: Threshold hardlopen
            [9, 'mon', 1, 177],  // Warming up hardlopen
            [9, 'mon', 2, 249],  // Threshold 4x1200m zone 3
            [9, 'mon', 3, 360],  // Cooling down + stretch cardio

            // Dag 2: Ergs + bike
            [9, 'tue', 1, 407],  // Warming up bike & foam roll
            [9, 'tue', 2, 211],  // Endurance zone 2 assbike 20 min
            [9, 'tue', 3, 214],  // Endurance zone 2 bike 30 min
            [9, 'tue', 4, 360],  // Cooling down + stretch cardio

            // Dag 3: Knieherstel + strength + core
            [9, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [9, 'wed', 2, 338],  // Strength Endurance benen schouder combi
            [9, 'wed', 3, 469],  // Core stability dynamic 3*30/10

            // Dag 4: Endurance ergs + finisher
            [9, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'thu', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'thu', 3, 420],  // Strength Endurance wall ball finisher C
            [9, 'thu', 4, 360],  // Cooling down + stretch cardio

            // Dag 5: Zone 2 run 50 min
            [9, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'fri', 2, 191],  // Run zone 2 50 min
            [9, 'fri', 3, 360],  // Cooling down + stretch cardio

            // ==================== WEEK 10 (HYROX Sim) ====================
            // Dag 1: HYROX Sim
            [10, 'mon', 1, 177],  // Warming up hardlopen
            [10, 'mon', 2, 363],  // HYROX Sim 50%
            [10, 'mon', 3, 360],  // Cooling down + stretch cardio

            // Dag 2: Recovery + knieherstel
            [10, 'tue', 1, 407],  // Warming up bike & foam roll
            [10, 'tue', 2, 184],  // Recovery bike zone 1 60 min
            [10, 'tue', 3, 352],  // Isometrische warming-up + knieherstel Variant B
            [10, 'tue', 4, 443],  // Stretch & mobiliteit

            // Dag 3: HYROX barbell + core
            [10, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'wed', 2, 406],  // Strength Training HYROX Specific barbell full body
            [10, 'wed', 3, 469],  // Core stability dynamic 3*30/10

            // Dag 4: Interval + kracht
            [10, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'thu', 2, 282],  // Interval 5x1200 + kracht
            [10, 'thu', 3, 360],  // Cooling down + stretch cardio

            // Dag 5: Knieherstel
            [10, 'fri', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // ==================== WEEK 11 ====================
            // Dag 1: Interval hardlopen
            [11, 'mon', 1, 177],  // Warming up hardlopen
            [11, 'mon', 2, 412],  // Interval 8x2min zone 4 / 2 min zone 1
            [11, 'mon', 3, 360],  // Cooling down + stretch cardio

            // Dag 2: Comprimised strength endurance
            [11, 'tue', 1, 176],  // Warming up algemeen
            [11, 'tue', 2, 314],  // Comprimised Strength Endurance 15min run + strenght
            [11, 'tue', 3, 360],  // Cooling down + stretch cardio

            // Dag 3: Recovery bike + ski test
            [11, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'wed', 2, 183],  // Recovery bike 40 min
            [11, 'wed', 3, 463],  // Ski test 2 km
            [11, 'wed', 4, 360],  // Cooling down + stretch cardio

            // Dag 4: Comprimised push pull
            [11, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'thu', 2, 310],  // Comprimised Strength endurance 4*10min push pull
            [11, 'thu', 3, 360],  // Cooling down + stretch cardio

            // Dag 5: Recovery run + finisher
            [11, 'fri', 1, 177],  // Warming up hardlopen
            [11, 'fri', 2, 181],  // Recovery run 60 min (PDF: 70 min zone 1, closest match)
            [11, 'fri', 3, 420],  // Strength Endurance wall ball finisher C
            [11, 'fri', 4, 360],  // Cooling down + stretch cardio

            // ==================== WEEK 12 (De-load) ====================
            // Dag 1: Easy ergs + bike
            [12, 'mon', 1, 407],  // Warming up bike & foam roll
            [12, 'mon', 2, 211],  // Endurance zone 2 assbike 20 min
            [12, 'mon', 3, 214],  // Endurance zone 2 bike 30 min
            [12, 'mon', 4, 360],  // Cooling down + stretch cardio

            // Dag 2: Interval
            [12, 'tue', 1, 177],  // Warming up hardlopen
            [12, 'tue', 2, 449],  // Interval 400-100-400-200
            [12, 'tue', 3, 360],  // Cooling down + stretch cardio

            // Dag 3: Knieherstel + core + stretch
            [12, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [12, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            [12, 'wed', 3, 443],  // Stretch & mobiliteit

            // Dag 4: Easy run
            [12, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [12, 'thu', 2, 190],  // Run zone 2 40 min
            [12, 'thu', 3, 350],  // Cooling down

            // Dag 5: Recovery bike + knieherstel + stretch
            [12, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [12, 'fri', 2, 183],  // Recovery bike 40 min (PDF: 50 min, closest match)
            [12, 'fri', 3, 352],  // Isometrische warming-up + knieherstel Variant B
            [12, 'fri', 4, 443],  // Stretch & mobiliteit
        ];

        $rows = [];
        $now = now();

        foreach ($items as [$week, $day, $sortOrder, $cardId]) {
            $rows[] = [
                'template_id' => $template->id,
                'week' => $week,
                'day' => $day,
                'training_card_id' => $cardId,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            TrainingPlanTemplateItem::insert($chunk);
        }

        $this->command->info("Template '{$template->name}' seeded with " . count($rows) . " items.");
    }

    /**
     * Knie-blessure traject — 6 dagen gevuld — 12 weken
     *
     * Gemapped vanuit PDF: "Basis schema 12 weken knie herstel 6 dagen gevuld"
     */
    private function seedKnee6Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'knee_6_days'],
            [
                'name' => 'Knie-blessure traject (6 dagen)',
                'injury_type' => 'knee',
                'max_days' => 6,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        $items = [
            // ==================== WEEK 1 ====================
            // Dag 1: Cardio ergs + stretch
            [1, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [1, 'mon', 2, 202],  // Endurance zone 2 assbike/row/ski 10 min
            [1, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen opbouw
            [1, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [1, 'tue', 2, 433],  // Endurance zone 2 hardlopen 6x60 sec opbouw
            [1, 'tue', 3, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [1, 'tue', 4, 350],  // Cooling down
            // Dag 3: Knieherstel
            [1, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            // Dag 4: Strength upper body
            [1, 'thu', 1, 359],  // Warming up dynamisch
            [1, 'thu', 2, 327],  // Strength endurance upper body
            [1, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery bike
            [1, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [1, 'fri', 2, 183],  // Recovery bike zone 1 40 min
            [1, 'fri', 3, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes + ergs
            [1, 'sat', 1, 354],  // Herstel Glutes & onderrug Variant A
            [1, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [1, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 2 ====================
            // Dag 1: Cardio ergs 15 min
            [2, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [2, 'mon', 2, 203],  // Endurance zone 2 assbike/row/ski 15 min
            [2, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen opbouw
            [2, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [2, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [2, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [2, 'tue', 4, 350],  // Cooling down
            // Dag 3: Knieherstel
            [2, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            // Dag 4: Strength sled + explosief
            [2, 'thu', 1, 359],  // Warming up dynamisch
            [2, 'thu', 2, 370],  // Strength Endurance Farm carry + sled pull
            [2, 'thu', 3, 374],  // Strength Endurance BBJ + push press
            [2, 'thu', 4, 350],  // Cooling down
            // Dag 5: Recovery bike
            [2, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [2, 'fri', 2, 183],  // Recovery bike zone 1 (PDF: 50 min, closest 40 min)
            [2, 'fri', 3, 443],  // Stretch & mobiliteit
            // Dag 6: Knieherstel + ergs
            [2, 'sat', 1, 352],  // Isometrische warming-up + knieherstel Variant B
            [2, 'sat', 2, 206],  // SkiErg 3 km
            [2, 'sat', 3, 208],  // RowErg 3 km
            [2, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 3 ====================
            // Dag 1: Cardio ergs 20 min
            [3, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [3, 'mon', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [3, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [3, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [3, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [3, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [3, 'tue', 4, 350],  // Cooling down
            // Dag 3: Knieherstel
            [3, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            // Dag 4: Strength circuit + finisher
            [3, 'thu', 1, 359],  // Warming up dynamisch
            [3, 'thu', 2, 332],  // Strength endurance circuit B total body
            [3, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [3, 'thu', 4, 443],  // Stretch
            // Dag 5: Bike + burpees finisher
            [3, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [3, 'fri', 2, 214],  // Endurance zone 2 bike 30 min
            [3, 'fri', 3, 440],  // Strength endurance burpees finisher B
            [3, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Knieherstel + ergs
            [3, 'sat', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [3, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [3, 'sat', 3, 208],  // RowErg zone 2 3 km
            [3, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 4 ====================
            // Dag 1: Threshold ski
            [4, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [4, 'mon', 2, 245],  // Threshold ski/row ladder zone 3
            [4, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [4, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [4, 'tue', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [4, 'tue', 3, 428],  // Endurance zone 2 hardlopen 2x10 min
            [4, 'tue', 4, 350],  // Cooling down
            // Dag 3: Knieherstel
            [4, 'wed', 1, 352],  // Isometrische warming-up + knieherstel Variant B
            // Dag 4: Strength circuit A + finisher
            [4, 'thu', 1, 359],  // Warming up dynamisch
            [4, 'thu', 2, 328],  // Strength endurance circuit A total body
            [4, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [4, 'thu', 4, 350],  // Cooling down
            // Dag 5: Bike + burpees
            [4, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [4, 'fri', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [4, 'fri', 3, 439],  // Strength endurance burpees finisher A
            [4, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Knieherstel + ergs
            [4, 'sat', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [4, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [4, 'sat', 3, 208],  // RowErg zone 2 3 km
            [4, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 5 ====================
            // Dag 1: HIIT EMOM
            [5, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [5, 'mon', 2, 390],  // High-Intensity Interval EMOM 32-40min
            [5, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Knieherstel (PDF typo: listed as "Dag 3")
            [5, 'tue', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            // Dag 3: Endurance + threshold hardlopen
            [5, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [5, 'wed', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [5, 'wed', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [5, 'wed', 4, 350],  // Cooling down
            // Dag 4: Strength chipper
            [5, 'thu', 1, 359],  // Warming up dynamisch
            [5, 'thu', 2, 318],  // Strength endurance chipper
            [5, 'thu', 3, 350],  // Cooling down
            // Dag 5: Bike + wall ball
            [5, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [5, 'fri', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [5, 'fri', 3, 339],  // Strength endurance wall ball finisher A
            [5, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Knieherstel + ski
            [5, 'sat', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [5, 'sat', 2, 207],  // SkiErg zone 2 5 km (closest generic)
            [5, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 6 (Testweek) ====================
            // Dag 1: Cooper test
            [6, 'mon', 1, 176],  // Warming up algemeen
            [6, 'mon', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [6, 'mon', 3, 364],  // 12 min looptest (coopertest)
            [6, 'mon', 4, 350],  // Cooling down
            // Dag 2: Recovery + knieherstel
            [6, 'tue', 1, 182],  // Recovery bike zone 1 30 min
            [6, 'tue', 2, 352],  // Isometrische warming-up + knieherstel Variant B
            [6, 'tue', 3, 361],  // Core Stability 3x20 sec circuit
            // Dag 3: Bike + wall ball
            [6, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [6, 'wed', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [6, 'wed', 3, 339],  // Strength endurance wall ball finisher A
            [6, 'wed', 4, 443],  // Stretch & mobiliteit
            // Dag 4: Hypertrofie
            [6, 'thu', 1, 359],  // Warming up dynamisch
            [6, 'thu', 2, 340],  // Strength hypertrofie krachtcircuit
            [6, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery run + threshold
            [6, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [6, 'fri', 2, 178],  // Recover zone 1 hardlopen (PDF: 20 min, closest 30 min)
            [6, 'fri', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [6, 'fri', 4, 350],  // Cooling down
            // Dag 6: Strength + threshold
            [6, 'sat', 1, 359],  // Warming up dynamisch
            [6, 'sat', 2, 329],  // Strength Endurance 5*20 upper body push pull 3
            [6, 'sat', 3, 245],  // Threshold ski/row ladder zone 3
            [6, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Dag 1: Endurance + threshold hardlopen
            [7, 'mon', 1, 176],  // Warming up algemeen
            [7, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [7, 'mon', 3, 432],  // Threshold zone 3 hardlopen 3x10 min
            [7, 'mon', 4, 350],  // Cooling down
            // Dag 2: Bike endurance
            [7, 'tue', 1, 407],  // Warming up bike & foam roll
            [7, 'tue', 2, 215],  // Endurance zone 2+ bike 45 min
            [7, 'tue', 3, 350],  // Cooling down
            // Dag 3: Knieherstel + core
            [7, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [7, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: Strength sled + wall ball
            [7, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'thu', 2, 441],  // Strength Endurance Sled + Lunges
            [7, 'thu', 3, 331],  // Strength Endurance wall ball sled
            [7, 'thu', 4, 350],  // Cooling down
            // Dag 5: Bike + threshold run
            [7, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'fri', 2, 438],  // Endurance zone 2 bike + FC
            [7, 'fri', 3, 431],  // Run zone 3 2x10 min
            [7, 'fri', 4, 350],  // Cooling down
            // Dag 6: Zone 2 run
            [7, 'sat', 1, 177],  // Warming up run
            [7, 'sat', 2, 190],  // Zone 2 run 40 min

            // ==================== WEEK 8 ====================
            // Dag 1: Endurance + interval
            [8, 'mon', 1, 176],  // Warming up algemeen
            [8, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [8, 'mon', 3, 251],  // Interval 8x200
            [8, 'mon', 4, 350],  // Cooling down
            // Dag 2: Ergs endurance
            [8, 'tue', 1, 407],  // Warming up bike & foam roll
            [8, 'tue', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [8, 'tue', 3, 350],  // Cooling down
            // Dag 3: Knieherstel + core
            [8, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [8, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: HYROX specific + finisher
            [8, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'thu', 2, 315],  // Strength Endurance hyrox specific
            [8, 'thu', 3, 419],  // Strength Endurance wall ball finisher B
            [8, 'thu', 4, 350],  // Cooling down
            // Dag 5: Run + row
            [8, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'fri', 2, 190],  // Run zone 2 40 min
            [8, 'fri', 3, 209],  // Endurance zone 2 rowerg 5 km
            [8, 'fri', 4, 350],  // Cooling down
            // Dag 6: Strength + burpees
            [8, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'sat', 2, 309],  // strenght en endurance 4x10 min blok kracht
            [8, 'sat', 3, 465],  // strength burpee madnes
            [8, 'sat', 4, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 9 ====================
            // Dag 1: Threshold hardlopen
            [9, 'mon', 1, 177],  // Warming up hardlopen
            [9, 'mon', 2, 249],  // Threshold 4x1200m zone 3
            [9, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Ergs + bike
            [9, 'tue', 1, 407],  // Warming up bike & foam roll
            [9, 'tue', 2, 211],  // Endurance zone 2 assbike 20 min
            [9, 'tue', 3, 214],  // Endurance zone 2 bike 30 min
            [9, 'tue', 4, 360],  // Cooling Down + stretch cardio
            // Dag 3: Knieherstel + core
            [9, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [9, 'wed', 2, 469],  // core stability dynamic 3*30/10
            // Dag 4: Endurance ergs + finisher
            [9, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'thu', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'thu', 3, 420],  // Strength Endurance wall ball finisher C
            [9, 'thu', 4, 360],  // Cooling Down + stretch cardio
            // Dag 5: Zone 2 run 50 min
            [9, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'fri', 2, 191],  // Run zone 2 50 min
            [9, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Benen + schouder
            [9, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'sat', 2, 338],  // Strength Endurance benen schouder combi
            [9, 'sat', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 10 (HYROX Sim) ====================
            // Dag 1: HYROX Sim
            [10, 'mon', 1, 177],  // Warming up hardlopen
            [10, 'mon', 2, 363],  // HYROX Sim 50%
            [10, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Recovery + knieherstel
            [10, 'tue', 1, 407],  // Warming up bike & foam roll
            [10, 'tue', 2, 184],  // Recovery bike zone 1 60 min
            [10, 'tue', 3, 352],  // Isometrische warming-up + knieherstel Variant B
            [10, 'tue', 4, 443],  // Stretch + mobiliteit
            // Dag 3: HYROX barbell + core
            [10, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'wed', 2, 406],  // Strength Training HYROX Specific barbell full body
            [10, 'wed', 3, 469],  // core stability dynamic 3*30/10
            // Dag 5: Run zone 2 (Dag 4 is rust)
            [10, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'fri', 2, 191],  // Run zone 2 50 min+
            [10, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Interval + kracht
            [10, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'sat', 2, 282],  // Interval 5x1200 +kracht
            [10, 'sat', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 11 ====================
            // Dag 1: Interval hardlopen
            [11, 'mon', 1, 177],  // Warming up hardlopen
            [11, 'mon', 2, 412],  // interval 8x2min zone 4
            [11, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Comprimised strength
            [11, 'tue', 1, 176],  // Warming up algemeen
            [11, 'tue', 2, 314],  // Comprimised Strength Endurance 15min run strenght
            [11, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Recovery bike + ski test
            [11, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'wed', 2, 183],  // Recovery bike 40 min
            [11, 'wed', 3, 463],  // Ski test 2 km
            [11, 'wed', 4, 360],  // Cooling Down + stretch cardio
            // Dag 4: Comprimised push pull
            [11, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'thu', 2, 310],  // Comprimised Strength endurance 4*10min push pull
            [11, 'thu', 3, 360],  // Cooling Down + stretch cardio
            // Dag 5: Long run zone 1
            [11, 'fri', 1, 177],  // Warming up hardlopen
            [11, 'fri', 2, 181],  // Run zone 1 70 min (closest: 60 min)
            [11, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Full body
            [11, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'sat', 2, 402],  // Strength Endurance Full body
            [11, 'sat', 3, 360],  // Cooling Down + stretch cardio
            // Dag 7: Knieherstel
            [11, 'sun', 1, 351],  // Isometrische warming-up + knieherstel Variant A

            // ==================== WEEK 12 (De-load) ====================
            // Dag 1: Easy ergs + bike
            [12, 'mon', 1, 407],  // Warming up bike & foam roll
            [12, 'mon', 2, 211],  // Endurance zone 2 assbike 20 min
            [12, 'mon', 3, 214],  // Endurance zone 2 bike 30 min
            [12, 'mon', 4, 360],  // Cooling Down + stretch cardio
            // Dag 2: Interval
            [12, 'tue', 1, 177],  // Warming up hardlopen
            [12, 'tue', 2, 449],  // interval 400-100-400-200
            [12, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Knieherstel + core + stretch
            [12, 'wed', 1, 351],  // Isometrische warming-up + knieherstel Variant A
            [12, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            [12, 'wed', 3, 443],  // Stretch + mobiliteit
            // Dag 4: Easy run
            [12, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [12, 'thu', 2, 190],  // Run zone 2 40 min
            [12, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery bike + knieherstel
            [12, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [12, 'fri', 2, 183],  // Recovery bike (PDF: 50 min, closest 40 min)
            [12, 'fri', 3, 352],  // Isometrische warming-up + knieherstel Variant B
            [12, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Stretch
            [12, 'sat', 1, 443],  // Stretch & mobiliteit
        ];

        $rows = [];
        $now = now();

        foreach ($items as [$week, $day, $sortOrder, $cardId]) {
            $rows[] = [
                'template_id' => $template->id,
                'week' => $week,
                'day' => $day,
                'training_card_id' => $cardId,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            TrainingPlanTemplateItem::insert($chunk);
        }

        $this->command->info("Template '{$template->name}' seeded with " . count($rows) . " items.");
    }

    /**
     * Herstel/opbouw traject — 4-5 dagen gevuld — 12 weken
     *
     * Gemapped vanuit PDF: "Opbouw Basis schema 12 weken vanuit herstel/opbouw 4-5 dagen gevuld"
     * Verschil met knie-template: "Herstel knie/heup/rug" (353) i.p.v. knieherstel cards (351/352)
     */
    private function seedGeneral45Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'general_4_5_days'],
            [
                'name' => 'Herstel/opbouw traject (4-5 dagen)',
                'injury_type' => null,
                'max_days' => 5,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        $items = [
            // ==================== WEEK 1 ====================
            // Dag 1: Cardio ergs + stretch
            [1, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [1, 'mon', 2, 202],  // Endurance zone 2 assbike/row/ski 10 min
            [1, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen opbouw
            [1, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [1, 'tue', 2, 433],  // Endurance zone 2 hardlopen 6x60 sec opbouw
            [1, 'tue', 3, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [1, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel
            [1, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 4: Strength upper body
            [1, 'thu', 1, 359],  // Warming up dynamisch
            [1, 'thu', 2, 327],  // Strength endurance upper body
            [1, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery bike
            [1, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [1, 'fri', 2, 183],  // Recovery bike zone 1 40 min
            [1, 'fri', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 2 ====================
            // Dag 1: Cardio ergs 15 min
            [2, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [2, 'mon', 2, 203],  // Endurance zone 2 assbike/row/ski 15 min
            [2, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [2, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [2, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [2, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [2, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel + recovery bike
            [2, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [2, 'wed', 2, 183],  // Recovery bike zone 1 (PDF: 50 min, closest 40 min)
            [2, 'wed', 3, 443],  // Stretch & mobiliteit
            // Dag 4: Strength sled + explosief
            [2, 'thu', 1, 359],  // Warming up dynamisch
            [2, 'thu', 2, 370],  // Strength Endurance Farm carry + sled pull
            [2, 'thu', 3, 374],  // Strength Endurance BBJ + push press
            [2, 'thu', 4, 350],  // Cooling down

            // ==================== WEEK 3 ====================
            // Dag 1: Cardio ergs 20 min
            [3, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [3, 'mon', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [3, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [3, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [3, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [3, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [3, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel + bike
            [3, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [3, 'wed', 2, 214],  // Endurance zone 2 bike 30 min
            [3, 'wed', 3, 443],  // Stretch & mobiliteit
            // Dag 4: Strength circuit + finisher
            [3, 'thu', 1, 359],  // Warming up dynamisch
            [3, 'thu', 2, 332],  // Strength endurance circuit B total body
            [3, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [3, 'thu', 4, 443],  // Stretch

            // ==================== WEEK 4 ====================
            // Dag 1: Threshold ski
            [4, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [4, 'mon', 2, 245],  // Threshold ski/row ladder zone 3
            [4, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen + row
            [4, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [4, 'tue', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [4, 'tue', 3, 428],  // Endurance zone 2 hardlopen 2x10 min
            [4, 'tue', 4, 208],  // RowErg zone 2 3 km
            [4, 'tue', 5, 350],  // Cooling down
            // Dag 3: Herstel + bike
            [4, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [4, 'wed', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [4, 'wed', 3, 443],  // Stretch & mobiliteit
            // Dag 4: Strength circuit A + finisher
            [4, 'thu', 1, 359],  // Warming up dynamisch
            [4, 'thu', 2, 328],  // Strength endurance circuit A total body
            [4, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [4, 'thu', 4, 350],  // Cooling down
            [4, 'thu', 5, 443],  // Stretch & mobiliteit

            // ==================== WEEK 5 ====================
            // Dag 1: HIIT EMOM
            [5, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [5, 'mon', 2, 390],  // High-Intensity Interval EMOM 32-40min
            [5, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Herstel + ski
            [5, 'tue', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [5, 'tue', 2, 207],  // SkiErg zone 2 5 km (closest generic)
            [5, 'tue', 3, 443],  // Stretch & mobiliteit
            // Dag 3: Endurance + threshold hardlopen
            [5, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [5, 'wed', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [5, 'wed', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [5, 'wed', 4, 350],  // Cooling down
            // Dag 4: Strength chipper
            [5, 'thu', 1, 359],  // Warming up dynamisch
            [5, 'thu', 2, 318],  // Strength endurance chipper
            [5, 'thu', 3, 350],  // Cooling down
            // Dag 5: Bike + wall ball
            [5, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [5, 'fri', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [5, 'fri', 3, 339],  // Strength endurance wall ball finisher A
            [5, 'fri', 4, 443],  // Stretch & mobiliteit

            // ==================== WEEK 6 (Testweek) ====================
            // Dag 1: Cooper test
            [6, 'mon', 1, 176],  // Warming up algemeen
            [6, 'mon', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [6, 'mon', 3, 364],  // 12 min looptest (coopertest)
            [6, 'mon', 4, 350],  // Cooling down
            // Dag 2: Recovery + herstel + strength
            [6, 'tue', 1, 437],  // Recovery bike 20 min
            [6, 'tue', 2, 353],  // Piriformis (heup) herstel & mobiliteit
            [6, 'tue', 3, 329],  // Strength Endurance 5*20 upper body push pull 3
            [6, 'tue', 4, 361],  // Core Stability 3x20 sec circuit
            // Dag 3: Bike + wall ball
            [6, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [6, 'wed', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [6, 'wed', 3, 339],  // Strength endurance wall ball finisher A
            [6, 'wed', 4, 443],  // Stretch & mobiliteit
            // Dag 4: Hypertrofie
            [6, 'thu', 1, 359],  // Warming up dynamisch
            [6, 'thu', 2, 340],  // Strength hypertrofie krachtcircuit
            [6, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery run + threshold
            [6, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [6, 'fri', 2, 178],  // Recover zone 1 hardlopen (PDF: 20 min, closest 30 min)
            [6, 'fri', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [6, 'fri', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Dag 1: Endurance + threshold hardlopen
            [7, 'mon', 1, 176],  // Warming up algemeen
            [7, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [7, 'mon', 3, 432],  // Threshold zone 3 hardlopen 3x10 min
            [7, 'mon', 4, 350],  // Cooling down
            // Dag 2: Bike endurance
            [7, 'tue', 1, 407],  // Warming up bike & foam roll
            [7, 'tue', 2, 215],  // Endurance zone 2+ bike 45 min
            [7, 'tue', 3, 350],  // Cooling down
            // Dag 3: Herstel + bike + core
            [7, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [7, 'wed', 2, 438],  // Endurance zone 2 bike + FC
            [7, 'wed', 3, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: Strength sled + wall ball
            [7, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'thu', 2, 441],  // Strength Endurance Sled + Lunges
            [7, 'thu', 3, 331],  // Strength Endurance wall ball sled
            [7, 'thu', 4, 350],  // Cooling down
            // Dag 5: Run + threshold
            [7, 'fri', 1, 176],  // Warming up algemeen
            [7, 'fri', 2, 190],  // Zone 2 run 40 min
            [7, 'fri', 3, 431],  // Run zone 3 2x10 min
            [7, 'fri', 4, 350],  // Cooling down

            // ==================== WEEK 8 ====================
            // Dag 1: Endurance + interval
            [8, 'mon', 1, 176],  // Warming up algemeen
            [8, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [8, 'mon', 3, 251],  // Interval 8x200
            [8, 'mon', 4, 350],  // Cooling down
            // Dag 2: Ergs endurance
            [8, 'tue', 1, 407],  // Warming up bike & foam roll
            [8, 'tue', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [8, 'tue', 3, 350],  // Cooling down
            // Dag 3: Herstel + strength + core
            [8, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [8, 'wed', 2, 309],  // strenght en endurance 4x10 min blok kracht
            [8, 'wed', 3, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: HYROX specific + finisher
            [8, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'thu', 2, 315],  // Strength Endurance hyrox specific
            [8, 'thu', 3, 419],  // Strength Endurance wall ball finisher B
            [8, 'thu', 4, 350],  // Cooling down
            // Dag 5: Run + row
            [8, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'fri', 2, 190],  // Run zone 2 40 min
            [8, 'fri', 3, 209],  // Endurance zone 2 rowerg 5 km
            [8, 'fri', 4, 350],  // Cooling down

            // ==================== WEEK 9 ====================
            // Dag 1: Threshold hardlopen
            [9, 'mon', 1, 177],  // Warming up hardlopen
            [9, 'mon', 2, 249],  // Threshold 4x1200m zone 3
            [9, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Ergs + bike
            [9, 'tue', 1, 407],  // Warming up bike & foam roll
            [9, 'tue', 2, 211],  // Endurance zone 2 assbike 20 min
            [9, 'tue', 3, 214],  // Endurance zone 2 bike 30 min
            [9, 'tue', 4, 360],  // Cooling Down + stretch cardio
            // Dag 3: Herstel + strength + core
            [9, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [9, 'wed', 2, 338],  // Strength Endurance benen schouder combi
            [9, 'wed', 3, 469],  // core stability dynamic 3*30/10
            // Dag 4: Endurance ergs + finisher
            [9, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'thu', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'thu', 3, 420],  // Strength Endurance wall ball finisher C
            [9, 'thu', 4, 360],  // Cooling Down + stretch cardio
            // Dag 5: Zone 2 run 50 min
            [9, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'fri', 2, 191],  // Run zone 2 50 min
            [9, 'fri', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 10 (HYROX Sim) ====================
            // Dag 1: HYROX Sim
            [10, 'mon', 1, 177],  // Warming up hardlopen
            [10, 'mon', 2, 363],  // HYROX Sim 50%
            [10, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Recovery run
            [10, 'tue', 1, 407],  // Warming up bike & foam roll
            [10, 'tue', 2, 181],  // Recovery run zone 1 60 min
            [10, 'tue', 3, 443],  // Stretch + mobiliteit
            // Dag 3: HYROX barbell + core
            [10, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'wed', 2, 406],  // Strength Training HYROX Specific barbell full body
            [10, 'wed', 3, 469],  // core stability dynamic 3*30/10
            // Dag 5: Interval + kracht (Dag 4 is rust)
            [10, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'fri', 2, 282],  // Interval 5x1200 +kracht
            [10, 'fri', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 11 ====================
            // Dag 1: Interval hardlopen
            [11, 'mon', 1, 177],  // Warming up hardlopen
            [11, 'mon', 2, 412],  // interval 8x2min zone 4
            [11, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Comprimised strength
            [11, 'tue', 1, 176],  // Warming up algemeen
            [11, 'tue', 2, 314],  // Comprimised Strength Endurance 15min run strenght
            [11, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Recovery bike + ski test
            [11, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'wed', 2, 183],  // Recovery bike 40 min
            [11, 'wed', 3, 463],  // Ski test 2 km
            [11, 'wed', 4, 360],  // Cooling Down + stretch cardio
            // Dag 4: Comprimised push pull
            [11, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'thu', 2, 310],  // Comprimised Strength endurance 4*10min push pull
            [11, 'thu', 3, 360],  // Cooling Down + stretch cardio
            // Dag 5: Long run zone 2
            [11, 'fri', 1, 177],  // Warming up hardlopen
            [11, 'fri', 2, 193],  // Run zone 2 70 min
            [11, 'fri', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 12 (De-load) ====================
            // Dag 1: Easy ergs + bike
            [12, 'mon', 1, 407],  // Warming up bike & foam roll
            [12, 'mon', 2, 211],  // Endurance zone 2 assbike 20 min
            [12, 'mon', 3, 214],  // Endurance zone 2 bike 30 min
            [12, 'mon', 4, 360],  // Cooling Down + stretch cardio
            // Dag 2: Interval
            [12, 'tue', 1, 177],  // Warming up hardlopen
            [12, 'tue', 2, 449],  // interval 400-100-400-200
            [12, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Herstel + recovery bike + core + stretch
            [12, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [12, 'wed', 2, 183],  // Recovery bike (PDF: 50 min, closest 40 min)
            [12, 'wed', 3, 361],  // Core Stability 3x20 sec circuit
            [12, 'wed', 4, 443],  // Stretch + mobiliteit
            // Dag 4: Easy run
            [12, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [12, 'thu', 2, 190],  // Run zone 2 40 min
            [12, 'thu', 3, 350],  // Cooling down
            // Dag 5: Stretch
            [12, 'fri', 1, 443],  // Stretch & mobiliteit
        ];

        $rows = [];
        $now = now();

        foreach ($items as [$week, $day, $sortOrder, $cardId]) {
            $rows[] = [
                'template_id' => $template->id,
                'week' => $week,
                'day' => $day,
                'training_card_id' => $cardId,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            TrainingPlanTemplateItem::insert($chunk);
        }

        $this->command->info("Template '{$template->name}' seeded with " . count($rows) . " items.");
    }

    /**
     * Herstel/opbouw traject — 6 dagen gevuld — 12 weken
     *
     * Gemapped vanuit PDF: "Opbouw Basis schema 12 weken 6 dagen gevuld"
     * Verschil met knie-6-dagen: "Herstel knie/heup/rug" (353) i.p.v. knieherstel cards (351/352)
     */
    private function seedGeneral6Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'general_6_days'],
            [
                'name' => 'Herstel/opbouw traject (6 dagen)',
                'injury_type' => null,
                'max_days' => 6,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        $items = [
            // ==================== WEEK 1 ====================
            // Dag 1: Cardio ergs + stretch
            [1, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [1, 'mon', 2, 202],  // Endurance zone 2 assbike/row/ski 10 min
            [1, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen opbouw
            [1, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [1, 'tue', 2, 433],  // Endurance zone 2 hardlopen 6x60 sec opbouw
            [1, 'tue', 3, 434],  // Endurance zone 2 hardlopen 10x60 sec opbouw
            [1, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel
            [1, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 4: Strength upper body
            [1, 'thu', 1, 359],  // Warming up dynamisch
            [1, 'thu', 2, 327],  // Strength endurance upper body
            [1, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery bike
            [1, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [1, 'fri', 2, 183],  // Recovery bike zone 1 40 min
            [1, 'fri', 3, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes + ski
            [1, 'sat', 1, 354],  // Herstel Glutes & onderrug Variant A
            [1, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [1, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 2 ====================
            // Dag 1: Cardio ergs 15 min
            [2, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [2, 'mon', 2, 203],  // Endurance zone 2 assbike/row/ski 15 min
            [2, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [2, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [2, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [2, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [2, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel
            [2, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 4: Strength sled + explosief
            [2, 'thu', 1, 359],  // Warming up dynamisch
            [2, 'thu', 2, 370],  // Strength Endurance Farm carry + sled pull
            [2, 'thu', 3, 374],  // Strength Endurance BBJ + push press
            [2, 'thu', 4, 350],  // Cooling down
            // Dag 5: Recovery bike
            [2, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [2, 'fri', 2, 183],  // Recovery bike (PDF: 50 min, closest 40 min)
            [2, 'fri', 3, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes + ergs
            [2, 'sat', 1, 354],  // Herstel Glutes & onderrug Variant A
            [2, 'sat', 2, 206],  // SkiErg 3 km
            [2, 'sat', 3, 208],  // RowErg 3 km
            [2, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 3 ====================
            // Dag 1: Cardio ergs 20 min
            [3, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [3, 'mon', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [3, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [3, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [3, 'tue', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [3, 'tue', 3, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [3, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel
            [3, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 4: Strength circuit + finisher
            [3, 'thu', 1, 359],  // Warming up dynamisch
            [3, 'thu', 2, 332],  // Strength endurance circuit B total body
            [3, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [3, 'thu', 4, 443],  // Stretch
            // Dag 5: Bike + burpees finisher
            [3, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [3, 'fri', 2, 214],  // Endurance zone 2 bike 30 min
            [3, 'fri', 3, 440],  // Strength endurance burpees finisher B
            [3, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes B + ergs
            [3, 'sat', 1, 355],  // Herstel Glutes & onderrug Variant B
            [3, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [3, 'sat', 3, 208],  // RowErg zone 2 3 km
            [3, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 4 ====================
            // Dag 1: Threshold ski
            [4, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [4, 'mon', 2, 245],  // Threshold ski/row ladder zone 3
            [4, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Endurance hardlopen
            [4, 'tue', 1, 454],  // Warming up (vanuit herstel)
            [4, 'tue', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [4, 'tue', 3, 428],  // Endurance zone 2 hardlopen 2x10 min
            [4, 'tue', 4, 350],  // Cooling down
            // Dag 3: Herstel
            [4, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 4: Strength circuit A + finisher
            [4, 'thu', 1, 359],  // Warming up dynamisch
            [4, 'thu', 2, 328],  // Strength endurance circuit A total body
            [4, 'thu', 3, 436],  // Strength Endurance wall ball squats finisher A
            [4, 'thu', 4, 350],  // Cooling down
            // Dag 5: Bike + burpees
            [4, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [4, 'fri', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [4, 'fri', 3, 439],  // Strength endurance burpees finisher A
            [4, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes B + ergs
            [4, 'sat', 1, 355],  // Herstel Glutes & onderrug Variant B
            [4, 'sat', 2, 206],  // SkiErg zone 2 3 km
            [4, 'sat', 3, 208],  // RowErg zone 2 3 km
            [4, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 5 ====================
            // Dag 1: HIIT EMOM
            [5, 'mon', 1, 454],  // Warming up (vanuit herstel)
            [5, 'mon', 2, 390],  // High-Intensity Interval EMOM 32-40min
            [5, 'mon', 3, 443],  // Stretch & mobiliteit
            // Dag 2: Herstel
            [5, 'tue', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            // Dag 3: Endurance + threshold hardlopen
            [5, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [5, 'wed', 2, 435],  // Endurance zone 2 hardlopen 8x90 sec
            [5, 'wed', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [5, 'wed', 4, 350],  // Cooling down
            // Dag 4: Strength chipper
            [5, 'thu', 1, 359],  // Warming up dynamisch
            [5, 'thu', 2, 318],  // Strength endurance chipper
            [5, 'thu', 3, 350],  // Cooling down
            // Dag 5: Bike + wall ball
            [5, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [5, 'fri', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [5, 'fri', 3, 339],  // Strength endurance wall ball finisher A
            [5, 'fri', 4, 443],  // Stretch & mobiliteit
            // Dag 6: Glutes A + ski
            [5, 'sat', 1, 354],  // Herstel Glutes & onderrug Variant A
            [5, 'sat', 2, 207],  // SkiErg zone 2 5 km (closest generic)
            [5, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 6 (Testweek) ====================
            // Dag 1: Cooper test
            [6, 'mon', 1, 176],  // Warming up algemeen
            [6, 'mon', 2, 434],  // Endurance zone 2 hardlopen 10x60 sec
            [6, 'mon', 3, 364],  // 12 min looptest (coopertest)
            [6, 'mon', 4, 350],  // Cooling down
            // Dag 2: Recovery + herstel
            [6, 'tue', 1, 182],  // Recovery bike zone 1 30 min
            [6, 'tue', 2, 353],  // Piriformis (heup) herstel & mobiliteit
            [6, 'tue', 3, 361],  // Core Stability 3x20 sec circuit
            // Dag 3: Bike + wall ball
            [6, 'wed', 1, 454],  // Warming up (vanuit herstel)
            [6, 'wed', 2, 214],  // Endurance zone 2 bike (PDF: 40 min, closest 30 min)
            [6, 'wed', 3, 339],  // Strength endurance wall ball finisher A
            [6, 'wed', 4, 443],  // Stretch & mobiliteit
            // Dag 4: Hypertrofie
            [6, 'thu', 1, 359],  // Warming up dynamisch
            [6, 'thu', 2, 340],  // Strength hypertrofie krachtcircuit
            [6, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery run + threshold
            [6, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [6, 'fri', 2, 178],  // Recover zone 1 hardlopen (PDF: 20 min, closest 30 min)
            [6, 'fri', 3, 431],  // Threshold zone 3 hardlopen 2x10 min
            [6, 'fri', 4, 350],  // Cooling down
            // Dag 6: Strength + threshold
            [6, 'sat', 1, 359],  // Warming up dynamisch
            [6, 'sat', 2, 329],  // Strength Endurance 5*20 upper body push pull 3
            [6, 'sat', 3, 245],  // Threshold ski/row ladder zone 3
            [6, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Dag 1: Endurance + threshold hardlopen
            [7, 'mon', 1, 176],  // Warming up algemeen
            [7, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [7, 'mon', 3, 432],  // Threshold zone 3 hardlopen 3x10 min
            [7, 'mon', 4, 350],  // Cooling down
            // Dag 2: Bike endurance
            [7, 'tue', 1, 407],  // Warming up bike & foam roll
            [7, 'tue', 2, 215],  // Endurance zone 2+ bike 45 min
            [7, 'tue', 3, 350],  // Cooling down
            // Dag 3: Herstel + core
            [7, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [7, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: Strength sled + wall ball
            [7, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'thu', 2, 441],  // Strength Endurance Sled + Lunges
            [7, 'thu', 3, 331],  // Strength Endurance wall ball sled
            [7, 'thu', 4, 350],  // Cooling down
            // Dag 5: Bike + threshold run
            [7, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [7, 'fri', 2, 438],  // Endurance zone 2 bike + FC
            [7, 'fri', 3, 431],  // Run zone 3 2x10 min
            [7, 'fri', 4, 350],  // Cooling down
            // Dag 6: Zone 2 run
            [7, 'sat', 1, 177],  // Warming up run
            [7, 'sat', 2, 190],  // Zone 2 run 40 min

            // ==================== WEEK 8 ====================
            // Dag 1: Endurance + interval
            [8, 'mon', 1, 176],  // Warming up algemeen
            [8, 'mon', 2, 428],  // Endurance zone 2 hardlopen 2x10 min
            [8, 'mon', 3, 251],  // Interval 8x200
            [8, 'mon', 4, 350],  // Cooling down
            // Dag 2: Ergs endurance
            [8, 'tue', 1, 407],  // Warming up bike & foam roll
            [8, 'tue', 2, 204],  // Endurance zone 2 assbike/row/ski 20 min
            [8, 'tue', 3, 350],  // Cooling down
            // Dag 3: Herstel + core
            [8, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [8, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            // Dag 4: HYROX specific + finisher
            [8, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'thu', 2, 315],  // Strength Endurance hyrox specific
            [8, 'thu', 3, 419],  // Strength Endurance wall ball finisher B
            [8, 'thu', 4, 350],  // Cooling down
            // Dag 5: Run + row
            [8, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'fri', 2, 190],  // Run zone 2 40 min
            [8, 'fri', 3, 209],  // Endurance zone 2 rowerg 5 km
            [8, 'fri', 4, 350],  // Cooling down
            // Dag 6: Strength + burpees
            [8, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [8, 'sat', 2, 309],  // strenght en endurance 4x10 min blok kracht
            [8, 'sat', 3, 465],  // strength burpee madnes
            [8, 'sat', 4, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 9 ====================
            // Dag 1: Threshold hardlopen
            [9, 'mon', 1, 177],  // Warming up hardlopen
            [9, 'mon', 2, 249],  // Threshold 4x1200m zone 3
            [9, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Ergs + bike
            [9, 'tue', 1, 407],  // Warming up bike & foam roll
            [9, 'tue', 2, 211],  // Endurance zone 2 assbike 20 min
            [9, 'tue', 3, 214],  // Endurance zone 2 bike 30 min
            [9, 'tue', 4, 360],  // Cooling Down + stretch cardio
            // Dag 3: Herstel + core
            [9, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [9, 'wed', 2, 469],  // core stability dynamic 3*30/10
            // Dag 4: Endurance ergs + finisher
            [9, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'thu', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'thu', 3, 420],  // Strength Endurance wall ball finisher C
            [9, 'thu', 4, 360],  // Cooling Down + stretch cardio
            // Dag 5: Zone 2 run 50 min
            [9, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'fri', 2, 191],  // Run zone 2 50 min
            [9, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Benen + schouder
            [9, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [9, 'sat', 2, 338],  // Strength Endurance benen schouder combi
            [9, 'sat', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 10 (HYROX Sim) ====================
            // Dag 1: HYROX Sim
            [10, 'mon', 1, 177],  // Warming up hardlopen
            [10, 'mon', 2, 363],  // HYROX Sim 50%
            [10, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Recovery bike
            [10, 'tue', 1, 407],  // Warming up bike & foam roll
            [10, 'tue', 2, 184],  // Recovery bike zone 1 60 min
            [10, 'tue', 3, 443],  // Stretch + mobiliteit
            // Dag 3: HYROX barbell + core
            [10, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'wed', 2, 406],  // Strength Training HYROX Specific barbell full body
            [10, 'wed', 3, 469],  // core stability dynamic 3*30/10
            // Dag 5: Run zone 2 (Dag 4 is rust)
            [10, 'fri', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'fri', 2, 191],  // Run zone 2 50 min+
            [10, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Interval + kracht
            [10, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [10, 'sat', 2, 282],  // Interval 5x1200 +kracht
            [10, 'sat', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 11 ====================
            // Dag 1: Interval hardlopen
            [11, 'mon', 1, 177],  // Warming up hardlopen
            [11, 'mon', 2, 412],  // interval 8x2min zone 4
            [11, 'mon', 3, 360],  // Cooling Down + stretch cardio
            // Dag 2: Comprimised strength
            [11, 'tue', 1, 176],  // Warming up algemeen
            [11, 'tue', 2, 314],  // Comprimised Strength Endurance 15min run strenght
            [11, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Recovery bike + ski test
            [11, 'wed', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'wed', 2, 183],  // Recovery bike 40 min
            [11, 'wed', 3, 463],  // Ski test 2 km
            [11, 'wed', 4, 360],  // Cooling Down + stretch cardio
            // Dag 4: Comprimised push pull
            [11, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'thu', 2, 310],  // Comprimised Strength endurance 4*10min push pull
            [11, 'thu', 3, 360],  // Cooling Down + stretch cardio
            // Dag 5: Long run zone 1
            [11, 'fri', 1, 177],  // Warming up hardlopen
            [11, 'fri', 2, 181],  // Run zone 1 70 min (closest: 60 min)
            [11, 'fri', 3, 360],  // Cooling Down + stretch cardio
            // Dag 6: Full body
            [11, 'sat', 1, 359],  // Warming up 5 min cardio + dynamic
            [11, 'sat', 2, 402],  // Strength Endurance Full body
            [11, 'sat', 3, 360],  // Cooling Down + stretch cardio

            // ==================== WEEK 12 (De-load) ====================
            // Dag 1: Easy ergs + bike
            [12, 'mon', 1, 407],  // Warming up bike & foam roll
            [12, 'mon', 2, 211],  // Endurance zone 2 assbike 20 min
            [12, 'mon', 3, 214],  // Endurance zone 2 bike 30 min
            [12, 'mon', 4, 360],  // Cooling Down + stretch cardio
            // Dag 2: Interval
            [12, 'tue', 1, 177],  // Warming up hardlopen
            [12, 'tue', 2, 449],  // interval 400-100-400-200
            [12, 'tue', 3, 360],  // Cooling Down + stretch cardio
            // Dag 3: Herstel + core + stretch
            [12, 'wed', 1, 353],  // Piriformis (heup) herstel & mobiliteit
            [12, 'wed', 2, 361],  // Core Stability 3x20 sec circuit
            [12, 'wed', 3, 443],  // Stretch + mobiliteit
            // Dag 4: Easy run
            [12, 'thu', 1, 359],  // Warming up 5 min cardio + dynamic
            [12, 'thu', 2, 190],  // Run zone 2 40 min
            [12, 'thu', 3, 350],  // Cooling down
            // Dag 5: Recovery bike + stretch
            [12, 'fri', 1, 454],  // Warming up (vanuit herstel)
            [12, 'fri', 2, 183],  // Recovery bike (PDF: 50 min, closest 40 min)
            [12, 'fri', 3, 443],  // Stretch & mobiliteit
            // Dag 6: Stretch
            [12, 'sat', 1, 443],  // Stretch & mobiliteit
        ];

        $rows = [];
        $now = now();

        foreach ($items as [$week, $day, $sortOrder, $cardId]) {
            $rows[] = [
                'template_id' => $template->id,
                'week' => $week,
                'day' => $day,
                'training_card_id' => $cardId,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            TrainingPlanTemplateItem::insert($chunk);
        }

        $this->command->info("Template '{$template->name}' seeded with " . count($rows) . " items.");
    }
}
