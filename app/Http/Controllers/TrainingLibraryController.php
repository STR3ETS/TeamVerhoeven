<?php
// app/Http/Controllers/TrainingLibraryController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingSection;
use App\Models\TrainingCard;
use App\Models\TrainingBlock;
use App\Models\TrainingItem;
use Illuminate\Http\Request;

class TrainingLibraryController extends Controller
{
    public function show(User $client) // {client} bindt aan User
    {
        abort_if($client->role !== 'client', 404);

        $sections = TrainingSection::with(['cards.blocks.items'])
            ->orderBy('sort_order')
            ->get();

        return view('coach.planning.create', [
            'client'   => $client->load('clientProfile'),
            'sections' => $sections,
        ]);
    }

    /**
     * Overzicht + CRUD pagina voor de trainingsbibliotheek (coach).
     */
    public function index(Request $request)
    {
        $sections = TrainingSection::with(['cards.blocks.items'])
            ->orderBy('sort_order')
            ->get();

        $currentCard = null;

        if ($request->filled('card')) {
            $currentCard = TrainingCard::with(['blocks.items'])
                ->find($request->integer('card'));
        } else {
            // eerste training als default
            $firstCard = $sections->flatMap->cards->sortBy('sort_order')->first();
            if ($firstCard) {
                $currentCard = $firstCard->load('blocks.items');
            }
        }

        return view('coach.training-library.index', [
            'sections'    => $sections,
            'currentCard' => $currentCard,
        ]);
    }

    /* ---------- SECTIONS ---------- */

    public function storeSection(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (TrainingSection::max('sort_order') ?? 0) + 1;
        }

        TrainingSection::create($data);

        return back()->with('status', 'Sectie aangemaakt.');
    }

    public function updateSection(TrainingSection $section, Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $section->update($data);

        return back()->with('status', 'Sectie bijgewerkt.');
    }

    public function destroySection(TrainingSection $section)
    {
        $section->delete();

        return back()->with('status', 'Sectie (en alle trainingen daarin) verwijderd.');
    }

    /* ---------- CARDS (TRAININGEN) ---------- */

    public function storeCard(Request $request)
    {
        $data = $request->validate([
            'training_section_id' => ['required', 'exists:training_sections,id'],
            'title'               => ['required', 'string', 'max:255'],
            'sort_order'          => ['nullable', 'integer'],
        ]);

        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (TrainingCard::where('training_section_id', $data['training_section_id'])->max('sort_order') ?? 0) + 1;
        }

        $card = TrainingCard::create($data);

        return redirect()
            ->route('coach.training-library.index', ['card' => $card->id])
            ->with('status', 'Training aangemaakt.');
    }

    public function updateCard(TrainingCard $card, Request $request)
    {
        $data = $request->validate([
            'training_section_id' => ['required', 'exists:training_sections,id'],
            'title'               => ['required', 'string', 'max:255'],
            'sort_order'          => ['nullable', 'integer'],
        ]);

        $card->update($data);

        return redirect()
            ->route('coach.training-library.index', ['card' => $card->id])
            ->with('status', 'Training bijgewerkt.');
    }

    public function destroyCard(TrainingCard $card)
    {
        $card->delete();

        return back()->with('status', 'Training verwijderd.');
    }

    /* ---------- BLOCKS ---------- */

    public function storeBlock(Request $request)
    {
        $data = $request->validate([
            'training_card_id' => ['required', 'exists:training_cards,id'],
            'label'            => ['required', 'string', 'max:255'],
            'badge_classes'    => ['nullable', 'string', 'max:255'],
            'sort_order'       => ['nullable', 'integer'],
        ]);

        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (TrainingBlock::where('training_card_id', $data['training_card_id'])->max('sort_order') ?? 0) + 1;
        }

        $block = TrainingBlock::create($data);

        return redirect()
            ->route('coach.training-library.index', ['card' => $block->training_card_id])
            ->with('status', 'Blok aangemaakt.');
    }

    public function updateBlock(TrainingBlock $block, Request $request)
    {
        $data = $request->validate([
            'label'         => ['required', 'string', 'max:255'],
            'badge_classes' => ['nullable', 'string', 'max:255'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        $block->update($data);

        return redirect()
            ->route('coach.training-library.index', ['card' => $block->training_card_id])
            ->with('status', 'Blok bijgewerkt.');
    }

    public function destroyBlock(TrainingBlock $block)
    {
        $cardId = $block->training_card_id;
        $block->delete();

        return redirect()
            ->route('coach.training-library.index', ['card' => $cardId])
            ->with('status', 'Blok verwijderd.');
    }

    /* ---------- ITEMS ---------- */

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'training_block_id' => ['required', 'exists:training_blocks,id'],
            'left_html'         => ['required', 'string'],
            'right_text'        => ['nullable', 'string', 'max:255'],
            'sort_order'        => ['nullable', 'integer'],
        ]);

        $block = TrainingBlock::findOrFail($data['training_block_id']);

        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (TrainingItem::where('training_block_id', $block->id)->max('sort_order') ?? 0) + 1;
        }

        $item = new TrainingItem($data);
        $item->training_block_id = $block->id;
        $item->save();

        return redirect()
            ->route('coach.training-library.index', ['card' => $block->training_card_id])
            ->with('status', 'Item toegevoegd.');
    }

    public function updateItem(TrainingItem $item, Request $request)
    {
        $data = $request->validate([
            'left_html'  => ['required', 'string'],
            'right_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $item->update($data);

        return redirect()
            ->route('coach.training-library.index', ['card' => $item->block->training_card_id])
            ->with('status', 'Item bijgewerkt.');
    }

    public function destroyItem(TrainingItem $item)
    {
        $cardId = $item->block->training_card_id;
        $item->delete();

        return redirect()
            ->route('coach.training-library.index', ['card' => $cardId])
            ->with('status', 'Item verwijderd.');
    }
}
