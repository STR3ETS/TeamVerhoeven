<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

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
