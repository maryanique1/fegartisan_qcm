<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $allScores = Score::where('user_id', $user->id)
            ->where('qcm_name', 'like', 'fega-%')
            ->orderByDesc('completed_at')->get();

        $totalAttempts = $allScores->count();
        $avgPct = $totalAttempts > 0 ? round($allScores->avg('percentage')) : 0;
        $bestPct = $totalAttempts > 0 ? $allScores->max('percentage') : 0;
        $totalTime = $allScores->sum('duration_seconds');

        $bestPerQcm = [];
        foreach ($allScores as $s) {
            if (!isset($bestPerQcm[$s->qcm_name]) || $s->percentage > $bestPerQcm[$s->qcm_name]) {
                $bestPerQcm[$s->qcm_name] = (int)$s->percentage;
            }
        }

        $progress = Progress::where('user_id', $user->id)
            ->where('qcm_name', 'like', 'fega-%')
            ->get()->keyBy('qcm_name');

        $qcmColors = [
            'fega-intro' => '#8B3D1A', 'fega-archi' => '#C17B4E', 'fega-laravel' => '#FF2D20',
            'fega-flutter' => '#0468D7', 'fega-msg' => '#4A7C59', 'fega-bdd' => '#00BCD4',
            'fega-exam' => '#ff9800',
        ];

        // Progression 7 derniers jours : score moyen + nombre de tentatives
        $chart7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayScores = $allScores->filter(fn($s) => $s->completed_at && $s->completed_at->isSameDay($date));
            $chart7Days[] = [
                'date'   => $date->format('Y-m-d'),
                'label'  => $date->isToday() ? "Auj." : $date->locale('fr')->isoFormat('dd D/MM'),
                'avg'    => $dayScores->count() > 0 ? round($dayScores->avg('percentage')) : null,
                'count'  => $dayScores->count(),
            ];
        }

        return view('profil', compact('user', 'allScores', 'totalAttempts', 'avgPct', 'bestPct', 'totalTime', 'bestPerQcm', 'progress', 'qcmColors', 'chart7Days'));
    }

    public function updateName(Request $request)
    {
        $request->validate(['nom' => 'required|max:100']);
        $user = Auth::user();
        $user->update(['nom' => $request->nom, 'name' => $request->nom]);
        return back()->with('success', 'Nom mis a jour.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Ancien mot de passe incorrect.']);
        }

        $request->validate([
            'new_password' => 'required|min:4|confirmed',
        ], [
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'new_password.min' => 'Le nouveau mot de passe doit faire au moins 4 caracteres.',
        ]);

        $user->update(['password' => $request->new_password]);
        return back()->with('success', 'Mot de passe mis a jour.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);
        $user = Auth::user();

        $file = $request->file('avatar');
        $mime = $file->getMimeType();
        $base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        $user->update(['avatar' => $base64]);
        return back()->with('success', 'Photo de profil mise a jour.');
    }

    public function updateBio(Request $request)
    {
        $request->validate(['bio' => 'nullable|max:500']);
        Auth::user()->update(['bio' => $request->bio]);
        return back()->with('success', 'Bio mise a jour.');
    }
}
