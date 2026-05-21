<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — FeGArtisan QCM</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpeg">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #FDF6EE; color: #2C1A0E; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        body::before { content:""; position:fixed; inset:0; background:radial-gradient(circle at 20% 20%, rgba(193,123,78,0.10), transparent 50%), radial-gradient(circle at 80% 80%, rgba(107,45,14,0.08), transparent 50%); pointer-events:none; z-index:0; }
        .auth-container { width: 100%; max-width: 420px; position:relative; z-index:1; }
        .auth-card { background: #ffffff; border-radius: 16px; padding: 40px 30px; box-shadow: 0 12px 40px rgba(107,45,14,0.12); border:1px solid rgba(193,123,78,0.18); }
        .auth-header { text-align: center; margin-bottom: 28px; }
        .auth-header img.logo { width: 84px; height: 84px; border-radius: 18px; object-fit: cover; margin-bottom: 16px; box-shadow: 0 8px 24px rgba(107,45,14,0.22); }
        .auth-header h1 { font-size: 24px; margin-bottom: 4px; color: #2C1A0E; font-weight:800; letter-spacing:-0.3px; }
        .auth-header h1 span { color: #C17B4E; }
        .auth-header p { color: #9A7A64; font-size: 13.5px; }
        .tabs { display: flex; margin-bottom: 25px; border-bottom: 2px solid rgba(193,123,78,0.18); }
        .tab { flex: 1; text-align: center; padding: 12px; color: #9A7A64; font-weight: bold; font-size: 14px; border-bottom: 2px solid transparent; margin-bottom: -2px; text-decoration: none; transition:color .2s; }
        .tab:hover { color: #2C1A0E; }
        .tab.active { color: #6B2D0E; border-bottom-color: #6B2D0E; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; color: #5A3A28; margin-bottom: 6px; font-weight:500; }
        .form-group input { width: 100%; padding: 12px 14px; border: 2px solid rgba(193,123,78,0.25); border-radius: 8px; background: #FDF6EE; color: #2C1A0E; font-size: 15px; outline: none; transition:border-color .2s,background .2s; font-family:inherit; }
        .form-group input:focus { border-color: #C17B4E; background:#fff; }
        .pw-wrapper { position:relative; }
        .pw-wrapper input { padding-right:44px; }
        .pw-toggle { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; padding:4px; color:#9A7A64; display:flex; align-items:center; z-index:2; }
        .pw-toggle:hover { color:#6B2D0E; }
        .pw-toggle svg { width:18px; height:18px; }
        .btn-submit { width: 100%; padding: 14px; border: none; border-radius: 8px; background:linear-gradient(135deg,#6B2D0E,#C17B4E); color: #fff; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 6px; transition:filter .2s,transform .15s; box-shadow:0 6px 18px rgba(107,45,14,0.25); }
        .btn-submit:hover { filter:brightness(1.08); transform:translateY(-1px); }
        .message { padding: 11px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 13.5px; }
        .message.error { background: rgba(201,74,58,0.10); border: 1px solid rgba(201,74,58,0.4); color: #8e2e22; }
        .message.success { background: rgba(74,124,89,0.10); border: 1px solid rgba(74,124,89,0.4); color: #2d5a3a; }
        .back-home { display:block; text-align:center; margin-top:18px; color:#9A7A64; font-size:13px; text-decoration:none; }
        .back-home:hover { color:#6B2D0E; }
        @media(max-width:480px) {
            .auth-card { padding: 28px 20px; }
            .auth-header h1 { font-size: 20px; }
            .auth-header img.logo { width: 70px; height: 70px; }
            .form-group input { padding: 10px 12px; font-size: 14px; }
            .btn-submit { padding: 12px; font-size: 15px; }
            .tab { padding: 10px; font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <img src="/logo.jpeg" alt="FeGArtisan" class="logo">
            <h1>FeG<span>Artisan</span> QCM</h1>
            <p>Reviser pour la soutenance</p>
        </div>
        <div class="tabs">
            <a class="tab {{ $mode === 'login' ? 'active' : '' }}" href="/login">Connexion</a>
            <a class="tab {{ $mode === 'register' ? 'active' : '' }}" href="/register">Inscription</a>
        </div>
        @if($errors->any())<div class="message error">{{ $errors->first() }}</div>@endif
        @if(session('success'))<div class="message success">{{ session('success') }}</div>@endif

        @if($mode === 'login')
        <form method="POST" action="/login">
            @csrf
            <div class="form-group"><label>Email</label><input type="email" name="email" required placeholder="votre@email.com" value="{{ old('email') }}"></div>
            <div class="form-group"><label>Mot de passe</label><div class="pw-wrapper"><input type="password" name="password" required placeholder="Votre mot de passe"><button type="button" class="pw-toggle" onclick="togglePw(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
        @else
        <form method="POST" action="/register">
            @csrf
            <div class="form-group"><label>Nom complet</label><input type="text" name="nom" required placeholder="Votre nom" value="{{ old('nom') }}"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required placeholder="votre@email.com" value="{{ old('email') }}"></div>
            <div class="form-group"><label>Mot de passe</label><div class="pw-wrapper"><input type="password" name="password" required placeholder="4 caracteres minimum"><button type="button" class="pw-toggle" onclick="togglePw(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
            <div class="form-group"><label>Confirmer le mot de passe</label><div class="pw-wrapper"><input type="password" name="password_confirmation" required placeholder="Retapez le mot de passe"><button type="button" class="pw-toggle" onclick="togglePw(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
            <button type="submit" class="btn-submit">Creer mon compte</button>
        </form>
        @endif
        <a href="/" class="back-home">&larr; Retour a l'accueil</a>
    </div>
</div>
<script>
function togglePw(btn){
    const input=btn.parentElement.querySelector('input');
    const show=input.type==='password';
    input.type=show?'text':'password';
}
</script>
</body>
</html>
