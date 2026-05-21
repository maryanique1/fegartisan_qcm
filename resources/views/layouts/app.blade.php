@php
    $authUser = auth()->user();
    $userName = $authUser->nom ?? $authUser->name ?? '';
    $canCert = \App\Models\Score::where('user_id', $authUser->id ?? 0)->where('qcm_name','fega-exam')->max('percentage') >= 80;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FeGArtisan QCM')</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpeg">
    <style>
        :root {
            --bg:#FDF6EE; --card:#ffffff; --text:#2C1A0E; --muted:#9A7A64; --dim:#b89a84;
            --border:rgba(193,123,78,0.25); --accent:#6B2D0E; --accent-2:#C17B4E; --input:#F5EDE0; --code:#fff7ee;
            --topbar-bg:#ffffff; --topbar-shadow:0 1px 3px rgba(107,45,14,.08);
            --bg-main:#FDF6EE; --bg-card:#ffffff; --bg-input:#F5EDE0; --bg-code:#fff7ee;
            --text-main:#2C1A0E; --text-muted:#9A7A64; --border-subtle:rgba(193,123,78,0.25);
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        html, body { overflow-x:hidden; width:100%; touch-action:pan-y; }
        body { font-family:'Segoe UI',Arial,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        .main-content { overflow-x:hidden; word-wrap:break-word; overflow-wrap:break-word; }

        .topbar {
            display:flex; align-items:center; justify-content:space-between;
            padding:14px 28px; background:var(--topbar-bg); box-shadow:var(--topbar-shadow); border-bottom:1px solid var(--border);
            position:sticky; top:0; z-index:100; margin-left:280px; transition:margin-left .3s;
        }
        .topbar-brand { display:flex; align-items:center; gap:12px; }
        .topbar-logo { width:36px; height:36px; border-radius:8px; object-fit:cover; box-shadow:0 4px 10px rgba(107,45,14,.15); }
        .topbar-title { font-size:20px; font-weight:800; letter-spacing:-0.5px; color:var(--text); }
        .topbar-title span { color:var(--accent-2); }
        .topbar-right { display:flex; align-items:center; gap:18px; }
        .topbar-user { font-size:14px; color:var(--muted); }
        .topbar-user strong { color:var(--text); font-weight:600; }
        .btn-menu {
            background:none; border:2px solid var(--border); border-radius:10px;
            width:38px; height:38px; cursor:pointer; font-size:20px;
            display:flex; align-items:center; justify-content:center; color:var(--text); transition:border-color .2s;
        }
        .btn-menu:hover { border-color:var(--accent-2); background:rgba(193,123,78,0.1); }

        .sidebar-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; opacity:0; visibility:hidden; transition:opacity .3s, visibility .3s; }
        .sidebar-overlay.open { opacity:1; visibility:visible; }
        .sidebar {
            position:fixed; top:0; left:0; width:280px; height:100%;
            background:var(--card); z-index:201; display:flex; flex-direction:column;
            box-shadow:4px 0 20px rgba(107,45,14,.08); border-right:1px solid var(--border);
            transition:transform .3s ease;
        }
        .sidebar-header { padding:18px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .sidebar-brand { display:flex; align-items:center; gap:12px; flex:1; }
        .sidebar-logo { width:44px; height:44px; border-radius:10px; object-fit:cover; box-shadow:0 4px 10px rgba(107,45,14,.18); flex-shrink:0; }
        .sidebar-brand-text { display:flex; flex-direction:column; line-height:1.1; }
        .sidebar-brand-text h3 { font-size:15px; color:var(--accent); font-weight:800; letter-spacing:-0.3px; }
        .sidebar-brand-text small { font-size:9.5px; color:var(--muted); font-weight:600; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .sidebar-close { background:none; border:none; color:var(--muted); font-size:24px; cursor:pointer; padding:0; line-height:1; display:none; }
        .sidebar-close:hover { color:var(--text); }
        .sidebar-user { padding:20px 24px; border-bottom:1px solid var(--border); }
        .sidebar-avatar {
            width:50px; height:50px; border-radius:50%; background:linear-gradient(135deg,#6B2D0E,#C17B4E);
            display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:bold; color:#fff; margin-bottom:10px;
        }
        .sidebar-user .name { font-weight:700; font-size:16px; color:var(--text); }
        .sidebar-nav { flex:1; padding:12px 0; overflow-y:auto; }
        .sidebar-nav a { display:flex; align-items:center; gap:14px; padding:12px 24px; text-decoration:none; color:var(--text); font-size:14px; transition:background .2s; }
        .sidebar-nav a:hover { background:rgba(193,123,78,0.1); color:var(--accent); }
        .sidebar-nav a.active { background:rgba(193,123,78,0.15); border-right:3px solid var(--accent-2); color:var(--accent); font-weight:600; }
        .sidebar-nav a .icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; color:var(--accent-2); }
        .sidebar-nav .separator { height:1px; background:var(--border); margin:8px 24px; }
        .sidebar-nav a.danger { color:var(--accent); }

        .main-content { margin-left:280px; padding:36px 20px 40px; max-width:1200px; transition:margin-left .3s; }

        @media (max-width:768px) {
            .sidebar { width:75vw; max-width:300px; transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-close { display:flex !important; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:var(--input); font-size:20px; color:var(--text); }
            .topbar { margin-left:0; padding:12px 16px; }
            .global-progress { margin-left:0; }
            .topbar-title { font-size:17px; }
            .topbar-logo { width:32px; height:32px; }
            .main-content { margin-left:0; padding:20px 14px; }
            .btn-menu { display:flex !important; }
        }
        @media (max-width:480px) {
            .topbar { padding:10px 12px; }
            .topbar-title { font-size:15px; }
            .topbar-user { font-size:12px; }
            .topbar-right { gap:10px; }
            .main-content { padding:14px 10px; }
        }
        @media (min-width:769px) {
            .btn-menu { display:none !important; }
        }

        .sidebar-nav a .icon { background:none !important; }
        .sidebar-nav a .icon svg { width:20px; height:20px; }
        .sidebar-nav a .kbd-hint { margin-left:auto; font-size:9.5px; padding:2px 6px; border-radius:4px; background:var(--input); color:var(--muted); font-family:'Consolas',monospace; letter-spacing:0.5px; }

        /* Search modal */
        .search-overlay { position:fixed; inset:0; background:rgba(44,26,14,0.55); z-index:300; opacity:0; visibility:hidden; transition:all .2s; display:flex; align-items:flex-start; justify-content:center; padding-top:80px; }
        .search-overlay.open { opacity:1; visibility:visible; }
        .search-box { width:90%; max-width:640px; background:var(--card); border-radius:14px; box-shadow:0 30px 80px rgba(107,45,14,0.25); border:1px solid var(--border); overflow:hidden; }
        .search-input-wrap { display:flex; align-items:center; padding:14px 18px; border-bottom:1px solid var(--border); gap:12px; }
        .search-input-wrap svg { width:20px; height:20px; color:var(--muted); }
        .search-input { flex:1; border:none; background:none; outline:none; color:var(--text); font-size:16px; font-family:inherit; }
        .search-close { background:var(--input); border:none; border-radius:6px; padding:4px 10px; color:var(--muted); font-size:11px; cursor:pointer; font-family:'Consolas',monospace; }
        .search-results { max-height:50vh; overflow-y:auto; padding:10px 0; }
        .search-result { display:block; padding:11px 18px; text-decoration:none; color:var(--text); border-left:3px solid transparent; }
        .search-result:hover, .search-result.active { background:var(--input); border-left-color:var(--accent-2); }
        .search-result .sr-title { font-size:14px; font-weight:600; }
        .search-result .sr-snippet { font-size:12px; color:var(--muted); margin-top:3px; line-height:1.5; }
        .search-result .sr-tag { display:inline-block; font-size:10px; padding:1px 8px; border-radius:10px; background:var(--accent-2); color:#fff; margin-left:6px; font-weight:600; letter-spacing:0.5px; }
        .search-empty { padding:30px; text-align:center; color:var(--muted); font-style:italic; font-size:13px; }

        .global-progress { height:3px; background:var(--border); margin-left:280px; transition:margin-left .3s; }
        .global-progress-fill { height:100%; background:linear-gradient(90deg, #6B2D0E, #C17B4E, #E8B088); border-radius:0 2px 2px 0; transition:width .8s ease; }

        .btn-container { display:flex; flex-wrap:wrap; justify-content:center; gap:10px; margin-top:20px; }
        .btn-container .btn { margin-left:0 !important; }

        @yield('styles')
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    @yield('head')
</head>
<body>

<div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
        <button class="btn-menu" id="menuToggle" title="Menu">&#9776;</button>
        <div class="topbar-brand">
            <img src="/logo.jpeg" alt="FeGArtisan" class="topbar-logo">
            <div class="topbar-title">FeG<span>Artisan</span> QCM</div>
        </div>
    </div>
    <div class="topbar-right">
        <span class="topbar-user">Bonjour, <strong>{{ $userName }}</strong></span>
    </div>
</div>
@php
    $globalScores = \App\Models\Score::where('user_id', $authUser->id ?? 0)
        ->whereIn('qcm_name', ['fega-intro','fega-archi','fega-laravel','fega-flutter','fega-msg','fega-bdd'])
        ->select('qcm_name', \Illuminate\Support\Facades\DB::raw('MAX(percentage) as best'))
        ->groupBy('qcm_name')->get();
    $globalPct = $globalScores->count() > 0 ? round($globalScores->avg('best')) : 0;
@endphp
<div class="global-progress">
    <div class="global-progress-fill" style="width:{{ $globalPct }}%"></div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="/logo.jpeg" alt="FeGArtisan" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <h3>FeGArtisan</h3>
                <small>Soutenance</small>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">&times;</button>
    </div>
    <div class="sidebar-user">
        @if($authUser->avatar ?? false)
            <img src="{{ $authUser->avatar }}" alt="avatar" style="width:50px;height:50px;border-radius:50%;object-fit:cover;margin-bottom:10px;">
        @else
            <div class="sidebar-avatar">{{ strtoupper(mb_substr($userName, 0, 1)) }}</div>
        @endif
        <div class="name">{{ $userName }}</div>
    </div>
    <nav class="sidebar-nav">
        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}"><span class="icon"><i data-lucide="layout-dashboard"></i></span> Tableau de bord</a>
        <a href="/parcours" class="{{ request()->is('parcours') ? 'active' : '' }}"><span class="icon"><i data-lucide="book-open"></i></span> Parcours</a>
        <a href="/epreuves" class="{{ request()->is('epreuves') ? 'active' : '' }}"><span class="icon"><i data-lucide="file-text"></i></span> Epreuves</a>
        <a href="/classement" class="{{ request()->is('classement') ? 'active' : '' }}"><span class="icon"><i data-lucide="trophy"></i></span> Classement</a>
        <div class="separator"></div>
        <a href="/quiz/oral" class="{{ request()->is('quiz/oral') ? 'active' : '' }}"><span class="icon"><i data-lucide="mic"></i></span> Oral blanc</a>
        <a href="/glossaire" class="{{ request()->is('glossaire') ? 'active' : '' }}"><span class="icon"><i data-lucide="book-marked"></i></span> Glossaire</a>
        <a href="#" onclick="event.preventDefault();openSearch();" class="search-link"><span class="icon"><i data-lucide="search"></i></span> Rechercher <span class="kbd-hint">Ctrl+K</span></a>
        <div class="separator"></div>
        <a href="/profil" class="{{ request()->is('profil') ? 'active' : '' }}"><span class="icon"><i data-lucide="user"></i></span> Mon profil</a>
        @if($canCert)
        <a href="/certificat" class="{{ request()->is('certificat') ? 'active' : '' }}"><span class="icon"><i data-lucide="award"></i></span> Certificat</a>
        @endif
        <div class="separator"></div>
        @if($authUser->is_admin ?? false)
        <a href="/admin" class="{{ request()->is('admin*') ? 'active' : '' }}"><span class="icon"><i data-lucide="shield"></i></span> Administration</a>
        <div class="separator"></div>
        @endif
        <a href="#" class="danger" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><span class="icon"><i data-lucide="log-out"></i></span> Deconnexion</a>
        <form id="logout-form" action="/logout" method="POST" style="display:none">@csrf</form>
    </nav>
</div>

<div class="main-content">
    @yield('content')
</div>

<!-- Search modal Ctrl+K -->
<div class="search-overlay" id="searchOverlay" onclick="if(event.target===this) closeSearch()">
    <div class="search-box">
        <div class="search-input-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un terme, un parcours, une epreuve..." autocomplete="off">
            <button class="search-close" onclick="closeSearch()">Esc</button>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>
</div>

<script>
lucide.createIcons();

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
document.getElementById('menuToggle').addEventListener('click', () => {
    sidebar.classList.add('open');
    overlay.classList.add('open');
});
function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
}
document.getElementById('sidebarClose').addEventListener('click', closeSidebar);
overlay.addEventListener('click', closeSidebar);

// Search modal (Ctrl+K)
const SEARCH_INDEX = [
    {title:"Parcours - Presentation FeGArtisan", url:"/quiz/intro", tag:"Parcours", text:"introduction memoire callconnect cadre stage sondage 71 problematique objectifs cibles"},
    {title:"Parcours - Architecture & stack", url:"/quiz/archi", tag:"Parcours", text:"architecture stack hostinger polling websocket reverb sanctum bearer riverpod go_router dio firebase fcm tidb client serveur 3 niveaux"},
    {title:"Parcours - Backend Laravel", url:"/quiz/laravel", tag:"Parcours", text:"laravel controllers admin api sanctum middleware bearer flux inscription client artisan email verify events listeners auto-discovery firebase push service"},
    {title:"Parcours - App Flutter", url:"/quiz/flutter", tag:"Parcours", text:"flutter dart riverpod notifier asyncnotifier provider futureprovider autodispose family go_router dio video player chewie repositories optimistic update reset state"},
    {title:"Parcours - Messagerie & FCM", url:"/quiz/msg", tag:"Parcours", text:"messagerie temps reel polling 3s firebase cloud messaging fcm lifecycle widgetsbindingobserver dispose token push notification sodium"},
    {title:"Parcours - Base de donnees", url:"/quiz/bdd", tag:"Parcours", text:"base de donnees mysql tidb migrations eloquent relations cles etrangeres soft-delete corbeille schema normalisation cascade unique index"},
    {title:"Epreuve 1 - Flux d'authentification", url:"/quiz/1", tag:"Epreuve", text:"authentification inscription client artisan login email verify bootstrap"},
    {title:"Epreuve 2 - Messagerie & FCM en detail", url:"/quiz/2", tag:"Epreuve", text:"polling fcm push lifecycle dispose timer cancel"},
    {title:"Epreuve 3 - Routes API exhaustives", url:"/quiz/3", tag:"Epreuve", text:"routes api endpoints publiques authentifiees artisan"},
    {title:"Epreuve 4 - Riverpod & state Flutter", url:"/quiz/4", tag:"Epreuve", text:"riverpod state notifier provider invalidate watch read"},
    {title:"Epreuve 5 - Securite & contraintes Hostinger", url:"/quiz/5", tag:"Epreuve", text:"securite sanctum bcrypt hash csrf sodium injection sql mass assignment"},
    {title:"Epreuve 6 - Diagrammes UML & conception", url:"/quiz/6", tag:"Epreuve", text:"uml diagrammes draw.io staruml acteurs cas utilisation classes sequence activite cardinalite"},
    {title:"Epreuve 7 - Tests & deploiement", url:"/quiz/7", tag:"Epreuve", text:"tests unitaires integration fonctionnels securite performance compatibilite hostinger apk aab keystore github actions ci/cd"},
    {title:"Epreuve 8 - Frontend UX & outils", url:"/quiz/8", tag:"Epreuve", text:"frontend ux design system material 3 palette terra cotta responsive accessibilite postman thunder client git github vs code android studio"},
    {title:"Epreuve 9 - Questions pieges & scalabilite", url:"/quiz/9", tag:"Epreuve", text:"pieges scalabilite 10000 utilisateurs bdd tombe pourquoi laravel node django firebase points faibles perf monitoring offline"},
    {title:"Epreuve 10 - Generales & justifications", url:"/quiz/10", tag:"Epreuve", text:"nom fegartisan objectif modules cibles admin super admin difficultes ameliorations startup methodologie 11 semaines"},
    {title:"Examen final 50 questions chronometre", url:"/quiz/exam", tag:"Examen", text:"examen final chronometre 50 questions melange certificat 80%"},
    {title:"Oral blanc - Questions soutenance", url:"/quiz/oral", tag:"Oral", text:"oral blanc soutenance jury 20 questions reveal reponse attendue"},
    {title:"Glossaire de tous les termes techniques", url:"/glossaire", tag:"Reference", text:"glossaire termes techniques sanctum bearer fcm polling sodium tidb laravel flutter riverpod throttle"},
    {title:"Mes parcours et progression", url:"/parcours", tag:"Section", text:"parcours liste themes"},
    {title:"Mes epreuves transverses", url:"/epreuves", tag:"Section", text:"epreuves transverses qcm"},
    {title:"Classement des apprenants", url:"/classement", tag:"Section", text:"classement leaderboard scores apprenants"},
    {title:"Mon profil et statistiques", url:"/profil", tag:"Section", text:"profil avatar mot de passe statistiques historique"},
];

const searchOverlay = document.getElementById('searchOverlay');
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
let searchActiveIdx = 0;
let searchVisible = [];

function openSearch() {
    searchOverlay.classList.add('open');
    setTimeout(() => searchInput.focus(), 50);
    renderSearch('');
}
function closeSearch() {
    searchOverlay.classList.remove('open');
    searchInput.value = '';
}
function renderSearch(q) {
    q = q.trim().toLowerCase();
    if (!q) {
        searchVisible = SEARCH_INDEX.slice(0, 10);
    } else {
        searchVisible = SEARCH_INDEX.filter(it =>
            it.title.toLowerCase().includes(q) || it.text.toLowerCase().includes(q) || it.tag.toLowerCase().includes(q)
        );
    }
    searchActiveIdx = 0;
    if (searchVisible.length === 0) {
        searchResults.innerHTML = '<div class="search-empty">Aucun resultat pour "' + q + '"</div>';
        return;
    }
    searchResults.innerHTML = searchVisible.map((it, i) => {
        let snippet = it.text.length > 90 ? it.text.substring(0, 90) + '...' : it.text;
        return '<a href="' + it.url + '" class="search-result ' + (i === 0 ? 'active' : '') + '"><div class="sr-title">' + it.title + '<span class="sr-tag">' + it.tag + '</span></div><div class="sr-snippet">' + snippet + '</div></a>';
    }).join('');
}
searchInput.addEventListener('input', e => renderSearch(e.target.value));
searchInput.addEventListener('keydown', e => {
    if (e.key === 'ArrowDown') { e.preventDefault(); searchActiveIdx = Math.min(searchActiveIdx + 1, searchVisible.length - 1); updateActive(); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); searchActiveIdx = Math.max(searchActiveIdx - 1, 0); updateActive(); }
    else if (e.key === 'Enter') { if (searchVisible[searchActiveIdx]) window.location.href = searchVisible[searchActiveIdx].url; }
    else if (e.key === 'Escape') closeSearch();
});
function updateActive() {
    searchResults.querySelectorAll('.search-result').forEach((el, i) => el.classList.toggle('active', i === searchActiveIdx));
    const active = searchResults.querySelector('.search-result.active');
    if (active) active.scrollIntoView({block:'nearest'});
}
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openSearch(); }
    else if (e.key === 'Escape' && searchOverlay.classList.contains('open')) closeSearch();
});
</script>
@yield('scripts')
</body>
</html>
