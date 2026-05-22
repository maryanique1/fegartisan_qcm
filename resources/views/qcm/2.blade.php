@extends('layouts.app')
@section('title', 'Epreuve 2 : Messagerie & FCM en detail')

@php
    $config = [
        'qcm_key' => 'fega-2',
        'title' => 'Epreuve 2 : Messagerie & FCM en detail',
        'subtitle' => '16 questions . QCM transverse . Polling 3s, lifecycle, push notif',
        'badge' => 'Msg',
        'color' => '#4A7C59',
        'description' => 'QCM cible sur le mecanisme polling + FCM, la gestion du lifecycle Flutter, et le FirebasePushService cote backend.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Polling HTTP + FCM (vue transversale)', 'num' => 1, 'lesson' => '
            <p>Cette epreuve teste tout ce qui touche au <em>temps reel</em> dans FeGArtisan : pourquoi le <em>polling</em> (sondage HTTP periodique), pourquoi <em>FCM</em> (Firebase Cloud Messaging, service de push notifications de Google), comment Flutter gere le <em>lifecycle</em> (cycle de vie de l\'application : foreground, background, paused, resumed), et comment le backend Laravel pousse les notifications aux telephones.</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"L'app FeGArtisan utilise-t-elle Laravel Reverb ?", 'options'=>['Oui', 'Non, Hostinger mutualise ne permet pas de WebSockets persistants', 'En option', 'Seulement en local'], 'answer'=>1, 'explanation'=>"Reverb necessite un processus persistant impossible sur Hostinger shared. On utilise polling + FCM."],
        ['chapter'=>0, 'question'=>"Intervalle de polling pour les messages en chat ouvert ?", 'options'=>['1s', '3s', '5s', '30s'], 'answer'=>1, 'explanation'=>"<strong>3 secondes</strong>. Compromis acceptable entre reactivite et consommation."],
        ['chapter'=>0, 'question'=>"Endpoint de polling ?", 'options'=>['GET /api/poll', 'GET /api/conversations/{id}/messages?after={lastId}', 'POST /api/messages/since', 'PUT /api/check'], 'answer'=>1, 'explanation'=>"GET /api/conversations/{id}/messages?after={lastId}. Retourne uniquement <code>id &gt; lastId</code>."],
        ['chapter'=>0, 'question'=>"Le PollingService Flutter rafraichit les conversations toutes les ?", 'options'=>['3s', '30s', '60s', '180s'], 'answer'=>2, 'explanation'=>"<strong>60 secondes</strong> pour la liste des conversations. Le polling 3s est specifique au chat ouvert."],
        ['chapter'=>0, 'question'=>"Et pour la liste de notifications (inbox) ?", 'options'=>['60s', '180s', '300s', '600s'], 'answer'=>1, 'explanation'=>"<strong>180 secondes</strong> pour l\'inbox des notifications."],
        ['chapter'=>0, 'question'=>"Cancel du timer dans dispose() est-il obligatoire ?", 'options'=>['Non', 'Oui sinon fuite memoire + requetes inutiles', 'Optionnel selon plateforme', 'Geree par Flutter'], 'answer'=>1, 'explanation'=>"<strong>Oui</strong>. Sans <code>_timer?.cancel()</code> dans dispose, le timer continue de tourner apres la fermeture de l\'ecran."],
        ['chapter'=>0, 'question'=>"Sur AppLifecycleState.paused, que fait le ChatScreen ?", 'options'=>['Cancel le timer pour laisser FCM prendre le relais', 'Crash', 'Lance plus de polling', 'Rien'], 'answer'=>0, 'explanation'=>"Pause le polling et laisse FCM gerer les nouveaux messages en arriere-plan."],
        ['chapter'=>0, 'question'=>"Quel mixin Widget permet d'ecouter AppLifecycleState ?", 'options'=>['LifecycleObserver', 'WidgetsBindingObserver', 'AppStateObserver', 'StateAware'], 'answer'=>1, 'explanation'=>"<code>with WidgetsBindingObserver</code> + <code>WidgetsBinding.instance.addObserver(this)</code>."],
        ['chapter'=>0, 'question'=>"Quel handler FCM doit etre top-level avec @pragma('vm:entry-point') ?", 'options'=>['onMessage', '_firebaseBackgroundHandler', 'onTokenRefresh', 'getInitialMessage'], 'answer'=>1, 'explanation'=>"<code>_firebaseBackgroundHandler(RemoteMessage)</code> doit etre <strong>top-level</strong> et annotee <code>@pragma('vm:entry-point')</code> sinon Flutter ne peut pas l'invoquer quand l'app est terminee."],
        ['chapter'=>0, 'question'=>"Quel callback gere les notifs quand l'app est ouverte (premier plan) ?", 'options'=>['onMessage', 'onMessageOpenedApp', 'onBackgroundMessage', 'getInitialMessage'], 'answer'=>0, 'explanation'=>"<code>FirebaseMessaging.onMessage.listen()</code> pour l\'app au premier plan."],
        ['chapter'=>0, 'question'=>"Quel callback gere le tap sur notif quand l'app est en arriere-plan ?", 'options'=>['onMessage', 'onMessageOpenedApp', 'onBackgroundMessage', 'getInitialMessage'], 'answer'=>1, 'explanation'=>"<code>onMessageOpenedApp</code>. Typiquement <code>context.push('/chat/\$conversationId')</code> dans le callback."],
        ['chapter'=>0, 'question'=>"Quel package PHP pour Firebase HTTP v1 sur PHP 8.2 ?", 'options'=>['kreait/firebase-php ^8.0', 'kreait/firebase-php ^7.16', 'firebase/php-jwt', 'guzzle'], 'answer'=>1, 'explanation'=>"<strong>^7.16</strong>. La 8.x exige PHP 8.3+."],
        ['chapter'=>0, 'question'=>"Extension PHP indispensable pour Firebase ?", 'options'=>['gd', 'imagick', 'sodium', 'mbstring'], 'answer'=>2, 'explanation'=>"<strong>sodium</strong> pour signer le JWT OAuth2 vers Firebase. Dans php.ini : <code>extension=sodium</code>."],
        ['chapter'=>0, 'question'=>"Si le token FCM est invalide cote backend, que se passe-t-il ?", 'options'=>['Crash', 'Nettoyage automatique du fcm_token en DB', 'Email a l\'admin', 'Retry infini'], 'answer'=>1, 'explanation'=>"FirebasePushService gere l'erreur Firebase et nettoie automatiquement le token invalide en base."],
        ['chapter'=>0, 'question'=>"Combien de listeners reagissent a NewNotification ?", 'options'=>['1', '2 (PersistNotification + SendFcmForNotification)', '3', 'aucun'], 'answer'=>1, 'explanation'=>"2 listeners : un pour la persistance en DB, un pour le push FCM. Les 2 sont declenches en auto-discovery, donc PAS de Event::listen() manuel."],
        ['chapter'=>0, 'question'=>"Quelle commande Artisan pour tester FCM en envoyant un push reel ?", 'options'=>['php artisan fcm:check', 'php artisan fcm:check {user_id}', 'php artisan firebase:test', 'php artisan push:send'], 'answer'=>1, 'explanation'=>"<code>php artisan fcm:check {user_id}</code> envoie un push de test a l'user specifie. Sans argument, juste un diagnostic config."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
