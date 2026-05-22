@extends('layouts.app')
@section('title', 'Epreuve 5 : Securite & contraintes Hostinger')

@php
    $config = [
        'qcm_key' => 'fega-5',
        'title' => 'Epreuve 5 : Securite & contraintes Hostinger',
        'subtitle' => '14 questions . Avance . Sanctum, validation, sodium, deploiement',
        'badge' => 'Securite',
        'color' => '#C94A3A',
        'description' => 'Comment FeGArtisan se protege : Sanctum tokens, hash bcrypt, CSRF, validation, et toutes les contraintes Hostinger mutualise (pas de queue worker, pas exec, etc.).',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Securite et contraintes de production', 'num' => 1, 'lesson' => '
            <p>FeGArtisan tourne en production sur <em>Hostinger mutualise</em> (hebergement partage entre plusieurs sites sur la meme machine). Cela impose des choix de <em>securite</em> (protection des donnees et des comptes) et d\'<em>architecture</em> (organisation technique) tres specifiques.</p>
            <p>Cette epreuve couvre : le hash des mots de passe avec <em>bcrypt</em>, l\'authentification par Bearer token <em>Sanctum</em>, la protection contre les <em>injections SQL</em>, le <em>CSRF</em> (Cross-Site Request Forgery, attaque par fausse requete), les contraintes Hostinger (pas de queue worker, pas de WebSocket, extension <em>sodium</em> obligatoire pour FCM).</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Quelle facade Laravel hash les mots de passe ?", 'options'=>['Crypt::encrypt()', 'Hash::make() (bcrypt par defaut)', 'sha1()', 'md5()'], 'answer'=>1, 'explanation'=>"<code>Hash::make(\$password)</code> utilise <strong>bcrypt</strong> par defaut. Le cast <code>'password' =&gt; 'hashed'</code> dans le modele User automatise ca."],
        ['chapter'=>0, 'question'=>"Quel package gere l'auth API par tokens ?", 'options'=>['Laravel Passport', 'Laravel Sanctum', 'JWT-Auth', 'Custom'], 'answer'=>1, 'explanation'=>"<strong>Laravel Sanctum</strong>. Bearer tokens stateless. Le modele PersonalAccessToken est overridee pour throttler last_used_at."],
        ['chapter'=>0, 'question'=>"L'ORM Eloquent prevent-il les injections SQL ?", 'options'=>['Non', 'Oui via prepared statements', 'Partiellement', 'Seulement avec un plugin'], 'answer'=>1, 'explanation'=>"Oui. Eloquent utilise des <strong>prepared statements</strong>. Memes les arguments des methodes <code>where</code> sont parametres."],
        ['chapter'=>0, 'question'=>"Quel middleware protege le dashboard admin contre CSRF ?", 'options'=>['Aucun', 'Le middleware web Laravel inclut VerifyCsrfToken automatiquement', 'CORS', 'admin'], 'answer'=>1, 'explanation'=>"Le groupe middleware <strong>web</strong> de Laravel inclut <code>VerifyCsrfToken</code>. Tous les forms POST utilisent <code>@csrf</code>."],
        ['chapter'=>0, 'question'=>"Pourquoi l'API n'utilise PAS CSRF mais des Bearer tokens ?", 'options'=>["Plus simple a coder", "Stateless, adapte aux apps mobiles qui n'ont pas de cookies de session", "Mode rapide", "Aucun"], 'answer'=>1, 'explanation'=>"Bearer tokens sont <strong>stateless</strong>. Pas besoin de cookie de session => parfait pour Flutter. CSRF ne protege que les apps web stateful."],
        ['chapter'=>0, 'question'=>"Sur Hostinger, peut-on utiliser <code>php artisan queue:work</code> ?", 'options'=>['Oui', 'Non, pas de processus persistant => listeners synchrones obligatoires', 'Avec config speciale', 'Que pour les emails'], 'answer'=>1, 'explanation'=>"<strong>Non</strong>. Le processus ne survit pas. Tous les listeners FCM sont <strong>synchrones</strong> (pas de <code>ShouldQueue</code>)."],
        ['chapter'=>0, 'question'=>"Comment cree-t-on le lien storage:link sur Hostinger ?", 'options'=>['php artisan storage:link', 'Manuellement via ln -s (exec() est desactive)', 'Automatique au deploiement', 'Pas necessaire'], 'answer'=>1, 'explanation'=>"<code>ln -s ~/fegartisan/storage/app/public ~/fegartisan/public/storage</code> via SSH. Pas <code>storage:link</code> car <code>exec()</code> est desactive."],
        ['chapter'=>0, 'question'=>"Driver de session/cache recommande sur Hostinger ?", 'options'=>['database', 'redis', 'file (disque local)', 'memcached'], 'answer'=>2, 'explanation'=>"<strong>file</strong>. Evite que chaque Cache::has/put ou session genere une query DB additionnelle."],
        ['chapter'=>0, 'question'=>"Limite memoire PHP-FPM Hostinger mutualise ?", 'options'=>['64 Mo', '128 Mo', '256 Mo', '512 Mo'], 'answer'=>2, 'explanation'=>"<strong>256 Mo</strong>. Important pour les uploads d\'images / videos artisan."],
        ['chapter'=>0, 'question'=>"Comment le scheduler tourne-t-il sur Hostinger ?", 'options'=>['php artisan schedule:work', 'Cron hPanel : * * * * * php artisan schedule:run', 'Auto', 'Via systemd'], 'answer'=>1, 'explanation'=>"Un seul cron <code>* * * * *</code> qui execute <code>schedule:run</code>. Laravel relit <code>routes/console.php</code> chaque minute."],
        ['chapter'=>0, 'question'=>"Quel fichier credentials Firebase est-il critique de NE JAMAIS commiter ?", 'options'=>['fcm.json', 'storage/firebase/service-account.json', '.env', 'firebase-config.js'], 'answer'=>1, 'explanation'=>"<code>storage/firebase/service-account.json</code>. C'est une clef maitresse OAuth2. Le .gitignore l'exclut. Upload manuel via SCP."],
        ['chapter'=>0, 'question'=>"Quelle extension PHP est requise pour signer le JWT FCM ?", 'options'=>['gd', 'sodium', 'mcrypt', 'openssl seulement'], 'answer'=>1, 'explanation'=>"<strong>sodium</strong>. Sans : erreurs JWT Firebase. Verif avec <code>php -m | findstr sodium</code>."],
        ['chapter'=>0, 'question'=>"Comment Flutter chiffre-t-il le token Sanctum sur le device ?", 'options'=>['SharedPreferences', 'flutter_secure_storage (Android Keystore / iOS Keychain)', 'fichier texte', 'pas chiffre'], 'answer'=>1, 'explanation'=>"<code>flutter_secure_storage</code> utilise Android Keystore et iOS Keychain pour chiffrer."],
        ['chapter'=>0, 'question'=>"L'admin peut-il forcer la suppression sans email ?", 'options'=>['Oui directement DELETE', 'Soft-delete (corbeille 30j) puis trash:purge ou force-delete via UI corbeille', 'Non, jamais', 'Email automatique'], 'answer'=>1, 'explanation'=>"Suppression admin = soft-delete par defaut. La corbeille <code>/admin/trash</code> permet restore ou force-delete avec confirmation."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
