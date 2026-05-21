@extends('layouts.app')
@section('title', 'Architecture & Stack FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-archi',
        'title' => 'Architecture et stack technique',
        'subtitle' => '25 questions . 5 chapitres . Comprendre les choix techniques',
        'badge' => 'ARCHI',
        'color' => '#C17B4E',
        'description' => 'Vue d\'ensemble client-serveur, contraintes Hostinger mutualise, polling vs WebSockets, librairies clefs Flutter et Laravel, et la structure interne du code.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Vue d\'ensemble et acteurs', 'num' => 1, 'lesson' => '
            <p>FeGArtisan repose sur une architecture <strong>client-serveur a 3 niveaux</strong> (presentation, logique metier, donnees), avec deux clients distincts qui dialoguent avec le meme <em>backend</em> (serveur applicatif central) :</p>
            <div class="code-example">      App Flutter (Android / iOS)              Dashboard Admin (navigateur)
            |                                          |
            +------- API REST JSON --------+-- session cookie ---+
                                           v
                                   Laravel 12 (PHP 8.2)
                                       /          \
                              TiDB Cloud         Firebase FCM
                              (eu-central-1)      (push notifs)</div>

            <p><strong>3 composants principaux :</strong></p>
            <ul>
                <li><strong>Laravel 12</strong> (framework PHP, derniere version majeure) : coeur du projet. Il expose une <em>API REST</em> (interface de programmation suivant les principes REST, qui repond en JSON) pour Flutter, et un <em>dashboard</em> (tableau de bord web) <em>Blade</em> (moteur de templates HTML natif de Laravel) pour l\'administrateur.</li>
                <li><strong>TiDB Cloud (Frankfurt)</strong> : base de donnees MySQL <em>serverless</em> (sans serveur a gerer soi-meme, facturee a l\'usage), distribuee horizontalement. Choisie dans la region europeenne <code>eu-central-1</code> pour minimiser la <em>latence</em> (temps aller-retour reseau) depuis les serveurs Hostinger.</li>
                <li><strong>Firebase FCM</strong> (Firebase Cloud Messaging, service Google gratuit) : envoie des <em>push notifications</em> (notifications systeme qui apparaissent meme quand l\'app est fermee) vers les telephones Android. Voie principale pour le temps reel quand l\'utilisateur n\'est pas dans l\'application.</li>
            </ul>

            <p><strong>Securite et authentification de l\'API :</strong></p>
            <p><strong>Laravel Sanctum</strong> (extension officielle Laravel qui gere l\'authentification par jetons pour les API) genere les <strong>Bearer tokens</strong> (chaines de caracteres secretes envoyees dans l\'en-tete HTTP <code>Authorization: Bearer &lt;token&gt;</code> a chaque requete API, pour prouver l\'identite de l\'utilisateur).</p>
            <p>Le modele <code>PersonalAccessToken</code> (classe Eloquent qui represente un jeton en base) est <em>override</em> (remplace par une version personnalisee) pour <em>throttler</em> (limiter, espacer) les ecritures sur la colonne <code>last_used_at</code> (horodatage de la derniere utilisation du jeton) a un maximum d\'<strong>1 UPDATE par minute et par jeton</strong>. Sans cette limitation, chaque requete API authentifiee declencherait une requete SQL <code>UPDATE</code> supplementaire vers TiDB, doublant la charge sur la base de donnees.</p>
        '],
        ['title' => 'Contraintes de l\'hebergement Hostinger mutualise', 'num' => 2, 'lesson' => '
            <p>Le projet a ete conçu des le depart pour fonctionner sur <strong>Hostinger shared hosting</strong> (offre d\'hebergement mutualise, ou plusieurs sites partagent la meme machine). Cette plateforme est economique mais impose des contraintes techniques fortes qu\'il ne faut <strong>jamais</strong> tenter de contourner :</p>
            <ul>
                <li><strong>Aucun WebSocket persistant</strong> (les WebSockets sont des connexions HTTP qui restent ouvertes en continu entre client et serveur) — par consequent <strong>Laravel Reverb</strong> (serveur WebSocket officiel de Laravel) est impossible a installer.</li>
                <li><strong>Aucun queue worker persistant</strong> (un <em>queue worker</em> est un processus qui consomme les jobs en arriere-plan) — la commande <code>php artisan queue:work</code> est tuee des qu\'elle depasse quelques secondes.</li>
                <li><strong>Fonction <code>exec()</code> desactivee</strong> (la fonction PHP qui permet d\'executer des commandes systeme) — la commande <code>php artisan storage:link</code> (qui cree un lien entre <code>public/storage</code> et <code>storage/app/public</code>) doit donc etre executee manuellement via SSH avec la commande Unix <code>ln -s</code>.</li>
                <li><strong>Limite memoire PHP-FPM</strong> (FastCGI Process Manager, le gestionnaire de processus PHP qui sert les requetes) plafonnee a <strong>256 Mo</strong> par requete — contrainte forte pour les uploads d\'images et de videos artisans.</li>
            </ul>

            <p><strong>Consequences techniques directes :</strong></p>
            <ul>
                <li>Les <em>listeners</em> FCM (classes Laravel qui ecoutent les evenements et envoient les push Firebase Cloud Messaging) sont declares <strong>synchrones</strong> — ils ne portent pas l\'interface <code>ShouldQueue</code> (marqueur Laravel qui sinon les enverrait dans la file d\'attente).</li>
                <li>Pour la communication en <em>temps reel</em> dans la messagerie, on combine deux mecanismes complementaires : du <strong>polling court</strong> (sondage HTTP toutes les 3 secondes quand le chat est ouvert) + des <strong>push FCM</strong> (notifications Firebase quand l\'application est en arriere-plan).</li>
                <li>Le <em>scheduler</em> Laravel (planificateur de taches recurrentes defini dans <code>routes/console.php</code>) est declenche via une unique tache <em>cron</em> (planificateur de taches Unix) configuree dans le hPanel Hostinger : <code>* * * * * cd ~/fegartisan && /usr/bin/php artisan schedule:run</code> (execution toutes les minutes).</li>
                <li>Les sessions et le cache sont stockes en mode <strong>file</strong> (fichiers serialises sur le disque local) plutot qu\'en base de donnees — cela evite qu\'une simple lecture de session ne declenche une requete SQL additionnelle vers TiDB Cloud.</li>
            </ul>

            <div class="tip">En environnement de developpement local, on peut conserver <code>SESSION_DRIVER=database</code> (commode pour inspecter les sessions via phpMyAdmin). En production sur Hostinger, on bascule sur <code>SESSION_DRIVER=file</code> pour eviter cette query SQL a chaque requete authentifiee — gain de performance significatif vu la latence du cluster TiDB europeen.</div>
        '],
        ['title' => 'Polling HTTP vs WebSockets', 'num' => 3, 'lesson' => '
            <p>Le besoin de <strong>messagerie en temps reel</strong> (echange de messages avec un delai d\'affichage de quelques secondes maximum) sur Hostinger est resolu par <strong>deux mecanismes complementaires</strong> :</p>

            <p><strong>1) Polling HTTP (sondage periodique) — conversation ouverte :</strong> l\'application Flutter interroge le <em>backend</em> (serveur applicatif) toutes les 3 secondes via la requete :<br>
            <code>GET /api/conversations/{id}/messages?after={lastId}</code><br>
            Le serveur ne retourne que les messages dont l\'<code>id</code> est strictement superieur a <code>lastId</code> (identifiant du dernier message deja recu cote client), ce qui economise la bande passante.</p>

            <p><strong>2) FCM (Firebase Cloud Messaging) — application en arriere-plan ou ecran verrouille :</strong> le backend Laravel envoie une <em>push notification</em> (notification systeme affichee meme app fermee) via Firebase. Delai typique ~1 seconde.</p>

            <div class="code-example">Scenario                          | Mecanisme                | Delai
--------------------------------|--------------------------|-------
Chat ouvert, app au premier plan| Timer 3s + GET messages  | &lt;= 3s
App en arriere-plan/verrouille  | Push FCM                 | ~1s</div>

            <p><strong>Pourquoi ne pas utiliser Laravel Reverb (serveur WebSocket) ?</strong> Reverb necessite un <em>processus persistant</em> (programme qui tourne en continu sur le serveur) qui ne survit pas sur Hostinger mutualise. La combinaison <em>polling</em> court + FCM remplace integralement le besoin temps reel, sans dependance externe payante ni cout supplementaire.</p>

            <div class="tip">Cote Flutter, le polling doit etre <strong>annule dans la methode <code>dispose()</code></strong> (cycle de vie : nettoyage de l\'ecran a sa destruction) et mis en pause lors de la transition <code>AppLifecycleState.paused</code> (etat du cycle de vie de l\'app : passage en arriere-plan) — FCM prend le relais automatiquement.</div>
        '],
        ['title' => 'Librairies cles cote Flutter et Laravel', 'num' => 4, 'lesson' => '
            <p><strong>Cote Flutter (architecture <em>Riverpod</em> 2.x integrale) :</strong></p>
            <ul>
                <li><code>flutter_riverpod</code> : <em>state management</em> (gestion centralisee de l\'etat de l\'application) via <code>Notifier</code> / <code>AsyncNotifier</code> / <code>FutureProvider</code>. <strong>Pas</strong> de package <code>provider</code> classique.</li>
                <li><code>go_router</code> : <em>routing declaratif</em> (navigation decrite comme un arbre de routes) + <em>guards</em> (gardes de route qui controlent l\'acces) via la fonction <code>redirect</code> — utilises pour proteger les pages selon l\'authentification et la verification email.</li>
                <li><code>dio</code> : <em>client HTTP</em> (librairie d\'appels reseau plus puissante que <code>http</code>). Un <em>interceptor</em> (intercepteur de requetes) ajoute automatiquement le Bearer token a chaque appel et emet un <em>stream</em> (flux d\'evenements) <code>unauthorized</code> sur reponse 401.</li>
                <li><code>flutter_secure_storage</code> : stockage chiffre du jeton Sanctum (utilise <em>Android Keystore</em> ou <em>iOS Keychain</em>, services systeme dedies au stockage securise des secrets).</li>
                <li><code>firebase_messaging</code> : reception des push FCM, avec deux <em>handlers</em> (fonctions de traitement) <code>onMessage</code> (app au premier plan) et <code>onBackgroundMessage</code> (app fermee).</li>
                <li><code>video_player + chewie + visibility_detector</code> : lecture video dans le <em>feed</em> (fil d\'actualite). Lecture muette automatique au scroll, plein ecran au tap.</li>
                <li><code>cached_network_image</code> : <em>cache disque</em> (sauvegarde locale) des images chargees, pour eviter de les re-telecharger.</li>
                <li><code>share_plus</code> : partage d\'une publication ou d\'un profil artisan via les applications externes (WhatsApp, SMS, Messenger...).</li>
            </ul>
            <p><strong>Cote Laravel :</strong></p>
            <ul>
                <li><code>laravel/sanctum</code> : authentification API par Bearer tokens, avec le modele <code>PersonalAccessToken</code> surcharge pour limiter (<em>throttler</em>) les ecritures sur <code>last_used_at</code>.</li>
                <li><code>kreait/firebase-php ^7.16</code> : librairie PHP officielle pour l\'envoi de push via l\'<em>API HTTP v1</em> de Firebase (interface moderne de FCM). Version 7.16 obligatoire car la 8.x exige PHP 8.3+.</li>
                <li>Gestion des roles : <em>enum</em> (type de donnees a valeurs fixees) sur la colonne <code>role</code> + <em>middleware custom</em> (filtres de requete personnalises), plutot qu\'un package comme <code>spatie/laravel-permission</code> jugee trop lourde pour le besoin.</li>
            </ul>
        '],
        ['title' => 'Structure du code (organisation par feature)', 'num' => 5, 'lesson' => '
            <p>Le projet Flutter suit le pattern <strong>Clean Architecture par feature</strong> (organisation du code par fonctionnalite metier plutot que par type technique). Chaque feature regroupe 3 dossiers :</p>
            <ul>
                <li><code>application/</code> : <em>controllers Riverpod</em> qui exposent l\'etat de la feature (couche logique applicative).</li>
                <li><code>data/</code> : <em>repositories</em> (classes qui encapsulent les appels reseau a l\'API) + modeles JSON (classes Dart qui representent les donnees recues).</li>
                <li><code>presentation/</code> : <em>screens</em> (ecrans) et <em>widgets</em> (composants reutilisables d\'interface).</li>
            </ul>

            <p><strong>Regle d\'or :</strong> les ecrans ne touchent <strong>JAMAIS</strong> a Dio directement. Ils <em>watchent</em> (s\'abonnent a) un <em>provider</em> Riverpod qui depend lui-meme d\'un repository.</p>

            <div class="code-example">lib/
+- main.dart                       # init Firebase, ProviderScope
+- onboarding/                     # code commun aux 2 roles (client + artisan)
|   +- core/                       # config / network / router / theme / polling / push
|   +- features/                   # auth, categories, feed, messages, search, menu
+- client/                         # ecrans specifiques au role client (feed, search...)
+- artisan/                        # ecrans specifiques au role artisan (dashboard, publications)</div>

            <p><strong>Cote Laravel :</strong> separation physique stricte entre <code>app/Http/Controllers/Admin/</code> (controllers du dashboard web, authentification par <em>sessions cookies</em> = cookies de session navigateur) et <code>app/Http/Controllers/Api/</code> (controllers de l\'API mobile, authentification par Bearer tokens Sanctum).</p>

            <div class="tip">Cette discipline d\'organisation garantit qu\'un changement de comportement reste <em>localise</em> (limite dans un seul dossier), et qu\'on peut <em>tester chaque couche separement</em> via des tests unitaires ou d\'integration.</div>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Quel est le pattern d'architecture global de FeGArtisan ?", 'options'=>['Monolithique pur sans separation', 'Client-serveur a 3 niveaux', 'Microservices', 'P2P decentralise'], 'answer'=>1, 'explanation'=>"Architecture <strong>client-serveur a 3 niveaux</strong> : presentation (Flutter + Blade), logique metier (Laravel), donnees (MySQL/TiDB)."],
        ['chapter'=>0, 'question'=>"Quels sont les deux clients distincts qui parlent au backend Laravel ?", 'options'=>['App Flutter + dashboard web admin', 'Deux apps Flutter (client + artisan)', 'CLI + script Python', 'iOS + Android natifs'], 'answer'=>0, 'explanation'=>"Deux clients : l'<strong>app Flutter mobile</strong> (pour clients et artisans) et le <strong>dashboard web Laravel Blade</strong> (pour admin)."],
        ['chapter'=>0, 'question'=>"Dans quelle region cloud le cluster TiDB est-il hebergee ?", 'options'=>['us-east-1', 'eu-central-1 (Frankfurt)', 'ap-southeast', 'sa-east'], 'answer'=>1, 'explanation'=>"<strong>eu-central-1 (Frankfurt)</strong> pour minimiser la latence depuis Hostinger (~10ms par query vs ~80ms si US)."],
        ['chapter'=>0, 'question'=>"Quel role joue Sanctum dans l'architecture ?", 'options'=>['Gerer les emails', 'Generer les Bearer tokens d\'auth API', 'Heberger le frontend', 'Faire du caching SQL'], 'answer'=>1, 'explanation'=>"<strong>Sanctum</strong> genere les Bearer tokens API. Le modele <code>PersonalAccessToken</code> est overridee pour throttler les UPDATE de <code>last_used_at</code> a 1/min/token."],
        ['chapter'=>0, 'question'=>"Pourquoi override-t-on le modele PersonalAccessToken de Sanctum ?", 'options'=>['Pour ajouter des champs custom', 'Pour throttler les UPDATE de last_used_at a 1/min/token', 'Pour signer les tokens en JWT', 'Pour les rendre opaques'], 'answer'=>1, 'explanation'=>"Sans throttle, chaque requete authentifiee genererait un <code>UPDATE personal_access_tokens SET last_used_at</code> en plus des queries metier => surcout massif."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Quelle contrainte de Hostinger empeche d'utiliser Laravel Reverb ?", 'options'=>['Pas de PHP 8.2', 'Pas de WebSockets persistants', 'Pas de MySQL', 'Pas de HTTPS'], 'answer'=>1, 'explanation'=>"Hostinger mutualise n'autorise <strong>pas de WebSockets persistants</strong>. Reverb necessite un processus serveur persistant impossible en shared hosting."],
        ['chapter'=>1, 'question'=>"Comment les listeners FCM doivent-ils etre marques sur Hostinger ?", 'options'=>["Avec ShouldQueue (asynchrones)", "Sans ShouldQueue (synchrones)", "Avec ShouldBroadcast", "Sans aucune interface"], 'answer'=>1, 'explanation'=>"<strong>Synchrones</strong> (pas de <code>ShouldQueue</code>) car Hostinger n'a pas de queue worker persistant."],
        ['chapter'=>1, 'question'=>"Pourquoi <code>storage:link</code> ne marche-t-il pas sur Hostinger ?", 'options'=>['Limite memoire', 'exec() est desactive => il faut creer le lien manuellement avec <code>ln -s</code>', 'PHP 7 obligatoire', 'Aucune raison'], 'answer'=>1, 'explanation'=>"<strong><code>exec()</code> est desactive</strong> sur Hostinger. On cree donc le lien symbolique manuellement : <code>ln -s ~/fegartisan/storage/app/public ~/fegartisan/public/storage</code>."],
        ['chapter'=>1, 'question'=>"Comment le scheduler Laravel est-il lance sur Hostinger ?", 'options'=>["Via systemd", "Via cron Hostinger : * * * * * php artisan schedule:run", "Via Docker", "Pas de scheduler"], 'answer'=>1, 'explanation'=>"Un seul cron Hostinger : <code>* * * * * cd ~/fegartisan && /usr/bin/php artisan schedule:run >> /dev/null 2>&1</code>. Laravel relit <code>routes/console.php</code> chaque minute."],
        ['chapter'=>1, 'question'=>"Quels drivers de session et cache sont preferes en production Hostinger ?", 'options'=>['database', 'redis', 'file (disque local)', 'memcached'], 'answer'=>2, 'explanation'=>"<strong>file</strong> (disque local) pour eviter que chaque <code>Cache::has/put</code> ou session genere une query DB additionnelle. Gain massif en perf."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Quel est l'intervalle exact de polling pour les messages en chat ouvert ?", 'options'=>['1 s', '3 s', '5 s', '10 s'], 'answer'=>1, 'explanation'=>"<strong>3 secondes</strong>. Le polling appelle <code>GET /api/conversations/{id}/messages?after={lastId}</code>."],
        ['chapter'=>2, 'question'=>"Que retourne l'endpoint de polling si rien de nouveau ?", 'options'=>['Une 304 Not Modified', 'Un JSON vide <code>{ "messages": [] }</code>', 'Une 204 No Content', 'Une erreur 408'], 'answer'=>1, 'explanation'=>"L'endpoint retourne <strong><code>{ \"messages\": [] }</code></strong> (requete legere, pas de JOIN). Flutter ne fait rien."],
        ['chapter'=>2, 'question'=>"Quel mecanisme prend le relais quand l'app passe en arriere-plan ?", 'options'=>['Le polling continue en background', 'Firebase Cloud Messaging (FCM)', 'Un service Worker', 'Le bluetooth'], 'answer'=>1, 'explanation'=>"<strong>Firebase Cloud Messaging</strong> (FCM) pousse une notification. Le polling Flutter est en pause via <code>AppLifecycleState.paused</code>."],
        ['chapter'=>2, 'question'=>"Pourquoi polling + FCM remplace-t-il avantageusement les WebSockets ?", 'options'=>['Plus rapide en latence', "Compatible Hostinger shared, sans dependance externe ni cout supplementaire", "Moins de bande passante", "Toujours moins fiable"], 'answer'=>1, 'explanation'=>"<strong>Compatible avec tout hebergeur standard sans configuration speciale</strong>, fonctionnel meme sur connexion 3G instable, plus simple a maintenir et deboguer."],
        ['chapter'=>2, 'question'=>"Quel est le delai typique pour une notification FCM ?", 'options'=>['~5 ms', '~1 seconde', '~30 secondes', '~5 minutes'], 'answer'=>1, 'explanation'=>"Le delai FCM est de <strong>~1 seconde</strong> typiquement, ce qui est suffisant pour l'usage messagerie."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"Quelle librairie est utilisee pour le state management cote Flutter ?", 'options'=>['Provider classique', 'flutter_riverpod (Riverpod 2.x)', 'Redux', 'BLoC'], 'answer'=>1, 'explanation'=>"<strong>flutter_riverpod (Riverpod 2.x)</strong> en full : Notifier, AsyncNotifier, FutureProvider. Pas de <code>provider</code> classique."],
        ['chapter'=>3, 'question'=>"Quel package gere la navigation declarative ?", 'options'=>['Navigator 1', 'go_router', 'auto_route', 'fluro'], 'answer'=>1, 'explanation'=>"<strong>go_router</strong> est utilise. Les guards d'authentification passent par sa fonction <code>redirect</code>."],
        ['chapter'=>3, 'question'=>"Comment Dio injecte-t-il le Bearer token a chaque requete ?", 'options'=>["Manuellement dans chaque appel", "Via un interceptor qui lit le token depuis flutter_secure_storage", "Via cookies", "Via une variable globale"], 'answer'=>1, 'explanation'=>"Un <strong>interceptor Dio</strong> lit le token Sanctum depuis <code>flutter_secure_storage</code> et l'ajoute au header <code>Authorization: Bearer ...</code> a chaque requete."],
        ['chapter'=>3, 'question'=>"Ou est stocke le token Sanctum sur le telephone ?", 'options'=>['SharedPreferences (clair)', "flutter_secure_storage (Android Keystore / iOS Keychain, chiffree)", "Fichier texte", "Dans Dio en memoire seulement"], 'answer'=>1, 'explanation'=>"<strong>flutter_secure_storage</strong> utilise Android Keystore et iOS Keychain pour stocker le token de facon <strong>chiffree</strong>."],
        ['chapter'=>3, 'question'=>"Quelle lib gere la lecture video dans le feed ?", 'options'=>['video_player + chewie + visibility_detector', 'youtube_player', 'flick_video_player', 'webview'], 'answer'=>0, 'explanation'=>"<strong>video_player + chewie + visibility_detector</strong>. Auto-play muet en feed via VisibilityDetector, plein ecran via Chewie au tap."],

        // CHAPITRE 5
        ['chapter'=>4, 'question'=>"Quels sont les 3 dossiers d'une feature Clean Architecture cote Flutter ?", 'options'=>["models/, services/, ui/", "application/, data/, presentation/", "controllers/, views/, models/", "lib/, src/, test/"], 'answer'=>1, 'explanation'=>"<strong>application/</strong> (controllers Riverpod), <strong>data/</strong> (repositories + modeles JSON), <strong>presentation/</strong> (screens et widgets)."],
        ['chapter'=>4, 'question'=>"Les ecrans Flutter touchent-ils directement Dio ?", 'options'=>['Oui, c\'est la regle', "Non, jamais. Ils watchent un provider qui depend d'un repository", "Seulement dans les FAQ", "Uniquement en debug"], 'answer'=>1, 'explanation'=>"<strong>Jamais</strong>. Les ecrans watchent un provider Riverpod, qui depend d'un repository, qui seul utilise Dio. Cela garantit testabilite et reuse."],
        ['chapter'=>4, 'question'=>"Comment sont organises les controllers Laravel ?", 'options'=>["Tous dans un seul dossier", "Separes en Admin/ (web sessions) et Api/ (Bearer Sanctum)", "Par modele", "Par middleware"], 'answer'=>1, 'explanation'=>"Separation claire : <code>app/Http/Controllers/Admin/</code> pour le dashboard web (sessions cookies) et <code>app/Http/Controllers/Api/</code> pour l'API REST (Bearer Sanctum)."],
        ['chapter'=>4, 'question'=>"A quoi sert le dossier <code>onboarding/</code> dans lib/ Flutter ?", 'options'=>["Tutoriels au premier lancement uniquement", "Tout ce qui touche aux deux roles (commun client + artisan)", "Animations d'intro", "Vide"], 'answer'=>1, 'explanation'=>"<code>onboarding/</code> contient <strong>tout ce qui est commun aux deux roles</strong> : auth, messagerie, feed, recherche, menu, notifications, categories."],
        ['chapter'=>4, 'question'=>"Que contiennent les dossiers <code>client/</code> et <code>artisan/</code> dans lib/ ?", 'options'=>["Les modeles JSON", "Les ecrans specifiques a chaque role (feed client, dashboard artisan, etc.)", "Les tests", "Rien d'utile"], 'answer'=>1, 'explanation'=>"Ces dossiers contiennent les <strong>ecrans specifiques a chaque role</strong> : <code>client/features/clients/presentation/</code> et <code>artisan/features/artisan/presentation/</code>."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
