<?php
// database/seeders/TrainingLibrarySeeder.php
namespace Database\Seeders;

use App\Models\TrainingBlock;
use App\Models\TrainingCard;
use App\Models\TrainingItem;
use App\Models\TrainingSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingLibrarySeeder extends Seeder
{
    public function run(): void
    {
        // Laad exact dezelfde data-structuur als in je Blade:
        $trainingLibrary = config('training_library');

        DB::transaction(function () use ($trainingLibrary) {
            // Leeg eerst tabellen (optioneel)
            TrainingItem::query()->delete();
            TrainingBlock::query()->delete();
            TrainingCard::query()->delete();
            TrainingSection::query()->delete();

            foreach ($trainingLibrary as $sIndex => $sectionData) {
                /** @var \App\Models\TrainingSection $section */
                $section = TrainingSection::create([
                    'name'       => $sectionData['section'],
                    'sort_order' => $sIndex + 1,
                ]);

                foreach ($sectionData['cards'] as $cIndex => $cardData) {
                    $card = $section->cards()->create([
                        'title'      => $cardData['title'],   // LET OP: titels blijven exact (ook "Treshold")
                        'sort_order' => $cIndex + 1,
                    ]);

                    foreach ($cardData['blocks'] as $bIndex => $blockData) {
                        $block = $card->blocks()->create([
                            'label'         => $blockData['label'],
                            'badge_classes' => $blockData['badge_classes'] ?? null,
                            'sort_order'    => $bIndex + 1,
                        ]);

                        foreach ($blockData['items'] as $iIndex => $itemData) {
                            $block->items()->create([
                                // Gebruik andere kolomnamen om SQL gereserveerde woorden te vermijden
                                'left_html'  => $itemData['left']  ?? '',
                                'right_text' => $itemData['right'] ?? null,
                                'sort_order' => $iIndex + 1,
                            ]);
                        }
                    }
                }
            }
        });
    }
}
