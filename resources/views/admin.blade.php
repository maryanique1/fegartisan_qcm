@extends('layouts.app')
@section('title', 'Administration — FeGArtisan QCM')

@section('styles')
    .container { max-width:1080px; margin:0 auto; }
    h1 { color:var(--accent); font-size:26px; margin-bottom:6px; }
    .subtitle { color:var(--muted); font-size:14px; margin-bottom:32px; }
    .stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:32px; }
    .stat { background:var(--card); border-radius:12px; padding:18px; text-align:center; border:1px solid var(--border); }
    .stat-num { font-size:24px; font-weight:800; color:var(--accent); }
    .stat-lbl { font-size:11px; color:var(--dim); text-transform:uppercase; letter-spacing:1px; margin-top:4px; }
    .section { background:var(--card); border-radius:14px; padding:22px; border:1px solid var(--border); margin-bottom:20px; }
    .section h2 { font-size:16px; color:var(--accent); margin-bottom:14px; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    table th, table td { padding:10px 12px; text-align:left; border-bottom:1px solid var(--border); }
    table th { color:var(--accent); text-transform:uppercase; font-size:11px; letter-spacing:1px; }
    .btn-mini { padding:5px 10px; border:none; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; }
    .btn-mini.toggle { background:#0468D7; color:#fff; }
    .btn-mini.del { background:#C94A3A; color:#fff; }
    .badge { padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600; }
    .badge.admin { background:#C17B4E; color:#fff; } .badge.user { background:var(--input); color:var(--muted); }
    @media (max-width:768px) { .stats { grid-template-columns:1fr 1fr; } table th:nth-child(3), table td:nth-child(3), table th:nth-child(4), table td:nth-child(4) { display:none; } }
@endsection

@section('content')
<div class="container">
    <h1>Administration</h1>
    <p class="subtitle">Vue d'ensemble FeGArtisan QCM</p>

    @if(session('success'))<div style="background:rgba(74,124,89,0.15);color:#7dc78f;padding:12px;border-radius:8px;margin-bottom:14px;">{{ session('success') }}</div>@endif

    <div class="stats">
        <div class="stat"><div class="stat-num">{{ $totalUsers }}</div><div class="stat-lbl">Utilisateurs</div></div>
        <div class="stat"><div class="stat-num">{{ $totalAttempts }}</div><div class="stat-lbl">Tentatives FegA</div></div>
        <div class="stat"><div class="stat-num">{{ $avgScore }}%</div><div class="stat-lbl">Score moyen</div></div>
        <div class="stat"><div class="stat-num" style="font-size:14px;">{{ $popularName }}</div><div class="stat-lbl">QCM le + tente</div></div>
    </div>

    <div class="section">
        <h2>Utilisateurs ({{ count($allUsers) }})</h2>
        <table>
            <tr><th>Nom</th><th>Email</th><th>Tentatives</th><th>Score moyen</th><th>Statut</th><th>Actions</th></tr>
            @foreach($allUsers as $u)
            <tr>
                <td>{{ $u->nom }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->attempts }}</td>
                <td>{{ $u->avg_score ?? '-' }}%</td>
                <td><span class="badge {{ $u->is_admin ? 'admin' : 'user' }}">{{ $u->is_admin ? 'Admin' : 'User' }}</span></td>
                <td>
                    <form method="POST" action="/admin/users/{{ $u->id }}/toggle-admin" style="display:inline">@csrf<button class="btn-mini toggle" type="submit">{{ $u->is_admin ? 'Demote' : 'Promote' }}</button></form>
                    <form method="POST" action="/admin/users/{{ $u->id }}" style="display:inline" onsubmit="return confirm('Supprimer ?');">@csrf @method('DELETE')<button class="btn-mini del" type="submit">X</button></form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Stats par QCM</h2>
        <table>
            <tr><th>QCM</th><th>Tentatives</th><th>Moyenne</th><th>Meilleur</th></tr>
            @foreach($qcmStats as $q)
            <tr>
                <td>{{ $q->qcm_name }}</td>
                <td>{{ $q->attempts }}</td>
                <td>{{ $q->avg_pct }}%</td>
                <td>{{ $q->best }}%</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
