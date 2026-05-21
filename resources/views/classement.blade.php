@extends('layouts.app')
@section('title', 'Classement — FeGArtisan QCM')

@section('styles')
    .container { max-width:900px; margin:0 auto; }
    h1 { text-align:center; color:var(--accent); font-size:26px; margin-bottom:6px; }
    .subtitle { text-align:center; color:var(--muted); font-size:14px; margin-bottom:32px; }
    .my-rank { background:var(--card); border:2px solid var(--accent); border-radius:14px; padding:18px 24px; margin-bottom:24px; text-align:center; font-size:15px; }
    .my-rank strong { color:var(--accent); font-size:18px; }
    table.rank { width:100%; border-collapse:collapse; background:var(--card); border-radius:14px; overflow:hidden; }
    table.rank th, table.rank td { padding:14px 18px; text-align:left; border-bottom:1px solid var(--border); font-size:14px; }
    table.rank th { background:var(--input); color:var(--accent); font-weight:600; text-transform:uppercase; font-size:11px; letter-spacing:1px; }
    .rank-pos { display:inline-block; width:32px; height:32px; border-radius:50%; background:var(--input); color:var(--text); font-weight:700; text-align:center; line-height:32px; }
    .rank-pos.gold { background:#E8A020;color:#fff; } .rank-pos.silver { background:#9A7A64;color:#fff; } .rank-pos.bronze { background:#8B3D1A;color:#fff; }
    .pct-pill { font-weight:700; padding:3px 10px; border-radius:20px; font-size:12px; }
    .pp-g { background:#4A7C59;color:#fff; } .pp-b { background:#0468D7;color:#fff; } .pp-o { background:#E8A020;color:#fff; } .pp-r { background:#C94A3A;color:#fff; }
    tr.me td { background:rgba(193,123,78,0.08); font-weight:600; }
    @media (max-width:600px) { table.rank th:nth-child(4), table.rank td:nth-child(4) { display:none; } table.rank th, table.rank td { padding:10px; font-size:13px; } }
@endsection

@section('content')
<div class="container">
    <h1>Classement</h1>
    <p class="subtitle">{{ $totalUsers }} apprenant{{ $totalUsers > 1 ? 's' : '' }} actif{{ $totalUsers > 1 ? 's' : '' }} sur FeGArtisan QCM</p>

    @if($myRank > 0)
        <div class="my-rank">Vous etes <strong>#{{ $myRank }}</strong> sur {{ $totalUsers }} participants</div>
    @endif

    <table class="rank">
        <tr><th>Pos.</th><th>Apprenant</th><th>Score moyen</th><th>QCM</th></tr>
        @foreach($rankings as $i => $r)
        @php
            $pos = $i + 1;
            $cls = $pos === 1 ? 'gold' : ($pos === 2 ? 'silver' : ($pos === 3 ? 'bronze' : ''));
            $s = (int)$r->avg_score;
            if ($s >= 80) $sc='pp-g'; elseif ($s >= 60) $sc='pp-b'; elseif ($s >= 40) $sc='pp-o'; else $sc='pp-r';
            $isMe = $r->id === auth()->id();
        @endphp
        <tr class="{{ $isMe ? 'me' : '' }}">
            <td><span class="rank-pos {{ $cls }}">{{ $pos }}</span></td>
            <td>{{ $r->nom ?? 'Anonyme' }}{{ $isMe ? ' (vous)' : '' }}</td>
            <td><span class="pct-pill {{ $sc }}">{{ $s }}%</span></td>
            <td>{{ $r->qcm_count }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
