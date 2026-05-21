<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QcmController extends Controller
{
    private array $validSlugs = [
        'intro', 'archi', 'laravel', 'flutter', 'msg', 'bdd', 'exam', 'oral',
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
    ];

    public function show(string $slug, Request $request)
    {
        return $this->render($slug, $request->query('mode', 'quiz'));
    }

    public function fiche(string $slug)
    {
        return $this->render($slug, 'fiche');
    }

    public function flashcards(string $slug)
    {
        return $this->render($slug, 'flashcards');
    }

    private function render(string $slug, string $mode)
    {
        if (!in_array($slug, $this->validSlugs)) {
            abort(404);
        }
        if (!in_array($mode, ['quiz', 'fiche', 'flashcards'])) {
            $mode = 'quiz';
        }
        $user = Auth::user();
        return view('qcm.' . $slug, compact('user', 'mode'));
    }
}
