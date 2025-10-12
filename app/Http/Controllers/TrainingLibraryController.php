<?php
// app/Http/Controllers/TrainingLibraryController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingSection;

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
}
