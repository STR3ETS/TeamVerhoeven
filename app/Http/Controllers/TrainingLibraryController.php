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

    /* ---------- SOFT RESPONSE HELPER ---------- */

    private function respond(Request $request, string $status, string $redirectUrl)
    {
        // fetch() stuurt Accept: application/json => geef JSON terug
        if ($request->expectsJson()) {
            return response()->json([
                'ok'       => true,
                'status'   => $status,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('status', $status);
    }

    private function firstCardUrl(): string
    {
        $firstCardId = TrainingCard::query()
            ->join('training_sections', 'training_sections.id', '=', 'training_cards.training_section_id')
            ->orderBy('training_sections.sort_order')
            ->orderBy('training_cards.sort_order')
            ->orderBy('training_cards.id')
            ->value('training_cards.id');

        return $firstCardId
            ? route('coach.training-library.index', ['card' => $firstCardId])
            : route('coach.training-library.index');
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

        // blijf op dezelfde pagina (inclusief ?card=...) als je via normale submit terugkomt
        $url = url()->previous() ?: route('coach.training-library.index');

        return $this->respond($request, 'Sectie aangemaakt.', $url);
    }

    public function updateSection(TrainingSection $section, Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $section->update($data);

        $url = url()->previous() ?: route('coach.training-library.index');

        return $this->respond($request, 'Sectie bijgewerkt.', $url);
    }

    public function destroySection(TrainingSection $section, Request $request)
    {
        $section->delete();

        // na verwijderen: ga naar eerste bestaande card (of index)
        $url = $this->firstCardUrl();

        return $this->respond($request, 'Sectie (en alle trainingen daarin) verwijderd.', $url);
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

        $url = route('coach.training-library.index', ['card' => $card->id]);

        return $this->respond($request, 'Training aangemaakt.', $url);
    }

    public function updateCard(TrainingCard $card, Request $request)
    {
        $data = $request->validate([
            'training_section_id' => ['required', 'exists:training_sections,id'],
            'title'               => ['required', 'string', 'max:255'],
            'sort_order'          => ['nullable', 'integer'],
        ]);

        $card->update($data);

        $url = route('coach.training-library.index', ['card' => $card->id]);

        return $this->respond($request, 'Training bijgewerkt.', $url);
    }

    public function destroyCard(TrainingCard $card, Request $request)
    {
        $deletedId = $card->id;
        $card->delete();

        // kies volgende "eerste" kaart (maar niet de verwijderde)
        $nextCardId = TrainingCard::query()
            ->join('training_sections', 'training_sections.id', '=', 'training_cards.training_section_id')
            ->where('training_cards.id', '!=', $deletedId)
            ->orderBy('training_sections.sort_order')
            ->orderBy('training_cards.sort_order')
            ->orderBy('training_cards.id')
            ->value('training_cards.id');

        $url = $nextCardId
            ? route('coach.training-library.index', ['card' => $nextCardId])
            : route('coach.training-library.index');

        return $this->respond($request, 'Training verwijderd.', $url);
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

        $url = route('coach.training-library.index', ['card' => $block->training_card_id]);

        return $this->respond($request, 'Blok aangemaakt.', $url);
    }

    public function updateBlock(TrainingBlock $block, Request $request)
    {
        $data = $request->validate([
            'label'         => ['required', 'string', 'max:255'],
            'badge_classes' => ['nullable', 'string', 'max:255'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        $block->update($data);

        $url = route('coach.training-library.index', ['card' => $block->training_card_id]);

        return $this->respond($request, 'Blok bijgewerkt.', $url);
    }

    public function destroyBlock(TrainingBlock $block, Request $request)
    {
        $cardId = $block->training_card_id;
        $block->delete();

        $url = route('coach.training-library.index', ['card' => $cardId]);

        return $this->respond($request, 'Blok verwijderd.', $url);
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

        $url = route('coach.training-library.index', ['card' => $block->training_card_id]);

        return $this->respond($request, 'Item toegevoegd.', $url);
    }

    public function updateItem(TrainingItem $item, Request $request)
    {
        $data = $request->validate([
            'left_html'  => ['required', 'string'],
            'right_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $item->update($data);

        $url = route('coach.training-library.index', ['card' => $item->block->training_card_id]);

        return $this->respond($request, 'Item bijgewerkt.', $url);
    }

    public function destroyItem(TrainingItem $item, Request $request)
    {
        $cardId = $item->block->training_card_id;
        $item->delete();

        $url = route('coach.training-library.index', ['card' => $cardId]);

        return $this->respond($request, 'Item verwijderd.', $url);
    }
}
