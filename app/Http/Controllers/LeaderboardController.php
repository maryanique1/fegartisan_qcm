<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $rankings = DB::select("
            SELECT u.id, u.nom, u.avatar, ROUND(AVG(best_pct)) as avg_score, COUNT(*) as qcm_count, SUM(attempts) as total_attempts
            FROM users u
            JOIN (
                SELECT user_id, qcm_name, MAX(percentage) as best_pct, COUNT(*) as attempts
                FROM scores
                WHERE qcm_name LIKE 'fega-%'
                GROUP BY user_id, qcm_name
            ) s ON u.id = s.user_id
            GROUP BY u.id, u.nom, u.avatar
            ORDER BY avg_score DESC, qcm_count DESC, total_attempts DESC
            LIMIT 50
        ");

        $totalUsers = DB::selectOne("SELECT COUNT(DISTINCT user_id) as cnt FROM scores WHERE qcm_name LIKE 'fega-%'")->cnt ?? 0;

        $myRank = 0;
        foreach ($rankings as $i => $r) {
            if ($r->id === auth()->id()) {
                $myRank = $i + 1;
                break;
            }
        }

        return view('classement', compact('rankings', 'totalUsers', 'myRank'));
    }

    public function userDetails(int $id)
    {
        $scores = DB::select("
            SELECT qcm_name, MAX(percentage) as best_pct, COUNT(*) as attempts
            FROM scores
            WHERE user_id = ? AND qcm_name LIKE 'fega-%'
            GROUP BY qcm_name
            ORDER BY qcm_name
        ", [$id]);

        return response()->json($scores);
    }
}
