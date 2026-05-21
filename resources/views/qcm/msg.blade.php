@extends('layouts.app')
@section('title', 'Messagerie & FCM FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-msg',
        'title' => 'Messagerie temps reel & FCM',
        'subtitle' => '20 questions . 4 chapitres . Polling + Firebase + lifecycle',
        'badge' => 'MSG',
        'color' => '#4A7C59',
        'description' => 'Comment FeGArtisan resout le temps reel sans WebSockets : polling court + FCM push, lifecycle Flutter, FirebasePushService backend, et les notifications enrichies.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Envoi d\'un message + notification push', 'num' => 1, 'lesson' => '
            <p>Flux complet d\'un envoi de message avec notification au destinataire — toutes les etapes qui se declenchent entre l\'appui sur le bouton "Envoyer" et l\'affichage de la notification sur l\'autre telephone :</p>
            <div class="code-example">1. Flutter ChatScreen : l\'utilisateur tape un message, valide l\'envoi
2. POST /api/conversations/{id}/messages (Bearer Sanctum requis)
3. ConversationController::sendMessage cote Laravel
   |- valide le contenu (texte ou fichier)
   |- Message::create() => INSERT en base de donnees
   |- conversation->update(["last_message_at" => now()])
       // remonte la conversation en tete de liste
   |- Message->load("sender", "reactions")
       // charge en eager loading les relations utiles
   |- event(new NewNotification(
         userId: $recipientId,             // destinataire
         type: "new_message",
         title: nom de l\'expediteur,
         body: apercu du message (80 caracteres max),
         data: { conversation_id, message_id,
                 sender_avatar, sender_id }  // payload contextuel
       ))

4. L\'auto-discovery Laravel 12 declenche 2 listeners en parallele :
   |- PersistNotification : INSERT dans la table notifications
       (centre de notifications consultable plus tard)
   |- SendFcmForNotification : FirebasePushService->sendToUser($userId)
       |- recupere User::find($userId)->fcm_token
       |- POST https://fcm.googleapis.com/v1/projects/{id}/messages:send
          avec OAuth2 bearer signe via sodium

5. Telephone du destinataire :
   |- App fermee  : FirebaseMessaging.onBackgroundMessage
                    => notification systeme affichee (banniere + son)
   |- App ouverte : onMessage => PushService gere l\'affichage in-app
                    (snackbar discrete car l\'utilisateur est deja la)

6. Reponse a l\'envoyeur : { message: {...} } (code HTTP 201 Created)
7. Flutter (envoyeur) : insere le message localement (optimistic UI)
8. Flutter (destinataire) :
   |- Si chat ouvert : le polling 3s recupere le nouveau message
   |- Sinon : la notification FCM informe au moment voulu</div>
        '],
        ['title' => 'Polling cote Flutter (sondage HTTP)', 'num' => 2, 'lesson' => '
            <p>Quand la conversation est ouverte, l\'application Flutter interroge le backend <strong>toutes les 3 secondes</strong> via l\'endpoint suivant :</p>
            <div class="code-example">GET /api/conversations/{id}/messages?after={lastId}

// Reponse si rien de nouveau (requete legere, pas de JOIN cote SQL) :
{ "messages": [] }

// Reponse si nouveau message :
{ "messages": [
    { "id": 42, "content": "Bonjour", "sender": {...}, ... }
] }</div>
            <p>Le widget <code>ChatScreen</code> suit ce <em>pattern</em> (modele de code reutilisable) critique pour eviter les fuites memoire :</p>
            <ul>
                <li><code>initState()</code> : <code>WidgetsBinding.instance.addObserver(this)</code> (ecouter le cycle de vie de l\'app), <code>_loadInitial()</code> (charger l\'historique), <code>_startPolling()</code>.</li>
                <li><code>_startPolling()</code> : <code>Timer.periodic(Duration(seconds: 3), ...)</code> — programme un appel toutes les 3 secondes.</li>
                <li><code>didChangeAppLifecycleState(AppLifecycleState.paused)</code> : <code>_timer?.cancel()</code> (app en arriere-plan, FCM prend le relais).</li>
                <li><code>didChangeAppLifecycleState(AppLifecycleState.resumed)</code> : <code>_startPolling()</code> (retour au premier plan, on relance).</li>
                <li><code>dispose()</code> : <strong>IMPORTANT</strong> — <code>_timer?.cancel()</code> et <code>WidgetsBinding.instance.removeObserver(this)</code> pour nettoyer.</li>
            </ul>
            <div class="tip">Sans <code>cancel()</code> dans <code>dispose()</code> (methode appelee a la destruction du widget), le timer continue de tourner apres la fermeture de l\'ecran — provoquant une <strong>fuite memoire</strong> (memory leak : objet jamais libere) et des requetes HTTP inutiles qui consomment batterie et data.</div>
        '],
        ['title' => 'Firebase Cloud Messaging cote Flutter', 'num' => 3, 'lesson' => '
            <p><strong>Initialisation dans <code>main.dart</code> :</strong></p>
            <div class="code-example">// Handler pour les notifications en arriere-plan :
// DOIT etre une fonction top-level (en dehors d\'une classe)
// et annotee @pragma("vm:entry-point") pour que Flutter puisse
// la rappeler depuis la VM Dart quand l\'app est terminee.
@pragma("vm:entry-point")
Future<void> _firebaseBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  // Traitement minimal : l\'app n\'est PAS lancee
  // (on ne peut pas appeler la navigation, le state, etc.)
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  FirebaseMessaging.onBackgroundMessage(_firebaseBackgroundHandler);
  runApp(const FeGArtisanApp());
}</div>

            <p><strong>Enregistrement du token FCM apres le login :</strong></p>
            <div class="code-example">Future<void> registerFcmToken() async {
  // Demande la permission (Android 13+ exige la permission explicite)
  final settings = await FirebaseMessaging.instance.requestPermission(
    alert: true, badge: true, sound: true,
  );
  if (settings.authorizationStatus == AuthorizationStatus.authorized) {
    final token = await FirebaseMessaging.instance.getToken();
    if (token != null) {
      await ApiClient.instance.post("/me/fcm-token", data: {
        "token": token, "platform": "android",
      });
    }
  }
  // Re-upload automatique si Firebase rotate le token
  FirebaseMessaging.instance.onTokenRefresh.listen((newToken) async {
    await ApiClient.instance.post("/me/fcm-token", data: { ... });
  });
}</div>

            <p><strong>3 cas de reception cote app :</strong></p>
            <ul>
                <li><code>onMessage</code> : app au <em>premier plan</em> (active a l\'ecran) — on affiche une notification in-app discrete (snackbar) plutot qu\'une notif systeme.</li>
                <li><code>onMessageOpenedApp</code> : app en arriere-plan + tap sur la notification systeme — on navigue vers la conversation : <code>context.push("/chat/$conversationId")</code>.</li>
                <li><code>getInitialMessage()</code> : app <em>terminee</em> (kill) + le tap sur la notif a lance l\'app — on doit attendre que le router soit pret avant de naviguer.</li>
            </ul>
        '],
        ['title' => 'FirebasePushService cote Laravel', 'num' => 4, 'lesson' => '
            <p><strong>FirebasePushService</strong> est un <em>singleton</em> (classe instanciee une seule fois) qui encapsule l\'envoi de push via l\'<em>API HTTP v1</em> de Firebase (interface moderne, qui remplace l\'ancienne API legacy).</p>

            <p><strong>Configuration dans <code>.env</code> :</strong></p>
            <div class="code-example">FIREBASE_CREDENTIALS=storage/firebase/service-account.json
FIREBASE_PUSH_ENABLED=true</div>
            <p>Le fichier <code>service-account.json</code> est uploade <strong>manuellement</strong> via <em>SCP</em> (Secure Copy Protocol, copie de fichier via SSH) sur Hostinger. <strong>JAMAIS</strong> commit dans git — c\'est une clef maitresse qui donnerait acces a Firebase a un attaquant.</p>

            <p><strong>Methode principale :</strong> <code>sendToUser($userId, $title, $body, $data)</code></p>
            <ul>
                <li>Recupere le token Firebase de l\'utilisateur : <code>User::find($userId)->fcm_token</code></li>
                <li>Construit le <em>payload</em> (charge utile) avec un bloc <code>notification</code> (titre + corps affiches systeme) et un bloc <code>data</code> (donnees techniques traitees par l\'app).</li>
                <li>POST vers <code>https://fcm.googleapis.com/v1/projects/{id}/messages:send</code> avec un <em>OAuth2 bearer</em> (jeton OAuth2 obtenu par signature du service account).</li>
                <li>Gere les retours d\'erreur : si Firebase repond que le token est invalide (telephone desinstalle, reset...), nettoyage automatique du <code>fcm_token</code> en base.</li>
            </ul>

            <p><strong>Extension PHP requise : <code>sodium</code></strong> (extension cryptographique moderne) pour signer le <em>JWT</em> (JSON Web Token, jeton signe) OAuth2 envoye a Firebase. Dans <code>php.ini</code>, decommenter la ligne : <code>extension=sodium</code>.</p>

            <p><strong>Commandes Artisan de diagnostic :</strong></p>
            <div class="code-example">php artisan fcm:check          # diagnostique la config Firebase
                               # (credentials, sodium, connexion)
php artisan fcm:check 1        # envoie un push de test a user #1
                               # (utile pour valider end-to-end)</div>

            <p>Le <em>centre de notifications</em> (inbox de notifs enrichies, avec avatars et conversations prechargees en <em>bulk</em> = en une seule requete SQL groupee) est expose via :</p>
            <ul>
                <li><code>GET /api/notifications</code> (paginees)</li>
                <li><code>GET /api/notifications/unread-count</code> (badge global affiche sur la BottomNav)</li>
                <li><code>PATCH /api/notifications/{id}/read</code> (marquer une notif comme lue)</li>
                <li><code>PATCH /api/notifications/read-all</code> (marquer tout comme lu)</li>
                <li><code>DELETE /api/notifications/{id}</code> (supprimer par geste de <em>swipe</em>)</li>
                <li><code>DELETE /api/notifications/all</code> (purger tout)</li>
            </ul>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Quel event est emis cote Laravel lors de l'envoi d'un message ?", 'options'=>['MessageSent', 'NewNotification', 'BroadcastMessage', 'FcmPush'], 'answer'=>1, 'explanation'=>"<code>event(new NewNotification(userId, type:'new_message', title, body, data))</code>. Cet event declenche les 2 listeners auto-discovery."],
        ['chapter'=>0, 'question'=>"Combien de listeners reagissent a NewNotification ?", 'options'=>['1', '2 (PersistNotification + SendFcmForNotification)', '3', '5'], 'answer'=>1, 'explanation'=>"<strong>2 listeners</strong> : <code>PersistNotification</code> (INSERT en DB) + <code>SendFcmForNotification</code> (push FCM)."],
        ['chapter'=>0, 'question'=>"Quel champ est mis a jour sur la conversation a chaque message ?", 'options'=>['updated_at uniquement', 'last_message_at', 'message_count', 'aucun'], 'answer'=>1, 'explanation'=>"<code>conversation-&gt;update(['last_message_at' =&gt; now()])</code> pour trier la liste des conversations recentes."],
        ['chapter'=>0, 'question'=>"Quelle donnee est typiquement dans le payload FCM data pour un message ?", 'options'=>['Le password', 'conversation_id, message_id, sender_avatar, sender_id', 'rien', 'L\'IP source'], 'answer'=>1, 'explanation'=>"Le payload FCM data contient : <code>conversation_id</code>, <code>message_id</code>, <code>sender_avatar</code>, <code>sender_id</code>. Permet a Flutter de naviguer au tap."],
        ['chapter'=>0, 'question'=>"Quelle longueur a l'apercu du message dans le body de la notif ?", 'options'=>['10 chars', '40 chars', '80 chars', 'Illimite'], 'answer'=>2, 'explanation'=>"<strong>80 caracteres</strong> pour l'apercu (body de la notif)."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Quel est l'endpoint de polling Flutter ?", 'options'=>['GET /api/poll', 'GET /api/conversations/{id}/messages?after={lastId}', 'POST /api/messages/since', 'WebSocket'], 'answer'=>1, 'explanation'=>"<code>GET /api/conversations/{id}/messages?after={lastId}</code>. Retourne uniquement les messages avec <code>id &gt; lastId</code>."],
        ['chapter'=>1, 'question'=>"Quelle methode Flutter cancel le timer de polling quand l'ecran est detruit ?", 'options'=>['build()', 'initState()', 'dispose()', 'rebuild()'], 'answer'=>2, 'explanation'=>"<strong>dispose()</strong> doit absolument faire <code>_timer?.cancel()</code> sinon fuite memoire + requetes inutiles."],
        ['chapter'=>1, 'question'=>"Que se passe-t-il sur AppLifecycleState.paused ?", 'options'=>['Le polling continue', '_timer?.cancel() pour laisser FCM prendre le relais', 'Plus rien', 'App ferme'], 'answer'=>1, 'explanation'=>"Sur <code>paused</code>, on cancel le timer pour laisser FCM gerer les notifs. Sur <code>resumed</code>, on relance <code>_startPolling()</code>."],
        ['chapter'=>1, 'question'=>"Quel widget mixin permet d'ecouter AppLifecycleState ?", 'options'=>['StateListener', 'WidgetsBindingObserver', 'LifecycleAware', 'AppObserver'], 'answer'=>1, 'explanation'=>"<code>WidgetsBindingObserver</code> avec <code>WidgetsBinding.instance.addObserver(this)</code> dans initState et <code>removeObserver(this)</code> dans dispose."],
        ['chapter'=>1, 'question'=>"Que renvoie l'endpoint si aucun nouveau message ?", 'options'=>['404', '{ messages: [] }', '304 Not Modified', '204 No Content'], 'answer'=>1, 'explanation'=>"<code>{ \"messages\": [] }</code>. Requete legere, pas de JOIN. Flutter ne fait rien."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Le handler FCM background doit etre marque comment ?", 'options'=>['async function normale', '@pragma(\'vm:entry-point\') Future<void> _firebaseBackgroundHandler(RemoteMessage)', 'static method', '@override'], 'answer'=>1, 'explanation'=>"Doit etre une <strong>fonction top-level</strong> annotee <code>@pragma('vm:entry-point')</code> sinon Flutter ne peut pas la rappeler quand l'app est terminee."],
        ['chapter'=>2, 'question'=>"Que faire dans le handler background ?", 'options'=>['Beaucoup de logique metier', 'Traitement minimal car l\'app n\'est pas lancee', 'Acceder a la base SQLite', 'Lancer le router'], 'answer'=>1, 'explanation'=>"<strong>Traitement minimal</strong> car l'app n'est pas lancee. Juste <code>await Firebase.initializeApp()</code> et eventuellement loguer."],
        ['chapter'=>2, 'question'=>"Quand le token FCM peut-il etre renouvele automatiquement par Firebase ?", 'options'=>['Jamais', 'Periodiquement, ecoute via onTokenRefresh.listen()', 'Toutes les minutes', 'Au logout'], 'answer'=>1, 'explanation'=>"<code>FirebaseMessaging.instance.onTokenRefresh.listen((newToken) =&gt; ...)</code> permet de re-uploader le token quand Firebase le rotate."],
        ['chapter'=>2, 'question'=>"Quel callback est appele sur tap d'une notif quand l'app est en arriere-plan ?", 'options'=>['onMessage', 'onMessageOpenedApp', 'getInitialMessage', 'onBackgroundMessage'], 'answer'=>1, 'explanation'=>"<code>FirebaseMessaging.onMessageOpenedApp.listen(...)</code> est appele quand l'utilisateur tape sur la notif (app en arriere-plan)."],
        ['chapter'=>2, 'question'=>"Quel callback recupere une notif qui a lance l'app depuis fermee ?", 'options'=>['onMessage', 'onMessageOpenedApp', 'FirebaseMessaging.instance.getInitialMessage()', 'launchListener'], 'answer'=>2, 'explanation'=>"<code>getInitialMessage()</code> recupere le message qui a lance l'app depuis l'etat termine. Souvent appele apres que le router soit pret."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"Quel package PHP gere l'envoi FCM HTTP v1 ?", 'options'=>['guzzlehttp/guzzle', 'kreait/firebase-php ^7.16 (compatible PHP 8.2)', 'firebase/php-jwt', 'google/cloud-firestore'], 'answer'=>1, 'explanation'=>"<strong>kreait/firebase-php ^7.16</strong>. La version 8.x exige PHP 8.3+, donc on reste sur 7.16 pour PHP 8.2."],
        ['chapter'=>3, 'question'=>"Quelle extension PHP est indispensable pour Firebase ?", 'options'=>['gd', 'sodium (signature JWT OAuth2)', 'imagick', 'mbstring'], 'answer'=>1, 'explanation'=>"<strong>sodium</strong>. Dans php.ini, decommenter <code>extension=sodium</code>. Sans ca : erreurs JWT Firebase."],
        ['chapter'=>3, 'question'=>"Quelle commande Artisan diagnostique la config FCM ?", 'options'=>['php artisan fcm:test', 'php artisan fcm:check', 'php artisan firebase:diagnose', 'php artisan health'], 'answer'=>1, 'explanation'=>"<code>php artisan fcm:check</code> diagnostique (push activee, credentials, JSON valide, sodium, connexion Firebase). <code>php artisan fcm:check 1</code> envoie un push test au user #1."],
        ['chapter'=>3, 'question'=>"Que fait FirebasePushService si le token FCM est invalide ?", 'options'=>['Crash', 'Nettoyage automatique en DB (le supprime de users.fcm_token)', 'Retry illimite', 'Email a l\'admin'], 'answer'=>1, 'explanation'=>"Le service gere les retours d'erreur Firebase. Sur token invalide =&gt; <strong>nettoyage automatique</strong> du <code>fcm_token</code> en base."],
        ['chapter'=>3, 'question'=>"Comment Flutter recupere-t-il son badge global de notifs non lues ?", 'options'=>['Via FCM seul', 'GET /api/notifications/unread-count (polling 180s ou refresh manuel)', 'WebSocket', 'localStorage'], 'answer'=>1, 'explanation'=>"<code>GET /api/notifications/unread-count</code> pour le badge sur la BottomNav. Le PollingService de l'app rafraichit toutes les 180s."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
