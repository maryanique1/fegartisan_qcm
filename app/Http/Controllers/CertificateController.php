<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CertificateController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $exam = Score::where('user_id', $user->id)
            ->where('qcm_name', 'fega-exam')
            ->select(DB::raw('MAX(percentage) as best'), DB::raw('MAX(score) as best_score'), DB::raw('MAX(total) as total'))
            ->first();

        $bestPct = (int)($exam->best ?? 0);
        $bestScore = (int)($exam->best_score ?? 0);
        $total = (int)($exam->total ?? 0);

        if ($bestPct < 80) {
            return redirect('/dashboard');
        }

        $techScores = Score::where('user_id', $user->id)
            ->where('qcm_name', 'like', 'fega-%')
            ->select('qcm_name', DB::raw('MAX(percentage) as best'))
            ->groupBy('qcm_name')
            ->pluck('best', 'qcm_name')
            ->toArray();

        return view('certificat', compact('user', 'bestPct', 'bestScore', 'total', 'techScores'));
    }
}
