<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoachPlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_if(!$user || !$user->isCoach(), 404);
        
        return view('coach.plans.index');
    }
}
