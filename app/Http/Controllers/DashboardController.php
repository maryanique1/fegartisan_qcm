<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Progress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $userScores = Cache::remember("fega_scores_user_{$user->id}", 60, function () use ($user) {
            return Score::where('user_id', $user->id)
                ->where('qcm_name', 'like', 'fega-%')
                ->select('qcm_name', DB::raw('MAX(percentage) as best'), DB::raw('COUNT(*) as attempts'), DB::raw('MAX(completed_at) as last_at'))
                ->groupBy('qcm_name')
                ->get()
                ->keyBy('qcm_name');
        });

        $totalCompleted = $userScores->count();
        $totalAttempts = $userScores->sum('attempts');
        $avgBest = $totalCompleted > 0 ? round($userScores->avg('best')) : 0;

        $userProgress = Cache::remember("fega_progress_user_{$user->id}", 60, function () use ($user) {
            return Progress::where('user_id', $user->id)
                ->where('qcm_name', 'like', 'fega-%')
                ->get()
                ->keyBy('qcm_name');
        });

        $canCertificate = isset($userScores['fega-exam']) && (int)$userScores['fega-exam']->best >= 80;

        $path_steps = [
            ['name' => 'Intro',     'qcm' => 'fega-intro',   'color' => '#8B3D1A'],
            ['name' => 'Archi',     'qcm' => 'fega-archi',   'color' => '#C17B4E'],
            ['name' => 'Laravel',   'qcm' => 'fega-laravel', 'color' => '#FF2D20'],
            ['name' => 'Flutter',   'qcm' => 'fega-flutter', 'color' => '#0468D7'],
            ['name' => 'Msg',       'qcm' => 'fega-msg',     'color' => '#4A7C59'],
            ['name' => 'BDD',       'qcm' => 'fega-bdd',     'color' => '#00BCD4'],
        ];

        return view('dashboard', compact('user', 'userScores', 'userProgress', 'totalCompleted', 'totalAttempts', 'avgBest', 'canCertificate', 'path_steps'));
    }

    private function getUserData()
    {
        $user = Auth::user();
        $userScores = Cache::remember("fega_scores_user_{$user->id}", 60, fn() =>
            Score::where('user_id', $user->id)
                ->where('qcm_name', 'like', 'fega-%')
                ->select('qcm_name', DB::raw('MAX(percentage) as best'), DB::raw('COUNT(*) as attempts'), DB::raw('MAX(completed_at) as last_at'))
                ->groupBy('qcm_name')->get()->keyBy('qcm_name')
        );
        $userProgress = Cache::remember("fega_progress_user_{$user->id}", 60, fn() =>
            Progress::where('user_id', $user->id)
                ->where('qcm_name', 'like', 'fega-%')
                ->get()->keyBy('qcm_name')
        );
        return compact('user', 'userScores', 'userProgress');
    }

    public function parcours()
    {
        $data = $this->getUserData();
        return view('parcours', $data);
    }

    public function epreuves()
    {
        $data = $this->getUserData();
        $data['canCertificate'] = isset($data['userScores']['fega-exam']) && (int)$data['userScores']['fega-exam']->best >= 80;
        return view('epreuves', $data);
    }
}
