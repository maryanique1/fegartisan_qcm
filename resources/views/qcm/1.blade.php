@extends('layouts.app')
@section('title', 'Epreuve 1 : Flux d\'authentification')

@php
    $config = [
        'qcm_key' => 'fega-1',
        'title' => 'Epreuve 1 : Flux d\'authentification',
        'subtitle' => '18 questions . QCM transverse . Inscription, login, email verify, artisan en 2 etapes',
        'badge' => 'Auth',
        'color' => '#C17B4E',
        'description' => 'Detail des 3 flux d\'auth : inscription client + email verify, inscription artisan en 2 etapes + validation admin, login + bootstrap au demarrage.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Authentification complete (3 flux principaux)', 'num' => 1, 'lesson' => '
            <p>Cette epreuve teste votre maitrise des 3 <em>flux</em> (sequences d\'evenements coordonnes entre le mobile et le serveur) d\'<em>authentification</em> (verification de l\'identite d\'un utilisateur) du projet.</p>
            <p><strong>1. Inscription client :</strong> un seul appel <em>POST</em> (verbe HTTP de creation), le token Sanctum est pose immediatement, puis le <em>router</em> Flutter (composant qui gere la navigation) verrouille sur la page <code>/email-verify</code> jusqu\'a confirmation de l\'email.</p>
            <p><strong>2. Inscription artisan :</strong> deux etapes successives (infos personnelles puis upload du justificatif), suivies d\'une <em>validation manuelle</em> par un administrateur et de la verification email.</p>
            <p><strong>3. Bootstrap au demarrage :</strong> sequence d\'initialisation de l\'app — etat <em>AuthUnknown</em> (inconnu) au demarrage =&gt; lecture du token stocke =&gt; appel <code>GET /api/me</code> =&gt; bascule vers <em>AuthAuthenticated</em> (utilisateur valide) ou <em>AuthUnauthenticated</em> (pas connecte).</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Quel est le throttle applique a POST /api/register/client ?", 'options'=>['1/min', '5/min', '10/min', 'Aucun'], 'answer'=>2, 'explanation'=>"<strong>10/min</strong> par IP (<code>throttle:10,1</code>)."],
        ['chapter'=>0, 'question'=>"L'inscription client se fait en combien d'appels API ?", 'options'=>['1', '2', '3', '4'], 'answer'=>0, 'explanation'=>"<strong>Un seul</strong> POST /api/register/client. Les 3 etapes Flutter du formulaire ne sont qu\'un decoupage UI."],
        ['chapter'=>0, 'question'=>"A la creation d'un compte client, email_verified_at vaut ?", 'options'=>['now()', 'NULL', 'L\'email lui-meme', 'true'], 'answer'=>1, 'explanation'=>"NULL. Mis a now() apres click sur le lien de verif."],
        ['chapter'=>0, 'question'=>"Quelle methode envoie le mail de verification au client ?", 'options'=>['$user->sendEmail()', '$user->notify(new VerifyEmailNotification)', 'Mail::raw(...)', 'Aucune'], 'answer'=>1, 'explanation'=>"<code>$user-&gt;notify(new VerifyEmailNotification)</code>. Mail Brevo SMTP avec URL signee temporaire (7 jours)."],
        ['chapter'=>0, 'question'=>"Le verrou /email-verify dans Flutter se base sur quel getter ?", 'options'=>['user.isClient', 'user.isWaitingEmailVerify (isClientWaitingEmailVerify || isArtisanWaitingEmailVerify)', 'user.tokenExpired', 'user.isPending'], 'answer'=>1, 'explanation'=>"<code>isWaitingEmailVerify</code> qui combine <code>isClientWaitingEmailVerify</code> (client && !verified) et <code>isArtisanWaitingEmailVerify</code> (artisan && approved && !verified)."],
        ['chapter'=>0, 'question'=>"Comment Flutter detecte qu'un email vient d'etre verifie ?", 'options'=>['WebSocket', 'didChangeAppLifecycleState(resumed) => authController.refreshUser() => GET /api/me', 'Polling', 'Manuel par bouton'], 'answer'=>1, 'explanation'=>"Au foreground, l\'observer detecte resumed et appelle <code>refreshUser()</code>."],
        ['chapter'=>0, 'question'=>"Inscription artisan etape 1 est-elle authentifiee ?", 'options'=>['Oui', 'Non, publique (cree le User + token)', 'Seulement avec recaptcha', 'Optionnelle'], 'answer'=>1, 'explanation'=>"<strong>Publique</strong>. Cree le User (role=artisan, email_verified_at=NULL) et retourne le token. L\'etape 2 sera authentifiee."],
        ['chapter'=>0, 'question'=>"Inscription artisan etape 2 : ou est uploade le justificatif ?", 'options'=>['storage/app/public/proofs/ (public)', 'storage/app/private/proofs/ (prive disque local)', 'storage/firebase/', 'CDN externe'], 'answer'=>1, 'explanation'=>"<code>storage/app/private/proofs/</code>, hors du dossier public. Accessible uniquement via le controller admin <code>downloadProofDocument</code>."],
        ['chapter'=>0, 'question'=>"Quels types de fichiers sont acceptes pour le justificatif ?", 'options'=>['JPG seulement', 'PDF/JPG/PNG, max 5 Mo', 'Tout type', 'DOC/DOCX'], 'answer'=>1, 'explanation'=>"PDF, JPG ou PNG. Max <strong>5 Mo</strong>. Le proof_type est diplome | certificat | preuve_experience."],
        ['chapter'=>0, 'question'=>"Quel mail est envoye apres validation admin de l'artisan ?", 'options'=>['Aucun', 'ArtisanApprovedMail (avec URL signee /email/verify)', 'WelcomeMail', 'Receipt'], 'answer'=>1, 'explanation'=>"<code>Mail::to($user)-&gt;send(new ArtisanApprovedMail)</code> avec URL signee /email/verify."],
        ['chapter'=>0, 'question'=>"Que peut faire un artisan approved mais non email-verifie ?", 'options'=>['Tout', 'Rien, bloque sur /email-verify', 'Lire seulement', 'Publier sans contrainte'], 'answer'=>1, 'explanation'=>"<strong>Bloque sur /email-verify</strong> par le router Flutter. La verification email est obligatoire avant /home."],
        ['chapter'=>0, 'question'=>"Que se passe-t-il au login si l'utilisateur a is_active=false ?", 'options'=>['Login normal', 'Le middleware EnsureUserIsActive supprime le token + 403 "Compte suspendu"', 'Email automatique', 'Crash'], 'answer'=>1, 'explanation'=>"<code>EnsureUserIsActive</code> intervient apres le login et supprime le token + 403."],
        ['chapter'=>0, 'question'=>"Au bootstrap si GET /api/me renvoie 401, que fait AuthController ?", 'options'=>['Retry', 'storage.clear() + state = AuthUnauthenticated', 'Logout total', 'Crash'], 'answer'=>1, 'explanation'=>"Sur 401/403 : nettoyage du token corrompu et passage en AuthUnauthenticated."],
        ['chapter'=>0, 'question'=>"Quel throttle pour POST /api/login ?", 'options'=>['1/min', '5/min', '10/min', 'illimite'], 'answer'=>1, 'explanation'=>"<strong>5/min</strong> par IP pour POST /api/login (anti brute-force). 10/min pour les inscriptions."],
        ['chapter'=>0, 'question'=>"Quel throttle pour POST /api/forgot-password ?", 'options'=>['1/min', '3/min', '5/min', '10/min'], 'answer'=>1, 'explanation'=>"<strong>3/min</strong> pour POST /api/forgot-password (plus strict, anti spam reset)."],
        ['chapter'=>0, 'question'=>"Apres bootstrap reussi, quelle action est appelee automatiquement ?", 'options'=>['rien', '_syncPushToken() => POST /me/fcm-token', 'analytics', 'fetch profile photo'], 'answer'=>1, 'explanation'=>"<code>_syncPushToken()</code> enregistre le token FCM du device en base pour permettre les push notifications."],
        ['chapter'=>0, 'question'=>"DELETE /api/me supprime-t-il dur ou soft ?", 'options'=>['Dur', 'Soft (corbeille 30j RGPD)', 'Soft + email', 'Aucun'], 'answer'=>1, 'explanation'=>"<strong>Soft-delete</strong> (RGPD). L'enregistrement entre en corbeille pour 30 jours avant purge definitive."],
        ['chapter'=>0, 'question'=>"Le router Flutter expose-t-il sa logique d'auth en clair dans les ecrans ?", 'options'=>['Oui', 'Non, via go_router redirect qui watch authControllerProvider', 'Hardcode in main.dart', 'C\'est interdit'], 'answer'=>1, 'explanation'=>"La logique est dans <code>redirect</code> de go_router qui watch <code>authControllerProvider</code>. Les ecrans n'ont pas a connaitre l'etat d'auth."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
