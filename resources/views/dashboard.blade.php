@extends('layouts.app')
@section('title', 'FeGArtisan QCM — Tableau de bord')

@section('styles')
    .container { max-width:900px; margin:0 auto; }
    .welcome { margin-bottom:36px; }
    .welcome h1 { font-size:28px; font-weight:800; margin-bottom:4px; }
    .welcome h1 span { color:var(--accent); }
    .welcome p { color:var(--muted); font-size:14px; }

    .stats { display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:36px; }
    .stat { background:var(--card); border-radius:14px; padding:22px; text-align:center; border:1px solid var(--border); }
    .stat-num { font-size:32px; font-weight:800; color:var(--accent); line-height:1; }
    .stat-lbl { font-size:11px; color:var(--dim); margin-top:6px; text-transform:uppercase; letter-spacing:1px; }

    .continue-card { background:var(--card); border-radius:16px; padding:28px; margin-bottom:36px; border:1px solid var(--border); display:flex; align-items:center; gap:20px; }
    .continue-icon { width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; color:#fff; }
    .continue-info { flex:1; }
    .continue-info h3 { font-size:17px; font-weight:700; margin-bottom:4px; }
    .continue-info p { font-size:13px; color:var(--muted); }
    .continue-btn { padding:10px 24px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; }

    .path { display:flex; align-items:center; justify-content:center; gap:0; flex-wrap:wrap; padding:20px 0; margin-bottom:36px; }
    .path-step { display:flex; flex-direction:column; align-items:center; gap:6px; }
    .path-circle { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:#fff; border:3px solid transparent; }
    .path-circle.done { box-shadow:0 0 12px rgba(74,124,89,0.4); border-color:#4A7C59; }
    .path-circle .check { font-size:20px; }
    .path-label { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; }
    .path-arrow { width:36px; height:3px; background:var(--border); margin:0 4px; margin-bottom:20px; border-radius:2px; }
    .path-arrow.done { background:#4A7C59; }

    .quick-links { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:36px; }
    .quick-link { background:var(--card); border-radius:14px; padding:24px; text-decoration:none; color:inherit; text-align:center; border:1px solid var(--border); transition:all .2s; }
    .quick-link:hover { border-color:var(--accent); transform:translateY(-3px); }
    .quick-link h3 { font-size:15px; font-weight:700; margin-bottom:4px; }
    .quick-link p { font-size:12px; color:var(--muted); }

    .motivation { background:var(--card); border-radius:16px; padding:32px; text-align:center; border:1px solid var(--border); margin-bottom:36px; }
    .motivation .quote { font-size:18px; font-style:italic; line-height:1.6; margin-bottom:12px; }
    .motivation .author { font-size:13px; color:var(--accent); font-weight:600; }

    .dash-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:36px; }
    .dash-card { background:var(--card); border-radius:16px; padding:24px; border:1px solid var(--border); }
    .dash-card h3 { font-size:14px; font-weight:700; margin-bottom:16px; color:var(--accent); }

    .donut-wrap { display:flex; align-items:center; justify-content:center; gap:24px; }
    .donut { position:relative; width:120px; height:120px; }
    .donut svg { transform:rotate(-90deg); }
    .donut-label { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .donut-pct { font-size:28px; font-weight:800; color:var(--accent); }
    .donut-sub { font-size:10px; color:var(--muted); }
    .donut-legend { font-size:12px; color:var(--muted); line-height:1.9; }
    .donut-legend span { display:inline-block; width:10px; height:10px; border-radius:3px; margin-right:6px; vertical-align:middle; }

    .goal-card { display:flex; align-items:center; gap:16px; }
    .goal-icon { width:52px; height:52px; border-radius:14px; background:rgba(193,123,78,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .goal-text h4 { font-size:15px; font-weight:700; margin-bottom:4px; }
    .goal-text p { font-size:13px; color:var(--muted); line-height:1.5; }
    .goal-btn { display:inline-block; margin-top:10px; padding:8px 20px; background:var(--accent); color:#fff; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; }

    @media (max-width:768px) {
        .stats { grid-template-columns:1fr 1fr 1fr; gap:10px; }
        .stat { padding:16px; } .stat-num { font-size:24px; }
        .continue-card { flex-direction:column; text-align:center; gap:14px; }
        .path { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; } .path-arrow { display:none; }
        .quick-links { grid-template-columns:1fr 1fr; }
        .dash-row { grid-template-columns:1fr; }
    }
    @media (max-width:480px) {
        .welcome h1 { font-size:22px; }
        .stats { grid-template-columns:1fr; }
        .quick-links { grid-template-columns:1fr; }
    }
@endsection

@section('content')
<div class="container">
    @php
        $hour = (int) now()->format('H');
        if ($hour < 12) $greeting = 'Bonjour';
        elseif ($hour < 18) $greeting = 'Bon apres-midi';
        else $greeting = 'Bonsoir';
        $u = auth()->user();
    @endphp
    <div class="welcome">
        <h1>{{ $greeting }}, <span>{{ $u->nom ?? $u->name }}</span></h1>
        <p>Pret a reviser pour la soutenance FeGArtisan ?</p>
    </div>

    <div class="stats">
        <div class="stat"><div class="stat-num">{{ $totalCompleted }}</div><div class="stat-lbl">QCM completes</div></div>
        <div class="stat"><div class="stat-num">{{ $avgBest }}%</div><div class="stat-lbl">Score moyen</div></div>
        <div class="stat"><div class="stat-num">{{ $totalAttempts }}</div><div class="stat-lbl">Tentatives</div></div>
    </div>

    @php
        $techScores = [];
        $techColors = ['Intro'=>'#8B3D1A','Archi'=>'#C17B4E','Laravel'=>'#FF2D20','Flutter'=>'#0468D7','Msg'=>'#4A7C59','BDD'=>'#00BCD4'];
        $techMap = ['fega-intro'=>'Intro','fega-archi'=>'Archi','fega-laravel'=>'Laravel','fega-flutter'=>'Flutter','fega-msg'=>'Msg','fega-bdd'=>'BDD'];
        foreach($techMap as $qcm => $name) {
            $techScores[$name] = isset($userScores[$qcm]) ? (int)$userScores[$qcm]->best : 0;
        }
        $completedTechs = count(array_filter($techScores, fn($s) => $s >= 60));
        $donutPct = round(($completedTechs / 6) * 100);
        $donutDash = round(($donutPct / 100) * 339);

        $weakest = null; $weakScore = 101;
        foreach($techScores as $name => $sc) { if ($sc < $weakScore) { $weakScore = $sc; $weakest = $name; } }
        $weakSlug = array_search($weakest, $techMap) ? str_replace('fega-', '', array_search($weakest, $techMap)) : 'intro';
    @endphp
    <div class="dash-row">
        <div class="dash-card">
            <h3>Progression globale</h3>
            <div class="donut-wrap">
                <div class="donut">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--input)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--accent)" stroke-width="10"
                            stroke-dasharray="{{ $donutDash }} 339" stroke-linecap="round">
                            <animate attributeName="stroke-dasharray" from="0 339" to="{{ $donutDash }} 339" dur="1s" fill="freeze"/>
                        </circle>
                    </svg>
                    <div class="donut-label">
                        <div class="donut-pct">{{ $completedTechs }}/6</div>
                        <div class="donut-sub">valides</div>
                    </div>
                </div>
                <div class="donut-legend">
                    @foreach($techScores as $name => $sc)
                        <div><span style="background:{{ $techColors[$name] }}"></span>{{ $name }} — {{ $sc }}%</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="dash-card">
            <h3>Objectif du jour</h3>
            <div class="goal-card">
                <div class="goal-icon"><i data-lucide="target" style="width:24px;height:24px;color:var(--accent);"></i></div>
                <div class="goal-text">
                    @if($weakScore >= 60 && $completedTechs >= 6)
                        <h4>Tous les parcours valides !</h4>
                        <p>Tentez l'examen final pour obtenir votre certificat.</p>
                        <a href="/quiz/exam" class="goal-btn">Passer l'examen</a>
                    @elseif($weakest)
                        <h4>Renforcer le parcours {{ $weakest }}</h4>
                        <p>Score actuel : {{ $weakScore }}%. Visez 60% pour le valider.</p>
                        <a href="/quiz/{{ $weakSlug }}" class="goal-btn">Commencer</a>
                    @else
                        <h4>Premier parcours</h4>
                        <p>Choisissez un parcours et lancez-vous !</p>
                        <a href="/parcours" class="goal-btn">Voir les parcours</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $lastProgress = $userProgress->filter(fn($p) => $p->chapter_completed > 0 && $p->chapter_completed < $p->total_chapters)->sortByDesc('updated_at')->first();
        $progressMap = [
            'fega-intro' => ['name'=>'Presentation','color'=>'#8B3D1A','slug'=>'intro'],
            'fega-archi' => ['name'=>'Architecture','color'=>'#C17B4E','slug'=>'archi'],
            'fega-laravel' => ['name'=>'Laravel','color'=>'#FF2D20','slug'=>'laravel'],
            'fega-flutter' => ['name'=>'Flutter','color'=>'#0468D7','slug'=>'flutter'],
            'fega-msg' => ['name'=>'Messagerie','color'=>'#4A7C59','slug'=>'msg'],
            'fega-bdd' => ['name'=>'Base de donnees','color'=>'#00BCD4','slug'=>'bdd'],
        ];
    @endphp
    @if($lastProgress && isset($progressMap[$lastProgress->qcm_name]))
        @php $lp = $progressMap[$lastProgress->qcm_name]; @endphp
        <div class="continue-card">
            <div class="continue-icon" style="background:{{ $lp['color'] }}">{{ strtoupper(substr($lp['name'], 0, 4)) }}</div>
            <div class="continue-info">
                <h3>Reprendre {{ $lp['name'] }}</h3>
                <p>Chapitre {{ $lastProgress->chapter_completed + 1 }} / {{ $lastProgress->total_chapters }} &bull; {{ round(($lastProgress->chapter_completed / $lastProgress->total_chapters) * 100) }}% termine</p>
            </div>
            <a href="/quiz/{{ $lp['slug'] }}" class="continue-btn">Continuer</a>
        </div>
    @endif

    <div class="path">
        @foreach($path_steps as $i => $step)
            @php
                $has_score = isset($userScores[$step['qcm']]);
                $best = $has_score ? (float)($userScores[$step['qcm']]->best ?? 0) : 0;
                $passed = $has_score && $best >= 60;
            @endphp
            <div class="path-step">
                <div class="path-circle {{ $passed ? 'done' : '' }}" style="background:{{ $step['color'] }}">
                    @if($passed)<span class="check">&#10003;</span>@elseif($has_score){{ round($best) }}%@else{{ $i+1 }}@endif
                </div>
                <span class="path-label">{{ $step['name'] }}</span>
            </div>
            @if($i < count($path_steps) - 1)
                <div class="path-arrow {{ $passed ? 'done' : '' }}"></div>
            @endif
        @endforeach
    </div>

    <div class="quick-links">
        <a href="/parcours" class="quick-link">
            <div><i data-lucide="book-open" style="width:28px;height:28px;color:var(--accent);"></i></div>
            <h3>Parcours</h3>
            <p>6 themes du projet FeGArtisan</p>
        </a>
        <a href="/epreuves" class="quick-link">
            <div><i data-lucide="file-text" style="width:28px;height:28px;color:var(--accent);"></i></div>
            <h3>Epreuves</h3>
            <p>QCM transverses cibles</p>
        </a>
        <a href="/quiz/exam" class="quick-link">
            <div><i data-lucide="award" style="width:28px;height:28px;color:var(--accent);"></i></div>
            <h3>Examen Final</h3>
            <p>50 questions, simulation oral</p>
        </a>
        <a href="/classement" class="quick-link">
            <div><i data-lucide="trophy" style="width:28px;height:28px;color:var(--accent);"></i></div>
            <h3>Classement</h3>
            <p>Comparer les scores</p>
        </a>
    </div>

    @php
    $quotes = [
        ['text'=>'La meilleure facon de connaitre son projet, c\'est de le tester.','author'=>'Anonyme'],
        ['text'=>'Comprendre le pourquoi est plus puissant que retenir le comment.','author'=>'FeGArtisan'],
        ['text'=>'Une soutenance reussie commence par une preparation rigoureuse.','author'=>'HECM'],
        ['text'=>'Chaque flux applicatif a une raison d\'etre : sachez l\'expliquer.','author'=>'Anonyme'],
        ['text'=>'Hostinger mutualise impose des contraintes : polling et FCM en sont la reponse.','author'=>'FeGArtisan'],
    ];
    $quote = $quotes[array_rand($quotes)];
    @endphp
    <div class="motivation">
        <div class="quote">"{{ $quote['text'] }}"</div>
        <div class="author">— {{ $quote['author'] }}</div>
    </div>

    @if($canCertificate)
    <div style="text-align:center;margin-bottom:24px;">
        <a href="/certificat" style="color:var(--accent);font-weight:700;font-size:14px;text-decoration:none;">Telecharger mon certificat de reussite &rarr;</a>
    </div>
    @endif
</div>
@endsection
