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
        $this->seedGevorderd4Days();
        $this->seedGevorderd6Days();
        $this->seedExpert4Days();
        $this->seedExpert6Days();
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
                'level' => 'beginner',
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
                'level' => 'beginner',
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
                'level' => 'beginner',
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
                'level' => 'beginner',
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

    /**
     * Gevorderd — 4 dagen per week — 12 weken
     *
     * Gemapped vanuit PDF 9 linkerkolom: "basis 12 weken 4 keer per week — Gevorderde"
     * Card IDs verwijzen naar training_cards in de database.
     *
     * Mapping formaat: [week, day, sort_order, training_card_id]
     */
    private function seedGevorderd4Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'gevorderd_4_days'],
            [
                'name' => 'Gevorderd traject (4 dagen)',
                'level' => 'gevorderd',
                'injury_type' => null,
                'max_days' => 4,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        // Card ID referenties:
        // 176 = Warming-up Algemeen
        // 177 = Warming-up Hardlopen / Cardio
        // 182 = Recovery 30 min bike
        // 190 = Endurance 40 min zone 2
        // 191 = Endurance 50 min zone 2
        // 193 = Endurance 70 min zone 2
        // 196 = Endurance 40 min zone 2+
        // 197 = Endurance 50 min zone 2+
        // 198 = Endurance 60 min zone 2+
        // 199 = Endurance 70 min zone 2+
        // 195 = Endurance 30 min zone 2+
        // 203 = Endurance zone 2 assbike/row/ski 15
        // 205 = Endurance zone 2+ ski/row/assbike 10min
        // 214 = Endurance zone 2 bike 30min
        // 215 = Endurance zone 2+ bike 45min
        // 232 = Threshold zone 3 run 4x2km + wallball
        // 226 = Threshold zone 3 test loop 5km
        // 241 = Treshold zone 3 run 5x1200m + kracht (closest to 6x1200m)
        // 245 = Treshold ski/row ladder
        // 246 = Treshold ski/row/assbike aflopend
        // 247 = Treshold ski/row ladder sled push pull
        // 252 = Interval 12x200
        // 258 = Interval 10x 400
        // 261 = Interval 10x600
        // 265 = Interval 10x600 kracht
        // 269 = Interval 8x800
        // 275 = Interval 6x1000
        // 279 = Interval 5x1200
        // 286 = Interval 200/400/600
        // 288 = Interval roei 250
        // 289 = Interval roei 500
        // 290 = Interval roei 1000
        // 292 = Interval ski 250
        // 293 = Interval ski 500
        // 294 = Interval ski 1000
        // 300 = Interval e2mom run/roei.ski/assbike
        // 306 = hyrox specific emom 56min
        // 311 = Comprimised Endurance e2mom run + benen
        // 312 = Comprimised Endurance e2mom ergs + upper body
        // 313 = Comprimised Endurance ski/row/assbike ladder + strength
        // 315 = Strength Endurance hyrox specific 5r 20hh
        // 317 = Strength Endurance ladder 50>10 +
        // 319 = Strength Endurance chipper slee 120m
        // 320 = Strength Endurance chipper 100/90/80/70/60/50/60/70/80/90/100
        // 321 = Strength Endurance upper body 20hh
        // 322 = Strength Endurance sled 20m oplopend
        // 323 = Strength Endurance sled 5*15m
        // 326 = Strength Endurance 5*25 upper body 2
        // 327 = Strength Endurance 5*20 upper body push pull
        // 330 = Strength Endurance push pull sled 1
        // 336 = Snelheid heuvel sprint 12x20s
        // 340 = Strength hypertrofie ladder
        // 348 = Core Stability
        // 349 = Core
        // 350 = Cooling Down
        // 358 = Warming-up Ski/Row/Bike + Mobility + Sprints
        // 359 = Warming-up 5 min cardio + dynamic
        // 360 = Cooling Down Cardio + stretch
        // 361 = Core Stability 3x20 sec circuit
        // 362 = Core Stability 2x30 sec circuit
        // 363 = HYROX Sim 50%
        // 369 = Strength Endurance Thruster + sled push (4-6 rds ASAP)
        // 370 = Strength Endurance Farm carry + sled pull (4-6 rds ASAP)
        // 371 = Strength Endurance Lunges + BBJ (4-6 rds ASAP)
        // 381 = Comprimised Endurance run/ski/wallball ladder
        // 382 = Comprimised Endurance run/row/FC/BBJ ladder
        // 390 = High-Intensity Interval EMOM 32-40min ergs + bike
        // 407 = Warming-up bike & foam roll
        // 408 = Endurance Training zone 2 (ergs, sled, run)
        // 416 = interval 5x 5 min zone 4 / 2 min zone 1
        // 417 = interval pyramide
        // 423 = comprimised endurance run-cardio 1500 en strenght 5x30 200m
        // 426 = comprimised endurance run-cardio 1500 en strenght 5x30
        // 443 = Stretch & mobiliteit
        // 445 = Treshold 4x 8 min (+10% sneller dan race pace)
        // 451 = Strength / power jump squat (3 sets)
        // 457 = strenght endurance push pull upperbody 1
        // 458 = strenght endurance push pull upperbody 2
        // 459 = strength endurance push pull lower body 1
        // 460 = strength endurance push pull lower body 2
        // 465 = strength burpee madnes
        // 467 = comprimised endurance e2mom hyrox specific
        // 468 = comprimised strength 500m 5x30/15 tr hyrox specific
        // 469 = core stability dynamic 3*30/10
        // 470 = core stability 3*30/10 statisch

        $items = [
            // ==================== WEEK 1 ====================
            // Ma: Interval + core (zelfde voor beide niveaus)
            [1, 'mon', 1, 176],  // Warming up algemeen
            [1, 'mon', 2, 252],  // Interval 12x200
            [1, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [1, 'mon', 4, 350],  // Cooling down

            // Wo: Endurance ergs + strength endurance upper body
            [1, 'wed', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [1, 'wed', 2, 205],  // Endurance zone 2+ ski/row/assbike 10min
            [1, 'wed', 3, 321],  // Strength Endurance upper body 20hh
            [1, 'wed', 4, 360],  // Cooling down cardio + stretch

            // Vr: Endurance run + core
            [1, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [1, 'fri', 2, 190],  // Endurance 40 min zone 2
            [1, 'fri', 3, 348],  // Core stability
            [1, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Comprimised endurance dubbel
            [1, 'sat', 1, 176],  // Warming up algemeen
            [1, 'sat', 2, 381],  // Comprimised endurance run/ski/wallball ladder
            [1, 'sat', 3, 382],  // Comprimised endurance run/row/FC/BBJ ladder
            [1, 'sat', 4, 443],  // Stretch & mobiliteit

            // ==================== WEEK 2 ====================
            // Ma: Interval 10x400 + core (zelfde voor beide niveaus)
            [2, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [2, 'mon', 2, 258],  // Interval 10x 400
            [2, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [2, 'mon', 4, 350],  // Cooling down

            // Wo: Interval ergs + SE hyrox specific
            [2, 'wed', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [2, 'wed', 2, 292],  // Interval ski 250
            [2, 'wed', 3, 288],  // Interval roei 250
            [2, 'wed', 4, 315],  // Strength Endurance hyrox specific 5r 20hh
            [2, 'wed', 5, 350],  // Cooling down

            // Vr: Endurance zone 2+ + core
            [2, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [2, 'fri', 2, 196],  // Endurance 40 min zone 2+
            [2, 'fri', 3, 348],  // Core stability
            [2, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Hyrox specific emom
            [2, 'sat', 1, 176],  // Warming up algemeen
            [2, 'sat', 2, 306],  // Hyrox specific emom 56min
            [2, 'sat', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 3 ====================
            // Ma: Interval 10x600 + core (zelfde voor beide niveaus)
            [3, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [3, 'mon', 2, 261],  // Interval 10x600
            [3, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [3, 'mon', 4, 350],  // Cooling down

            // Di: Threshold + SE upper body
            [3, 'tue', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [3, 'tue', 2, 246],  // Treshold ski/row/assbike aflopend
            [3, 'tue', 3, 326],  // Strength Endurance 5*25 upper body
            [3, 'tue', 4, 350],  // Cooling down

            // Vr: Endurance 50 min zone 2 + core
            [3, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [3, 'fri', 2, 191],  // Endurance 50 min zone 2
            [3, 'fri', 3, 348],  // Core stability
            [3, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Comprimised strength + endurance bike
            [3, 'sat', 1, 176],  // Warming up algemeen
            [3, 'sat', 2, 468],  // Comprimised strength 500m 5x30/15 hyrox specific
            [3, 'sat', 3, 215],  // Endurance zone 2+ bike 45min
            [3, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 4 ====================
            // Ma: Interval 200/400/600 + core dynamisch (zelfde voor beide niveaus)
            [4, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [4, 'mon', 2, 286],  // Interval 200/400/600
            [4, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [4, 'mon', 4, 350],  // Cooling down

            // Wo: Comprimised endurance run-cardio
            [4, 'wed', 1, 176],  // Warming up algemeen
            [4, 'wed', 2, 426],  // Comprimised endurance run-cardio 1500 en strenght 5x30
            [4, 'wed', 3, 443],  // Stretch & mobiliteit

            // Vr: Interval ergs + SE push pull upper body
            [4, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [4, 'fri', 2, 289],  // Interval roei 500
            [4, 'fri', 3, 293],  // Interval ski 500
            [4, 'fri', 4, 457],  // Strength endurance push pull upperbody 1
            [4, 'fri', 5, 458],  // Strength endurance push pull upperbody 2
            [4, 'fri', 6, 350],  // Cooling down

            // Za: SE chipper + recovery bike
            [4, 'sat', 1, 176],  // Warming up algemeen
            [4, 'sat', 2, 320],  // Strength Endurance chipper 100/90/80/70/60/50
            [4, 'sat', 3, 182],  // Recovery 30 min bike
            [4, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 5 ====================
            // Ma: Interval 8x800 + core dynamisch (zelfde voor beide niveaus)
            [5, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [5, 'mon', 2, 269],  // Interval 8x800
            [5, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [5, 'mon', 4, 350],  // Cooling down

            // Di: Treshold sled + SE push pull lower body
            [5, 'tue', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [5, 'tue', 2, 247],  // Treshold ski/row ladder sled push pull
            [5, 'tue', 3, 459],  // Strength endurance push pull lower body 1
            [5, 'tue', 4, 460],  // Strength endurance push pull lower body 2
            [5, 'tue', 5, 443],  // Stretch & mobiliteit

            // Do: Strength power + treshold run
            [5, 'thu', 1, 176],  // Warming up algemeen
            [5, 'thu', 2, 451],  // Strength / power jump squat (3 sets)
            [5, 'thu', 3, 241],  // Treshold zone 3 run 5x1200m + kracht (closest to 6x1200m)
            [5, 'thu', 4, 443],  // Stretch & mobiliteit

            // Vr: Endurance zone 2+ + core
            [5, 'fri', 1, 407],  // Warming-up bike & foam roll
            [5, 'fri', 2, 198],  // Endurance 60 min zone 2+
            [5, 'fri', 3, 470],  // Core stability 3*30/10 statisch

            // Za: Comprimised strength hyrox specific
            [5, 'sat', 1, 176],  // Warming up algemeen
            [5, 'sat', 2, 468],  // Comprimised strength 500m 5x30/15 hyrox specific
            [5, 'sat', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 6 ====================
            // Ma: Interval 6x1000 + core dynamisch
            [6, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [6, 'mon', 2, 275],  // Interval 6x1000
            [6, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [6, 'mon', 4, 350],  // Cooling down

            // Di: Endurance ergs + strength sled
            [6, 'tue', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [6, 'tue', 2, 203],  // Endurance zone 2 assbike/row/ski 15
            [6, 'tue', 3, 322],  // Strength Endurance sled 20m oplopend
            [6, 'tue', 4, 443],  // Stretch & mobiliteit

            // Vr: Strength hypertrofie + sled
            [6, 'fri', 1, 176],  // Warming up algemeen
            [6, 'fri', 2, 340],  // Strength hypertrofie ladder
            [6, 'fri', 3, 323],  // Strength Endurance sled 5*15m
            [6, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Endurance 70 min zone 2+ + core
            [6, 'sat', 1, 177],  // Warming-up Hardlopen / Cardio
            [6, 'sat', 2, 199],  // Endurance 70 min zone 2+
            [6, 'sat', 3, 349],  // Core
            [6, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Ma: Interval pyramide + core (zelfde voor beide niveaus)
            [7, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [7, 'mon', 2, 417],  // Interval pyramide
            [7, 'mon', 3, 349],  // Core
            [7, 'mon', 4, 350],  // Cooling down

            // Wo: SE ladder
            [7, 'wed', 1, 176],  // Warming up algemeen
            [7, 'wed', 2, 317],  // Strength Endurance ladder 50>10 +
            [7, 'wed', 3, 443],  // Stretch & mobiliteit

            // Vr: HIIT EMOM + SE sled
            [7, 'fri', 1, 359],  // Warming-up 5 min cardio + dynamic
            [7, 'fri', 2, 390],  // High-Intensity Interval EMOM 32-40min ergs + bike
            [7, 'fri', 3, 330],  // Strength Endurance push pull sled 1
            [7, 'fri', 4, 350],  // Cooling down

            // Za: Endurance training zone 2
            [7, 'sat', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [7, 'sat', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [7, 'sat', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 8 ====================
            // Ma: Interval 5x5min zone 4 + core dynamisch
            [8, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [8, 'mon', 2, 416],  // Interval 5x 5 min zone 4 / 2 min zone 1
            [8, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [8, 'mon', 4, 350],  // Cooling down

            // Di: Treshold sled + SE push pull
            [8, 'tue', 1, 359],  // Warming-up 5 min cardio + dynamic
            [8, 'tue', 2, 247],  // Treshold ski/row ladder sled push pull
            [8, 'tue', 3, 459],  // Strength endurance push pull lower body 1
            [8, 'tue', 4, 458],  // Strength endurance push pull upperbody 2
            [8, 'tue', 5, 360],  // Cooling down cardio + stretch

            // Do: Comprimised endurance dubbel
            [8, 'thu', 1, 176],  // Warming up algemeen
            [8, 'thu', 2, 311],  // Comprimised Endurance e2mom run + benen
            [8, 'thu', 3, 312],  // Comprimised Endurance e2mom ergs + upper body
            [8, 'thu', 4, 350],  // Cooling down

            // Vr: Endurance zone 2+ + bike + core
            [8, 'fri', 1, 359],  // Warming-up 5 min cardio + dynamic
            [8, 'fri', 2, 198],  // Endurance 60 min zone 2+
            [8, 'fri', 3, 214],  // Endurance zone 2 bike 30min
            [8, 'fri', 4, 469],  // Core stability dynamic 3*30/10
            [8, 'fri', 5, 360],  // Cooling down cardio + stretch

            // ==================== WEEK 9 ====================
            // Ma: Interval 5x1200 + core dynamisch
            [9, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [9, 'mon', 2, 279],  // Interval 5x1200
            [9, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [9, 'mon', 4, 350],  // Cooling down

            // Di: Endurance training zone 2
            [9, 'tue', 1, 176],  // Warming up algemeen
            [9, 'tue', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'tue', 3, 443],  // Stretch & mobiliteit

            // Wo: Threshold run 4x2km + wallball
            [9, 'wed', 1, 176],  // Warming up algemeen
            [9, 'wed', 2, 232],  // Threshold zone 3 run 4x2km + wallball
            [9, 'wed', 3, 360],  // Cooling down cardio + stretch

            // Vr: Comprimised endurance hyrox specific + core
            [9, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [9, 'fri', 2, 467],  // Comprimised endurance e2mom hyrox specific
            [9, 'fri', 3, 349],  // Core
            [9, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: SE chipper slee
            [9, 'sat', 1, 359],  // Warming-up 5 min cardio + dynamic
            [9, 'sat', 2, 319],  // Strength Endurance chipper slee 120m
            [9, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 10 ====================
            // Ma: Interval 10x600 kracht + core dynamisch
            [10, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [10, 'mon', 2, 265],  // Interval 10x600 kracht
            [10, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [10, 'mon', 4, 350],  // Cooling down

            // Wo: Strength burpee + SE push pull
            [10, 'wed', 1, 176],  // Warming up algemeen
            [10, 'wed', 2, 465],  // Strength burpee madnes
            [10, 'wed', 3, 459],  // Strength endurance push pull lower body 1
            [10, 'wed', 4, 458],  // Strength endurance push pull upperbody 2
            [10, 'wed', 5, 350],  // Cooling down

            // Do: Endurance 70 min zone 2+ + core
            [10, 'thu', 1, 177],  // Warming-up Hardlopen / Cardio
            [10, 'thu', 2, 199],  // Endurance 70 min zone 2+
            [10, 'thu', 3, 362],  // Core Stability 2x30 sec circuit
            [10, 'thu', 4, 350],  // Cooling down

            // Za: HYROX Sim 50% + recovery bike
            [10, 'sat', 1, 176],  // Warming up algemeen
            [10, 'sat', 2, 363],  // HYROX Sim 50%
            [10, 'sat', 3, 182],  // Recovery 30 min bike
            [10, 'sat', 4, 443],  // Stretch & mobiliteit

            // ==================== WEEK 11 ====================
            // Ma: Treshold race pace + core dynamisch
            [11, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [11, 'mon', 2, 445],  // Treshold 4x 8 min (+10% sneller dan race pace)
            [11, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [11, 'mon', 4, 350],  // Cooling down

            // Wo: Comprimised endurance ladder + strength
            [11, 'wed', 1, 359],  // Warming-up 5 min cardio + dynamic
            [11, 'wed', 2, 313],  // Comprimised Endurance ski/row/assbike ladder + strength
            [11, 'wed', 3, 350],  // Cooling down

            // Do: Comprimised endurance run-cardio 200m
            [11, 'thu', 1, 176],  // Warming up algemeen
            [11, 'thu', 2, 423],  // Comprimised endurance run-cardio 1500 en strenght 5x30 200m
            [11, 'thu', 3, 443],  // Stretch & mobiliteit

            // Za: Endurance 70 min zone 2+ + core dynamisch
            [11, 'sat', 1, 177],  // Warming-up Hardlopen / Cardio
            [11, 'sat', 2, 199],  // Endurance 70 min zone 2+
            [11, 'sat', 3, 469],  // Core stability dynamic 3*30/10
            [11, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 12 ====================
            // Ma: Threshold test loop 5km + core dynamisch
            [12, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [12, 'mon', 2, 226],  // Threshold zone 3 test loop 5km
            [12, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [12, 'mon', 4, 350],  // Cooling down

            // Di: Endurance ergs + SE Thruster/Farm carry/Lunges
            [12, 'tue', 1, 359],  // Warming-up 5 min cardio + dynamic
            [12, 'tue', 2, 205],  // Endurance zone 2+ ski/row/assbike 10min
            [12, 'tue', 3, 369],  // Strength Endurance Thruster + sled push (4-6 rds)
            [12, 'tue', 4, 370],  // Strength Endurance Farm carry + sled pull (4-6 rds)
            [12, 'tue', 5, 371],  // Strength Endurance Lunges + BBJ (4-6 rds)
            [12, 'tue', 6, 350],  // Cooling down

            // Do: Endurance 60 min zone 2+ + core
            [12, 'thu', 1, 177],  // Warming-up Hardlopen / Cardio
            [12, 'thu', 2, 198],  // Endurance 60 min zone 2+
            [12, 'thu', 3, 361],  // Core Stability 3x20 sec circuit
            [12, 'thu', 4, 443],  // Stretch & mobiliteit

            // Vr: Interval ergs + endurance
            [12, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [12, 'fri', 2, 290],  // Interval roei 1000
            [12, 'fri', 3, 294],  // Interval ski 1000
            [12, 'fri', 4, 195],  // Endurance 30 min zone 2+
            [12, 'fri', 5, 360],  // Cooling down cardio + stretch

            // Za: SE chipper + snelheid heuvel sprint
            [12, 'sat', 1, 359],  // Warming-up 5 min cardio + dynamic
            [12, 'sat', 2, 320],  // Strength Endurance chipper 100/90/80/70/60/50/60/70/80/90/100
            [12, 'sat', 3, 336],  // Snelheid heuvel sprint 12x20s
            [12, 'sat', 4, 443],  // Stretch & mobiliteit
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
     * Gevorderd — 6 dagen per week — 12 weken
     *
     * Gemapped vanuit PDF 11/12 linkerkolom: "basis 12 weken 6 keer per week — Gevorderd"
     * Card IDs verwijzen naar training_cards in de database.
     *
     * Mapping formaat: [week, day, sort_order, training_card_id]
     */
    private function seedGevorderd6Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'gevorderd_6_days'],
            [
                'name' => 'Gevorderd traject (6 dagen)',
                'level' => 'gevorderd',
                'injury_type' => null,
                'max_days' => 6,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        $items = [

            // ==================== WEEK 1 ====================
            [1, 'mon', 1, 176],
            [1, 'mon', 2, 252],
            [1, 'mon', 3, 470],
            [1, 'mon', 4, 350],
            [1, 'tue', 1, 358],
            [1, 'tue', 2, 205],
            [1, 'tue', 3, 321],
            [1, 'tue', 4, 360],
            [1, 'wed', 1, 177],
            [1, 'wed', 2, 224],
            [1, 'wed', 3, 389],
            [1, 'thu', 1, 358],
            [1, 'thu', 2, 292],
            [1, 'thu', 3, 288],
            [1, 'thu', 4, 323],
            [1, 'thu', 5, 360],
            [1, 'fri', 1, 177],
            [1, 'fri', 2, 190],
            [1, 'fri', 3, 348],
            [1, 'fri', 4, 443],
            [1, 'sat', 1, 176],
            [1, 'sat', 2, 381],
            [1, 'sat', 3, 382],
            [1, 'sat', 4, 443],

            // ==================== WEEK 2 ====================
            [2, 'mon', 1, 177],
            [2, 'mon', 2, 258],
            [2, 'mon', 3, 470],
            [2, 'mon', 4, 350],
            [2, 'tue', 1, 358],
            [2, 'tue', 2, 203],
            [2, 'tue', 3, 321],
            [2, 'tue', 4, 350],
            [2, 'wed', 1, 176],
            [2, 'wed', 2, 315],
            [2, 'wed', 3, 182],
            [2, 'wed', 4, 350],
            [2, 'thu', 1, 358],
            [2, 'thu', 2, 245],
            [2, 'thu', 3, 322],
            [2, 'thu', 4, 360],
            [2, 'fri', 1, 177],
            [2, 'fri', 2, 196],
            [2, 'fri', 3, 348],
            [2, 'fri', 4, 443],
            [2, 'sat', 1, 176],
            [2, 'sat', 2, 306],
            [2, 'sat', 3, 443],

            // ==================== WEEK 3 ====================
            [3, 'mon', 1, 177],
            [3, 'mon', 2, 261],
            [3, 'mon', 3, 470],
            [3, 'mon', 4, 350],
            [3, 'tue', 1, 358],
            [3, 'tue', 2, 246],
            [3, 'tue', 3, 326],
            [3, 'tue', 4, 350],
            [3, 'wed', 1, 176],
            [3, 'wed', 2, 320],
            [3, 'wed', 3, 182],
            [3, 'wed', 4, 350],
            [3, 'thu', 1, 358],
            [3, 'thu', 2, 205],
            [3, 'thu', 3, 329],
            [3, 'thu', 4, 360],
            [3, 'fri', 1, 177],
            [3, 'fri', 2, 191],
            [3, 'fri', 3, 348],
            [3, 'fri', 4, 443],
            [3, 'sat', 1, 176],
            [3, 'sat', 2, 468],
            [3, 'sat', 3, 215],
            [3, 'sat', 4, 350],

            // ==================== WEEK 4 ====================
            [4, 'mon', 1, 177],
            [4, 'mon', 2, 286],
            [4, 'mon', 3, 469],
            [4, 'mon', 4, 350],
            [4, 'tue', 1, 358],
            [4, 'tue', 2, 203],
            [4, 'tue', 3, 329],
            [4, 'tue', 4, 350],
            [4, 'wed', 1, 176],
            [4, 'wed', 2, 426],
            [4, 'wed', 3, 443],
            [4, 'thu', 1, 407],
            [4, 'thu', 2, 212],
            [4, 'thu', 3, 469],
            [4, 'thu', 4, 350],
            [4, 'fri', 1, 358],
            [4, 'fri', 2, 289],
            [4, 'fri', 3, 293],
            [4, 'fri', 4, 457],
            [4, 'fri', 5, 458],
            [4, 'fri', 6, 350],
            [4, 'sat', 1, 176],
            [4, 'sat', 2, 314],
            [4, 'sat', 3, 443],

            // ==================== WEEK 5 ====================
            [5, 'mon', 1, 177],
            [5, 'mon', 2, 269],
            [5, 'mon', 3, 469],
            [5, 'mon', 4, 350],
            [5, 'tue', 1, 358],
            [5, 'tue', 2, 247],
            [5, 'tue', 3, 459],
            [5, 'tue', 4, 460],
            [5, 'tue', 5, 443],
            [5, 'wed', 1, 407],
            [5, 'wed', 2, 216],
            [5, 'wed', 3, 470],
            [5, 'thu', 1, 176],
            [5, 'thu', 2, 241],
            [5, 'thu', 3, 443],
            [5, 'fri', 1, 358],
            [5, 'fri', 2, 205],
            [5, 'fri', 3, 327],
            [5, 'fri', 4, 350],
            [5, 'sat', 1, 176],
            [5, 'sat', 2, 426],
            [5, 'sat', 3, 443],

            // ==================== WEEK 6 ====================
            [6, 'mon', 1, 177],
            [6, 'mon', 2, 275],
            [6, 'mon', 3, 469],
            [6, 'mon', 4, 350],
            [6, 'tue', 1, 358],
            [6, 'tue', 2, 203],
            [6, 'tue', 3, 322],
            [6, 'tue', 4, 443],
            [6, 'wed', 1, 177],
            [6, 'wed', 2, 240],
            [6, 'wed', 3, 443],
            [6, 'thu', 1, 407],
            [6, 'thu', 2, 216],
            [6, 'thu', 3, 350],
            [6, 'fri', 1, 176],
            [6, 'fri', 2, 340],
            [6, 'fri', 3, 323],
            [6, 'fri', 4, 443],
            [6, 'sat', 1, 177],
            [6, 'sat', 2, 198],
            [6, 'sat', 3, 349],
            [6, 'sat', 4, 350],

            // ==================== WEEK 7 ====================
            [7, 'mon', 1, 177],
            [7, 'mon', 2, 417],
            [7, 'mon', 3, 349],
            [7, 'mon', 4, 350],
            [7, 'tue', 1, 358],
            [7, 'tue', 2, 408],
            [7, 'tue', 3, 443],
            [7, 'wed', 1, 176],
            [7, 'wed', 2, 317],
            [7, 'wed', 3, 443],
            [7, 'thu', 1, 407],
            [7, 'thu', 2, 217],
            [7, 'thu', 3, 362],
            [7, 'thu', 4, 350],
            [7, 'fri', 1, 359],
            [7, 'fri', 2, 390],
            [7, 'fri', 3, 330],
            [7, 'fri', 4, 350],
            [7, 'sat', 1, 177],
            [7, 'sat', 2, 314],
            [7, 'sat', 3, 349],
            [7, 'sat', 4, 443],

            // ==================== WEEK 8 ====================
            [8, 'mon', 1, 177],
            [8, 'mon', 2, 416],
            [8, 'mon', 3, 469],
            [8, 'mon', 4, 350],
            [8, 'tue', 1, 359],
            [8, 'tue', 2, 247],
            [8, 'tue', 3, 459],
            [8, 'tue', 4, 458],
            [8, 'tue', 5, 360],
            [8, 'wed', 1, 217],
            [8, 'thu', 1, 176],
            [8, 'thu', 2, 311],
            [8, 'thu', 3, 312],
            [8, 'thu', 4, 350],
            [8, 'fri', 1, 359],
            [8, 'fri', 2, 178],
            [8, 'fri', 3, 214],
            [8, 'fri', 4, 361],
            [8, 'fri', 5, 350],
            [8, 'sat', 1, 176],
            [8, 'sat', 2, 318],
            [8, 'sat', 3, 182],
            [8, 'sat', 4, 350],

            // ==================== WEEK 9 ====================
            [9, 'mon', 1, 177],
            [9, 'mon', 2, 279],
            [9, 'mon', 3, 469],
            [9, 'mon', 4, 350],
            [9, 'tue', 1, 176],
            [9, 'tue', 2, 408],
            [9, 'tue', 3, 443],
            [9, 'wed', 1, 176],
            [9, 'wed', 2, 232],
            [9, 'wed', 3, 360],
            [9, 'thu', 1, 407],
            [9, 'thu', 2, 213],
            [9, 'thu', 3, 362],
            [9, 'thu', 4, 350],
            [9, 'fri', 1, 358],
            [9, 'fri', 2, 467],
            [9, 'fri', 3, 349],
            [9, 'fri', 4, 443],
            [9, 'sat', 1, 359],
            [9, 'sat', 2, 319],
            [9, 'sat', 3, 350],

            // ==================== WEEK 10 ====================
            [10, 'mon', 1, 177],
            [10, 'mon', 2, 265],
            [10, 'mon', 3, 469],
            [10, 'mon', 4, 350],
            [10, 'tue', 1, 218],
            [10, 'tue', 2, 361],
            [10, 'tue', 3, 350],
            [10, 'wed', 1, 176],
            [10, 'wed', 2, 465],
            [10, 'wed', 3, 459],
            [10, 'wed', 4, 458],
            [10, 'wed', 5, 350],
            [10, 'thu', 1, 177],
            [10, 'thu', 2, 199],
            [10, 'thu', 3, 362],
            [10, 'thu', 4, 350],
            [10, 'sat', 1, 176],
            [10, 'sat', 2, 363],
            [10, 'sat', 3, 182],
            [10, 'sat', 4, 443],

            // ==================== WEEK 11 ====================
            [11, 'mon', 1, 177],
            [11, 'mon', 2, 445],
            [11, 'mon', 3, 469],
            [11, 'mon', 4, 350],
            [11, 'tue', 1, 359],
            [11, 'tue', 2, 313],
            [11, 'tue', 3, 350],
            [11, 'wed', 1, 176],
            [11, 'wed', 2, 218],
            [11, 'wed', 3, 350],
            [11, 'thu', 1, 176],
            [11, 'thu', 2, 423],
            [11, 'thu', 3, 443],
            [11, 'fri', 1, 358],
            [11, 'fri', 2, 300],
            [11, 'fri', 3, 376],
            [11, 'fri', 4, 350],
            [11, 'sat', 1, 177],
            [11, 'sat', 2, 199],
            [11, 'sat', 3, 469],
            [11, 'sat', 4, 350],

            // ==================== WEEK 12 ====================
            [12, 'mon', 1, 177],
            [12, 'mon', 2, 226],
            [12, 'mon', 3, 469],
            [12, 'mon', 4, 350],
            [12, 'tue', 1, 359],
            [12, 'tue', 2, 205],
            [12, 'tue', 3, 369],
            [12, 'tue', 4, 370],
            [12, 'tue', 5, 371],
            [12, 'tue', 6, 350],
            [12, 'wed', 1, 177],
            [12, 'wed', 2, 198],
            [12, 'wed', 3, 361],
            [12, 'wed', 4, 443],
            [12, 'thu', 1, 359],
            [12, 'thu', 2, 317],
            [12, 'thu', 3, 360],
            [12, 'fri', 1, 358],
            [12, 'fri', 2, 290],
            [12, 'fri', 3, 294],
            [12, 'fri', 4, 195],
            [12, 'fri', 5, 360],
            [12, 'sat', 1, 359],
            [12, 'sat', 2, 320],
            [12, 'sat', 3, 336],
            [12, 'sat', 4, 443],
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
     * Expert — 4 dagen per week — 12 weken
     *
     * Gemapped vanuit PDF 9 rechterkolom: "basis 12 weken 4 keer per week — Expert"
     * Card IDs verwijzen naar training_cards in de database.
     *
     * Mapping formaat: [week, day, sort_order, training_card_id]
     */
    private function seedExpert4Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'expert_4_days'],
            [
                'name' => 'Expert traject (4 dagen)',
                'level' => 'expert',
                'injury_type' => null,
                'max_days' => 4,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        // Card ID referenties:
        // 176 = Warming-up Algemeen
        // 177 = Warming-up Hardlopen / Cardio
        // 182 = Recovery 30 min bike
        // 192 = Endurance 60 min zone 2
        // 191 = Endurance 50 min zone 2
        // 193 = Endurance 70 min zone 2
        // 197 = Endurance 50 min zone 2+
        // 198 = Endurance 60 min zone 2+
        // 199 = Endurance 70 min zone 2+
        // 195 = Endurance 30 min zone 2+
        // 205 = Endurance zone 2+ ski/row/assbike 10min
        // 214 = Endurance zone 2 bike 30min
        // 215 = Endurance zone 2+ bike 45min
        // 232 = Threshold zone 3 run 4x2km + wallball
        // 226 = Threshold zone 3 test loop 5km
        // 234 = Threshold zone 3 run 5x2km + wallball
        // 241 = Treshold zone 3 run 5x1200m + kracht
        // 245 = Treshold ski/row ladder
        // 246 = Treshold ski/row/assbike aflopend
        // 247 = Treshold ski/row ladder sled push pull
        // 252 = Interval 12x200
        // 258 = Interval 10x 400
        // 261 = Interval 10x600
        // 265 = Interval 10x600 kracht
        // 269 = Interval 8x800
        // 275 = Interval 6x1000
        // 279 = Interval 5x1200
        // 280 = Interval 6x1200
        // 286 = Interval 200/400/600
        // 288 = Interval roei 250
        // 289 = Interval roei 500
        // 290 = Interval roei 1000
        // 292 = Interval ski 250
        // 293 = Interval ski 500
        // 294 = Interval ski 1000
        // 311 = Comprimised Endurance e2mom run + benen
        // 312 = Comprimised Endurance e2mom ergs + upper body
        // 313 = Comprimised Endurance ski/row/assbike ladder + strength
        // 314 = Comprimised Strength Endurance 15min run strenght 15min run
        // 315 = Strength Endurance hyrox specific 5r 20hh
        // 317 = Strength Endurance ladder 50>10 +
        // 319 = Strength Endurance chipper slee 120m
        // 320 = Strength Endurance chipper 100/90/80/70/60/50/60/70/80/90/100
        // 321 = Strength Endurance upper body 20hh
        // 322 = Strength Endurance sled 20m oplopend
        // 323 = Strength Endurance sled 5*15m
        // 326 = Strength Endurance 5*25 upper body 2
        // 330 = Strength Endurance push pull sled 1
        // 336 = Snelheid heuvel sprint 12x20s
        // 340 = Strength hypertrofie ladder
        // 344 = Strength seated box jump / db snatch
        // 347 = Strength slamball / power push up
        // 348 = Core Stability
        // 349 = Core
        // 350 = Cooling Down
        // 358 = Warming-up Ski/Row/Bike + Mobility + Sprints
        // 359 = Warming-up 5 min cardio + dynamic
        // 360 = Cooling Down Cardio + stretch
        // 361 = Core Stability 3x20 sec circuit
        // 362 = Core Stability 2x30 sec circuit
        // 363 = HYROX Sim 50%
        // 369 = Strength Endurance Thruster + sled push (4-6 rds ASAP)
        // 370 = Strength Endurance Farm carry + sled pull (4-6 rds ASAP)
        // 371 = Strength Endurance Lunges + BBJ (4-6 rds ASAP)
        // 376 = Strength Endurance Wallball + sled + FC (5-8 rds)
        // 381 = Comprimised Endurance run/ski/wallball ladder
        // 382 = Comprimised Endurance run/row/FC/BBJ ladder
        // 383 = Comprimised Endurance 7x10min run + max station
        // 384 = Strength Power single leg box push-off jump
        // 385 = Strength Power one leg box jump
        // 386 = Strength Power broad jump met arm swing
        // 387 = Strength Power DB/KB clean into push press
        // 389 = Comprimised Strength Endurance sandbag/plate/BB (5-8 rds)
        // 390 = High-Intensity Interval EMOM 32-40min ergs + bike
        // 407 = Warming-up bike & foam roll
        // 408 = Endurance Training zone 2 (ergs, sled, run)
        // 416 = interval 5x 5 min zone 4 / 2 min zone 1
        // 417 = interval pyramide
        // 423 = comprimised endurance run-cardio 1500 en strenght 5x30 200m
        // 443 = Stretch & mobiliteit
        // 445 = Treshold 4x 8 min (+10% sneller dan race pace)
        // 450 = Strength / power jump lunge (3 sets per been)
        // 451 = Strength / power jump squat (3 sets)
        // 457 = strenght endurance push pull upperbody 1
        // 458 = strenght endurance push pull upperbody 2
        // 459 = strength endurance push pull lower body 1
        // 460 = strength endurance push pull lower body 2
        // 465 = strength burpee madnes
        // 467 = comprimised endurance e2mom hyrox specific
        // 468 = comprimised strength 500m 5x30/15 tr hyrox specific
        // 469 = core stability dynamic 3*30/10
        // 470 = core stability 3*30/10 statisch

        $items = [
            // ==================== WEEK 1 ====================
            // Ma: Interval + core (zelfde voor beide niveaus)
            [1, 'mon', 1, 176],  // Warming up algemeen
            [1, 'mon', 2, 252],  // Interval 12x200
            [1, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [1, 'mon', 4, 350],  // Cooling down

            // Wo: Strength power + interval ergs + SE sled
            [1, 'wed', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [1, 'wed', 2, 344],  // Strength seated box jump / db snatch
            [1, 'wed', 3, 292],  // Interval ski 250
            [1, 'wed', 4, 288],  // Interval roei 250
            [1, 'wed', 5, 323],  // Strength Endurance sled 5*15m
            [1, 'wed', 6, 360],  // Cooling down cardio + stretch

            // Vr: Endurance 50 min zone 2 + core
            [1, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [1, 'fri', 2, 191],  // Endurance 50 min zone 2
            [1, 'fri', 3, 348],  // Core stability
            [1, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Comprimised endurance dubbel
            [1, 'sat', 1, 176],  // Warming up algemeen
            [1, 'sat', 2, 381],  // Comprimised endurance run/ski/wallball ladder
            [1, 'sat', 3, 382],  // Comprimised endurance run/row/FC/BBJ ladder
            [1, 'sat', 4, 443],  // Stretch & mobiliteit

            // ==================== WEEK 2 ====================
            // Ma: Interval 10x400 + core (zelfde voor beide niveaus)
            [2, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [2, 'mon', 2, 258],  // Interval 10x 400
            [2, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [2, 'mon', 4, 350],  // Cooling down

            // Wo: Threshold + strength sled
            [2, 'wed', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [2, 'wed', 2, 245],  // Treshold ski/row ladder
            [2, 'wed', 3, 322],  // Strength Endurance sled 20m oplopend
            [2, 'wed', 4, 360],  // Cooling down cardio + stretch

            // Vr: Endurance 50 min zone 2+ + power + SE hyrox
            [2, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [2, 'fri', 2, 197],  // Endurance 50 min zone 2+
            [2, 'fri', 3, 451],  // Strength / power jump squat (3 sets)
            [2, 'fri', 4, 450],  // Strength / power jump lunge (3 sets per been)
            [2, 'fri', 5, 315],  // Strength Endurance hyrox specific 5r 20hh
            [2, 'fri', 6, 443],  // Stretch & mobiliteit

            // Za: Strength power + comprimised endurance hyrox
            [2, 'sat', 1, 176],  // Warming up algemeen
            [2, 'sat', 2, 384],  // Strength Power single leg box push-off jump
            [2, 'sat', 3, 385],  // Strength Power one leg box jump
            [2, 'sat', 4, 467],  // Comprimised endurance e2mom hyrox specific
            [2, 'sat', 5, 443],  // Stretch & mobiliteit

            // ==================== WEEK 3 ====================
            // Ma: Interval 10x600 + core (zelfde voor beide niveaus)
            [3, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [3, 'mon', 2, 261],  // Interval 10x600
            [3, 'mon', 3, 470],  // Core stability 3*30/10 statisch
            [3, 'mon', 4, 350],  // Cooling down

            // Di: Strength slamball + threshold + SE upper body
            [3, 'tue', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [3, 'tue', 2, 347],  // Strength slamball / power push up
            [3, 'tue', 3, 246],  // Treshold ski/row/assbike aflopend
            [3, 'tue', 4, 326],  // Strength Endurance 5*25 upper body
            [3, 'tue', 5, 350],  // Cooling down

            // Vr: Endurance 60 min zone 2 + core
            [3, 'fri', 1, 177],  // Warming-up Hardlopen / Cardio
            [3, 'fri', 2, 192],  // Endurance 60 min zone 2
            [3, 'fri', 3, 348],  // Core stability
            [3, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: SE chipper + recovery bike
            [3, 'sat', 1, 176],  // Warming up algemeen
            [3, 'sat', 2, 320],  // Strength Endurance chipper 100/90/80/70/60/50
            [3, 'sat', 3, 182],  // Recovery 30 min bike
            [3, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 4 ====================
            // Ma: Interval 200/400/600 + core dynamisch (zelfde voor beide niveaus)
            [4, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [4, 'mon', 2, 286],  // Interval 200/400/600
            [4, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [4, 'mon', 4, 350],  // Cooling down

            // Wo: Comprimised endurance run-cardio 200m
            [4, 'wed', 1, 176],  // Warming up algemeen
            [4, 'wed', 2, 423],  // Comprimised endurance run-cardio 1500 en strenght 5x30 200m
            [4, 'wed', 3, 443],  // Stretch & mobiliteit

            // Vr: Interval ergs + SE push pull upper body
            [4, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [4, 'fri', 2, 289],  // Interval roei 500
            [4, 'fri', 3, 293],  // Interval ski 500
            [4, 'fri', 4, 457],  // Strength endurance push pull upperbody 1
            [4, 'fri', 5, 458],  // Strength endurance push pull upperbody 2
            [4, 'fri', 6, 350],  // Cooling down

            // Za: Strength power + comprimised strength 15min run
            [4, 'sat', 1, 176],  // Warming up algemeen
            [4, 'sat', 2, 384],  // Strength Power single leg box push-off jump
            [4, 'sat', 3, 386],  // Strength Power broad jump met arm swing
            [4, 'sat', 4, 314],  // Comprimised Strength Endurance 15min run strenght 15min run
            [4, 'sat', 5, 443],  // Stretch & mobiliteit

            // ==================== WEEK 5 ====================
            // Ma: Interval 8x800 + core dynamisch (zelfde voor beide niveaus)
            [5, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [5, 'mon', 2, 269],  // Interval 8x800
            [5, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [5, 'mon', 4, 350],  // Cooling down

            // Wo: Treshold sled + SE push pull lower body
            [5, 'wed', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [5, 'wed', 2, 247],  // Treshold ski/row ladder sled push pull
            [5, 'wed', 3, 459],  // Strength endurance push pull lower body 1
            [5, 'wed', 4, 460],  // Strength endurance push pull lower body 2
            [5, 'wed', 5, 443],  // Stretch & mobiliteit

            // Do: Strength power + treshold run
            [5, 'thu', 1, 176],  // Warming up algemeen
            [5, 'thu', 2, 451],  // Strength / power jump squat (3 sets)
            [5, 'thu', 3, 241],  // Treshold zone 3 run 5x1200m + kracht (closest to 6x1200m)
            [5, 'thu', 4, 443],  // Stretch & mobiliteit

            // Vr: Endurance zone 2+ + core
            [5, 'fri', 1, 407],  // Warming-up bike & foam roll
            [5, 'fri', 2, 198],  // Endurance 60 min zone 2+
            [5, 'fri', 3, 470],  // Core stability 3*30/10 statisch

            // Za: Comprimised endurance run-cardio 200m
            [5, 'sat', 1, 176],  // Warming up algemeen
            [5, 'sat', 2, 423],  // Comprimised endurance run-cardio 1500 en strenght 5x30 200m
            [5, 'sat', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 6 ====================
            // Ma: Interval 6x1000 + core dynamisch
            [6, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [6, 'mon', 2, 275],  // Interval 6x1000
            [6, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [6, 'mon', 4, 350],  // Cooling down

            // Di: Strength power + SE sled
            [6, 'tue', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [6, 'tue', 2, 387],  // Strength Power DB/KB clean into push press
            [6, 'tue', 3, 322],  // Strength Endurance sled 20m oplopend
            [6, 'tue', 4, 443],  // Stretch & mobiliteit

            // Vr: Strength hypertrofie + sled
            [6, 'fri', 1, 176],  // Warming up algemeen
            [6, 'fri', 2, 340],  // Strength hypertrofie ladder
            [6, 'fri', 3, 323],  // Strength Endurance sled 5*15m
            [6, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: Endurance 70 min zone 2+ + core
            [6, 'sat', 1, 177],  // Warming-up Hardlopen / Cardio
            [6, 'sat', 2, 199],  // Endurance 70 min zone 2+
            [6, 'sat', 3, 349],  // Core
            [6, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 7 ====================
            // Ma: Interval pyramide + core (zelfde voor beide niveaus)
            [7, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [7, 'mon', 2, 417],  // Interval pyramide
            [7, 'mon', 3, 349],  // Core
            [7, 'mon', 4, 350],  // Cooling down

            // Wo: SE wallball + sled + FC
            [7, 'wed', 1, 176],  // Warming up algemeen
            [7, 'wed', 2, 376],  // Strength Endurance Wallball + sled + FC (5-8 rds)
            [7, 'wed', 3, 443],  // Stretch & mobiliteit

            // Vr: HIIT EMOM + SE sled
            [7, 'fri', 1, 359],  // Warming-up 5 min cardio + dynamic
            [7, 'fri', 2, 390],  // High-Intensity Interval EMOM 32-40min ergs + bike
            [7, 'fri', 3, 330],  // Strength Endurance push pull sled 1
            [7, 'fri', 4, 350],  // Cooling down

            // Za: Comprimised endurance 7x10min + strength
            [7, 'sat', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [7, 'sat', 2, 383],  // Comprimised Endurance 7x10min run + max station
            [7, 'sat', 3, 443],  // Stretch & mobiliteit

            // ==================== WEEK 8 ====================
            // Ma: Interval 5x5min zone 4 + core dynamisch
            [8, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [8, 'mon', 2, 416],  // Interval 5x 5 min zone 4 / 2 min zone 1
            [8, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [8, 'mon', 4, 350],  // Cooling down

            // Wo: Treshold sled + SE push pull mixed
            [8, 'wed', 1, 359],  // Warming-up 5 min cardio + dynamic
            [8, 'wed', 2, 247],  // Treshold ski/row ladder sled push pull
            [8, 'wed', 3, 459],  // Strength endurance push pull lower body 1
            [8, 'wed', 4, 458],  // Strength endurance push pull upperbody 2
            [8, 'wed', 5, 360],  // Cooling down cardio + stretch

            // Do: Comprimised endurance dubbel
            [8, 'thu', 1, 176],  // Warming up algemeen
            [8, 'thu', 2, 311],  // Comprimised Endurance e2mom run + benen
            [8, 'thu', 3, 312],  // Comprimised Endurance e2mom ergs + upper body
            [8, 'thu', 4, 350],  // Cooling down

            // Vr: Endurance zone 2+ + bike + core
            [8, 'fri', 1, 359],  // Warming-up 5 min cardio + dynamic
            [8, 'fri', 2, 198],  // Endurance 60 min zone 2+
            [8, 'fri', 3, 214],  // Endurance zone 2 bike 30min
            [8, 'fri', 4, 469],  // Core stability dynamic 3*30/10
            [8, 'fri', 5, 360],  // Cooling down cardio + stretch

            // ==================== WEEK 9 ====================
            // Ma: Interval 6x1200 + core dynamisch
            [9, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [9, 'mon', 2, 280],  // Interval 6x1200
            [9, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [9, 'mon', 4, 350],  // Cooling down

            // Di: Endurance training zone 2
            [9, 'tue', 1, 176],  // Warming up algemeen
            [9, 'tue', 2, 408],  // Endurance Training zone 2 (ergs, sled, run)
            [9, 'tue', 3, 443],  // Stretch & mobiliteit

            // Wo: Threshold run 5x2km + wallball
            [9, 'wed', 1, 176],  // Warming up algemeen
            [9, 'wed', 2, 234],  // Threshold zone 3 run 5x2km + wallball
            [9, 'wed', 3, 360],  // Cooling down cardio + stretch

            // Vr: Comprimised strength hyrox + SE sandbag
            [9, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [9, 'fri', 2, 468],  // Comprimised strength 500m 5x30/15 hyrox specific
            [9, 'fri', 3, 389],  // Comprimised Strength Endurance sandbag/plate/BB (5-8 rds)
            [9, 'fri', 4, 443],  // Stretch & mobiliteit

            // Za: SE chipper slee
            [9, 'sat', 1, 359],  // Warming-up 5 min cardio + dynamic
            [9, 'sat', 2, 319],  // Strength Endurance chipper slee 120m
            [9, 'sat', 3, 350],  // Cooling down

            // ==================== WEEK 10 ====================
            // Ma: Interval 10x600 kracht + core dynamisch
            [10, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [10, 'mon', 2, 265],  // Interval 10x600 kracht
            [10, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [10, 'mon', 4, 350],  // Cooling down

            // Wo: Strength burpee + SE push pull mixed
            [10, 'wed', 1, 176],  // Warming up algemeen
            [10, 'wed', 2, 465],  // Strength burpee madnes
            [10, 'wed', 3, 459],  // Strength endurance push pull lower body 1
            [10, 'wed', 4, 458],  // Strength endurance push pull upperbody 2
            [10, 'wed', 5, 350],  // Cooling down

            // Do: Endurance 70 min zone 2+ + core
            [10, 'thu', 1, 177],  // Warming-up Hardlopen / Cardio
            [10, 'thu', 2, 199],  // Endurance 70 min zone 2+
            [10, 'thu', 3, 362],  // Core Stability 2x30 sec circuit
            [10, 'thu', 4, 350],  // Cooling down

            // Za: HYROX Sim 50% + recovery bike
            [10, 'sat', 1, 176],  // Warming up algemeen
            [10, 'sat', 2, 363],  // HYROX Sim 50%
            [10, 'sat', 3, 182],  // Recovery 30 min bike
            [10, 'sat', 4, 443],  // Stretch & mobiliteit

            // ==================== WEEK 11 ====================
            // Ma: Treshold race pace + core dynamisch
            [11, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [11, 'mon', 2, 445],  // Treshold 4x 8 min (+10% sneller dan race pace)
            [11, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [11, 'mon', 4, 350],  // Cooling down

            // Wo: Comprimised endurance ladder + strength
            [11, 'wed', 1, 359],  // Warming-up 5 min cardio + dynamic
            [11, 'wed', 2, 313],  // Comprimised Endurance ski/row/assbike ladder + strength
            [11, 'wed', 3, 350],  // Cooling down

            // Do: Comprimised endurance run-cardio 200m
            [11, 'thu', 1, 176],  // Warming up algemeen
            [11, 'thu', 2, 423],  // Comprimised endurance run-cardio 1500 en strenght 5x30 200m
            [11, 'thu', 3, 443],  // Stretch & mobiliteit

            // Za: Endurance 70 min zone 2+ + core dynamisch
            [11, 'sat', 1, 177],  // Warming-up Hardlopen / Cardio
            [11, 'sat', 2, 199],  // Endurance 70 min zone 2+
            [11, 'sat', 3, 469],  // Core stability dynamic 3*30/10
            [11, 'sat', 4, 350],  // Cooling down

            // ==================== WEEK 12 ====================
            // Ma: Threshold test loop 5km + core dynamisch
            [12, 'mon', 1, 177],  // Warming-up Hardlopen / Cardio
            [12, 'mon', 2, 226],  // Threshold zone 3 test loop 5km
            [12, 'mon', 3, 469],  // Core stability dynamic 3*30/10
            [12, 'mon', 4, 350],  // Cooling down

            // Di: Endurance ergs + SE Thruster/Farm carry/Lunges
            [12, 'tue', 1, 359],  // Warming-up 5 min cardio + dynamic
            [12, 'tue', 2, 205],  // Endurance zone 2+ ski/row/assbike 10min
            [12, 'tue', 3, 369],  // Strength Endurance Thruster + sled push (4-6 rds)
            [12, 'tue', 4, 370],  // Strength Endurance Farm carry + sled pull (4-6 rds)
            [12, 'tue', 5, 371],  // Strength Endurance Lunges + BBJ (4-6 rds)
            [12, 'tue', 6, 350],  // Cooling down

            // Do: Endurance 60 min zone 2+ + core
            [12, 'thu', 1, 177],  // Warming-up Hardlopen / Cardio
            [12, 'thu', 2, 198],  // Endurance 60 min zone 2+
            [12, 'thu', 3, 361],  // Core Stability 3x20 sec circuit
            [12, 'thu', 4, 443],  // Stretch & mobiliteit

            // Vr: Interval ergs + endurance
            [12, 'fri', 1, 358],  // Warming-up Ski/Row/Bike + Mobility + Sprints
            [12, 'fri', 2, 290],  // Interval roei 1000
            [12, 'fri', 3, 294],  // Interval ski 1000
            [12, 'fri', 4, 195],  // Endurance 30 min zone 2+
            [12, 'fri', 5, 360],  // Cooling down cardio + stretch

            // Za: SE chipper + snelheid heuvel sprint
            [12, 'sat', 1, 359],  // Warming-up 5 min cardio + dynamic
            [12, 'sat', 2, 320],  // Strength Endurance chipper 100/90/80/70/60/50/60/70/80/90/100
            [12, 'sat', 3, 336],  // Snelheid heuvel sprint 12x20s
            [12, 'sat', 4, 443],  // Stretch & mobiliteit
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
     * Expert — 6 dagen per week — 12 weken
     *
     * Gemapped vanuit PDF 11 rechterkolom: "basis 12 weken 6 keer per week — Expert"
     * Card IDs verwijzen naar training_cards in de database.
     *
     * Mapping formaat: [week, day, sort_order, training_card_id]
     */
    private function seedExpert6Days(): void
    {
        $template = TrainingPlanTemplate::updateOrCreate(
            ['slug' => 'expert_6_days'],
            [
                'name' => 'Expert traject (6 dagen)',
                'level' => 'expert',
                'injury_type' => null,
                'max_days' => 6,
                'weeks' => 12,
            ]
        );

        $template->items()->delete();

        $items = [

            // ==================== WEEK 1 ====================
            [1, 'mon', 1, 176],
            [1, 'mon', 2, 252],
            [1, 'mon', 3, 470],
            [1, 'mon', 4, 350],
            [1, 'tue', 1, 358],
            [1, 'tue', 2, 347],
            [1, 'tue', 3, 205],
            [1, 'tue', 4, 321],
            [1, 'tue', 5, 360],
            [1, 'wed', 1, 177],
            [1, 'wed', 2, 224],
            [1, 'wed', 3, 389],
            [1, 'thu', 1, 358],
            [1, 'thu', 2, 344],
            [1, 'thu', 3, 292],
            [1, 'thu', 4, 288],
            [1, 'thu', 5, 323],
            [1, 'thu', 6, 360],
            [1, 'fri', 1, 177],
            [1, 'fri', 2, 191],
            [1, 'fri', 3, 348],
            [1, 'fri', 4, 443],
            [1, 'sat', 1, 176],
            [1, 'sat', 2, 381],
            [1, 'sat', 3, 382],
            [1, 'sat', 4, 443],

            // ==================== WEEK 2 ====================
            [2, 'mon', 1, 177],
            [2, 'mon', 2, 258],
            [2, 'mon', 3, 470],
            [2, 'mon', 4, 350],
            [2, 'tue', 1, 358],
            [2, 'tue', 2, 203],
            [2, 'tue', 3, 345],
            [2, 'tue', 4, 321],
            [2, 'tue', 5, 350],
            [2, 'wed', 1, 176],
            [2, 'wed', 2, 451],
            [2, 'wed', 3, 450],
            [2, 'wed', 4, 315],
            [2, 'wed', 5, 215],
            [2, 'wed', 6, 350],
            [2, 'thu', 1, 358],
            [2, 'thu', 2, 245],
            [2, 'thu', 3, 322],
            [2, 'thu', 4, 360],
            [2, 'fri', 1, 177],
            [2, 'fri', 2, 197],
            [2, 'fri', 3, 348],
            [2, 'fri', 4, 443],
            [2, 'sat', 1, 176],
            [2, 'sat', 2, 384],
            [2, 'sat', 3, 385],
            [2, 'sat', 4, 467],
            [2, 'sat', 5, 443],

            // ==================== WEEK 3 ====================
            [3, 'mon', 1, 177],
            [3, 'mon', 2, 261],
            [3, 'mon', 3, 470],
            [3, 'mon', 4, 350],
            [3, 'tue', 1, 358],
            [3, 'tue', 2, 347],
            [3, 'tue', 3, 246],
            [3, 'tue', 4, 326],
            [3, 'tue', 5, 350],
            [3, 'wed', 1, 176],
            [3, 'wed', 2, 320],
            [3, 'wed', 3, 215],
            [3, 'wed', 4, 350],
            [3, 'thu', 1, 358],
            [3, 'thu', 2, 205],
            [3, 'thu', 3, 329],
            [3, 'thu', 4, 360],
            [3, 'fri', 1, 177],
            [3, 'fri', 2, 192],
            [3, 'fri', 3, 348],
            [3, 'fri', 4, 443],
            [3, 'sat', 1, 176],
            [3, 'sat', 2, 426],
            [3, 'sat', 3, 215],
            [3, 'sat', 4, 350],

            // ==================== WEEK 4 ====================
            [4, 'mon', 1, 177],
            [4, 'mon', 2, 286],
            [4, 'mon', 3, 469],
            [4, 'mon', 4, 350],
            [4, 'tue', 1, 358],
            [4, 'tue', 2, 203],
            [4, 'tue', 3, 346],
            [4, 'tue', 4, 329],
            [4, 'tue', 5, 350],
            [4, 'wed', 1, 176],
            [4, 'wed', 2, 423],
            [4, 'wed', 3, 443],
            [4, 'thu', 1, 407],
            [4, 'thu', 2, 217],
            [4, 'thu', 3, 469],
            [4, 'thu', 4, 350],
            [4, 'fri', 1, 358],
            [4, 'fri', 2, 289],
            [4, 'fri', 3, 293],
            [4, 'fri', 4, 457],
            [4, 'fri', 5, 458],
            [4, 'fri', 6, 350],
            [4, 'sat', 1, 176],
            [4, 'sat', 2, 384],
            [4, 'sat', 3, 386],
            [4, 'sat', 4, 314],
            [4, 'sat', 5, 443],

            // ==================== WEEK 5 ====================
            [5, 'mon', 1, 177],
            [5, 'mon', 2, 269],
            [5, 'mon', 3, 469],
            [5, 'mon', 4, 350],
            [5, 'tue', 1, 358],
            [5, 'tue', 2, 247],
            [5, 'tue', 3, 459],
            [5, 'tue', 4, 460],
            [5, 'tue', 5, 443],
            [5, 'wed', 1, 407],
            [5, 'wed', 2, 217],
            [5, 'wed', 3, 470],
            [5, 'thu', 1, 176],
            [5, 'thu', 2, 451],
            [5, 'thu', 3, 241],
            [5, 'thu', 4, 443],
            [5, 'fri', 1, 358],
            [5, 'fri', 2, 205],
            [5, 'fri', 3, 342],
            [5, 'fri', 4, 327],
            [5, 'fri', 5, 350],
            [5, 'sat', 1, 176],
            [5, 'sat', 2, 423],
            [5, 'sat', 3, 443],

            // ==================== WEEK 6 ====================
            [6, 'mon', 1, 177],
            [6, 'mon', 2, 275],
            [6, 'mon', 3, 469],
            [6, 'mon', 4, 350],
            [6, 'tue', 1, 358],
            [6, 'tue', 2, 204],
            [6, 'tue', 3, 322],
            [6, 'tue', 4, 443],
            [6, 'wed', 1, 177],
            [6, 'wed', 2, 344],
            [6, 'wed', 3, 240],
            [6, 'wed', 4, 443],
            [6, 'thu', 1, 407],
            [6, 'thu', 2, 217],
            [6, 'thu', 3, 350],
            [6, 'fri', 1, 176],
            [6, 'fri', 2, 384],
            [6, 'fri', 3, 340],
            [6, 'fri', 4, 323],
            [6, 'fri', 5, 443],
            [6, 'sat', 1, 177],
            [6, 'sat', 2, 199],
            [6, 'sat', 3, 349],
            [6, 'sat', 4, 350],

            // ==================== WEEK 7 ====================
            [7, 'mon', 1, 177],
            [7, 'mon', 2, 417],
            [7, 'mon', 3, 349],
            [7, 'mon', 4, 350],
            [7, 'tue', 1, 358],
            [7, 'tue', 2, 408],
            [7, 'tue', 3, 443],
            [7, 'wed', 1, 176],
            [7, 'wed', 2, 345],
            [7, 'wed', 3, 317],
            [7, 'wed', 4, 443],
            [7, 'thu', 1, 407],
            [7, 'thu', 2, 218],
            [7, 'thu', 3, 362],
            [7, 'thu', 4, 350],
            [7, 'fri', 1, 359],
            [7, 'fri', 2, 347],
            [7, 'fri', 3, 345],
            [7, 'fri', 4, 390],
            [7, 'fri', 5, 330],
            [7, 'fri', 6, 350],
            [7, 'sat', 1, 177],
            [7, 'sat', 2, 314],
            [7, 'sat', 3, 349],
            [7, 'sat', 4, 443],

            // ==================== WEEK 8 ====================
            [8, 'mon', 1, 177],
            [8, 'mon', 2, 280],
            [8, 'mon', 3, 469],
            [8, 'mon', 4, 350],
            [8, 'tue', 1, 359],
            [8, 'tue', 2, 247],
            [8, 'tue', 3, 459],
            [8, 'tue', 4, 458],
            [8, 'tue', 5, 360],
            [8, 'wed', 1, 218],
            [8, 'thu', 1, 176],
            [8, 'thu', 2, 387],
            [8, 'thu', 3, 311],
            [8, 'thu', 4, 312],
            [8, 'thu', 5, 350],
            [8, 'fri', 1, 359],
            [8, 'fri', 2, 184],
            [8, 'fri', 3, 361],
            [8, 'fri', 4, 350],
            [8, 'sat', 1, 176],
            [8, 'sat', 2, 318],
            [8, 'sat', 3, 215],
            [8, 'sat', 4, 350],

            // ==================== WEEK 9 ====================
            [9, 'mon', 1, 177],
            [9, 'mon', 2, 273],
            [9, 'mon', 3, 469],
            [9, 'mon', 4, 350],
            [9, 'tue', 1, 176],
            [9, 'tue', 2, 408],
            [9, 'tue', 3, 443],
            [9, 'wed', 1, 176],
            [9, 'wed', 2, 234],
            [9, 'wed', 3, 360],
            [9, 'thu', 1, 407],
            [9, 'thu', 2, 218],
            [9, 'thu', 3, 362],
            [9, 'thu', 4, 350],
            [9, 'fri', 1, 358],
            [9, 'fri', 2, 344],
            [9, 'fri', 3, 345],
            [9, 'fri', 4, 467],
            [9, 'fri', 5, 349],
            [9, 'fri', 6, 443],
            [9, 'sat', 1, 359],
            [9, 'sat', 2, 319],
            [9, 'sat', 3, 196],
            [9, 'sat', 4, 350],

            // ==================== WEEK 10 ====================
            [10, 'mon', 1, 177],
            [10, 'mon', 2, 456],
            [10, 'mon', 3, 469],
            [10, 'mon', 4, 350],
            [10, 'tue', 1, 218],
            [10, 'tue', 2, 361],
            [10, 'tue', 3, 350],
            [10, 'wed', 1, 176],
            [10, 'wed', 2, 346],
            [10, 'wed', 3, 465],
            [10, 'wed', 4, 459],
            [10, 'wed', 5, 458],
            [10, 'wed', 6, 350],
            [10, 'thu', 1, 177],
            [10, 'thu', 2, 200],
            [10, 'thu', 3, 362],
            [10, 'thu', 4, 350],
            [10, 'sat', 1, 176],
            [10, 'sat', 2, 363],
            [10, 'sat', 3, 182],
            [10, 'sat', 4, 443],

            // ==================== WEEK 11 ====================
            [11, 'mon', 1, 177],
            [11, 'mon', 2, 445],
            [11, 'mon', 3, 341],
            [11, 'mon', 4, 469],
            [11, 'mon', 5, 350],
            [11, 'tue', 1, 359],
            [11, 'tue', 2, 347],
            [11, 'tue', 3, 342],
            [11, 'tue', 4, 313],
            [11, 'tue', 5, 350],
            [11, 'wed', 1, 176],
            [11, 'wed', 2, 213],
            [11, 'wed', 3, 350],
            [11, 'thu', 1, 176],
            [11, 'thu', 2, 450],
            [11, 'thu', 3, 384],
            [11, 'thu', 4, 423],
            [11, 'thu', 5, 443],
            [11, 'fri', 1, 358],
            [11, 'fri', 2, 300],
            [11, 'fri', 3, 331],
            [11, 'fri', 4, 350],
            [11, 'sat', 1, 177],
            [11, 'sat', 2, 200],
            [11, 'sat', 3, 469],
            [11, 'sat', 4, 350],

            // ==================== WEEK 12 ====================
            [12, 'mon', 1, 177],
            [12, 'mon', 2, 226],
            [12, 'mon', 3, 469],
            [12, 'mon', 4, 350],
            [12, 'tue', 1, 359],
            [12, 'tue', 2, 347],
            [12, 'tue', 3, 205],
            [12, 'tue', 4, 369],
            [12, 'tue', 5, 370],
            [12, 'tue', 6, 371],
            [12, 'tue', 7, 350],
            [12, 'wed', 1, 177],
            [12, 'wed', 2, 217],
            [12, 'wed', 3, 361],
            [12, 'wed', 4, 443],
            [12, 'thu', 1, 359],
            [12, 'thu', 2, 384],
            [12, 'thu', 3, 386],
            [12, 'thu', 4, 317],
            [12, 'thu', 5, 360],
            [12, 'fri', 1, 358],
            [12, 'fri', 2, 290],
            [12, 'fri', 3, 294],
            [12, 'fri', 4, 196],
            [12, 'fri', 5, 360],
            [12, 'sat', 1, 176],
            [12, 'sat', 2, 383],
            [12, 'sat', 3, 443],
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
