@extends('layouts.app')
@section('title', 'Parcours — FeGArtisan QCM')

@section('styles')
    .container { max-width:1060px; margin:0 auto; }
    h1 { text-align:center; margin-bottom:6px; color:var(--accent); font-size:26px; }
    .subtitle { text-align:center; color:var(--muted); font-size:14px; margin-bottom:32px; }
    .cards { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; }
    .card { background:var(--card); border-radius:16px; padding:28px; text-decoration:none; color:inherit; display:flex; flex-direction:column; border:2px solid transparent; transition:transform .2s, box-shadow .2s; }
    .card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,0.3); }
    .card-intro { border-color:rgba(139,61,26,0.2); } .card-intro:hover { border-color:#8B3D1A; }
    .card-archi { border-color:rgba(193,123,78,0.2); } .card-archi:hover { border-color:#C17B4E; }
    .card-laravel { border-color:rgba(255,45,32,0.2); } .card-laravel:hover { border-color:#FF2D20; }
    .card-flutter { border-color:rgba(4,104,215,0.2); } .card-flutter:hover { border-color:#0468D7; }
    .card-msg { border-color:rgba(74,124,89,0.2); } .card-msg:hover { border-color:#4A7C59; }
    .card-bdd { border-color:rgba(0,188,212,0.2); } .card-bdd:hover { border-color:#00BCD4; }
    .card-header { display:flex; align-items:center; gap:16px; margin-bottom:14px; }
    .card-logo { width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; flex-shrink:0; color:#fff; }
    .logo-intro { background:#8B3D1A; } .logo-archi { background:#C17B4E; } .logo-laravel { background:#FF2D20; } .logo-flutter { background:#0468D7; } .logo-msg { background:#4A7C59; } .logo-bdd { background:#00BCD4; color:#003040; }
    .card-title { font-size:18px; font-weight:bold; }
    .card-title small { display:block; font-size:12px; font-weight:normal; color:var(--muted); margin-top:2px; }
    .card-desc { color:var(--muted); font-size:14px; line-height:1.6; flex-grow:1; margin-bottom:14px; }
    .card-meta { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px; }
    .meta-tag { font-size:11px; padding:4px 10px; border-radius:20px; background:var(--input); color:var(--muted); }
    .card-score { display:flex; align-items:center; gap:10px; padding-top:12px; border-top:1px solid var(--border); }
    .card-progress { margin-top:10px; margin-bottom:10px; }
    .card-progress-label { font-size:12px; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between; }
    .card-progress-bar { background:var(--input); border-radius:10px; height:8px; overflow:hidden; }
    .card-progress-fill { height:100%; border-radius:10px; transition:width .4s ease; }
    .btn-continue { display:inline-block; margin-top:10px; padding:8px 20px; border-radius:8px; font-size:13px; font-weight:700; color:#fff; text-decoration:none; text-align:center; }
    .card-tools { display:flex; gap:6px; margin-top:10px; padding-top:10px; border-top:1px dashed var(--border); }
    .card-tool { flex:1; text-align:center; padding:6px 8px; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none; color:var(--muted); background:var(--input); transition:all .15s; }
    .card-tool:hover { background:var(--accent-2); color:#fff; }
    .card-tool svg { width:12px; height:12px; vertical-align:middle; margin-right:3px; }
    .score-badge { font-size:13px; font-weight:700; padding:4px 12px; border-radius:20px; display:inline-block; }
    .score-green { background:#4A7C59; color:#fff; } .score-blue { background:#0468D7; color:#fff; } .score-orange { background:#E8A020; color:#fff; } .score-red { background:#C94A3A; color:#fff; }
    .score-none { background:transparent; color:var(--dim); font-weight:400; font-size:12px; padding:4px 0; font-style:italic; }
    .score-attempts { font-size:12px; color:var(--dim); }
    @media (max-width:768px) { .cards { grid-template-columns:1fr; } .card { padding:20px; } }
@endsection

@section('content')
    <h1>Parcours thematiques</h1>
    <p class="subtitle">6 themes structures avec mini-lecons et QCM par chapitre</p>

    <div class="cards">
        @php
        $courses = [
            ['slug'=>'intro','cls'=>'intro','logo'=>'Intro','name'=>'Presentation du projet','desc'=>'Cadre institutionnel, problematique, sondage, objectifs et cibles.','qcm'=>'fega-intro','color'=>'#8B3D1A','chap'=>5,'q'=>25],
            ['slug'=>'archi','cls'=>'archi','logo'=>'Archi','name'=>'Architecture & stack','desc'=>'Vue d\'ensemble, choix Hostinger, polling vs WebSockets, libs Riverpod, go_router.','qcm'=>'fega-archi','color'=>'#C17B4E','chap'=>5,'q'=>25],
            ['slug'=>'laravel','cls'=>'laravel','logo'=>'Laravel','name'=>'Backend Laravel','desc'=>'Controllers, middlewares, Sanctum, validation, events, listeners, services.','qcm'=>'fega-laravel','color'=>'#FF2D20','chap'=>5,'q'=>25],
            ['slug'=>'flutter','cls'=>'flutter','logo'=>'Flutter','name'=>'App Flutter','desc'=>'Riverpod, Notifier/AsyncNotifier, Clean Architecture, go_router, ecrans.','qcm'=>'fega-flutter','color'=>'#0468D7','chap'=>5,'q'=>25],
            ['slug'=>'msg','cls'=>'msg','logo'=>'Msg','name'=>'Messagerie & FCM','desc'=>'Polling 3s, FCM push, reactions emoji, badge non-lus, lifecycle Flutter.','qcm'=>'fega-msg','color'=>'#4A7C59','chap'=>4,'q'=>20],
            ['slug'=>'bdd','cls'=>'bdd','logo'=>'BDD','name'=>'Base de donnees','desc'=>'Schemas, migrations, modeles Eloquent, relations, TiDB, soft-delete, corbeille.','qcm'=>'fega-bdd','color'=>'#00BCD4','chap'=>4,'q'=>20],
        ];
        @endphp
        @foreach($courses as $c)
        <a href="/quiz/{{ $c['slug'] }}" class="card card-{{ $c['cls'] }}">
            <div class="card-header">
                <div class="card-logo logo-{{ $c['cls'] }}">{{ $c['logo'] }}</div>
                <div class="card-title">{{ $c['name'] }}<small>Parcours progressif</small></div>
            </div>
            <div class="card-desc">{{ $c['desc'] }}</div>
            <div class="card-meta">
                <span class="meta-tag">{{ $c['q'] }} questions</span>
                <span class="meta-tag">{{ $c['chap'] }} chapitres</span>
            </div>
            @include('partials.progress-bar', ['userProgress' => $userProgress, 'qcmName' => $c['qcm'], 'color' => $c['color'], 'slug' => $c['slug']])
            <div class="card-score">@include('partials.score-badge', ['userScores' => $userScores, 'userProgress' => $userProgress, 'qcmName' => $c['qcm']])</div>
            <div class="card-tools" onclick="event.stopPropagation();">
                <a href="/fiche/{{ $c['slug'] }}" class="card-tool" onclick="event.stopPropagation();" title="Fiche de revision imprimable">Fiche</a>
                <a href="/flashcards/{{ $c['slug'] }}" class="card-tool" onclick="event.stopPropagation();" title="Memorisation par cartes">Flashcards</a>
            </div>
        </a>
        @endforeach
    </div>
@endsection
