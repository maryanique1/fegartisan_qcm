@extends('layouts.app')
@section('title', 'Mon profil — FeGArtisan QCM')

@section('styles')
    .container { max-width:900px; margin:0 auto; }
    h1 { color:var(--accent); font-size:26px; margin-bottom:6px; }
    .subtitle { color:var(--muted); font-size:14px; margin-bottom:32px; }
    .profile-header { display:flex; align-items:center; gap:24px; background:var(--card); padding:28px; border-radius:16px; border:1px solid var(--border); margin-bottom:24px; flex-wrap:wrap; }
    .profile-avatar { width:90px; height:90px; border-radius:50%; background:linear-gradient(135deg,#6B2D0E,#C17B4E); display:flex; align-items:center; justify-content:center; font-size:34px; color:#fff; font-weight:bold; flex-shrink:0; overflow:hidden; }
    .profile-avatar img { width:100%; height:100%; object-fit:cover; }
    .profile-info h2 { font-size:22px; margin-bottom:4px; }
    .profile-info p { color:var(--muted); font-size:14px; }
    .avatar-upload { margin-top:8px; }
    .avatar-upload input { display:none; }
    .avatar-upload label { display:inline-block; padding:6px 14px; background:var(--input); color:var(--accent); border-radius:8px; font-size:12px; cursor:pointer; }
    .avatar-upload label:hover { background:var(--accent); color:#fff; }

    .stats-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:32px; }
    .stat { background:var(--card); border-radius:14px; padding:20px; text-align:center; border:1px solid var(--border); }
    .stat-num { font-size:28px; font-weight:800; color:var(--accent); }
    .stat-lbl { font-size:11px; color:var(--dim); margin-top:6px; text-transform:uppercase; letter-spacing:1px; }

    .section { background:var(--card); border-radius:16px; padding:24px; border:1px solid var(--border); margin-bottom:20px; }
    .section h3 { font-size:16px; margin-bottom:14px; color:var(--accent); }
    .form-row { margin-bottom:14px; }
    .form-row label { display:block; font-size:13px; color:var(--muted); margin-bottom:6px; }
    .form-row input { width:100%; padding:11px 14px; background:var(--input); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:14px; }
    .form-row input:focus { border-color:var(--accent); outline:none; }
    .btn { padding:10px 22px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; }
    .btn:hover { filter:brightness(1.1); }
    .alert-success { padding:12px 16px; background:rgba(74,124,89,0.15); color:#7dc78f; border-radius:8px; font-size:13px; margin-bottom:14px; }
    .alert-error { padding:12px 16px; background:rgba(201,74,58,0.15); color:#C94A3A; border-radius:8px; font-size:13px; margin-bottom:14px; }

    table.scores { width:100%; border-collapse:collapse; }
    table.scores th, table.scores td { padding:10px 14px; text-align:left; border-bottom:1px solid var(--border); font-size:13px; }
    table.scores th { color:var(--accent); font-weight:600; text-transform:uppercase; font-size:11px; letter-spacing:1px; }
    .pct-pill { font-weight:700; padding:2px 10px; border-radius:20px; font-size:12px; display:inline-block; }
    .pp-g { background:#4A7C59;color:#fff; } .pp-b { background:#0468D7;color:#fff; } .pp-o { background:#E8A020;color:#fff; } .pp-r { background:#C94A3A;color:#fff; }

    /* Chart progression 7j */
    .chart-card { background:var(--card); border-radius:16px; padding:24px; border:1px solid var(--border); margin-bottom:20px; }
    .chart-card h3 { font-size:16px; margin-bottom:6px; color:var(--accent); }
    .chart-card .chart-sub { font-size:12px; color:var(--muted); margin-bottom:18px; }
    .chart-wrap { position:relative; height:200px; padding-top:30px; }
    .chart-axis { position:absolute; left:0; right:0; height:1px; background:var(--border); }
    .chart-axis.t { top:30px; }
    .chart-axis.m { top:90px; }
    .chart-axis.b { top:150px; }
    .chart-axis .lbl { position:absolute; left:-32px; top:-9px; font-size:10px; color:var(--muted); font-weight:600; }
    .chart-bars { position:relative; display:flex; align-items:flex-end; gap:8px; height:120px; margin-top:30px; padding-left:6px; padding-right:6px; }
    .chart-day { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; position:relative; }
    .chart-bar-wrap { width:100%; height:120px; display:flex; align-items:flex-end; }
    .chart-bar { width:100%; background:linear-gradient(180deg, var(--accent-2), var(--accent)); border-radius:6px 6px 0 0; min-height:2px; transition:opacity .2s; cursor:default; position:relative; }
    .chart-bar.empty { background:var(--input); }
    .chart-bar-value { position:absolute; top:-20px; left:50%; transform:translateX(-50%); font-size:11px; color:var(--accent); font-weight:700; white-space:nowrap; }
    .chart-bar-count { position:absolute; bottom:4px; left:50%; transform:translateX(-50%); font-size:9px; color:#fff; font-weight:700; }
    .chart-bar:hover { opacity:0.85; }
    .chart-bar.today { box-shadow:0 0 0 2px var(--accent), 0 0 0 4px var(--card); }
    .chart-day-label { font-size:11px; color:var(--muted); text-transform:capitalize; margin-top:6px; font-weight:500; }
    .chart-summary { display:flex; justify-content:space-around; gap:14px; margin-top:18px; padding-top:14px; border-top:1px solid var(--border); flex-wrap:wrap; }
    .chart-summary div { text-align:center; }
    .chart-summary strong { display:block; font-size:18px; color:var(--accent); font-weight:800; }
    .chart-summary span { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; }

    @media (max-width:768px) {
        .stats-grid { grid-template-columns:1fr 1fr; }
        .profile-header { flex-direction:column; text-align:center; }
        table.scores th:nth-child(3), table.scores td:nth-child(3) { display:none; }
    }
@endsection

@section('content')
<div class="container">
    <h1>Mon profil</h1>
    <p class="subtitle">Statistiques personnelles, historique et parametres</p>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

    <div class="profile-header">
        <div class="profile-avatar">
            @if($user->avatar)<img src="{{ $user->avatar }}" alt="avatar">@else{{ strtoupper(mb_substr($user->nom ?? $user->name, 0, 1)) }}@endif
        </div>
        <div class="profile-info">
            <h2>{{ $user->nom ?? $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <form action="/profil/update-avatar" method="POST" enctype="multipart/form-data" class="avatar-upload">
                @csrf
                <label for="avatar-input">Changer la photo</label>
                <input id="avatar-input" type="file" name="avatar" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat"><div class="stat-num">{{ $totalAttempts }}</div><div class="stat-lbl">Tentatives</div></div>
        <div class="stat"><div class="stat-num">{{ $avgPct }}%</div><div class="stat-lbl">Score moyen</div></div>
        <div class="stat"><div class="stat-num">{{ $bestPct }}%</div><div class="stat-lbl">Meilleur score</div></div>
        <div class="stat"><div class="stat-num">{{ floor($totalTime / 60) }}min</div><div class="stat-lbl">Temps total</div></div>
    </div>

    @php
        $chart7Active = collect($chart7Days)->filter(fn($d) => $d['count'] > 0);
        $weekTotal = $chart7Active->sum('count');
        $weekAvg = $chart7Active->count() > 0 ? round($chart7Active->avg('avg')) : 0;
        $weekDays = $chart7Active->count();
    @endphp
    <div class="chart-card">
        <h3>Progression des 7 derniers jours</h3>
        <p class="chart-sub">Score moyen et nombre de tentatives par jour. Releve quotidien pour suivre votre regularite.</p>
        <div class="chart-wrap">
            <div class="chart-axis t"><span class="lbl">100%</span></div>
            <div class="chart-axis m"><span class="lbl">50%</span></div>
            <div class="chart-axis b"><span class="lbl">0%</span></div>
            <div class="chart-bars">
                @foreach($chart7Days as $d)
                    @php $h = $d['avg'] !== null ? max(2, $d['avg'] * 1.2) : 0; @endphp
                    <div class="chart-day">
                        <div class="chart-bar-wrap">
                            @if($d['avg'] !== null)
                                <div class="chart-bar {{ $d['date'] === now()->format('Y-m-d') ? 'today' : '' }}" style="height:{{ $h }}px" title="{{ $d['date'] }} : {{ $d['avg'] }}% sur {{ $d['count'] }} tentatives">
                                    <span class="chart-bar-value">{{ $d['avg'] }}%</span>
                                    @if($d['count'] > 0)<span class="chart-bar-count">{{ $d['count'] }}</span>@endif
                                </div>
                            @else
                                <div class="chart-bar empty" style="height:4px" title="{{ $d['date'] }} : aucune tentative"></div>
                            @endif
                        </div>
                        <div class="chart-day-label">{{ $d['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="chart-summary">
            <div><strong>{{ $weekTotal }}</strong><span>Tentatives 7j</span></div>
            <div><strong>{{ $weekAvg }}%</strong><span>Moyenne 7j</span></div>
            <div><strong>{{ $weekDays }}/7</strong><span>Jours actifs</span></div>
        </div>
    </div>

    <div class="section">
        <h3>Modifier mon nom</h3>
        <form method="POST" action="/profil/update-name">
            @csrf
            <div class="form-row"><label>Nom complet</label><input type="text" name="nom" value="{{ $user->nom ?? $user->name }}" required></div>
            <button class="btn" type="submit">Mettre a jour</button>
        </form>
    </div>

    <div class="section">
        <h3>Changer le mot de passe</h3>
        <form method="POST" action="/profil/update-password">
            @csrf
            <div class="form-row"><label>Ancien mot de passe</label><input type="password" name="old_password" required></div>
            <div class="form-row"><label>Nouveau mot de passe</label><input type="password" name="new_password" required></div>
            <div class="form-row"><label>Confirmer</label><input type="password" name="new_password_confirmation" required></div>
            <button class="btn" type="submit">Changer</button>
        </form>
    </div>

    @if($allScores->count() > 0)
    <div class="section">
        <h3>Historique des scores ({{ $allScores->count() }})</h3>
        <table class="scores">
            <tr><th>QCM</th><th>Score</th><th>Date</th></tr>
            @foreach($allScores->take(30) as $s)
            @php
                $p = (int)$s->percentage;
                if ($p >= 80) $cls='pp-g'; elseif ($p >= 60) $cls='pp-b'; elseif ($p >= 40) $cls='pp-o'; else $cls='pp-r';
            @endphp
            <tr>
                <td>{{ $s->qcm_name }}</td>
                <td><span class="pct-pill {{ $cls }}">{{ $p }}%</span> {{ $s->score }}/{{ $s->total }}</td>
                <td>{{ $s->completed_at?->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>
@endsection
