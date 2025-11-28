<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use App\Mail\NewThreadNotification;
use Illuminate\Support\Facades\Mail;

class ClientThreadController extends Controller
{
    use AuthorizesRequests;

    /**
     * Lijst met threads voor de ingelogde client (gebaseerd op users-variant).
     */
    public function index()
    {
        $userId = auth()->id();
        abort_if(!$userId, 404);

        $threads = Thread::query()
            ->with(['clientUser', 'coachUser'])
            ->where('client_user_id', $userId)
            ->latest()
            ->get();

        return view('threads.index', compact('threads'))->with('role', 'client');
    }

    /**
     * Formulier om een nieuwe thread te starten.
     */
    public function create()
    {
        $this->authorize('create', Thread::class);

        return view('threads.create')->with('role', 'client');
    }

    /**
     * Slaat een nieuwe thread op (users-variant).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Thread::class);

        $userId = auth()->id();
        $user   = auth()->user();
        abort_if(!$userId || !$user, 404);

        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
        ]);

        // 1) Eerst proberen via client_profiles.coach_id
        $coachUserId = $user->clientProfile?->coach_id;

        // 2) Zo niet, kies een actieve coach
        if (!$coachUserId) {
            $coachUserId = User::query()
                ->where('role', 'coach')
                ->whereHas('coachProfile', fn($q) => $q->where('is_active', true))
                ->value('id');
        }

        // 3) Als er nog steeds geen coach is, validatiefout i.p.v. null in DB
        if (!$coachUserId) {
            throw ValidationException::withMessages([
                'coach_user_id' => 'Er is nog geen coach gekoppeld aan je profiel en er is geen actieve coach beschikbaar. Koppel eerst een coach.',
            ]);
        }

        // 4) Thread aanmaken
        $thread = Thread::create([
            'client_user_id' => $userId,
            'coach_user_id'  => $coachUserId,
            'subject'        => $data['subject'] ?? null,
        ]);

        // 5) Relaties laden voor de mail
        $thread->load(['clientUser', 'coachUser']);

        // 6) Mail sturen naar de coach van deze thread
        if ($thread->coachUser && $thread->coachUser->email) {
            Mail::to($thread->coachUser->email)
                ->send(new NewThreadNotification($thread));
        }

        // 7) Door naar de detailpagina van het gesprek
        return redirect()->route('client.threads.show', $thread);
    }

    /**
     * Detailweergave van één thread.
     */
    public function show(Thread $thread)
    {
        $this->authorize('view', $thread);

        $thread->load(['clientUser', 'coachUser', 'messages.sender']);

        return view('threads.show', compact('thread'))->with('role', 'client');
    }

    /**
     * Bericht posten binnen een thread.
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
