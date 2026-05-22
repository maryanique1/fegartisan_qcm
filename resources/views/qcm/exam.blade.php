@extends('layouts.app')
@section('title', 'Examen Final FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-exam',
        'title' => 'Examen Final FeGArtisan',
        'subtitle' => '50 questions . 30 minutes chronometre . Simulation soutenance',
        'badge' => 'EXAM',
        'color' => '#E8A020',
        'description' => '50 questions tirees aleatoirement de tous les themes (Memoire, Architecture, Laravel, Flutter, Messagerie, BDD, Conception, Securite, Deploiement). <br><strong>Chronometre 30 minutes</strong>. Certificat genere si vous obtenez >= 80%.',
        'is_exam' => true,
        'duration' => 1800,
    ];

    $chapters = [
        ['title' => 'Examen final', 'num' => 1, 'lesson' => ''],
    ];

    $allQuestions = [
        // PRESENTATION (5)
        ['chapter'=>0, 'question'=>"Date officielle de creation de CallConnect SARL ?", 'options'=>['Janvier 2022','30 mai 2023','Mars 2024','Aout 2021'], 'answer'=>1, 'explanation'=>"30 mai 2023, verifiable via le RCCM."],
        ['chapter'=>0, 'question'=>"Interet global du sondage FeGArtisan (71 repondants) ?", 'options'=>['35%','52%','70.4%','90%'], 'answer'=>2, 'explanation'=>"70.4% d'interet global. 72% cote clients, 66.7% cote artisans."],
        ['chapter'=>0, 'question'=>"L'obstacle #1 identifie cote artisans ?", 'options'=>['Concurrence','Saisonnalite','Manque de visibilite (95.2%)','Materiel'], 'answer'=>2, 'explanation'=>"Manque de visibilite avec 95.2%."],
        ['chapter'=>0, 'question'=>"Combien d'objectifs specifiques dans le memoire ?", 'options'=>['2','3','4','6'], 'answer'=>2, 'explanation'=>"4 objectifs : app Android clients, app artisans, messagerie, dashboard admin."],
        ['chapter'=>0, 'question'=>"L'admin se connecte via ?", 'options'=>['App Flutter','Dashboard web Laravel uniquement','SSH','Aucun'], 'answer'=>1, 'explanation'=>"Uniquement le dashboard web. Acces API Flutter intentionnellement bloque pour la securite."],

        // ARCHITECTURE (8)
        ['chapter'=>0, 'question'=>"Pattern d'architecture global ?", 'options'=>['Monolithe','Client-serveur 3 niveaux','Microservices','P2P'], 'answer'=>1, 'explanation'=>"Client-serveur a 3 niveaux."],
        ['chapter'=>0, 'question'=>"Region cloud TiDB ?", 'options'=>['us-east-1','eu-central-1 (Frankfurt)','ap-southeast','sa-east'], 'answer'=>1, 'explanation'=>"Frankfurt pour minimiser latence depuis Hostinger."],
        ['chapter'=>0, 'question'=>"Pourquoi le projet n'utilise pas Reverb ?", 'options'=>['Trop cher','Hostinger mutualise = pas de WebSockets persistants','Bug','Aucune raison'], 'answer'=>1, 'explanation'=>"Reverb necessite un processus persistant impossible sur shared hosting."],
        ['chapter'=>0, 'question'=>"Intervalle de polling messages en chat ouvert ?", 'options'=>['1s','3s','5s','30s'], 'answer'=>1, 'explanation'=>"3 secondes."],
        ['chapter'=>0, 'question'=>"Lib state management Flutter ?", 'options'=>['Provider','flutter_riverpod 2.x','BLoC','Redux'], 'answer'=>1, 'explanation'=>"Riverpod 2.x exclusivement. Pas de provider classique."],
        ['chapter'=>0, 'question'=>"Lib navigation Flutter ?", 'options'=>['Navigator 1','go_router','auto_route','fluro'], 'answer'=>1, 'explanation'=>"go_router avec guards via redirect."],
        ['chapter'=>0, 'question'=>"Ou est stocke le token Sanctum sur le device ?", 'options'=>['SharedPreferences','flutter_secure_storage (Android Keystore/iOS Keychain)','Fichier texte','Dio memory'], 'answer'=>1, 'explanation'=>"flutter_secure_storage chiffre via Keystore/Keychain."],
        ['chapter'=>0, 'question'=>"Limite memoire PHP-FPM Hostinger ?", 'options'=>['64 Mo','128 Mo','256 Mo','512 Mo'], 'answer'=>2, 'explanation'=>"256 Mo. A prendre en compte pour les uploads."],

        // LARAVEL BACKEND (10)
        ['chapter'=>0, 'question'=>"Ou sont les controllers admin ?", 'options'=>['app/Http/Controllers/','app/Http/Controllers/Admin/','app/Admin/','aucun'], 'answer'=>1, 'explanation'=>"app/Http/Controllers/Admin/ - separes de Api/."],
        ['chapter'=>0, 'question'=>"Throttle de POST /api/register/client ?", 'options'=>['5/min','10/min','3/min','illimite'], 'answer'=>1, 'explanation'=>"10/min par IP."],
        ['chapter'=>0, 'question'=>"Throttle de POST /api/login ?", 'options'=>['5/min','10/min','3/min','illimite'], 'answer'=>0, 'explanation'=>"5/min (anti brute-force)."],
        ['chapter'=>0, 'question'=>"Throttle de POST /api/forgot-password ?", 'options'=>['1/min','3/min','5/min','10/min'], 'answer'=>1, 'explanation'=>"3/min (strict anti spam reset)."],
        ['chapter'=>0, 'question'=>"Que fait LogRequestDuration ?", 'options'=>['Logge IP','Mesure temps + nb queries SQL, detecte N+1 (>30)','Cache requests','Compte les visites'], 'answer'=>1, 'explanation'=>"Log dans storage/logs/performance-YYYY-MM-DD.log. Dump SQL detaille si >10 queries."],
        ['chapter'=>0, 'question'=>"Que fait TouchLastSeen ?", 'options'=>['UPDATE last_seen_at a chaque requete authentifiee (throttle 1/min/user)','Cache','rien','Logge IP'], 'answer'=>0, 'explanation'=>"Pour l'indicateur En ligne/Hors ligne. Throttle via cache."],
        ['chapter'=>0, 'question'=>"Listeners de NewNotification (Laravel 12 auto-discovery) ?", 'options'=>['1','2 (PersistNotification + SendFcmForNotification, synchrones)','3','aucun'], 'answer'=>1, 'explanation'=>"2 listeners synchrones (pas ShouldQueue car Hostinger sans queue worker)."],
        ['chapter'=>0, 'question'=>"Pourquoi listeners synchrones ?", 'options'=>['Plus simple','Hostinger sans queue:work persistant','Plus rapide','Aucune raison'], 'answer'=>1, 'explanation'=>"Hostinger shared n'a pas de queue worker persistant."],
        ['chapter'=>0, 'question'=>"Bug historique des listeners ?", 'options'=>['Aucun','Double enregistrement (auto-discovery + manuel) = listeners 2x par event','Trop lents','Permission denied'], 'answer'=>1, 'explanation'=>"Fix : auto-discovery uniquement, pas de Event::listen() manuel."],
        ['chapter'=>0, 'question'=>"Service singleton pour les push FCM ?", 'options'=>['FCMService','FirebasePushService','PushNotifier','Notifier'], 'answer'=>1, 'explanation'=>"FirebasePushService. Methode sendToUser(\$userId, \$title, \$body, \$data)."],

        // FLUTTER (10)
        ['chapter'=>0, 'question'=>"Quel widget enveloppe l'app pour Riverpod ?", 'options'=>['MaterialApp','ProviderScope','RiverpodApp','Wrapper'], 'answer'=>1, 'explanation'=>"runApp(ProviderScope(child: ...))."],
        ['chapter'=>0, 'question'=>"Type de provider pour singleton sans etat mutable ?", 'options'=>['Provider','NotifierProvider','AsyncNotifierProvider','StreamProvider'], 'answer'=>0, 'explanation'=>"Provider pour services, repositories."],
        ['chapter'=>0, 'question'=>"Provider parametre par ID, libere quand sort ?", 'options'=>['Provider','NotifierProvider','FutureProvider.autoDispose.family','StreamProvider'], 'answer'=>2, 'explanation'=>"autoDispose libere quand plus d'ecouteur, family pour le parametre."],
        ['chapter'=>0, 'question'=>"3 etats principaux d'AuthState ?", 'options'=>['Loading/Success/Error','AuthUnknown/AuthUnauthenticated/AuthAuthenticated(user)','On/Off/Pending','start/middle/end'], 'answer'=>1, 'explanation'=>"AuthUnknown au demarrage, puis bootstrap decide."],
        ['chapter'=>0, 'question'=>"Comment Flutter detecte un email verifie sans relogin ?", 'options'=>['WebSocket','didChangeAppLifecycleState(resumed) => refreshUser() => GET /api/me','Polling 1s','Manuel'], 'answer'=>1, 'explanation'=>"L'observer detecte resumed et rafraichit le user."],
        ['chapter'=>0, 'question'=>"Pourquoi NE PAS invalider publicationsProvider apres un like ?", 'options'=>['Trop lent','Detruit AnimationController du LikeBtn = scintillement','Riverpod l\'interdit','aucune'], 'answer'=>1, 'explanation'=>"Utiliser override local _likeOverrides[id]."],
        ['chapter'=>0, 'question'=>"Pattern critique a chaque transition d'auth ?", 'options'=>['Reload app','_resetUserScopedState() invalide tous les providers user-dependants','Logout','rien'], 'answer'=>1, 'explanation'=>"Securite : empeche un nouveau user de voir le cache du precedent."],
        ['chapter'=>0, 'question'=>"Toggle emoji long-press meme emoji deja pose ?", 'options'=>['INSERT doublon','DELETE (retire)','UPDATE','Erreur'], 'answer'=>1, 'explanation'=>"Toggle intelligent : meme=DELETE, autre=UPDATE, aucun=INSERT."],
        ['chapter'=>0, 'question'=>"Video dans le feed : comportement par defaut au scroll ?", 'options'=>['Muted auto-play (setVolume 0)','Son active','Pause','Aucune lecture'], 'answer'=>0, 'explanation'=>"Auto-play muet par defaut. Tap = ChewieController avec son."],
        ['chapter'=>0, 'question'=>"Que doit obligatoirement faire dispose() du ChatScreen ?", 'options'=>['rien','_timer?.cancel() + removeObserver(this)','reload','Logout'], 'answer'=>1, 'explanation'=>"Critique : sinon fuite memoire + requetes inutiles."],

        // MESSAGERIE & FCM (5)
        ['chapter'=>0, 'question'=>"Endpoint polling messages ?", 'options'=>['GET /api/poll','GET /api/conversations/{id}/messages?after={lastId}','POST /api/since','WebSocket'], 'answer'=>1, 'explanation'=>"Retourne uniquement id > lastId. Reponse legere."],
        ['chapter'=>0, 'question'=>"Sur AppLifecycleState.paused, le ChatScreen ?", 'options'=>['Continue polling','Cancel timer pour laisser FCM prendre le relais','Crash','Force resume'], 'answer'=>1, 'explanation'=>"FCM gere les messages en background, plus efficace energie."],
        ['chapter'=>0, 'question'=>"Handler FCM background : annotation requise ?", 'options'=>['@override','@pragma(\\'vm:entry-point\\')','@static','aucune'], 'answer'=>1, 'explanation'=>"@pragma(vm:entry-point) car la fonction est appelee depuis Dart VM sans context d'app."],
        ['chapter'=>0, 'question'=>"Package PHP Firebase compatible PHP 8.2 ?", 'options'=>['kreait/firebase-php ^8.0','kreait/firebase-php ^7.16','firebase/php-jwt','google/cloud-firestore'], 'answer'=>1, 'explanation'=>"^7.16. La 8.x exige PHP 8.3+."],
        ['chapter'=>0, 'question'=>"Extension PHP requise pour Firebase ?", 'options'=>['gd','sodium','imagick','mbstring'], 'answer'=>1, 'explanation'=>"sodium pour le JWT OAuth2."],

        // BDD (6)
        ['chapter'=>0, 'question'=>"SGBD production FeGArtisan ?", 'options'=>['PostgreSQL','TiDB Cloud (MySQL compatible)','SQLite','MongoDB'], 'answer'=>1, 'explanation'=>"TiDB Cloud Serverless region eu-central-1."],
        ['chapter'=>0, 'question'=>"Statuts validation_status d'un artisan ?", 'options'=>['active/inactive','pending/approved/rejected/suspended','new/ok','accepted/refused'], 'answer'=>1, 'explanation'=>"4 statuts. Default = pending."],
        ['chapter'=>0, 'question'=>"Cle unique sur message_reactions ?", 'options'=>['emoji','(message_id, user_id) - une seule reaction par user par message','message_id seul','user_id seul'], 'answer'=>1, 'explanation'=>"Cle du toggle intelligent."],
        ['chapter'=>0, 'question'=>"Pour activer la galerie multi-media par publication ?", 'options'=>['JSON dans publications.media','Table publication_media avec position','Cloud externe','Impossible'], 'answer'=>1, 'explanation'=>"Table dediee publication_media avec position pour l'ordre."],
        ['chapter'=>0, 'question'=>"Soft-delete sur combien de modeles ?", 'options'=>['3','6 (users, artisan_profiles, publications, comments, reviews, categories)','10','tous'], 'answer'=>1, 'explanation'=>"6 modeles avec colonne deleted_at indexee."],
        ['chapter'=>0, 'question'=>"Duree de la corbeille avant purge ?", 'options'=>['7 jours','30 jours','90 jours','illimite'], 'answer'=>1, 'explanation'=>"30 jours. Commande trash:purge --days=30 tourne quotidiennement."],

        // SECURITE & UML (6)
        ['chapter'=>0, 'question'=>"Hash mots de passe ?", 'options'=>['md5','Hash::make() (bcrypt)','sha1','plain'], 'answer'=>1, 'explanation'=>"bcrypt via Hash::make(). Cast 'password' => 'hashed' dans le modele User."],
        ['chapter'=>0, 'question'=>"Auth API ?", 'options'=>['Passport','Sanctum (Bearer tokens, PersonalAccessToken override pour last_used_at)','JWT custom','Cookies'], 'answer'=>1, 'explanation'=>"Sanctum, override pour throttler last_used_at 1/min/token."],
        ['chapter'=>0, 'question'=>"Protection injections SQL ?", 'options'=>['Manuel','Eloquent ORM + prepared statements','Filtre IP','Aucune'], 'answer'=>1, 'explanation'=>"Eloquent utilise des prepared statements automatiquement."],
        ['chapter'=>0, 'question'=>"Acteurs UML du contexte statique ?", 'options'=>['1','3','4 (Client, Artisan, Admin, Super Admin)','10'], 'answer'=>2, 'explanation'=>"4 acteurs. Super Admin unique (multiplicite 1)."],
        ['chapter'=>0, 'question'=>"Outils principaux des diagrammes UML ?", 'options'=>['Visio','Draw.io + StarUML (pour le diagramme de classes)','Lucidchart','PlantUML'], 'answer'=>1, 'explanation'=>"Draw.io pour la plupart, StarUML pour le diagramme de classes."],
        ['chapter'=>0, 'question'=>"Sequence 'rechercher artisan' : criteres principaux ?", 'options'=>['nom et age','categorie et quartier','photo et tarif','aucun'], 'answer'=>1, 'explanation'=>"Categorie + quartier. Le reste est dans le profil artisan."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
