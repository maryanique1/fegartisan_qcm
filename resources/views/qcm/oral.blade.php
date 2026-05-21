@extends('layouts.app')
@section('title', 'Examen oral blanc - FeGArtisan QCM')

@section('styles')
    .container { max-width:860px; margin:0 auto; padding:30px 20px; }
    .oral-header { text-align:center; margin-bottom:28px; }
    .oral-header h1 { color:var(--accent); font-size:28px; margin-bottom:6px; }
    .oral-header p { color:var(--muted); font-size:14px; max-width:560px; margin:0 auto; line-height:1.6; }

    .oral-actions { display:flex; justify-content:center; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
    .btn { padding:11px 22px; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-primary { background:var(--accent); color:#fff; }
    .btn-primary:hover { filter:brightness(1.1); }
    .btn-restart { background:var(--input); color:var(--text); border:1px solid var(--border); }
    .btn-restart:hover { background:var(--accent-2); color:#fff; border-color:var(--accent-2); }

    .oral-counter { text-align:center; color:var(--muted); font-size:13px; letter-spacing:1px; font-weight:600; margin-bottom:18px; }
    .oral-card {
        background:var(--card); border-radius:16px; padding:32px 28px; min-height:300px;
        border:2px solid rgba(193,123,78,0.2); box-shadow:0 8px 24px rgba(107,45,14,0.08); margin-bottom:24px;
    }
    .oral-num { display:inline-block; background:var(--accent); color:#fff; font-size:11px; padding:4px 12px; border-radius:14px; letter-spacing:2px; font-weight:800; margin-bottom:14px; }
    .oral-question { font-size:20px; color:var(--text); line-height:1.6; font-weight:600; }

    .oral-answer { display:none; margin-top:22px; padding-top:20px; border-top:2px dashed var(--border); }
    .oral-answer .lbl { color:#4A7C59; font-size:11px; font-weight:800; letter-spacing:2px; margin-bottom:10px; }
    .oral-answer .body { color:var(--text); font-size:15px; line-height:1.75; }
    .oral-answer .body code { background:var(--input); padding:1px 6px; border-radius:4px; font-family:'Consolas',monospace; font-size:13px; color:var(--accent); }
    .oral-answer .body em { color:var(--accent-2); font-style:normal; font-weight:600; }

    .oral-tips { background:rgba(193,123,78,0.08); border-left:4px solid var(--accent-2); border-radius:0 10px 10px 0; padding:16px 18px; margin-top:18px; }
    .oral-tips .lbl { color:var(--accent); font-size:11px; font-weight:800; letter-spacing:2px; margin-bottom:6px; }
    .oral-tips .body { color:var(--text); font-size:13px; font-style:italic; line-height:1.6; }

    .oral-controls { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; }
    .oral-progress { background:var(--input); border-radius:10px; height:8px; margin-bottom:14px; overflow:hidden; }
    .oral-progress-fill { height:100%; background:linear-gradient(90deg, #6B2D0E, #C17B4E); border-radius:10px; transition:width .3s ease; }
    @media (max-width:600px) { .oral-card { padding:24px 18px; } .oral-question { font-size:17px; } .oral-answer .body { font-size:14px; } }
@endsection

@section('content')
<div class="container">
    <div class="oral-header">
        <h1>Examen oral blanc</h1>
        <p>Simulation des questions les plus probables du jury. <strong>Lisez la question, formulez votre reponse a voix haute</strong>, puis revelez la reponse attendue pour comparer.</p>
    </div>

    <div class="oral-actions">
        <button class="btn btn-restart" onclick="prevQ()">&larr; Precedente</button>
        <button class="btn btn-restart" onclick="shuffleQ()">Melanger</button>
        <button class="btn btn-restart" onclick="nextQ()">Suivante &rarr;</button>
    </div>

    <div class="oral-counter" id="oral-counter"></div>
    <div class="oral-progress"><div class="oral-progress-fill" id="oral-progress-fill"></div></div>

    <div class="oral-card">
        <div class="oral-num" id="oral-num">Q1</div>
        <div class="oral-question" id="oral-question"></div>

        <div class="oral-answer" id="oral-answer">
            <div class="lbl">REPONSE ATTENDUE</div>
            <div class="body" id="oral-answer-body"></div>

            <div class="oral-tips" id="oral-tips" style="display:none">
                <div class="lbl">CONSEIL DE FORMULATION</div>
                <div class="body" id="oral-tips-body"></div>
            </div>
        </div>
    </div>

    <div class="oral-controls">
        <button class="btn btn-primary" id="btn-reveal" onclick="reveal()">Voir la reponse</button>
    </div>
</div>

<script>
const ORAL_QUESTIONS = [
    {
        q: "Pouvez-vous nous presenter votre projet en 1 minute ?",
        a: "<strong>FeGArtisan</strong> est une application mobile qui met en relation <em>artisans</em> et <em>clients</em> au Benin. Elle resout le manque de visibilite des artisans (95.2 % le citent comme obstacle no 1 dans notre sondage) et la difficulte des clients a trouver rapidement un professionnel competent et proche. Stack technique : <em>Flutter</em> pour le mobile, <em>Laravel 12</em> pour le backend, <em>TiDB Cloud</em> pour la base de donnees et <em>Firebase FCM</em> pour les notifications push. Le projet est en production sur Hostinger.",
        tip: "Donnez le nom + le probleme resolu + la cible + la stack + ou c\'est deploye. Toujours dans cet ordre."
    },
    {
        q: "Pourquoi avoir choisi Laravel plutot que Node.js ou Django ?",
        a: "Quatre raisons : (1) <em>maturite</em> du framework qui integre nativement l\'authentification (Sanctum), la validation, l\'ORM Eloquent et les queues ; (2) syntaxe <em>MVC</em> claire et facile a expliquer en soutenance ; (3) hosting PHP omnipresent et abordable en Afrique de l\'Ouest (cPanel partout) ; (4) notre formation HECM est principalement PHP-centric. Django et Spring Boot sont d\'excellents frameworks mais leur hosting partage est plus rare au Benin et leurs couts d\'exploitation plus eleves.",
        tip: "Ne dites pas que Laravel est superieur. Dites que c\'est le bon choix pour CE contexte (Benin, budget, formation)."
    },
    {
        q: "Quelle a ete la difficulte technique principale et comment l\'avez-vous resolue ?",
        a: "La <em>messagerie en temps reel</em> sur Hostinger mutualise. Probleme : Hostinger n\'autorise pas les <em>WebSockets persistants</em> ni les <em>queue workers</em>, donc Laravel Reverb etait impossible. Solution : combiner deux mecanismes complementaires. Du <em>polling court</em> (sondage HTTP toutes les 3 secondes quand le chat est ouvert) pour le foreground, et des <em>push FCM</em> (notifications Firebase) quand l\'app est en arriere-plan. Le polling pause automatiquement sur <code>AppLifecycleState.paused</code> pour economiser la batterie.",
        tip: "C\'est LA question la plus probable. Maitrisez le polling + FCM par coeur."
    },
    {
        q: "Comment securisez-vous l\'authentification de l\'API ?",
        a: "Trois couches. (1) <em>Laravel Sanctum</em> qui genere des <em>Bearer tokens</em> stockes en base, envoyes dans l\'en-tete <code>Authorization: Bearer ...</code> a chaque requete. (2) Les mots de passe sont hashes avec <em>bcrypt</em> via <code>Hash::make()</code>, irreversible. (3) Cote Flutter, le token est stocke dans <em>flutter_secure_storage</em> qui utilise l\'Android Keystore ou l\'iOS Keychain, donc chiffre par le systeme. Plus un middleware <em>EnsureUserIsActive</em> qui supprime le token si le compte est suspendu, et un rate-limit <code>throttle:5,1</code> sur le login pour bloquer le brute-force.",
        tip: "Citez les 3 couches : Sanctum + bcrypt + flutter_secure_storage. C\'est ce que le jury veut entendre."
    },
    {
        q: "Que se passe-t-il si 10 000 utilisateurs se connectent en meme temps ?",
        a: "Hostinger shared saturerait vite (limite de <em>256 Mo PHP-FPM</em> par requete). Plan de scaling : (1) migrer vers Hostinger VPS ou Cloud pour avoir des ressources dediees ; (2) basculer sessions et cache vers <em>Redis</em> au lieu du disque ; (3) ajouter un <em>CDN</em> Cloudflare pour les medias ; (4) si necessaire, un load balancer avec plusieurs instances Laravel. Le backend est <em>stateless</em> grace aux tokens Sanctum donc le scaling horizontal est trivial. TiDB Cloud est deja distribue horizontalement, ce n\'est pas le goulot.",
        tip: "Ne paniquez pas. Reconnaissez la limite + donnez un plan concret. Le mot magique : <em>stateless</em>."
    },
    {
        q: "Pourquoi votre application n\'utilise-t-elle pas Firebase comme backend complet ?",
        a: "Trois raisons : (1) <em>vendor lock-in</em> Google, on devient dependant d\'une seule entreprise ; (2) couts qui explosent passe le free tier, surtout sur Firestore ; (3) les regles de securite Firestore sont trop limitees pour notre logique metier complexe (workflow de validation artisan, signalements polymorphes, etc.). On utilise Firebase UNIQUEMENT pour FCM, c\'est-a-dire les push notifications, parce que c\'est un cas d\'usage simple, gratuit et tres fiable.",
        tip: "Le piege : faire croire qu\'on ne connait pas Firebase. Montrez que vous l\'avez evalue et choisi un usage cible."
    },
    {
        q: "Expliquez le flux complet d\'inscription d\'un artisan.",
        a: "Deux etapes API + une validation manuelle. <strong>Etape 1</strong> (publique) : <code>POST /api/register/artisan</code> avec les infos de base, cree un User <code>role=artisan</code>, <code>email_verified_at=NULL</code> et retourne un token Sanctum immediatement. <strong>Etape 2</strong> (authentifiee) : <code>POST /api/register/artisan/verification</code> avec le justificatif (PDF / JPG / PNG, max 5 Mo) stocke dans <code>storage/app/private/proofs</code> hors du dossier public. Cree l\'<em>ArtisanProfile</em> avec <code>validation_status=pending</code>. <strong>Cote admin</strong> : consultation du dossier, click sur Valider declenche <code>PATCH /admin/users/{id}/verify-artisan</code> qui pose <code>approved</code> et envoie un mail signe de verification email. L\'artisan ouvre l\'app, le router Flutter le verrouille sur <code>/email-verify</code> jusqu\'a confirmation, puis bascule sur le dashboard.",
        tip: "Question piege classique. Decrivez en 3 blocs : Etape 1 / Etape 2 / Cote admin. Donnez 1 ou 2 endpoints concrets."
    },
    {
        q: "Pourquoi le mode sombre a-t-il ete supprime de cette plateforme de revision ?",
        a: "Cette plateforme de revision n\'est pas le projet FeGArtisan lui-meme : c\'est l\'outil personnel que j\'utilise pour preparer la soutenance. J\'ai voulu une <em>identite visuelle unique</em> calee sur la palette terra cotta du logo FeGArtisan, pour rester dans l\'univers du projet. Le mode sombre creait deux ambiances differentes qui distrayaient. L\'app mobile FeGArtisan elle-meme supporte le theme systeme.",
        tip: "Si on vous pose la question. Ne soyez pas defensif, expliquez le choix design."
    },
    {
        q: "Combien y a-t-il de tables principales dans la base de donnees ?",
        a: "Une vingtaine, avec les principales : <code>users</code>, <code>artisan_profiles</code>, <code>categories</code>, <code>publications</code>, <code>publication_media</code> (galerie multi-media), <code>conversations</code>, <code>messages</code>, <code>message_reactions</code> (avec contrainte unique sur la paire message_id + user_id), <code>profile_views</code> (suivi des visites de profil avec deduplication 24h), <code>reviews</code>, <code>favorites</code>, <code>reports</code> (polymorphique sur publication / user / comment), <code>comments</code>, <code>publication_likes</code>, <code>comment_likes</code>, <code>search_history</code>, <code>support_tickets</code>, <code>notifications</code>.",
        tip: "Pas besoin de tout citer. Insistez sur les <em>contraintes uniques</em> et la <em>polymorphie</em> (reports)."
    },
    {
        q: "Qu\'est-ce qu\'un middleware Laravel et a quoi sert-il dans votre projet ?",
        a: "Un <em>middleware</em> est un filtre qui s\'execute avant ou apres chaque requete HTTP, pour appliquer un controle transverse. Dans FeGArtisan on en utilise plusieurs : <code>auth:sanctum</code> qui renvoie 401 si pas de Bearer valide, <code>EnsureUserIsActive</code> qui bloque les comptes suspendus, <code>artisan.role</code> qui exige role=artisan, <code>artisan.verified</code> qui bloque l\'ecriture si pas approved, <code>admin</code> pour le dashboard, et des middlewares globaux comme <code>LogRequestDuration</code> qui mesure les performances et detecte les N+1, ou <code>TouchLastSeen</code> qui met a jour <code>users.last_seen_at</code>.",
        tip: "Ne listez pas tous les middlewares. Citez le concept + 3-4 exemples concrets."
    },
    {
        q: "Comment fonctionne Riverpod dans votre application Flutter ?",
        a: "<em>Riverpod 2.x</em> est notre gestionnaire d\'etat. On utilise 4 types de providers : <code>Provider</code> pour les singletons sans etat mutable (services, repositories), <code>NotifierProvider</code> pour un etat mutable synchrone (ex. <code>authControllerProvider</code> qui expose <code>AuthState</code>), <code>AsyncNotifierProvider</code> pour un etat avec init asynchrone (ex. <code>dashboardControllerProvider</code>), et <code>FutureProvider.autoDispose.family</code> pour les fetches HTTP parametres qui se liberent automatiquement quand l\'ecran sort. Pattern critique : les ecrans ne touchent <em>jamais</em> directement a Dio, ils watchent un provider qui depend d\'un repository.",
        tip: "Citez les 4 types + le pattern Repository =&gt; Notifier =&gt; UI. C\'est tres precis et impressionne."
    },
    {
        q: "Quelle est la difference entre un admin et un super admin ?",
        a: "Le principe : <em>moindre privilege</em>. Un <strong>admin</strong> standard fait la moderation des contenus, valide les artisans et gere le catalogue de categories. Un <strong>super admin</strong> peut faire tout cela PLUS gerer les autres admins (les creer, les suspendre, leur retirer les droits). C\'est protege par un middleware <code>admin.manage</code> qui verifie la colonne <code>can_manage_admins=true</code>. Cela evite qu\'un admin metier compromis ne puisse creer d\'autres comptes admin pour persister dans le systeme.",
        tip: "Le mot cle a placer : <em>principe du moindre privilege</em>. C\'est un concept de securite reconnu."
    },
    {
        q: "Comment votre projet gere-t-il les notifications en temps reel hors application ?",
        a: "Via <em>Firebase Cloud Messaging</em> (FCM), un service Google gratuit. Quand un evenement se produit (nouveau message, like, validation artisan), Laravel emet un event <code>NewNotification</code>, et le listener <code>SendFcmForNotification</code> appelle <code>FirebasePushService::sendToUser()</code> qui POST vers l\'API HTTP v1 de Firebase avec un payload contenant title, body et data. Le telephone reçoit la notification systeme en ~1 seconde meme si l\'app est fermee. Au tap, Flutter navigue vers le bon ecran via <code>onMessageOpenedApp</code>.",
        tip: "Soyez precis : citez l\'event, le listener et FirebasePushService. ~1 seconde de latence."
    },
    {
        q: "Pourquoi avoir cree un super admin pour gerer les autres admins ?",
        a: "Pour appliquer le <em>principe du moindre privilege</em> : un admin metier qui modere les publications ne devrait pas avoir le pouvoir de creer ou supprimer d\'autres admins. C\'est une action sensible (creation d\'un compte privilegie) qui doit etre tracee et limitee a une personne de confiance. En pratique, le middleware <code>admin.manage</code> filtre les routes d\'administration des admins via une colonne <code>can_manage_admins</code> en base.",
        tip: "Concept securite. Le jury aime entendre <em>moindre privilege</em>."
    },
    {
        q: "Si la base de donnees TiDB tombe, que se passe-t-il et comment vous protegez-vous ?",
        a: "L\'application renverrait des erreurs 500 immediatement, puisque toutes les requetes passent par Eloquent. <em>TiDB Cloud Serverless</em> est multi-AZ avec un SLA de 99.99 %, donc une panne complete est tres rare. En mitigation : (1) page d\'erreur custom plus rassurante qu\'un 500 brut ; (2) retry automatique du cote client ; (3) monitoring via le middleware <code>LogRequestDuration</code> qui ecrit dans <code>storage/logs/performance-YYYY-MM-DD.log</code>. Plan B si TiDB devient indispensable : migrer vers une MySQL classique (DigitalOcean Managed DB ~15$/mois), Eloquent est portable, on change juste les credentials .env.",
        tip: "Ne jurez pas que ça ne tombera jamais. Reconnaissez le risque + donnez le plan."
    },
    {
        q: "Si vous deviez recommencer le projet, que feriez-vous differemment ?",
        a: "Deux choses concretes. (1) <em>Tests automatises</em> des le jour 1 avec Pest cote backend et flutter_test cote mobile, plutot qu\'a la fin. On a fait beaucoup de tests manuels, certains bugs nous ont coute du temps qu\'un test automatise aurait detecte instantanement. (2) Une <em>etude UX usability</em> avec 5 vrais utilisateurs beninois avant le code, pour valider les hypotheses d\'interface plutot que de tout corriger apres coup. Le sondage etait utile mais ne remplace pas une vraie observation d\'usage.",
        tip: "Reponse honnete et concrete. Ne dites JAMAIS que vous ne changeriez rien."
    },
    {
        q: "Le projet FeGArtisan peut-il devenir une vraie startup ?",
        a: "Oui, plusieurs signaux positifs. (1) Le besoin est <em>valide</em> : sondage 70.4 % d\'interet sur 71 repondants. (2) Le MVP est <em>fonctionnel</em> et deploye, pas juste une maquette. (3) Modele economique possible : commission sur la mise en relation, abonnement Pro pour les artisans qui veulent plus de visibilite, ou sponsoring de categories. (4) Le marche local est <em>sous-digitalise</em> : 80 % des emplois en Afrique subsaharienne sont informels. Concurrence faible au Benin. Les ameliorations cles pour le passage en startup : iOS, KKiaPay/FedaPay pour le paiement in-app, et systeme de notation verifiee.",
        tip: "Montrez que vous avez pense au modele eco + aux prochaines etapes. Pas juste un projet d\'ecole."
    },
    {
        q: "Comment fonctionne la corbeille a retention de 30 jours ?",
        a: "Implementee via le trait <em>SoftDeletes</em> de Laravel sur 6 modeles (users, artisan_profiles, publications, comments, reviews, categories). La suppression ne supprime pas physiquement la ligne, elle pose juste <code>deleted_at = now()</code>. La cascade vers les modeles lies est orchestree manuellement dans <code>booted::deleting</code> avec un test sur <code>isForceDeleting()</code>. Le scheduler Laravel execute quotidiennement <code>trash:purge --days=30</code> qui force-delete les enregistrements expires, en respectant l\'ordre des cles etrangeres : commentaires =&gt; avis =&gt; publications =&gt; profils artisans =&gt; categories =&gt; users.",
        tip: "Insistez sur la cascade manuelle et l\'ordre de purge. Le jury teste si vous comprenez les FK."
    },
    {
        q: "Quelle est la difference entre le polling et un WebSocket ?",
        a: "Le <em>polling</em> est un sondage : le client envoie une requete HTTP toutes les 3 secondes pour demander s\'il y a du nouveau. Simple a implementer, compatible partout, mais latence jusqu\'a 3 secondes et bande passante gaspillee si rien ne se passe. Le <em>WebSocket</em> est une connexion HTTP qui reste ouverte en continu et permet au serveur de pousser des messages instantanement. Latence quasi nulle mais necessite un processus serveur persistant, ce qui est impossible sur Hostinger mutualise. C\'est pour ca qu\'on a choisi polling + FCM.",
        tip: "Expliquez les 2 + dites POURQUOI vous avez choisi polling pour CE projet."
    },
    {
        q: "Quelle methodologie avez-vous suivi pour gerer ce projet ?",
        a: "Une approche <em>iterative</em> inspiree d\'Agile, en 5 phases sur environ 11 semaines. Phase 1 (1 semaine) : analyse des besoins + sondage de 71 repondants. Phase 2 (2 semaines) : conception UML complete (cas d\'utilisation, classes, sequences, activites) avec Draw.io et StarUML. Phase 3 (5 semaines) : developpement par feature, en commencant par l\'authentification puis la recherche, le feed, la messagerie. Phase 4 (2 semaines) : tests unitaires, integration, fonctionnels, securite, performance, compatibilite. Phase 5 (1 semaine) : deploiement Hostinger + CI/CD GitHub Actions + generation APK signe.",
        tip: "Donnez la duree de chaque phase. Cela montre une vraie gestion de projet, pas du bricolage."
    },
];

let oralDeck = [...ORAL_QUESTIONS];
let oralIdx = 0;

function shuffleInPlace(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
}

function render() {
    const q = oralDeck[oralIdx];
    document.getElementById('oral-num').textContent = 'Q' + (oralIdx + 1);
    document.getElementById('oral-question').innerHTML = q.q;
    document.getElementById('oral-answer-body').innerHTML = q.a;
    document.getElementById('oral-tips-body').innerHTML = q.tip || '';
    document.getElementById('oral-tips').style.display = q.tip ? 'block' : 'none';
    document.getElementById('oral-answer').style.display = 'none';
    document.getElementById('btn-reveal').style.display = 'inline-block';
    document.getElementById('btn-reveal').textContent = 'Voir la reponse';
    document.getElementById('oral-counter').textContent = 'Question ' + (oralIdx + 1) + ' / ' + oralDeck.length;
    document.getElementById('oral-progress-fill').style.width = (((oralIdx + 1) / oralDeck.length) * 100) + '%';
}

function reveal() {
    document.getElementById('oral-answer').style.display = 'block';
    document.getElementById('btn-reveal').style.display = 'none';
}

function nextQ() { oralIdx = (oralIdx + 1) % oralDeck.length; render(); }
function prevQ() { oralIdx = (oralIdx - 1 + oralDeck.length) % oralDeck.length; render(); }
function shuffleQ() { shuffleInPlace(oralDeck); oralIdx = 0; render(); }

shuffleInPlace(oralDeck);
render();
</script>
@endsection
