@extends('layouts.app')
@section('title', 'Backend Laravel FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-laravel',
        'title' => 'Backend Laravel',
        'subtitle' => '25 questions . 5 chapitres . Controllers, flux, middlewares, events',
        'badge' => 'LARAVEL',
        'color' => '#FF2D20',
        'description' => 'Tout ce qui tourne cote serveur : controllers Admin/Api, middlewares custom, flux applicatifs (inscription, like, vue profil), events et listeners, services.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Controllers Admin et controllers API', 'num' => 1, 'lesson' => '
            <p>Laravel separe physiquement les <strong>controllers Admin</strong> (qui gerent le <em>dashboard</em> web sur lequel on s\'authentifie par <em>session cookie</em>, c\'est-a-dire un cookie de session stocke par le navigateur) des <strong>controllers API</strong> (qui repondent a l\'application Flutter authentifiee par <em>Bearer token Sanctum</em>, jeton porte dans l\'en-tete <code>Authorization</code>).</p>

            <p><strong>Controllers Admin (cote web) :</strong></p>
            <ul>
                <li><code>AuthController</code> : connexion administrateur</li>
                <li><code>DashboardController</code> : page d\'accueil avec statistiques globales</li>
                <li><code>UserController</code> : validation des artisans, consultation du dossier detaille, lecture du justificatif prive</li>
                <li><code>CategoryController</code> : <em>CRUD</em> (Create / Read / Update / Delete : les 4 operations de base sur les donnees) des categories + upload de l\'image de couverture</li>
                <li><code>PublicationController</code> : moderation du <em>feed</em> (fil de publications)</li>
                <li><code>ReportController</code> : gestion des signalements</li>
                <li><code>AdministratorController</code> : gestion des autres administrateurs (reserve au super-admin)</li>
                <li><code>ProfileController</code> : profil de l\'admin connecte</li>
                <li><code>TrashController</code> : corbeille avec retention de 30 jours</li>
            </ul>

            <p><strong>Controllers API (cote mobile, authentification Bearer Sanctum) :</strong></p>
            <ul>
                <li><code>AuthController</code> : <code>registerClient</code>, <code>registerArtisanStep1/2</code>, <code>login</code>, <code>logout</code>, <code>me</code> (renvoie le profil de l\'utilisateur connecte), <code>updateProfile</code>, <code>deleteAccount</code> (suppression RGPD), <code>changePassword</code>, <code>privacy</code>, <code>saveFcmToken</code>, <code>deleteFcmToken</code>, <code>forgotPassword</code>, <code>resetPassword</code>, <code>resendVerificationEmail</code></li>
                <li><code>ArtisanController</code> : <code>index</code> (recherche), <code>show</code>, <code>dashboard</code> (statistiques de l\'artisan), <code>searchHistory</code></li>
                <li><code>PublicationController</code> : <code>index</code>, <code>show</code>, <code>store</code> (creer), <code>update</code>, <code>destroy</code>, <code>mine</code>, <code>byArtisan</code>, <code>toggleLike</code>, <code>share</code></li>
                <li><code>CommentController</code> : <code>index</code>, <code>store</code>, <code>update</code>, <code>destroy</code>, <code>toggleLike</code></li>
                <li><code>ConversationController</code>, <code>MessageReactionController</code>, <code>NotificationController</code>, etc.</li>
            </ul>

            <div class="tip">L\'admin se connecte par session cookie sur <code>/admin/login</code>. <strong>Il ne peut PAS</strong> se connecter via l\'API Flutter — c\'est un verrou de securite volontaire (un compte admin compromis sur mobile aurait des consequences plus graves qu\'un simple compte utilisateur).</div>
        '],
        ['title' => 'Flux d\'inscription client + verification email', 'num' => 2, 'lesson' => '
            <p>L\'inscription client consiste en un formulaire decoupe en 3 etapes cote Flutter, mais un <strong>seul appel API</strong> reel cote serveur. Le token est pose immediatement, puis le <em>router</em> (composant Flutter qui gere la navigation entre ecrans) verrouille l\'utilisateur sur la page <code>/email-verify</code> tant que l\'email n\'est pas confirme.</p>
            <div class="code-example">1. Flutter : RegisterClientScreen (formulaire en 3 etapes UI)
2. POST /api/register/client          (throttle:10,1 = max 10 req/min/IP)
3. AuthController::registerClient
   |- valide : email unique, password >= 8 caracteres, ville requise
   |- User::create() avec role=client, email_verified_at = NULL
   |- $user->createToken() => Bearer token Sanctum
   |- $user->notify(new VerifyEmailNotification)
        |- mail envoye via Brevo SMTP (service mail transactionnel)
        |- contient une URL signee temporaire (valide 7 jours)
4. Reponse JSON : { token, user }
5. Flutter stocke le token (flutter_secure_storage), state = AuthAuthenticated
6. Router Flutter bloque sur /email-verify car emailVerifiedAt == null
7. User clique sur le lien recu par mail
8. GET /email/verify/{id}/{hash}      (middleware "signed" qui valide la signature)
9. EmailVerificationController::verify
   |- markEmailAsVerified() => set email_verified_at = now()
10. User revient dans l\'app (basculement foreground)
    didChangeAppLifecycleState(resumed) => authController.refreshUser()
11. GET /api/me retourne un user avec email_verified_at != null
12. Le router Flutter bascule automatiquement sur /home</div>
            <div class="tip">Critique : tant que <code>email_verified_at</code> est <code>NULL</code>, le router Flutter verrouille sur <code>/email-verify</code>. Le rafraichissement automatique au passage en <em>foreground</em> (retour au premier plan de l\'application) evite a l\'utilisateur d\'avoir a se reconnecter manuellement apres avoir clique sur le lien.</div>
        '],
        ['title' => 'Flux d\'inscription artisan (2 etapes + validation admin)', 'num' => 3, 'lesson' => '
            <p>L\'inscription d\'un artisan se fait en 2 etapes API, suivies d\'une <strong>validation manuelle par un administrateur</strong> avant que le compte puisse publier.</p>

            <div class="code-example">--- Etape 1 (route publique) ---
POST /api/register/artisan            (throttle:10,1)
- valide les infos de base (nom, email, tel, ville, quartier, password)
- User::create() avec role=artisan, email_verified_at = NULL
- createToken() => retourne { token, user }

--- Etape 2 (route authentifiee, Bearer requis) ---
POST /api/register/artisan/verification
- valide proof_document (PDF/JPG/PNG, taille max 5 Mo)
- valide proof_type (diplome | certificat | preuve_experience)
- valide category_id (lien vers la categorie de metier choisie)
- upload du document => storage/app/private/proofs/ (disque LOCAL PRIVE
  hors public, accessible uniquement par l\'admin via Laravel)
- ArtisanProfile::create() avec validation_status="pending"

--- Cote administrateur ---
- L\'admin se rend sur /admin/users?validation=pending
- Click sur un dossier => /admin/users/{id}
- Click "Consulter le document" => /admin/users/{id}/proof-document
  - Storage::disk("local")->response() avec Content-Disposition: inline
  - le document s\'ouvre directement dans le navigateur (pas en telechargement)
- Click "Valider" => PATCH /admin/users/{id}/verify-artisan
  - validation_status = "approved", validated_at = now()
  - Mail::to($user)->send(new ArtisanApprovedMail)
    (mail avec URL signee /email/verify integree)

- Flutter : l\'artisan ouvre l\'app => /api/me retourne
  status="approved", email_verified_at=null
- Le router bloque sur /email-verify (meme flux que pour le client)
- Une fois l\'email confirme => acces /home (dashboard artisan)</div>
            <div class="tip">Tant que <code>validation_status != "approved"</code>, le <em>middleware</em> (filtre de requete Laravel) <code>artisan.verified</code> bloque toute <strong>ecriture</strong> (creation/modification/suppression de publications). En revanche, la <strong>lecture</strong> reste autorisee (dashboard, mes publications) pour que l\'artisan puisse explorer son interface en attendant la decision.</div>
        '],
        ['title' => 'Middlewares custom (filtres de requete)', 'num' => 4, 'lesson' => '
            <p>Les <strong>middlewares</strong> (filtres qui s\'executent avant ou apres chaque requete HTTP, pour appliquer des controles transverses) custom du projet sont dans <code>app/Http/Middleware/</code>.</p>

            <p><strong>Middlewares globaux API</strong> (appliques a toutes les routes de <code>routes/api.php</code>) :</p>
            <ul>
                <li><code>LogRequestDuration</code> (<em>prepend</em> = execute avant tout, en premier dans la pile) : mesure le temps total + le nombre de queries SQL par requete. Ecrit dans <code>storage/logs/performance-YYYY-MM-DD.log</code>. Detecte les <em>N+1</em> (anti-pattern Eloquent ou une boucle declenche N requetes au lieu d\'une seule, signale a partir de 30 queries). Effectue un <em>dump SQL</em> (impression detaillee des requetes) si plus de 10 queries.</li>
                <li><code>TouchLastSeen</code> (<em>append</em> = execute apres le controller, en dernier) : met a jour <code>users.last_seen_at = now()</code> a chaque requete authentifiee. <em>Throttle</em> (limite) via cache : 1 UPDATE maximum par minute par utilisateur. C\'est la source de l\'indicateur "En ligne / Hors ligne" affiche dans l\'app.</li>
            </ul>

            <p><strong>Middlewares appliques selon le groupe de routes (alias) :</strong></p>
            <ul>
                <li><code>auth:sanctum</code> : renvoie HTTP 401 (Unauthorized) si le Bearer token est absent ou invalide.</li>
                <li><code>EnsureUserIsActive</code> : si la colonne <code>is_active</code> du user vaut <code>false</code>, supprime le token courant et renvoie HTTP 403 (Forbidden) avec le message "Compte suspendu".</li>
                <li><code>artisan.role</code> (classe <code>EnsureIsArtisan</code>) : 403 si <code>role != "artisan"</code>.</li>
                <li><code>artisan.verified</code> (classe <code>EnsureArtisanIsVerified</code>) : 403 sur les ecritures si <code>validation_status != "approved"</code>.</li>
                <li><code>admin</code> (classe <code>EnsureIsAdmin</code>) : redirige vers <code>/admin/login</code> si pas de session admin active.</li>
                <li><code>admin.manage</code> (classe <code>EnsureCanManageAdmins</code>) : 403 si <code>can_manage_admins != true</code> — reserve au <em>super-admin</em>.</li>
                <li><code>signed</code> (middleware natif Laravel) : 403 si la signature de l\'URL est invalide ou expiree (utilise pour <code>/email/verify</code>).</li>
                <li><code>throttle:N,M</code> : <em>rate limiting</em> (limitation de debit) — maximum N requetes par M minutes par IP. Exemple : <code>throttle:5,1</code> = 5 requetes par minute.</li>
            </ul>
        '],
        ['title' => 'Events, Listeners et Services', 'num' => 5, 'lesson' => '
            <p>Un <em>event</em> (evenement) est un message diffuse dans l\'application Laravel quand quelque chose se passe (un message envoye, un like, etc.). Un <em>listener</em> (ecouteur) est une classe qui reagit a un event en executant une action.</p>

            <p>Laravel 12 utilise l\'<strong>auto-discovery des listeners</strong> (decouverte automatique) : tout listener qui implemente une methode <code>handle(EventType $event)</code> est enregistre automatiquement par le framework. <strong>Aucun</strong> appel <code>Event::listen()</code> manuel dans <code>AppServiceProvider</code> n\'est necessaire — au contraire, en faire un declencherait l\'execution <em>en double</em>.</p>

            <p><strong>Event principal :</strong></p>
            <ul>
                <li><code>NewNotification</code> : emis a chaque creation de message, like, commentaire, visite de profil ou validation d\'artisan. <em>Payload</em> (charge utile, donnees transportees) : <code>userId</code> (destinataire), <code>type</code>, <code>title</code>, <code>body</code>, <code>data</code> (tableau de donnees contextuelles).</li>
            </ul>

            <p><strong>Listeners attaches a NewNotification :</strong></p>
            <ul>
                <li><code>PersistNotification</code> : INSERT dans la table <code>notifications</code> (systeme natif Laravel, <em>polymorphique</em> sur la colonne <code>notifiable</code> = peut pointer vers n\'importe quel modele). Alimente le centre de notifications expose par <code>GET /api/notifications</code>.</li>
                <li><code>SendFcmForNotification</code> : recupere le <code>fcm_token</code> du destinataire et appelle <code>FirebasePushService::sendToUser()</code>. Marque <strong>synchrone</strong> (pas d\'interface <code>ShouldQueue</code>) car Hostinger ne dispose pas de <em>queue worker</em> persistant.</li>
            </ul>

            <p><strong>Services <em>singletons</em></strong> (classes instanciees une seule fois par cycle de requete) :</p>
            <ul>
                <li><code>FirebasePushService</code> : envoi des push via l\'<em>API HTTP v1</em> de Firebase (interface moderne). Lit les <em>credentials</em> (identifiants secrets) depuis <code>storage/firebase/service-account.json</code> (compte de service <em>OAuth2</em>, protocole d\'autorisation Google). Methode principale : <code>sendToUser($userId, $title, $body, $data)</code>. Gere les retours d\'erreur Firebase : si le token est invalide, nettoyage automatique en base.</li>
            </ul>

            <div class="tip"><strong>Bug historique :</strong> ces listeners etaient au depart aussi enregistres manuellement dans <code>AppServiceProvider::boot</code>. Resultat : ils tournaient <strong>2 fois par event</strong> — notifications dupliquees en base + push FCM envoye en double. Correction : on s\'appuie uniquement sur l\'auto-discovery (les listeners doivent juste typer leur parametre <code>handle(NewNotification $event)</code>).</div>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Ou se trouvent les controllers du dashboard admin (sessions web) ?", 'options'=>['app/Http/Controllers/', 'app/Http/Controllers/Admin/', 'app/Admin/', 'resources/views/admin/'], 'answer'=>1, 'explanation'=>"Les controllers admin sont dans <code>app/Http/Controllers/Admin/</code>, separes des controllers API."],
        ['chapter'=>0, 'question'=>"Ou se trouvent les controllers de l'API Flutter (Bearer Sanctum) ?", 'options'=>['app/Http/Controllers/', 'app/Http/Controllers/Api/', 'app/Api/', 'routes/api.php'], 'answer'=>1, 'explanation'=>"Les controllers API sont dans <code>app/Http/Controllers/Api/</code>."],
        ['chapter'=>0, 'question'=>"L'admin peut-il se connecter via l'API Flutter ?", 'options'=>['Oui, automatiquement', 'Non, c\'est intentionnellement bloque pour la securite', 'Seulement en lecture', 'Avec un second token'], 'answer'=>1, 'explanation'=>"L'admin ne peut <strong>PAS</strong> se connecter via l'API Flutter. Il utilise uniquement <code>/admin/login</code> en session web."],
        ['chapter'=>0, 'question'=>"Quel controller admin gere la corbeille 30 jours ?", 'options'=>['AdminController', 'TrashController', 'CleanupController', 'DeleteController'], 'answer'=>1, 'explanation'=>"<code>TrashController</code> liste les enregistrements SoftDeleted par onglet et permet restaure / purge definitive."],
        ['chapter'=>0, 'question'=>"Quelle action est reservee au Super Admin via le middleware admin.manage ?", 'options'=>["Voir le dashboard", "Gerer les autres admins (ajouter, suspendre)", "Lire le journal", "Vider le cache"], 'answer'=>1, 'explanation'=>"<code>EnsureCanManageAdmins</code> filtre les actions reservees au super-admin via la colonne <code>can_manage_admins=true</code>."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Quelle methode HTTP et endpoint pour l'inscription client ?", 'options'=>['GET /api/register', 'POST /api/register/client', 'PUT /api/users', 'POST /api/clients/new'], 'answer'=>1, 'explanation'=>"<code>POST /api/register/client</code> avec throttle 10/min."],
        ['chapter'=>1, 'question'=>"A la creation d'un compte client, que vaut email_verified_at ?", 'options'=>['now()', 'NULL', 'Faux', 'Une URL'], 'answer'=>1, 'explanation'=>"<code>email_verified_at = NULL</code> a la creation. Sera mis a <code>now()</code> apres click sur le lien de verification."],
        ['chapter'=>1, 'question'=>"Quel middleware protege la route /email/verify/{id}/{hash} ?", 'options'=>['auth', 'signed', 'throttle', 'csrf'], 'answer'=>1, 'explanation'=>"Le middleware <strong>signed</strong> verifie la signature de l'URL temporaire (7 jours d'expiration)."],
        ['chapter'=>1, 'question'=>"Comment le router Flutter detecte-t-il que l'email vient d'etre confirme ?", 'options'=>["Polling continu", "Au foreground, didChangeAppLifecycleState(resumed) => refreshUser() => GET /api/me", "Via Firebase Realtime DB", "Manuellement par l'utilisateur"], 'answer'=>1, 'explanation'=>"Quand l'app revient au premier plan, <code>didChangeAppLifecycleState(resumed)</code> appelle <code>authController.refreshUser()</code> qui refait <code>GET /api/me</code>. Si <code>email_verified_at</code> est non-null, le router bascule sur <code>/home</code>."],
        ['chapter'=>1, 'question'=>"Quel service envoie le mail de verification ?", 'options'=>['SendGrid', 'Brevo SMTP', 'Mailgun', 'AWS SES'], 'answer'=>1, 'explanation'=>"<strong>Brevo SMTP</strong> est le mailer transactionnel utilise pour la verification email, le reset password et les notifs admin."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Combien d'etapes a l'inscription artisan ?", 'options'=>['1', '2', '3', '4'], 'answer'=>1, 'explanation'=>"<strong>2 etapes</strong> : etape 1 publique (infos de base + token), etape 2 authentifiee (upload justificatif + categorie)."],
        ['chapter'=>2, 'question'=>"Ou est stocke le justificatif d'artisan ?", 'options'=>["storage/app/public/proofs/ (public)", "storage/app/private/proofs/ (prive, disque local)", "storage/firebase/", "/uploads/"], 'answer'=>1, 'explanation'=>"Dans <code>storage/app/private/proofs/</code> sur le disque local, <strong>hors du dossier public</strong>. Accessible uniquement via le controller admin via <code>Storage::disk('local')-&gt;response()</code>."],
        ['chapter'=>2, 'question'=>"Quel est le statut initial de validation d'un artisan ?", 'options'=>["approved", "pending", "rejected", "suspended"], 'answer'=>1, 'explanation'=>"<code>validation_status = 'pending'</code> a la creation. L'admin doit valider pour passer a <code>approved</code>."],
        ['chapter'=>2, 'question'=>"Que se passe-t-il quand l'admin clique 'Valider' sur un dossier artisan ?", 'options'=>["Email envoye seulement", "PATCH /admin/users/{id}/verify-artisan => approved + Mail ArtisanApprovedMail", "Suppression du compte", "Aucun changement"], 'answer'=>1, 'explanation'=>"PATCH /admin/users/{id}/verify-artisan : <code>validation_status='approved'</code>, <code>validated_at=now()</code>, et envoi de <code>ArtisanApprovedMail</code> avec URL signee /email/verify."],
        ['chapter'=>2, 'question'=>"Pourquoi un artisan approved mais non email-verifie est-il bloque sur /email-verify ?", 'options'=>['Bug', 'Le router Flutter exige email_verified_at != null avant /home', 'Pour faire patienter', 'Pas bloque'], 'answer'=>1, 'explanation'=>"Le router Flutter applique le verrou <code>isWaitingEmailVerify</code> : <code>isArtisan && status == approved && !isEmailVerified</code>. Empeche l'acces a /home tant que l'email n'est pas confirme."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"A quoi sert le middleware LogRequestDuration ?", 'options'=>["Logger les visites", "Mesurer temps + nb queries SQL par requete, detecter N+1 (>30 queries)", "Logger les IP suspectes", "Compter les utilisateurs"], 'answer'=>1, 'explanation'=>"<code>LogRequestDuration</code> mesure le temps total et le nombre de queries SQL. Log dans <code>storage/logs/performance-YYYY-MM-DD.log</code>. Detecte les N+1 (&gt;30 queries) et dump SQL detaille si &gt;10 queries."],
        ['chapter'=>3, 'question'=>"Quelle est la frequence max d'UPDATE de users.last_seen_at par user ?", 'options'=>['Chaque requete', '1 fois par minute par user (throttle via cache)', '1 fois par heure', '1 fois par jour'], 'answer'=>1, 'explanation'=>"<code>TouchLastSeen</code> throttle a <strong>1 UPDATE max par minute par user</strong> via cache. Sans ca, chaque requete API genererait un UPDATE additionnel."],
        ['chapter'=>3, 'question'=>"Que fait le middleware EnsureUserIsActive si is_active=false ?", 'options'=>['Redirige vers /home', 'Supprime le token courant et renvoie 403 "Compte suspendu"', 'Ne fait rien', 'Bloque pendant 24h'], 'answer'=>1, 'explanation'=>"<code>EnsureUserIsActive</code> <strong>supprime le token courant</strong> (deconnecte) et renvoie <strong>403 \"Compte suspendu\"</strong>."],
        ['chapter'=>3, 'question'=>"Quelle est la difference entre artisan.role et artisan.verified ?", 'options'=>["Aucune", "role = doit avoir role=artisan ; verified = doit etre approved (bloque l'ecriture)", "role est plus fort", "verified est pour les emails"], 'answer'=>1, 'explanation'=>"<code>artisan.role</code> verifie que <code>role=artisan</code>. <code>artisan.verified</code> verifie en plus que <code>validation_status=approved</code> => utilise pour bloquer l'ecriture (creation/modif publications)."],
        ['chapter'=>3, 'question'=>"Combien de requetes max permet le throttle 10/min ?", 'options'=>['1 par 10 minutes', '10 par minute par IP', '10 par jour', 'Illimite'], 'answer'=>1, 'explanation'=>"<code>throttle:10,1</code> = max <strong>10 requetes par 1 minute</strong> par IP. Sinon 429 Too Many Requests."],

        // CHAPITRE 5
        ['chapter'=>4, 'question'=>"Comment Laravel 12 enregistre-t-il les listeners ?", 'options'=>["Manuellement dans AppServiceProvider::boot", "Auto-discovery : tout listener avec handle(EventType $event) est auto enregistre", "Via config/listeners.php", "Avec un attribut #[Listener]"], 'answer'=>1, 'explanation'=>"Laravel 12 utilise <strong>l'auto-discovery</strong>. Il suffit de typer la methode <code>handle(NewNotification $event)</code>. Pas de <code>Event::listen()</code> manuel sinon double execution."],
        ['chapter'=>4, 'question'=>"Que se passe-t-il si on enregistre manuellement les listeners en plus de l'auto-discovery ?", 'options'=>['Rien', 'Les listeners tournent 2 fois par event => notifs et push doubles', 'Bug fatal', 'Plus rapide'], 'answer'=>1, 'explanation'=>"Bug historique : double enregistrement => listeners tournent <strong>2 fois par event</strong> => notifications dupliquees en DB + push FCM double. Fix : auto-discovery uniquement."],
        ['chapter'=>4, 'question'=>"Quel listener insere dans la table notifications (systeme natif Laravel) ?", 'options'=>['SendFcmForNotification', 'PersistNotification', 'LogNotification', 'CreateNotification'], 'answer'=>1, 'explanation'=>"<code>PersistNotification</code> ecoute <code>NewNotification</code> et INSERT dans la table <code>notifications</code> (polymorphe sur notifiable). Permet le centre de notifs via <code>GET /api/notifications</code>."],
        ['chapter'=>4, 'question'=>"Quel listener envoie le push FCM ?", 'options'=>['PersistNotification', 'SendFcmForNotification (synchrone, pas ShouldQueue)', 'QueueFcmJob', 'FirebaseListener'], 'answer'=>1, 'explanation'=>"<code>SendFcmForNotification</code>. Synchrone (<strong>pas de <code>ShouldQueue</code></strong>) car Hostinger n'a pas de queue worker persistant."],
        ['chapter'=>4, 'question'=>"Ou sont stockees les credentials Firebase service account ?", 'options'=>["Dans .env directement", "storage/firebase/service-account.json (hors git)", "Dans config/services.php", "Dans la base de donnees"], 'answer'=>1, 'explanation'=>"<code>storage/firebase/service-account.json</code> (clef OAuth2 service account). <strong>Pas dans git</strong>, uploadee manuellement via SCP sur Hostinger."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
