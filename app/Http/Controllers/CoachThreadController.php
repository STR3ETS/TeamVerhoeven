<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CoachThreadController extends Controller
{
    /**
     * Overzicht van alle threads voor de ingelogde coach.
     */
    public function index()
    {
        $user = auth()->user();
        abort_if(!$user || !$user->isCoach(), 404);

        $threads = Thread::query()
            ->with(['clientUser']) // handig voor weergave
            ->where('coach_user_id', $user->id)
            ->latest()
            ->get();

        return view('threads.index', compact('threads'))->with('role', 'coach');
    }

    public function create()
    {
        $this->authorize('create', Thread::class);

        $coach = auth()->user();

        // Alle cliënten die aan deze coach gekoppeld zijn
        $clients = User::query()
            ->where('role', 'client')
            ->whereHas('clientProfile', fn($q) => $q->where('coach_id', $coach->id))
            ->orderBy('name')
            ->get();

        return view('threads.create', [
            'role'    => 'coach',
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Thread::class);

        $coach = auth()->user();

        $data = $request->validate([
            'client_user_id' => ['required', 'integer', 'exists:users,id'],
            'subject'        => ['nullable', 'string', 'max:150'],
        ]);

        // Check: is deze client echt gekoppeld aan deze coach?
        $client = User::query()
            ->where('id', $data['client_user_id'])
            ->where('role', 'client')
            ->whereHas('clientProfile', fn($q) => $q->where('coach_id', $coach->id))
            ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client_user_id' => 'Deze cliënt is niet aan jou gekoppeld.',
            ]);
        }

        // Als er al een thread bestaat tussen deze coach en client → daarheen redirecten
        $existing = Thread::query()
            ->where('client_user_id', $client->id)
            ->where('coach_user_id',  $coach->id)
            ->latest()
            ->first();

        if ($existing) {
            return redirect()
                ->route('coach.threads.show', $existing)
                ->with('status', 'Er bestaat al een gesprek met deze klant, je bent daarheen doorgestuurd.');
        }

        // Nieuwe thread aanmaken
        $thread = Thread::create([
            'client_user_id' => $client->id,
            'coach_user_id'  => $coach->id,
            'subject'        => $data['subject'] ?? null,
        ]);

        return redirect()->route('coach.threads.show', $thread);
    }

    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread);

        // Eventueel eerst berichten weggooien, als je geen cascade hebt:
        // $thread->messages()->delete();

        $thread->delete();

        return redirect()
            ->route('coach.threads.index')
            ->with('status', 'Het gesprek is gesloten.');
    }

    /**
     * Toon één thread.
     */
    public function show(Thread $thread)
    {
        // Policy checkt of deze coach aan de thread gekoppeld is
        $this->authorize('view', $thread);

        $thread->load(['clientUser', 'coachUser', 'messages.sender']);

        return view('threads.show', compact('thread'))->with('role', 'coach');
    }

    /**
     * Plaats een bericht in de thread.
     */
    public function storeMessage(Request $request, Thread $thread)
    {
        $this->authorize('reply', $thread);

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $thread->messages()->create([
            'sender_id' => auth()->id(),
            'body'      => $validated['body'],
        ]);

        return back();
    }
}
