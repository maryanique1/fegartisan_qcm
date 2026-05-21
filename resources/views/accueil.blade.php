<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeGArtisan QCM — Reviser pour la soutenance</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpeg">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { overflow-x: hidden; width: 100%; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #FDF6EE; color: #2C1A0E; min-height: 100vh; }

        .navbar { display:flex; align-items:center; justify-content:space-between; padding:18px 48px; position:fixed; top:0; left:0; right:0; z-index:100; background:rgba(253,246,238,0.92); backdrop-filter:blur(16px); border-bottom:1px solid rgba(193,123,78,0.18); }
        .navbar-brand { display:flex; align-items:center; gap:12px; font-size:22px; font-weight:800; color:#2C1A0E; letter-spacing:-0.5px; }
        .navbar-brand img { width:38px; height:38px; border-radius:9px; object-fit:cover; box-shadow:0 4px 12px rgba(107,45,14,.18); }
        .navbar-brand span { color:#C17B4E; }
        .nav-links { display:flex; gap:12px; align-items:center; }
        .nav-links a { color:#9A7A64; text-decoration:none; font-size:14px; font-weight:500; transition:color .2s; }
        .nav-links a:hover { color:#6B2D0E; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:all .25s; font-family:inherit; }
        .btn-accent { background:linear-gradient(135deg,#6B2D0E,#C17B4E); color:#fff; box-shadow:0 4px 12px rgba(107,45,14,.2); }
        .btn-accent:hover { filter:brightness(1.08); transform:translateY(-2px); box-shadow:0 8px 24px rgba(107,45,14,0.25); }
        .btn-outline { background:transparent; border:1.5px solid rgba(193,123,78,0.4); color:#2C1A0E; }
        .btn-outline:hover { border-color:#6B2D0E; color:#6B2D0E; background:rgba(193,123,78,0.06); }
        .btn-lg { padding:16px 36px; font-size:16px; border-radius:10px; }

        .hero { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:140px 48px 100px; gap:80px; max-width:1200px; margin:0 auto; }
        .hero-text { flex:1; max-width:620px; }
        .hero-badge { display:inline-flex; align-items:center; gap:8px; background:rgba(193,123,78,0.12); border:1px solid rgba(193,123,78,0.28); border-radius:50px; padding:8px 18px; font-size:13px; color:#6B2D0E; font-weight:600; margin-bottom:28px; }
        .hero-text h1 { font-size:48px; font-weight:800; line-height:1.1; margin-bottom:24px; letter-spacing:-1.5px; color:#2C1A0E; }
        .hero-text h1 span { color:#C17B4E; }
        .hero-sub { font-size:17px; color:#5A3A28; line-height:1.8; margin-bottom:36px; }
        .hero-buttons { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:48px; }
        .hero-metrics { display:flex; gap:40px; padding-top:32px; border-top:1px solid rgba(193,123,78,0.2); flex-wrap:wrap; }
        .metric-num { font-size:32px; font-weight:800; color:#6B2D0E; }
        .metric-lbl { font-size:13px; color:#9A7A64; margin-top:4px; }
        .hero-visual { flex:1; max-width:420px; display:flex; align-items:center; justify-content:center; position:relative; }
        .hero-logo-wrap { width:100%; aspect-ratio:1; max-width:380px; border-radius:32px; background:linear-gradient(135deg, rgba(193,123,78,0.18), rgba(232,176,136,0.10)); padding:36px; display:flex; align-items:center; justify-content:center; box-shadow:0 30px 80px rgba(107,45,14,0.18); border:1px solid rgba(193,123,78,0.18); }
        .hero-logo-wrap img { width:100%; height:100%; object-fit:cover; border-radius:24px; box-shadow:0 20px 50px rgba(107,45,14,0.25); }

        .section { padding:100px 48px; max-width:1200px; margin:0 auto; }
        .section-alt { background:#F5EDE0; }
        .section-header { text-align:center; margin-bottom:64px; }
        .section-header h2 { font-size:38px; font-weight:800; margin-bottom:14px; letter-spacing:-1px; color:#2C1A0E; }
        .section-header h2 span { color:#C17B4E; }
        .section-header p { color:#9A7A64; font-size:16px; max-width:540px; margin:0 auto; }

        .steps-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:24px; }
        .step-card { background:#ffffff; border-radius:16px; padding:36px 28px; border:1px solid rgba(193,123,78,0.15); transition:all .3s; box-shadow:0 4px 16px rgba(107,45,14,0.04); }
        .step-card:hover { border-color:rgba(193,123,78,0.35); transform:translateY(-4px); box-shadow:0 12px 32px rgba(107,45,14,0.10); }
        .step-num { font-size:48px; font-weight:800; color:rgba(193,123,78,0.22); margin-bottom:16px; line-height:1; }
        .step-icon { width:46px; height:46px; border-radius:12px; background:rgba(193,123,78,0.14); display:flex; align-items:center; justify-content:center; color:#6B2D0E; margin-bottom:16px; }
        .step-card h3 { font-size:18px; font-weight:700; margin-bottom:10px; color:#2C1A0E; }
        .step-card p { color:#5A3A28; font-size:14px; line-height:1.7; }

        .techs-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:16px; }
        .tech-card { background:#ffffff; border-radius:14px; padding:28px 16px; text-align:center; border:1px solid rgba(193,123,78,0.15); transition:all .3s; box-shadow:0 2px 8px rgba(107,45,14,0.03); }
        .tech-card:hover { border-color:#C17B4E; transform:translateY(-4px); box-shadow:0 12px 28px rgba(107,45,14,0.10); }
        .tech-logo { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; margin:0 auto 14px; color:#fff; }
        .tech-card h4 { font-size:15px; font-weight:700; margin-bottom:4px; color:#2C1A0E; }
        .tech-card p { font-size:11px; color:#9A7A64; }

        .cta { text-align:center; padding:120px 48px; position:relative; }
        .cta h2 { font-size:40px; font-weight:800; margin-bottom:16px; letter-spacing:-1px; color:#2C1A0E; }
        .cta h2 span { color:#C17B4E; }
        .cta p { color:#9A7A64; font-size:16px; margin-bottom:36px; }

        .footer { text-align:center; padding:28px; border-top:1px solid rgba(193,123,78,0.2); color:#9A7A64; font-size:13px; background:#F5EDE0; }

        .reveal { opacity:0; transform:translateY(30px); transition:opacity .7s ease, transform .7s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }
        .reveal:nth-child(2) { transition-delay:.1s; }
        .reveal:nth-child(3) { transition-delay:.2s; }

        .mobile-toggle { display:none; background:none; border:2px solid rgba(193,123,78,0.35); border-radius:8px; padding:8px 10px; cursor:pointer; color:#2C1A0E; }
        .nav-links.open { display:flex !important; flex-direction:column; position:absolute; top:100%; left:0; right:0; background:rgba(253,246,238,0.98); padding:20px; gap:12px; border-bottom:1px solid rgba(193,123,78,0.2); }

        @media (max-width:900px) {
            .hero { flex-direction:column; padding:130px 24px 60px; gap:40px; text-align:center; }
            .hero-buttons, .hero-metrics { justify-content:center; }
            .hero-text h1 { font-size:34px; }
            .hero-visual { max-width:100%; }
            .navbar { padding:16px 20px; }
            .nav-links { display:none; }
            .mobile-toggle { display:block; }
            .section { padding:60px 24px; }
            .section-header h2 { font-size:28px; }
            .cta { padding:80px 24px; }
            .cta h2 { font-size:28px; }
        }
        @media (max-width:480px) {
            .navbar { padding:14px 16px; }
            .navbar-brand { font-size:18px; }
            .navbar-brand img { width:32px; height:32px; }
            .hero-text h1 { font-size:26px; }
            .hero-sub { font-size:14px; }
            .btn-lg { padding:14px 24px; font-size:14px; }
            .hero-metrics { gap:24px; }
            .metric-num { font-size:24px; }
            .hero-logo-wrap { padding:24px; border-radius:24px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <img src="/logo.jpeg" alt="FeGArtisan">
        <span style="color:#2C1A0E;">FeG<span style="color:#C17B4E;">Artisan</span> QCM</span>
    </div>
    <button class="mobile-toggle" id="mobileToggle"><i data-lucide="menu" style="width:20px;height:20px;"></i></button>
    <div class="nav-links" id="navLinks">
        <a href="/login" class="btn btn-outline" style="padding:10px 20px;"><i data-lucide="log-in" style="width:15px;height:15px;"></i> Connexion</a>
        <a href="/register" class="btn btn-accent" style="padding:10px 20px;"><i data-lucide="arrow-right" style="width:15px;height:15px;"></i> Inscription</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-text">
        <div class="hero-badge"><i data-lucide="graduation-cap" style="width:16px;height:16px;"></i> Plateforme de revision soutenance</div>
        <h1>Maitrisez votre projet <span>FeGArtisan</span> en vous testant</h1>
        <p class="hero-sub">6 parcours thematiques (Architecture, Laravel, Flutter, Messagerie, BDD, Memoire), 10 epreuves transverses (flux, API, securite, UX, pieges) et un examen final pour valider la soutenance.</p>
        <div class="hero-buttons">
            <a href="/register" class="btn btn-accent btn-lg"><i data-lucide="arrow-right" style="width:18px;height:18px;"></i> Commencer maintenant</a>
            <a href="/login" class="btn btn-outline btn-lg">J'ai deja un compte</a>
        </div>
        <div class="hero-metrics">
            <div><div class="metric-num">6</div><div class="metric-lbl">Parcours</div></div>
            <div><div class="metric-num">10</div><div class="metric-lbl">Epreuves</div></div>
            <div><div class="metric-num">360+</div><div class="metric-lbl">Questions</div></div>
            <div><div class="metric-num">1</div><div class="metric-lbl">Examen final</div></div>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-logo-wrap"><img src="/logo.jpeg" alt="FeGArtisan logo"></div>
    </div>
</section>

<div class="section-alt">
<section class="section">
    <div class="section-header reveal">
        <h2>3 etapes pour <span>maitriser</span> la soutenance</h2>
        <p>Un parcours pense pour reviser efficacement avant le jour J</p>
    </div>
    <div class="steps-grid">
        <div class="step-card reveal">
            <div class="step-num">01</div>
            <div class="step-icon"><i data-lucide="book-open"></i></div>
            <h3>Apprenez chapitre par chapitre</h3>
            <p>Chaque parcours debute par une mini-lecon avec exemples concrets tires du projet FeGArtisan reel.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">02</div>
            <div class="step-icon"><i data-lucide="check-circle"></i></div>
            <h3>Testez vos connaissances</h3>
            <p>QCM progressifs avec explications detaillees apres chaque reponse, et mode revision des erreurs.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">03</div>
            <div class="step-icon"><i data-lucide="award"></i></div>
            <h3>Validez avec l'examen final</h3>
            <p>50 questions chronometrees pour simuler l'oral. Un certificat est genere a partir de 80%.</p>
        </div>
    </div>
</section>
</div>

<section class="section">
    <div class="section-header reveal">
        <h2>6 <span>parcours</span> couvrant tout le projet</h2>
        <p>De l'architecture haut niveau aux details techniques des flux applicatifs</p>
    </div>
    <div class="techs-grid">
        <div class="tech-card reveal"><div class="tech-logo" style="background:#8B3D1A;">Intro</div><h4>Presentation</h4><p>Cadre, problemes, sondage</p></div>
        <div class="tech-card reveal"><div class="tech-logo" style="background:#C17B4E;">Archi</div><h4>Architecture</h4><p>Stack, schemas, choix</p></div>
        <div class="tech-card reveal"><div class="tech-logo" style="background:#FF2D20;">Laravel</div><h4>Backend Laravel</h4><p>Controllers, flux, securite</p></div>
        <div class="tech-card reveal"><div class="tech-logo" style="background:#0468D7;">Flutter</div><h4>App Flutter</h4><p>Riverpod, ecrans, routing</p></div>
        <div class="tech-card reveal"><div class="tech-logo" style="background:#4A7C59;">Msg</div><h4>Messagerie</h4><p>Polling, FCM, reactions</p></div>
        <div class="tech-card reveal"><div class="tech-logo" style="background:#00BCD4;color:#003040;">BDD</div><h4>Base de donnees</h4><p>Migrations, modeles, TiDB</p></div>
    </div>
</section>

<section class="cta">
    <h2 class="reveal">Pret a reviser pour la <span>soutenance</span> ?</h2>
    <p class="reveal">Creez votre compte et commencez par le parcours qui vous semble le plus important.</p>
    <a href="/register" class="btn btn-accent btn-lg reveal"><i data-lucide="arrow-right" style="width:18px;height:18px;"></i> Creer mon compte</a>
</section>

<footer class="footer">FeGArtisan QCM &mdash; Plateforme de revision dediee au memoire FeGArtisan (HECM 2026)</footer>

<script>
lucide.createIcons();
document.getElementById('mobileToggle').addEventListener('click', () => {
    document.getElementById('navLinks').classList.toggle('open');
});
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.15 });
reveals.forEach(el => observer.observe(el));
</script>
</body>
</html>
