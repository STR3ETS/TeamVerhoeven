<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\ClientTodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CoachClientTodoController extends Controller
{
    /** Zorg dat ingelogde coach deze client mag beheren */
    private function authorizeCoachForClient(User $client, Request $request): ClientProfile
    {
        $coach   = $request->user();
        $profile = ClientProfile::where('user_id', $client->id)->first();

        if (!$profile) {
            abort(404, 'Profiel niet gevonden.');
        }
        if ($profile->coach_id && (int)$profile->coach_id !== (int)$coach->id) {
            abort(403);
        }
        return $profile;
    }

    /** Taak toevoegen (manual) */
    public function store(User $client, Request $request)
    {
        $this->authorizeCoachForClient($client, $request);

        $data = $request->validate([
            'label'       => ['required','string','max:200'],
            'is_optional' => ['sometimes','boolean'],
            'due_date'    => ['nullable','date'],
            'notes'       => ['nullable','string','max:2000'],
        ]);

        // bepaal position als laatste
        $lastPos = (int) ClientTodoItem::where('client_user_id', $client->id)->max('position');
        $pos     = $lastPos + 10;

        ClientTodoItem::create([
            'client_user_id'     => $client->id,
            'created_by_user_id' => $request->user()->id,
            'label'              => $data['label'],
            'is_optional'        => (bool)($data['is_optional'] ?? false),
            'due_date'           => $data['due_date'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'position'           => $pos,
            'source'             => 'manual',
            'package'            => null,
            'duration_weeks'     => null,
        ]);

        return back()->with('success', 'Taak toegevoegd.');
    }

    /** Toggle complete/undo */
    public function toggle(User $client, ClientTodoItem $todo, Request $request)
    {
        $this->authorizeCoachForClient($client, $request);

        // Veiligheid: item moet bij deze cliënt horen
        if ((int)$todo->client_user_id !== (int)$client->id) {
            abort(404);
        }

        $isCompleted = !is_null($todo->completed_at);
        if ($isCompleted) {
            $todo->completed_at       = null;
            $todo->completed_by_user_id = null;
        } else {
            $todo->completed_at       = now();
            $todo->completed_by_user_id = $request->user()->id;
        }
        $todo->save();

        return back()->with('success', $isCompleted ? 'Taak opnieuw geopend.' : 'Taak afgevinkt.');
    }

    /** Verwijderen (alleen manual of system mag ook – jouw keuze). We laten beide toe. */
    public function destroy(User $client, ClientTodoItem $todo, Request $request)
    {
        $this->authorizeCoachForClient($client, $request);

        if ((int)$todo->client_user_id !== (int)$client->id) {
            abort(404);
        }

        $todo->delete();

        return back()->with('success', 'Taak verwijderd.');
    }

    /** Reorder: verwacht array ids[] in nieuwe volgorde */
    public function reorder(User $client, Request $request)
    {
        $this->authorizeCoachForClient($client, $request);

        $data = $request->validate([
            'ids'   => ['required','array','min:1'],
            'ids.*' => [
                'integer',
                Rule::exists('client_todo_items','id')->where('client_user_id', $client->id),
            ],
        ]);

        DB::transaction(function () use ($data) {
            $pos = 10;
            foreach ($data['ids'] as $id) {
                ClientTodoItem::where('id', $id)->update(['position' => $pos]);
                $pos += 10;
            }
        });

        return response()->json(['ok' => true]);
    }
}
