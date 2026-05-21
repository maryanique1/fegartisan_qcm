<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAttempts = Score::where('qcm_name', 'like', 'fega-%')->count();
        $avgScore = (int)Score::where('qcm_name', 'like', 'fega-%')->avg('percentage');
        $popular = Score::where('qcm_name', 'like', 'fega-%')
            ->select('qcm_name', DB::raw('COUNT(*) as cnt'))
            ->groupBy('qcm_name')->orderByDesc('cnt')->first();
        $popularName = $popular ? $popular->qcm_name : '-';

        $allUsers = DB::select("SELECT u.id, u.nom, u.email, u.is_admin, u.created_at,
            COUNT(s.id) as attempts, ROUND(AVG(s.percentage)) as avg_score
            FROM users u
            LEFT JOIN scores s ON u.id = s.user_id AND s.qcm_name LIKE 'fega-%'
            GROUP BY u.id, u.nom, u.email, u.is_admin, u.created_at
            ORDER BY u.created_at DESC");

        $qcmStats = DB::select("SELECT qcm_name, COUNT(*) as attempts, ROUND(AVG(percentage)) as avg_pct, MAX(percentage) as best
            FROM scores WHERE qcm_name LIKE 'fega-%'
            GROUP BY qcm_name ORDER BY attempts DESC");

        return view('admin', compact('totalUsers', 'totalAttempts', 'avgScore', 'popularName', 'allUsers', 'qcmStats'));
    }

    public function toggleAdmin(int $userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', 'Statut admin mis a jour.');
    }

    public function deleteUser(int $userId)
    {
        if ($userId === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous supprimer.']);
        }
        Score::where('user_id', $userId)->where('qcm_name', 'like', 'fega-%')->delete();
        $user = User::findOrFail($userId);
        if (Score::where('user_id', $userId)->count() === 0) {
            $user->delete();
        } else {
            return back()->with('success', 'Scores FeGArtisan supprimes (compte conserve, scores Dev Learn intacts).');
        }
        return back()->with('success', 'Utilisateur supprime.');
    }

    public function deleteScore(int $scoreId)
    {
        Score::findOrFail($scoreId)->delete();
        return back()->with('success', 'Score supprime.');
    }
}
