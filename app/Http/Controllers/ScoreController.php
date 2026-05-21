<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScoreController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'qcm_name' => 'required|string|max:50',
            'score' => 'required|integer',
            'total' => 'required|integer',
            'percentage' => 'required|integer',
            'duration_seconds' => 'nullable|integer',
        ]);

        $score = Score::create([
            'user_id' => auth()->id(),
            ...$data,
        ]);

        Cache::forget('fega_scores_user_' . auth()->id());

        return response()->json(['success' => true, 'id' => $score->id]);
    }
}
