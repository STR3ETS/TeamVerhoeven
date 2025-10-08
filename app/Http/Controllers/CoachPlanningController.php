<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CoachPlanningController extends Controller
{
    public function create(User $client)
    {
        return view('coach.planning.create', compact('client'));
    }
}
