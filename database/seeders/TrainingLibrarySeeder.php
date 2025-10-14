<?php
// database/seeders/TrainingLibrarySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingSection;

class TrainingLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $trainingLibrary = config('training_library');

        DB::transaction(function () use ($trainingLibrary) {
            foreach ($trainingLibrary as $sIndex => $sectionData) {
                // Section (natuurlijke sleutel: name)
                $section = TrainingSection::updateOrCreate(
                    ['name' => $sectionData['section']],
                    ['sort_order' => $sIndex + 1]
                );

                foreach ($sectionData['cards'] as $cIndex => $cardData) {
                    // Card via relatie => FK wordt automatisch gezet (ongeacht kolomnaam)
                    $card = $section->cards()->updateOrCreate(
                        ['title' => $cardData['title']],
                        ['sort_order' => $cIndex + 1]
                    );

                    foreach ($cardData['blocks'] as $bIndex => $blockData) {
                        // Block via relatie
                        $block = $card->blocks()->updateOrCreate(
                            ['label' => $blockData['label']],
                            [
                                'badge_classes' => $blockData['badge_classes'] ?? null,
                                'sort_order'    => $bIndex + 1,
                            ]
                        );

                        foreach ($blockData['items'] as $iIndex => $itemData) {
                            // Item via relatie (natuurlijke sleutel: left_html + right_text)
                            $block->items()->updateOrCreate(
                                [
                                    'left_html'  => $itemData['left']  ?? '',
                                    'right_text' => $itemData['right'] ?? null,
                                ],
                                ['sort_order' => $iIndex + 1]
                            );
                        }
                    }
                }
            }
        });
    }
}
