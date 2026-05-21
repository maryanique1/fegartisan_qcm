@extends('layouts.app')
@section('title', 'Epreuves — FeGArtisan QCM')

@section('styles')
    .container { max-width:1060px; margin:0 auto; }
    h1 { text-align:center; margin-bottom:6px; color:var(--accent); font-size:26px; }
    .subtitle { text-align:center; color:var(--muted); font-size:14px; margin-bottom:32px; }
    .section-title { font-size:13px; text-transform:uppercase; letter-spacing:2px; color:var(--dim); margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid var(--border); font-weight:600; }
    .section { margin-bottom:40px; }
    .cards { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; }
    .card { background:var(--card); border-radius:14px; padding:22px; text-decoration:none; color:inherit; display:flex; flex-direction:column; border:2px solid transparent; transition:transform .2s, box-shadow .2s; }
    .card:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(0,0,0,0.3); }
    .card-mix { border-color:rgba(193,123,78,0.2); } .card-mix:hover { border-color:#C17B4E; }
    .card-exam { border-color:rgba(232,160,32,0.2); } .card-exam:hover { border-color:#E8A020; }
    .card-header { display:flex; align-items:center; gap:14px; margin-bottom:12px; }
    .card-logo { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:13px; flex-shrink:0; }
    .logo-mix { background:linear-gradient(135deg, #6B2D0E, #C17B4E); color:#fff; }
    .logo-exam { background:linear-gradient(135deg, #E8A020, #C94A3A); color:#fff; }
    .card-title { font-size:16px; font-weight:bold; }
    .card-title small { display:block; font-size:11px; font-weight:normal; color:var(--muted); margin-top:2px; }
    .card-meta { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px; }
    .meta-tag { font-size:10px; padding:3px 8px; border-radius:20px; background:var(--input); color:var(--muted); }
    .card-score { display:flex; align-items:center; gap:10px; padding-top:10px; border-top:1px solid var(--border); }
    .score-badge { font-size:13px; font-weight:700; padding:4px 12px; border-radius:20px; display:inline-block; }
    .score-green { background:#4A7C59; color:#fff; } .score-blue { background:#0468D7; color:#fff; } .score-orange { background:#E8A020; color:#fff; } .score-red { background:#C94A3A; color:#fff; }
    .score-none { background:transparent; color:var(--dim); font-weight:400; font-size:12px; padding:4px 0; font-style:italic; }
    .score-attempts { font-size:12px; color:var(--dim); }
    @media (max-width:768px) { .cards { grid-template-columns:1fr; } }
@endsection

@section('content')
    <h1>Epreuves & QCM transverses</h1>
    <p class="subtitle">QCM cibles sur les flux et points cles susceptibles d'etre demandes en soutenance</p>

    <div class="section">
        <div class="section-title">Epreuves transverses</div>
        <div class="cards">
            @php
            $qcms = [
                ['slug'=>'1','name'=>'Flux d\'authentification','sub'=>'Inscription client + artisan + email verif','q'=>18,'lvl'=>'Cle','qcm'=>'fega-1'],
                ['slug'=>'2','name'=>'Messagerie & FCM en detail','sub'=>'Polling 3s, lifecycle, push notif','q'=>16,'lvl'=>'Cle','qcm'=>'fega-2'],
                ['slug'=>'3','name'=>'Routes API exhaustives','sub'=>'Publiques + auth + artisan','q'=>20,'lvl'=>'Cle','qcm'=>'fega-3'],
                ['slug'=>'4','name'=>'Riverpod & state Flutter','sub'=>'Providers, Notifiers, invalidate','q'=>16,'lvl'=>'Avance','qcm'=>'fega-4'],
                ['slug'=>'5','name'=>'Securite & contraintes Hostinger','sub'=>'Sanctum, validation, sodium, FCM','q'=>14,'lvl'=>'Avance','qcm'=>'fega-5'],
                ['slug'=>'6','name'=>'Diagrammes UML & conception','sub'=>'Acteurs, cas, classes, sequence','q'=>14,'lvl'=>'Cle','qcm'=>'fega-6'],
                ['slug'=>'7','name'=>'Tests & deploiement','sub'=>'Stack, Hostinger, APK signe','q'=>12,'lvl'=>'Intermediaire','qcm'=>'fega-7'],
                ['slug'=>'8','name'=>'Frontend, UX, design & outils','sub'=>'Material 3, palette, responsive, Postman/Git/StarUML','q'=>18,'lvl'=>'Soutenance','qcm'=>'fega-8'],
                ['slug'=>'9','name'=>'Questions pieges & scalabilite','sub'=>'10k users, BDD down, pourquoi Laravel, points faibles','q'=>16,'lvl'=>'Soutenance','qcm'=>'fega-9'],
                ['slug'=>'10','name'=>'Generales & justifications projet','sub'=>'Nom, modules, roles, ameliorations, ce qu\'on a appris','q'=>18,'lvl'=>'Soutenance','qcm'=>'fega-10'],
            ];
            @endphp
            @foreach($qcms as $qcm)
            <a href="/quiz/{{ $qcm['slug'] }}" class="card card-mix">
                <div class="card-header">
                    <div class="card-logo logo-mix">Mix</div>
                    <div class="card-title">{{ $qcm['name'] }}<small>{{ $qcm['sub'] }}</small></div>
                </div>
                <div class="card-meta">
                    <span class="meta-tag">{{ $qcm['q'] }} questions</span>
                    <span class="meta-tag">{{ $qcm['lvl'] }}</span>
                </div>
                <div class="card-score">@include('partials.score-badge', ['userScores' => $userScores, 'userProgress' => $userProgress, 'qcmName' => $qcm['qcm']])</div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Examen Final</div>
        <div class="cards">
            <a href="/quiz/exam" class="card card-exam">
                <div class="card-header">
                    <div class="card-logo logo-exam">EXAM</div>
                    <div class="card-title">Examen Final FeGArtisan<small>50 questions &bull; 30 min &bull; Chronometre</small></div>
                </div>
                <div class="card-meta">
                    <span class="meta-tag">50 questions</span>
                    <span class="meta-tag">6 themes melanges</span>
                    <span class="meta-tag">Certificat 80%+</span>
                </div>
                <div class="card-score">@include('partials.score-badge', ['userScores' => $userScores, 'userProgress' => $userProgress, 'qcmName' => 'fega-exam'])</div>
            </a>
        </div>
    </div>
@endsection
