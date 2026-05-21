@extends('layouts.app')
@section('title', 'Application Flutter FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-flutter',
        'title' => 'Application Flutter',
        'subtitle' => '25 questions . 5 chapitres . Riverpod, Clean Archi, routing, ecrans',
        'badge' => 'FLUTTER',
        'color' => '#0468D7',
        'description' => 'Comprendre Riverpod (Notifier, AsyncNotifier, FutureProvider), Clean Architecture par feature, go_router et ses guards, et les flux applicatifs cles.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Hierarchie Riverpod et providers', 'num' => 1, 'lesson' => '
            <p><em>Riverpod</em> (librairie de <strong>state management</strong>, c\'est-a-dire gestion centralisee de l\'etat de l\'application) en version 2.x organise les <em>providers</em> (sources d\'etat exposees a l\'arbre des widgets) selon une <strong>hierarchie d\'injection</strong> (chaque provider depend d\'un autre via <code>ref.read</code> ou <code>ref.watch</code>).</p>
            <div class="code-example">apiClientProvider (Provider)
    |
    injecte dans
    v
authStorageProvider, authRepositoryProvider (Provider)
    |
    utilise par
    v
authControllerProvider (NotifierProvider&lt;AuthController, AuthState&gt;)
    |
    ecoute par
    v
router redirect, ecrans, autres controllers</div>

            <p><strong>Les 4 types de providers utilises dans le projet :</strong></p>
            <ul>
                <li><code>Provider</code> : <em>singleton</em> (instance unique partagee) sans etat mutable — pour les services et repositories. Exemple : <code>apiClientProvider</code>.</li>
                <li><code>NotifierProvider</code> : etat mutable type, methode <code>build()</code> synchrone. Exemple : <code>authControllerProvider</code> (qui expose <code>AuthState</code>).</li>
                <li><code>AsyncNotifierProvider</code> : etat mutable avec initialisation <em>asynchrone</em> (le <code>build()</code> retourne un <code>Future</code> et peut faire des appels HTTP). Exemple : <code>dashboardControllerProvider</code> (charge les stats au montage de l\'ecran).</li>
                <li><code>FutureProvider.autoDispose.family</code> : <em>fetch HTTP</em> (recuperation de donnees reseau) parametre, qui se libere quand l\'ecran disparait. Exemple : <code>publicationsProvider(categoryId)</code>.</li>
            </ul>
            <div class="tip"><code>autoDispose</code> = libere la memoire des qu\'il n\'y a plus d\'ecouteur (evite les fuites memoire). <code>family</code> = permet de passer un parametre dynamique au provider. La combinaison des deux est ideale pour les fetches par identifiant.</div>
        '],
        ['title' => 'Bootstrap d\'authentification + rafraichissement au foreground', 'num' => 2, 'lesson' => '
            <p><strong>Flux 1 — <em>Bootstrap</em> d\'authentification au demarrage</strong> (sequence d\'initialisation qui determine si l\'utilisateur est deja connecte) :</p>
            <div class="code-example">1. main.dart : runApp(ProviderScope(...))
2. AuthController.build() retourne AuthUnknown
   + lance _bootstrap() en arriere-plan
3. _bootstrap() lit storage.readToken() (token Sanctum local)
   |-- Si null => state = AuthUnauthenticated
   |-- Sinon => GET /api/me avec le token
       |-- Succes : state = AuthAuthenticated(user)
       |          + _syncPushToken() => POST /me/fcm-token
       |-- 401/403 : storage.clear() + state = AuthUnauthenticated
4. Le router watch authControllerProvider et redirige :
   |-- AuthUnknown          => reste sur "/" (splash screen)
   |-- AuthUnauthenticated  => redirect /onboarding
   |-- AuthAuthenticated :
       |-- user.isWaitingEmailVerify   => redirect /email-verify
       |-- user.isPendingArtisan       => redirect /artisan/pending
       |-- sinon                       => /home (client) ou /artisan/dashboard</div>

            <p><strong>Flux 2 — <em>Foreground refresh</em></strong> (rafraichissement automatique quand l\'utilisateur revient au premier plan, pour detecter qu\'un email vient d\'etre confirme sans avoir a se reconnecter) :</p>
            <div class="code-example">1. Le user clique sur le lien de verification dans son mail (navigateur)
2. Le backend confirme l\'email
3. Le user revient sur l\'app (changement d\'app)
4. main.dart implemente WidgetsBindingObserver :
   didChangeAppLifecycleState(AppLifecycleState.resumed) =>
   if (user.isWaitingEmailVerify) authController.refreshUser()
5. refreshUser() => GET /api/me => user a jour
6. state = AuthAuthenticated(freshUser)
7. Le router declenche un redirect automatique => /home (le verrou tombe)</div>
        '],
        ['title' => 'Optimistic update du like + reactions emoji', 'num' => 3, 'lesson' => '
            <p><strong>Flux 3 — <em>Toggle</em> like avec <em>optimistic update</em></strong> (mise a jour anticipee de l\'interface, sans attendre la reponse serveur, pour fluidite percue) :</p>
            <p>Probleme : invalider <code>publicationsProvider</code> apres un like detruirait l\'<code>AnimationController</code> (gestionnaire d\'animation Flutter) du bouton <code>LikeBtn</code> et ferait <em>scintiller</em> (clignoter brievement) l\'icone coeur.</p>
            <div class="code-example">1. Le user tape sur ❤ dans PublicationCard
2. _toggleLike(publication) => publicationsRepository.toggleLike()
3. POST /api/publications/{id}/like => reponse { liked, likes_count }
4. setState(() => _likeOverrides[id] = updatedPub)
   // override LOCAL stocke dans le widget, au lieu d\'invalider
   //  publicationsProvider qui rebuilderait toute la carte
5. Le widget se reconstruit avec le nouvel etat liked + count,
   et l\'animation reste fluide</div>

            <p><strong>Flux 4 — Reactions emoji sur message (appui long)</strong> :</p>
            <div class="code-example">1. Long-press (appui prolonge) sur la bulle de message
   => MessageBubble ouvre un picker (selecteur) de 6 emojis
2. Selection d\'un emoji => MessageReactionRepository.toggle(messageId, emoji)
3. POST /api/messages/{id}/reactions { emoji: "❤" }
4. Backend MessageReactionController::toggle :
   |- recupere la reaction existante (cle unique message_id+user_id)
   |- Si meme emoji => DELETE (retire la reaction)
   |- Si autre emoji => UPDATE (remplace)
   |- Si aucune existante => INSERT (ajoute)
5. Reponse JSON : liste complete des reactions du message
6. message.copyWithReactions(newReactions) => setState
7. _ReactionsRow affiche les <em>chips</em> (puces emoji avec compteur)
   sous la bulle de message</div>
            <div class="tip">Le <em>toggle</em> est <strong>intelligent</strong> : meme emoji = retirer, autre emoji = remplacer, aucun existant = ajouter. Une seule reaction par utilisateur par message (garantie par une <em>unique key</em> SQL — index unique sur la paire <code>message_id + user_id</code>).</div>
        '],
        ['title' => 'Lecture video dans le feed', 'num' => 4, 'lesson' => '
            <p><strong>Flux 5 — Lecture video dans le <em>feed</em></strong> (fil d\'actualite scrollable) :</p>
            <div class="code-example">1. PublicationCard contient PublicationMediaGallery
2. Si media[i].type == video => widget _VideoSlide
3. _VideoSlide initState() => _initialize()
   (UN SEUL VideoPlayerController par slide ; le PageView.builder
    de Flutter ne construit que les slides visibles + adjacents)
   |- VideoPlayerController.networkUrl(Env.mediaUrl(path))
   |- await v.initialize()
   |- v.setLooping(true), v.setVolume(0)  // muet par defaut en feed
   |- setState(_video = v)
4. VisibilityDetector (widget qui mesure la portion visible) :
   |- fraction >= 0.5 && !isPlaying => v.play()
   |- fraction <  0.3 && isPlaying  => v.pause()
5. Si le user tap sur la video => _enableControls()
   => ChewieController avec autoPlay + son active + UI complete
      (seek bar, plein ecran, bouton son)</div>
            <div class="tip">Une tentative de chargement <em>lazy</em> (paresseux : init seulement quand la slide entre dans le <em>viewport</em> = zone visible de l\'ecran) via VisibilityDetector a ete essayee puis <em>reverte</em> (annulee) : <code>v.initialize()</code> echouait silencieusement en production sur Android, meme avec des URLs qui marchaient parfaitement en navigateur. L\'initialisation dans <code>initState</code> reste plus fiable. Seul gain conserve : suppression du <em>probe</em> (sondage prealable) d\'<em>aspect ratio</em> (rapport largeur/hauteur) qui faisait telecharger chaque video en double.</div>
        '],
        ['title' => 'Repositories et controllers Riverpod', 'num' => 5, 'lesson' => '
            <p>Un <strong>repository</strong> (en francais : depot, classe d\'acces aux donnees) encapsule tous les appels HTTP vers l\'API pour une fonctionnalite. Les ecrans ne voient <strong>jamais</strong> directement la librairie HTTP <code>Dio</code>.</p>
            <p><strong>Repositories principaux :</strong> <code>AuthRepository</code>, <code>AuthStorage</code>, <code>ArtisansRepository</code>, <code>PublicationsRepository</code>, <code>CommentsRepository</code>, <code>ConversationsRepository</code>, <code>MessageReactionRepository</code>, <code>ReviewsRepository</code>, <code>FavoritesRepository</code>, <code>PrivacyRepository</code>, <code>NotificationsInboxRepository</code>, <code>CategoriesRepository</code>, <code>ReportRepository</code>, <code>SupportRepository</code>, <code>DashboardRepository</code>, <code>MyPublicationsRepository</code>, <code>MyReviewsRepository</code>.</p>

            <p><strong>Principaux controllers Riverpod :</strong></p>
            <ul>
                <li><code>AuthController</code> : <code>Notifier&lt;AuthState&gt;</code>. Etats possibles : <code>AuthUnknown</code> / <code>AuthUnauthenticated</code> / <code>AuthAuthenticated(user)</code>. Methodes : <code>login</code>, <code>registerClient</code>, <code>refreshUser</code>, <code>logout</code>, etc.</li>
                <li><code>DashboardController</code> : <code>AsyncNotifier&lt;DashboardStats&gt;</code>. La methode <code>build()</code> <em>auto-fetch</em> (charge automatiquement) les stats au montage.</li>
                <li><code>MyPublicationsController</code> : <code>AsyncNotifier&lt;List&lt;Publication&gt;&gt;</code>. CRUD complet avec optimistic update.</li>
                <li><code>ConversationsController</code>, <code>NotificationsInboxController</code>, <code>FavoritesController</code>, <code>PrivacyController</code>, etc.</li>
            </ul>

            <p><strong>Pattern critique :</strong> a chaque transition d\'authentification (login, logout, register, ou 401 sur une requete), la methode <code>AuthController._resetUserScopedState()</code> invalide tous les providers qui dependent de l\'utilisateur connecte :</p>
            <div class="code-example">void _resetUserScopedState() {
  ref.invalidate(myPublicationsControllerProvider);
  ref.invalidate(dashboardControllerProvider);
  ref.invalidate(myReviewsControllerProvider);
  ref.invalidate(conversationsControllerProvider);
  ref.invalidate(notificationsInboxControllerProvider);
  ref.invalidate(favoritesControllerProvider);
  ref.invalidate(privacyControllerProvider);
}</div>
            <div class="tip">Critique pour la securite : sans cette invalidation, un nouvel utilisateur connecte sur le meme telephone pourrait voir le <strong>cache du precedent</strong> (ses publications, ses conversations, ses notifs). L\'invalidation force un <em>re-fetch</em> (rechargement) propre depuis l\'API.</div>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Quelle version de Riverpod est utilisee dans FeGArtisan ?", 'options'=>['1.x', '2.x (Notifier/AsyncNotifier)', '3.x beta', 'Provider classique uniquement'], 'answer'=>1, 'explanation'=>"<strong>Riverpod 2.x</strong> en full : Notifier, AsyncNotifier, FutureProvider. Pas de <code>provider</code> package classique."],
        ['chapter'=>0, 'question'=>"Quel type de provider pour un singleton sans etat mutable ?", 'options'=>['Provider', 'NotifierProvider', 'StateProvider', 'StreamProvider'], 'answer'=>0, 'explanation'=>"<strong>Provider</strong> : singleton sans etat mutable. Utilise pour les services (<code>apiClientProvider</code>) et les repositories."],
        ['chapter'=>0, 'question'=>"Quel type de provider pour un etat mutable avec init async ?", 'options'=>['Provider', 'NotifierProvider', 'AsyncNotifierProvider', 'FutureProvider'], 'answer'=>2, 'explanation'=>"<strong>AsyncNotifierProvider</strong> : etat mutable avec init async. Ex : <code>dashboardControllerProvider</code> charge les stats au mount."],
        ['chapter'=>0, 'question'=>"A quoi sert FutureProvider.autoDispose.family ?", 'options'=>["Fetch HTTP parametre, libere quand l'ecran sort", "Eviter les memory leaks dans les tests", "Synchroniser plusieurs widgets", "Charger les assets"], 'answer'=>0, 'explanation'=>"<code>FutureProvider.autoDispose.family</code> = fetch HTTP parametre (family), libere automatiquement quand plus d'ecouteur (autoDispose). Ex : <code>publicationsProvider(categoryId)</code>, <code>artisanProvider(id)</code>."],
        ['chapter'=>0, 'question'=>"Quel state expose AuthController au demarrage ?", 'options'=>['AuthUnknown', 'AuthAuthenticated', 'AuthUnauthenticated', 'AuthLoading'], 'answer'=>0, 'explanation'=>"<code>AuthController.build()</code> retourne <code>AuthUnknown</code> et lance <code>_bootstrap()</code> en arriere-plan."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Que fait _bootstrap() s'il trouve un token mais que GET /api/me renvoie 401 ?", 'options'=>['Garde l\'etat AuthUnknown', 'storage.clear() + state = AuthUnauthenticated', 'Redirige vers /500', 'Crash de l\'app'], 'answer'=>1, 'explanation'=>"Sur 401/403 : <code>storage.clear()</code> (efface le token invalide) + state = <code>AuthUnauthenticated</code>."],
        ['chapter'=>1, 'question'=>"Quand AuthAuthenticated est emis, le router peut rediriger sur 3 ecrans. Lesquels ?", 'options'=>['/login, /register, /home', '/email-verify (si pas verif), /artisan/pending, /home ou /artisan/dashboard', '/error, /onboarding, /home', '/maintenance, /splash, /home'], 'answer'=>1, 'explanation'=>"3 cas : <code>isWaitingEmailVerify =&gt; /email-verify</code>, <code>isPendingArtisan =&gt; /artisan/pending</code>, sinon <code>/home</code> (client) ou <code>/artisan/dashboard</code>."],
        ['chapter'=>1, 'question'=>"Comment detecter au foreground qu'un email vient d'etre verifie ?", 'options'=>['Polling toutes les 5 sec', "WidgetsBindingObserver : didChangeAppLifecycleState(resumed) => refreshUser()", "Push notif", "Manuel"], 'answer'=>1, 'explanation'=>"<code>main.dart</code> implemente <code>WidgetsBindingObserver</code>. Sur <code>AppLifecycleState.resumed</code>, si <code>user.isWaitingEmailVerify</code>, appelle <code>authController.refreshUser()</code> qui refait GET /api/me."],
        ['chapter'=>1, 'question'=>"Que fait refreshUser() ?", 'options'=>["Recharge l'app", "GET /api/me + setState(AuthAuthenticated(freshUser))", "Logout", "Polling continu"], 'answer'=>1, 'explanation'=>"<code>refreshUser()</code> fait <code>GET /api/me</code> et emet <code>AuthAuthenticated(freshUser)</code>. Le router redirige automatiquement vers /home si le verrou email tombe."],
        ['chapter'=>1, 'question'=>"Que fait _syncPushToken() apres bootstrap reussi ?", 'options'=>["Rien", "POST /me/fcm-token pour enregistrer le token FCM du device", "Envoie un push de test", "Reset Firebase"], 'answer'=>1, 'explanation'=>"Apres bootstrap succes, <code>_syncPushToken()</code> appelle <code>POST /me/fcm-token</code> pour enregistrer le token FCM en base, permettant les push notifs."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Pourquoi NE PAS invalidate(publicationsProvider) apres un like ?", 'options'=>['Trop lent', "Ca detruirait l'AnimationController du LikeBtn et ferait scintiller", "C'est interdit par Riverpod", "Aucune raison"], 'answer'=>1, 'explanation'=>"Invalider le provider detruit le widget et son <code>AnimationController</code>, ce qui fait scintiller l'icone coeur. Solution : <strong>override local</strong> via <code>_likeOverrides[id]</code> dans le widget."],
        ['chapter'=>2, 'question'=>"Quelle structure remplace l'invalidate sur un like ?", 'options'=>['Reload complet', 'Map _likeOverrides[id] = updatedPub dans le widget', 'StreamController', 'Reducer Redux'], 'answer'=>1, 'explanation'=>"<code>setState(() =&gt; _likeOverrides[publication.id] = updatedPub)</code>. Override local au lieu d'invalider le provider global."],
        ['chapter'=>2, 'question'=>"Combien de reactions emoji un user peut-il poser sur un message ?", 'options'=>['Aucune', '1 seule (unique key message_id+user_id)', '3 max', "Illimitee"], 'answer'=>1, 'explanation'=>"<strong>1 seule reaction par user par message</strong>, garanti par une unique key SQL sur <code>(message_id, user_id)</code>."],
        ['chapter'=>2, 'question'=>"Long-press sur un message + meme emoji que celui deja pose : que se passe-t-il ?", 'options'=>['INSERT doublon', 'DELETE (retire la reaction)', 'UPDATE valeur', 'Erreur'], 'answer'=>1, 'explanation'=>"Toggle intelligent : meme emoji =&gt; <strong>DELETE</strong> (retire). Autre emoji =&gt; UPDATE (remplace). Aucune existante =&gt; INSERT."],
        ['chapter'=>2, 'question'=>"Comment s'affiche une reaction posee par l'utilisateur courant ?", 'options'=>["Identique aux autres", "Avec une bordure terracotta", "Cachee", "En clignotant"], 'answer'=>1, 'explanation'=>"<code>_ReactionsRow</code> affiche les chips comptes avec <strong>bordure terracotta</strong> si la reaction est la mienne."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"Combien de VideoPlayerController par slide video dans le feed ?", 'options'=>['Un seul par slide', 'Un par feed entier', 'Plusieurs en parallele', 'Aucun, on utilise WebView'], 'answer'=>0, 'explanation'=>"<strong>1 seul VideoPlayerController par slide</strong>. PageView.builder ne construit que les slides visibles + adjacents."],
        ['chapter'=>3, 'question'=>"Comportement par defaut d'une video au scroll dans le feed ?", 'options'=>['Toujours muette en auto-play', 'Avec son a 100%', 'Pause automatique', "Pas de lecture"], 'answer'=>0, 'explanation'=>"Auto-play <strong>muet par defaut</strong> en feed (<code>v.setVolume(0)</code>) + <code>v.setLooping(true)</code>. Pour avoir le son et les controles, l'user tap dessus =&gt; ChewieController."],
        ['chapter'=>3, 'question'=>"Que detecte VisibilityDetector pour piloter play/pause ?", 'options'=>["Le scroll seul", "Fraction visible >= 0.5 => play, < 0.3 => pause", "Le clic", "Le wifi"], 'answer'=>1, 'explanation'=>"Quand le slide a une fraction visible <code>&gt;= 0.5</code> il joue. Quand <code>&lt; 0.3</code> il pause. Evite que toutes les videos jouent en meme temps."],
        ['chapter'=>3, 'question'=>"Pourquoi le lazy-load video via VisibilityDetector a-t-il ete reverte ?", 'options'=>["Trop lent", "v.initialize() echouait silencieusement en prod Android malgre URLs valides", "Pas supporte par Flutter", "Pas utile"], 'answer'=>1, 'explanation'=>"En prod sur Android, l'init differee echouait silencieusement meme avec URLs qui marchaient en navigateur. L'init en <code>initState</code> reste plus fiable. Seul gain conserve : suppression du probe d'aspect ratio qui doublait le download."],
        ['chapter'=>3, 'question'=>"Quel widget affiche la video en plein ecran au tap ?", 'options'=>['VideoPlayer brut', 'ChewieController (UI complete : seek, plein ecran, son)', 'WebView', 'Image.network'], 'answer'=>1, 'explanation'=>"Au tap sur la video du feed, <code>_enableControls()</code> cree un <code>ChewieController</code> avec autoPlay + son active + UI complete (seek bar, plein ecran)."],

        // CHAPITRE 5
        ['chapter'=>4, 'question'=>"Qu'est-ce qu'un repository dans cette architecture Flutter ?", 'options'=>["Un widget", "Une classe qui encapsule tous les appels HTTP pour une feature", "Un service Riverpod", "Un mixin"], 'answer'=>1, 'explanation'=>"Un <strong>repository</strong> encapsule tous les appels HTTP vers l'API pour une feature donnee. Les ecrans ne voient jamais Dio directement."],
        ['chapter'=>4, 'question'=>"Quel pattern critique est applique a chaque transition d'auth (login, logout, 401) ?", 'options'=>["Refresh manuel par l'user", "AuthController._resetUserScopedState() invalide tous les providers dependant du user", "Logout obligatoire", "Aucun"], 'answer'=>1, 'explanation'=>"<code>_resetUserScopedState()</code> invalide tous les providers qui dependent du user (publications, dashboard, conversations, notifs, favoris, privacy)."],
        ['chapter'=>4, 'question'=>"Que se passerait-il sans ce reset ?", 'options'=>['Rien', "Le nouvel utilisateur verrait le cache du precedent (failure de securite)", "App plus rapide", "Compilation echoue"], 'answer'=>1, 'explanation'=>"Sans le reset, un nouvel utilisateur connecte verrait les publications, conversations et notifs du <strong>precedent utilisateur</strong> en cache. <strong>Faille de securite</strong>."],
        ['chapter'=>4, 'question'=>"Quel controller expose la liste des publications de l'artisan connecte ?", 'options'=>['PublicationsController (feed public)', 'MyPublicationsController : AsyncNotifier<List<Publication>>', 'AdminPublicationsController', 'AllPubsController'], 'answer'=>1, 'explanation'=>"<code>MyPublicationsController</code> : AsyncNotifier qui charge les publications de l'artisan connecte. Methodes : create, update, delete avec optimistic update."],
        ['chapter'=>4, 'question'=>"Combien de repositories environ dans l'app ?", 'options'=>['3-4', '5-7', '15-17 (auth, artisans, publications, commentaires, conversations, etc.)', '50+'], 'answer'=>2, 'explanation'=>"Environ <strong>15-17 repositories</strong> : Auth, AuthStorage, Artisans, Publications, Comments, Conversations, MessageReaction, Reviews, Favorites, Privacy, NotificationsInbox, Categories, Report, Support, Dashboard, MyPublications, MyReviews."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
