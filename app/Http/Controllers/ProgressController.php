<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgressController extends Controller
{
    public function show(string $qcmName)
    {
        $progress = Progress::where('user_id', auth()->id())
            ->where('qcm_name', $qcmName)
            ->first();

        if ($progress) {
            return response()->json([
                'found' => true,
                'chapter_completed' => $progress->chapter_completed,
                'total_chapters' => $progress->total_chapters,
                'score_so_far' => $progress->score_so_far,
                'total_so_far' => $progress->total_so_far,
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'qcm_name' => 'required|string|max:50',
            'chapter_completed' => 'required|integer',
            'total_chapters' => 'required|integer',
            'score_so_far' => 'nullable|integer',
            'total_so_far' => 'nullable|integer',
        ]);

        if ((int)$data['chapter_completed'] === -1) {
            Progress::where('user_id', auth()->id())
                ->where('qcm_name', $data['qcm_name'])
                ->delete();
            Cache::forget('fega_progress_user_' . auth()->id());
            return response()->json(['success' => true, 'deleted' => true]);
        }

        Progress::updateOrCreate(
            ['user_id' => auth()->id(), 'qcm_name' => $data['qcm_name']],
            [
                'chapter_completed' => $data['chapter_completed'],
                'total_chapters' => $data['total_chapters'],
                'score_so_far' => $data['score_so_far'] ?? 0,
                'total_so_far' => $data['total_so_far'] ?? 0,
            ]
        );

        Cache::forget('fega_progress_user_' . auth()->id());

        return response()->json(['success' => true]);
    }
}
