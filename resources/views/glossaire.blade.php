@extends('layouts.app')
@section('title', 'Glossaire — FeGArtisan QCM')

@section('styles')
    .container { max-width:900px; margin:0 auto; }
    h1 { color:var(--accent); font-size:26px; margin-bottom:6px; }
    .subtitle { color:var(--muted); font-size:14px; margin-bottom:20px; }
    .gl-tools { display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap; align-items:center; }
    .gl-search { flex:1; min-width:240px; padding:11px 16px; border:2px solid var(--border); border-radius:10px; background:var(--card); color:var(--text); font-size:14px; }
    .gl-search:focus { outline:none; border-color:var(--accent-2); }
    .gl-print { padding:11px 18px; border:none; border-radius:10px; background:var(--accent); color:#fff; font-weight:700; font-size:13px; cursor:pointer; }
    .gl-print:hover { filter:brightness(1.1); }
    .gl-tags { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:24px; }
    .gl-tag { padding:5px 14px; border-radius:20px; background:var(--input); border:1px solid var(--border); font-size:12px; cursor:pointer; color:var(--text); text-decoration:none; transition:all .2s; }
    .gl-tag:hover { border-color:var(--accent-2); color:var(--accent); }
    .gl-tag.active { background:var(--accent-2); color:#fff; border-color:var(--accent-2); }

    .gl-section { margin-bottom:34px; }
    .gl-section h2 { font-size:18px; color:var(--accent); margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid var(--border); display:flex; align-items:center; gap:10px; }
    .gl-section h2 .count { font-size:11px; background:var(--input); padding:3px 10px; border-radius:14px; color:var(--muted); font-weight:600; letter-spacing:1px; }

    .gl-list { display:grid; grid-template-columns:1fr; gap:10px; }
    .gl-item { background:var(--card); border:1px solid var(--border); border-radius:10px; padding:14px 18px; transition:border-color .2s; }
    .gl-item:hover { border-color:var(--accent-2); }
    .gl-item dt { font-weight:700; color:var(--accent); font-size:14px; margin-bottom:4px; display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; }
    .gl-item dt .badge { font-size:9px; background:var(--input); color:var(--muted); padding:2px 7px; border-radius:6px; font-weight:600; letter-spacing:1px; text-transform:uppercase; }
    .gl-item dd { color:var(--text); font-size:13.5px; line-height:1.6; }
    .gl-item dd code { background:var(--input); padding:1px 5px; border-radius:4px; font-family:'Consolas',monospace; font-size:12px; color:var(--accent); }

    .gl-empty { padding:30px; text-align:center; color:var(--muted); font-style:italic; }

    @media print {
        body { background:#fff !important; color:#000 !important; }
        .topbar, .sidebar, .global-progress, .gl-tools, .gl-tags { display:none !important; }
        .main-content { margin:0 !important; padding:0 !important; max-width:100% !important; }
        .gl-section { page-break-inside:avoid; }
        .gl-item { page-break-inside:avoid; border:1px solid #ccc !important; }
        h1 { font-size:18px; } .gl-section h2 { font-size:14px; }
    }
@endsection

@section('content')
<div class="container">
    <h1>Glossaire FeGArtisan</h1>
    <p class="subtitle">Tous les termes techniques du projet, avec leur explication courte. Reference rapide pour la soutenance.</p>

    <div class="gl-tools">
        <input type="text" class="gl-search" id="gl-search" placeholder="Rechercher un terme (ex. polling, sodium, throttle)..." autofocus>
        <button class="gl-print" onclick="window.print()">Imprimer / PDF</button>
    </div>

    <div class="gl-tags">
        <a href="#" class="gl-tag active" data-filter="all">Tout</a>
        <a href="#" class="gl-tag" data-filter="backend">Backend Laravel</a>
        <a href="#" class="gl-tag" data-filter="flutter">Flutter / Dart</a>
        <a href="#" class="gl-tag" data-filter="bdd">Base de donnees</a>
        <a href="#" class="gl-tag" data-filter="archi">Architecture</a>
        <a href="#" class="gl-tag" data-filter="securite">Securite</a>
        <a href="#" class="gl-tag" data-filter="deploiement">Deploiement</a>
        <a href="#" class="gl-tag" data-filter="general">General</a>
    </div>

    @php
        $glossary = [
            'Backend Laravel' => [
                ['t'=>'Laravel', 'b'=>'backend', 'd'=>'Framework PHP open-source utilise comme coeur du backend FeGArtisan. Architecture MVC, ORM Eloquent integre, ecosysteme tres mature.'],
                ['t'=>'MVC', 'b'=>'backend', 'd'=>'Modele-Vue-Controleur. Pattern d\'architecture qui separe le code en 3 couches : Modele (donnees), Vue (affichage), Controleur (logique).'],
                ['t'=>'Eloquent ORM', 'b'=>'backend', 'd'=>'Object-Relational Mapping de Laravel. Permet de manipuler les tables comme des classes PHP sans ecrire de SQL.'],
                ['t'=>'Sanctum', 'b'=>'backend', 'd'=>'Extension Laravel officielle pour l\'authentification API. Genere des Bearer tokens stockes en base de donnees.'],
                ['t'=>'Bearer token', 'b'=>'backend', 'd'=>'Chaine de caracteres secrete envoyee dans l\'en-tete HTTP <code>Authorization: Bearer &lt;token&gt;</code> pour prouver l\'identite de l\'utilisateur.'],
                ['t'=>'PersonalAccessToken', 'b'=>'backend', 'd'=>'Classe Eloquent qui represente un jeton Sanctum en base. Surchargee dans FeGArtisan pour throttler les ecritures de <code>last_used_at</code>.'],
                ['t'=>'Middleware', 'b'=>'backend', 'd'=>'Filtre de requete qui s\'execute avant ou apres le controleur. Sert au controle d\'authentification, rate-limiting, logging, etc.'],
                ['t'=>'Migration', 'b'=>'backend', 'd'=>'Fichier PHP versionne qui decrit la structure d\'une table. Permet de reconstruire la base sur n\'importe quelle machine.'],
                ['t'=>'Seeder', 'b'=>'backend', 'd'=>'Classe PHP qui remplit la base avec des donnees de test ou initiales (comptes admin par exemple).'],
                ['t'=>'Factory', 'b'=>'backend', 'd'=>'Generateur de donnees aleatoires (via faker) pour creer rapidement des centaines de modeles en test.'],
                ['t'=>'Blade', 'b'=>'backend', 'd'=>'Moteur de templates HTML natif de Laravel. Utilise pour le dashboard admin.'],
                ['t'=>'Artisan', 'b'=>'backend', 'd'=>'CLI (interface en ligne de commande) de Laravel. <code>php artisan migrate</code>, <code>php artisan make:controller</code>, etc.'],
                ['t'=>'Policy', 'b'=>'backend', 'd'=>'Classe d\'autorisation qui regle qui peut faire quoi sur quel modele. Plus fin que les middlewares.'],
                ['t'=>'Event', 'b'=>'backend', 'd'=>'Message diffuse dans l\'application quand quelque chose se passe. Ex. <code>NewNotification</code>.'],
                ['t'=>'Listener', 'b'=>'backend', 'd'=>'Classe qui reagit a un event en executant une action (envoi push, INSERT en base, etc.).'],
                ['t'=>'Auto-discovery', 'b'=>'backend', 'd'=>'Laravel 12 enregistre automatiquement les listeners qui ont une methode <code>handle(EventType $event)</code>. Pas besoin de Event::listen() manuel.'],
                ['t'=>'ShouldQueue', 'b'=>'backend', 'd'=>'Interface Laravel qui marque un listener ou job comme asynchrone (a executer plus tard via la queue). FeGArtisan ne l\'utilise PAS (Hostinger pas de queue worker).'],
                ['t'=>'Queue worker', 'b'=>'backend', 'd'=>'Processus qui consomme les jobs en arriere-plan. Lance avec <code>php artisan queue:work</code>. Impossible sur Hostinger mutualise.'],
                ['t'=>'Scheduler', 'b'=>'backend', 'd'=>'Planificateur de taches recurrentes Laravel, defini dans <code>routes/console.php</code>. Declenche par une tache cron unique.'],
                ['t'=>'Soft-delete', 'b'=>'backend', 'd'=>'Suppression douce : la ligne est marquee comme supprimee (<code>deleted_at</code> rempli) mais reste en base. Permet la corbeille 30 jours.'],
                ['t'=>'Force-delete', 'b'=>'backend', 'd'=>'Suppression definitive d\'une ligne soft-deleted. Irreversible. Libere l\'espace disque.'],
                ['t'=>'Throttle', 'b'=>'backend', 'd'=>'Limiter le debit ou la frequence d\'une operation. Ex. <code>throttle:5,1</code> = max 5 requetes par minute.'],
            ],
            'Flutter et Dart' => [
                ['t'=>'Flutter', 'b'=>'flutter', 'd'=>'Framework Google open-source pour developper des apps multiplateformes (Android, iOS, web, desktop) depuis une seule base de code.'],
                ['t'=>'Dart', 'b'=>'flutter', 'd'=>'Langage de programmation cree par Google, utilise par Flutter. Syntaxe proche de Java/TypeScript.'],
                ['t'=>'Widget', 'b'=>'flutter', 'd'=>'Brique de base de l\'interface Flutter. Chaque element visuel (bouton, texte, conteneur) est un widget.'],
                ['t'=>'StatelessWidget', 'b'=>'flutter', 'd'=>'Widget qui n\'a pas d\'etat mutable. Une fois construit, il ne change pas.'],
                ['t'=>'StatefulWidget', 'b'=>'flutter', 'd'=>'Widget qui peut changer d\'etat en cours de vie. Utilise <code>setState()</code> pour redessiner.'],
                ['t'=>'Riverpod', 'b'=>'flutter', 'd'=>'Librairie de state management (gestion d\'etat) pour Flutter. Version 2.x = Notifier / AsyncNotifier / FutureProvider.'],
                ['t'=>'State management', 'b'=>'flutter', 'd'=>'Gestion centralisee de l\'etat de l\'application (donnees partagees entre ecrans).'],
                ['t'=>'Provider (Riverpod)', 'b'=>'flutter', 'd'=>'Singleton sans etat mutable. Sert pour les services et repositories.'],
                ['t'=>'Notifier', 'b'=>'flutter', 'd'=>'Provider Riverpod avec etat mutable synchrone. Ex. <code>AuthController</code>.'],
                ['t'=>'AsyncNotifier', 'b'=>'flutter', 'd'=>'Provider Riverpod avec initialisation asynchrone. Le <code>build()</code> peut faire des appels HTTP.'],
                ['t'=>'autoDispose', 'b'=>'flutter', 'd'=>'Modificateur Riverpod qui libere la memoire du provider quand plus aucun widget ne l\'ecoute.'],
                ['t'=>'family', 'b'=>'flutter', 'd'=>'Modificateur Riverpod qui permet de passer un parametre dynamique au provider (ex. ID).'],
                ['t'=>'go_router', 'b'=>'flutter', 'd'=>'Librairie de navigation declarative pour Flutter. Gere les routes en arbre + guards.'],
                ['t'=>'Dio', 'b'=>'flutter', 'd'=>'Client HTTP pour Dart/Flutter, plus puissant que <code>http</code>. Supporte les interceptors.'],
                ['t'=>'Interceptor', 'b'=>'flutter', 'd'=>'Fonction qui intercepte chaque requete ou reponse HTTP. Utilise pour ajouter le Bearer token automatiquement.'],
                ['t'=>'flutter_secure_storage', 'b'=>'flutter', 'd'=>'Stockage chiffre via Android Keystore / iOS Keychain. Utilise pour le token Sanctum.'],
                ['t'=>'cached_network_image', 'b'=>'flutter', 'd'=>'Librairie qui cache automatiquement les images sur disque pour eviter de les re-telecharger.'],
                ['t'=>'chewie', 'b'=>'flutter', 'd'=>'Surcouche UI pour <code>video_player</code> : controles (seek, son, plein ecran, vitesse).'],
                ['t'=>'VisibilityDetector', 'b'=>'flutter', 'd'=>'Widget qui mesure la fraction visible d\'un autre widget. Utilise pour declencher auto-play video.'],
                ['t'=>'Lifecycle', 'b'=>'flutter', 'd'=>'Cycle de vie de l\'application : resumed (foreground), paused (background), detached, inactive.'],
                ['t'=>'WidgetsBindingObserver', 'b'=>'flutter', 'd'=>'Mixin Flutter pour ecouter les changements de lifecycle de l\'application.'],
                ['t'=>'optimistic update', 'b'=>'flutter', 'd'=>'Mettre a jour l\'UI avant la confirmation serveur, pour donner une impression de fluidite.'],
                ['t'=>'ProviderScope', 'b'=>'flutter', 'd'=>'Widget racine de Riverpod qui doit envelopper toute l\'app. Stocke les providers.'],
            ],
            'Architecture et reseau' => [
                ['t'=>'API REST', 'b'=>'archi', 'd'=>'Architecture d\'API qui utilise HTTP (GET, POST, PUT, DELETE) et expose des ressources via des URLs claires. Reponses generalement en JSON.'],
                ['t'=>'JSON', 'b'=>'archi', 'd'=>'JavaScript Object Notation. Format texte leger d\'echange de donnees, lisible par humain et machine.'],
                ['t'=>'Client-serveur', 'b'=>'archi', 'd'=>'Modele ou un client (Flutter ou navigateur) envoie des requetes a un serveur (Laravel) qui repond.'],
                ['t'=>'3-tier', 'b'=>'archi', 'd'=>'Architecture en 3 niveaux : Presentation, Logique metier, Donnees. C\'est ce qu\'utilise FeGArtisan.'],
                ['t'=>'Polling', 'b'=>'archi', 'd'=>'Sondage : le client envoie regulierement (toutes les 3s) une requete au serveur pour verifier les nouveautes. Utilise dans le chat.'],
                ['t'=>'WebSocket', 'b'=>'archi', 'd'=>'Connexion HTTP qui reste ouverte en continu pour echanger des messages bidirectionnels en temps reel. Impossible sur Hostinger mutualise.'],
                ['t'=>'FCM', 'b'=>'archi', 'd'=>'Firebase Cloud Messaging. Service Google gratuit qui envoie des push notifications a un telephone meme app fermee.'],
                ['t'=>'Push notification', 'b'=>'archi', 'd'=>'Notification systeme envoyee a un appareil par un serveur, sans que l\'utilisateur n\'agisse.'],
                ['t'=>'OAuth2', 'b'=>'archi', 'd'=>'Protocole d\'autorisation standardise. Utilise par Firebase pour authentifier les requetes serveur.'],
                ['t'=>'JWT', 'b'=>'archi', 'd'=>'JSON Web Token. Jeton signe contenant des informations encodees. Utilise pour OAuth2 avec FCM.'],
                ['t'=>'Endpoint', 'b'=>'archi', 'd'=>'Point d\'entree d\'une API : URL + verbe HTTP. Ex. <code>GET /api/me</code>.'],
                ['t'=>'Payload', 'b'=>'archi', 'd'=>'Charge utile : les donnees transportees dans une requete ou un event.'],
            ],
            'Base de donnees' => [
                ['t'=>'MySQL', 'b'=>'bdd', 'd'=>'Systeme de gestion de base de donnees relationnel open-source le plus utilise au monde.'],
                ['t'=>'TiDB Cloud', 'b'=>'bdd', 'd'=>'Base de donnees MySQL distribuee serverless. Region eu-central-1 (Frankfurt) pour FeGArtisan, gratuit jusqu\'a 5 Go.'],
                ['t'=>'Serverless', 'b'=>'bdd', 'd'=>'Sans serveur a gerer soi-meme. Le fournisseur s\'occupe de tout, vous payez a l\'usage.'],
                ['t'=>'SGBD', 'b'=>'bdd', 'd'=>'Systeme de Gestion de Base de Donnees. MySQL, PostgreSQL, Oracle, etc.'],
                ['t'=>'ORM', 'b'=>'bdd', 'd'=>'Object-Relational Mapping. Traduit les tables SQL en objets PHP (Eloquent dans Laravel).'],
                ['t'=>'Foreign key', 'b'=>'bdd', 'd'=>'Cle etrangere : colonne d\'une table qui pointe vers la cle primaire d\'une autre. Garantit l\'integrite.'],
                ['t'=>'Primary key', 'b'=>'bdd', 'd'=>'Cle primaire : colonne unique qui identifie chaque ligne d\'une table.'],
                ['t'=>'Index', 'b'=>'bdd', 'd'=>'Structure SQL qui accelere les recherches sur une colonne. A poser sur les colonnes souvent filtrees.'],
                ['t'=>'Unique constraint', 'b'=>'bdd', 'd'=>'Contrainte SQL qui interdit deux memes valeurs dans une colonne (ou une combinaison de colonnes).'],
                ['t'=>'CASCADE', 'b'=>'bdd', 'd'=>'Option de cle etrangere : la suppression du parent supprime automatiquement les enfants.'],
                ['t'=>'Normalisation', 'b'=>'bdd', 'd'=>'Decoupage des donnees en plusieurs tables liees pour eviter la redondance.'],
                ['t'=>'Denormalisation', 'b'=>'bdd', 'd'=>'Inverse : dupliquer une donnee pour eviter une jointure couteuse (ex. <code>comments_count</code>).'],
                ['t'=>'Migration', 'b'=>'bdd', 'd'=>'Cf. Backend. Fichier qui decrit la structure d\'une table en code versionne.'],
                ['t'=>'CRUD', 'b'=>'bdd', 'd'=>'Create / Read / Update / Delete : les 4 operations de base sur les donnees.'],
                ['t'=>'N+1 (anti-pattern)', 'b'=>'bdd', 'd'=>'Anti-pattern Eloquent : une boucle declenche N requetes au lieu d\'une seule. Resolu avec <code>with()</code> (eager loading).'],
                ['t'=>'Eager loading', 'b'=>'bdd', 'd'=>'Chargement anticipe des relations Eloquent en une seule requete avec <code>->load()</code> ou <code>->with()</code>.'],
            ],
            'Securite' => [
                ['t'=>'bcrypt', 'b'=>'securite', 'd'=>'Algorithme de hash de mot de passe robuste. Utilise par Laravel via <code>Hash::make()</code>. Irreversible.'],
                ['t'=>'Hash', 'b'=>'securite', 'd'=>'Transformation a sens unique d\'une donnee. Impossible de revenir au texte d\'origine.'],
                ['t'=>'CSRF', 'b'=>'securite', 'd'=>'Cross-Site Request Forgery. Attaque ou un site malveillant envoie une requete au nom de l\'utilisateur. Laravel protege avec <code>@csrf</code>.'],
                ['t'=>'XSS', 'b'=>'securite', 'd'=>'Cross-Site Scripting. Injection de code JavaScript malveillant. Blade echappe par defaut avec <code>{{ }}</code>.'],
                ['t'=>'Injection SQL', 'b'=>'securite', 'd'=>'Attaque ou un attaquant injecte du SQL dans un champ. Empechee par les prepared statements (Eloquent).'],
                ['t'=>'Prepared statement', 'b'=>'securite', 'd'=>'Requete SQL parametree ou les valeurs utilisateur sont separees de la structure. Empeche l\'injection.'],
                ['t'=>'HTTPS', 'b'=>'securite', 'd'=>'HTTP chiffre via TLS/SSL. Obligatoire pour proteger les Bearer tokens en transit.'],
                ['t'=>'TLS / SSL', 'b'=>'securite', 'd'=>'Protocoles de chiffrement reseau. SSL est l\'ancien nom, TLS le successeur.'],
                ['t'=>'sodium', 'b'=>'securite', 'd'=>'Extension PHP de cryptographie moderne. Obligatoire pour signer les JWT Firebase.'],
                ['t'=>'Rate limiting', 'b'=>'securite', 'd'=>'Limiter le nombre de requetes par IP ou par utilisateur. Empeche le brute-force.'],
                ['t'=>'Mass assignment', 'b'=>'securite', 'd'=>'Assignation en masse des proprietes d\'un modele. Dangereux si pas de liste blanche <code>$fillable</code>.'],
            ],
            'Deploiement et infrastructure' => [
                ['t'=>'Hostinger', 'b'=>'deploiement', 'd'=>'Hebergeur web mutualise utilise en production pour FeGArtisan. Pas cher mais avec des contraintes.'],
                ['t'=>'Shared hosting', 'b'=>'deploiement', 'd'=>'Hebergement mutualise : plusieurs sites partagent la meme machine. Pas de WebSocket, pas de queue worker.'],
                ['t'=>'PHP-FPM', 'b'=>'deploiement', 'd'=>'FastCGI Process Manager. Gestionnaire de processus PHP qui sert les requetes HTTP.'],
                ['t'=>'SSH', 'b'=>'deploiement', 'd'=>'Secure Shell. Protocole pour se connecter en console a distance au serveur de maniere chiffree.'],
                ['t'=>'SCP', 'b'=>'deploiement', 'd'=>'Secure Copy Protocol. Copier un fichier via SSH.'],
                ['t'=>'cron', 'b'=>'deploiement', 'd'=>'Planificateur de taches Unix. Configure pour declencher le scheduler Laravel toutes les minutes.'],
                ['t'=>'CI/CD', 'b'=>'deploiement', 'd'=>'Continuous Integration / Continuous Deployment. Automatisation des tests + deploiement.'],
                ['t'=>'GitHub Actions', 'b'=>'deploiement', 'd'=>'Service CI/CD integre a GitHub. Declenche un workflow a chaque push.'],
                ['t'=>'APK', 'b'=>'deploiement', 'd'=>'Android Package Kit. Fichier d\'installation d\'une app Android. Genere par <code>flutter build apk</code>.'],
                ['t'=>'AAB', 'b'=>'deploiement', 'd'=>'Android App Bundle. Format optimise pour le Google Play Store, plus petit que l\'APK.'],
                ['t'=>'keystore', 'b'=>'deploiement', 'd'=>'Fichier qui contient la cle de signature de l\'APK. Genere avec <code>keytool</code>. A garder en lieu sur.'],
                ['t'=>'Brevo SMTP', 'b'=>'deploiement', 'd'=>'Service mail transactionnel utilise pour la verification email et le reset mot de passe.'],
                ['t'=>'OPcache', 'b'=>'deploiement', 'd'=>'Cache bytecode PHP qui evite la recompilation a chaque requete. Active par defaut en production.'],
            ],
            'Concepts generaux' => [
                ['t'=>'UML', 'b'=>'general', 'd'=>'Unified Modeling Language. Langage standardise de modelisation pour les systemes orientes objet.'],
                ['t'=>'Cas d\'utilisation', 'b'=>'general', 'd'=>'Diagramme UML qui represente les actions des acteurs sur le systeme.'],
                ['t'=>'Cardinalite', 'b'=>'general', 'd'=>'Nombre d\'instances en relation entre deux classes. Ex. 1..* = un a plusieurs.'],
                ['t'=>'Singleton', 'b'=>'general', 'd'=>'Pattern de conception : classe qui n\'a qu\'une seule instance partagee.'],
                ['t'=>'Polymorphisme', 'b'=>'general', 'd'=>'Capacite d\'un meme code a manipuler differents types d\'objets. Ex. <code>notifiable</code> peut etre n\'importe quel modele.'],
                ['t'=>'Open source', 'b'=>'general', 'd'=>'Code source librement accessible, modifiable et redistribuable. Laravel, Flutter, MySQL sont open source.'],
                ['t'=>'MVP', 'b'=>'general', 'd'=>'Minimum Viable Product. Premiere version d\'un produit avec le minimum de fonctionnalites pour etre utilisable.'],
                ['t'=>'IDE', 'b'=>'general', 'd'=>'Integrated Development Environment. Environnement de developpement integre (VS Code, Android Studio).'],
                ['t'=>'CLI', 'b'=>'general', 'd'=>'Command Line Interface. Interface en ligne de commande, par opposition a une interface graphique.'],
                ['t'=>'Git', 'b'=>'general', 'd'=>'Systeme de gestion de versions distribue. Permet de suivre l\'historique du code source.'],
                ['t'=>'GitHub', 'b'=>'general', 'd'=>'Plateforme web qui heberge des depots Git, avec gestion d\'issues, pull requests et CI/CD.'],
                ['t'=>'Frontend', 'b'=>'general', 'd'=>'Cote client : ce que voit l\'utilisateur. Flutter pour mobile, Blade pour le dashboard admin.'],
                ['t'=>'Backend', 'b'=>'general', 'd'=>'Cote serveur : logique metier, base de donnees. Laravel pour FeGArtisan.'],
                ['t'=>'Fullstack', 'b'=>'general', 'd'=>'Developpeur qui maitrise frontend ET backend.'],
                ['t'=>'Responsive design', 'b'=>'general', 'd'=>'Conception qui s\'adapte a toutes les tailles d\'ecran (mobile, tablette, desktop).'],
                ['t'=>'UX', 'b'=>'general', 'd'=>'User Experience. Qualite percue de l\'experience utilisateur globale.'],
                ['t'=>'UI', 'b'=>'general', 'd'=>'User Interface. Interface visuelle (couleurs, boutons, layout).'],
            ],
        ];
        $totalTerms = 0;
        foreach ($glossary as $items) { $totalTerms += count($items); }
    @endphp

    <p class="subtitle" style="margin-bottom:24px;"><strong>{{ $totalTerms }} termes</strong> repartis en {{ count($glossary) }} categories. Cliquez sur les tags pour filtrer.</p>

    <div id="gl-content">
    @foreach($glossary as $catName => $items)
        @php
            $catSlug = ['Backend Laravel'=>'backend','Flutter et Dart'=>'flutter','Architecture et reseau'=>'archi','Base de donnees'=>'bdd','Securite'=>'securite','Deploiement et infrastructure'=>'deploiement','Concepts generaux'=>'general'][$catName] ?? 'all';
        @endphp
        <section class="gl-section" data-cat="{{ $catSlug }}">
            <h2>{{ $catName }} <span class="count">{{ count($items) }}</span></h2>
            <dl class="gl-list">
                @foreach($items as $item)
                    <div class="gl-item" data-term="{{ mb_strtolower($item['t']) }}" data-cat="{{ $item['b'] }}">
                        <dt>{{ $item['t'] }} <span class="badge">{{ $item['b'] }}</span></dt>
                        <dd>{!! $item['d'] !!}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endforeach
    </div>

    <div class="gl-empty" id="gl-empty" style="display:none">Aucun terme trouve. Essayez un autre mot-cle.</div>
</div>

<script>
const glSearch = document.getElementById('gl-search');
const glItems = document.querySelectorAll('.gl-item');
const glSections = document.querySelectorAll('.gl-section');
const glTags = document.querySelectorAll('.gl-tag');
const glEmpty = document.getElementById('gl-empty');
let activeFilter = 'all';

function applyFilter() {
    const q = glSearch.value.trim().toLowerCase();
    let visibleCount = 0;
    glItems.forEach(item => {
        const term = item.dataset.term;
        const cat = item.dataset.cat;
        const text = item.textContent.toLowerCase();
        const matchSearch = !q || term.includes(q) || text.includes(q);
        const matchFilter = activeFilter === 'all' || cat === activeFilter;
        const visible = matchSearch && matchFilter;
        item.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });
    glSections.forEach(sec => {
        const visibleItems = sec.querySelectorAll('.gl-item:not([style*="display: none"])').length;
        sec.style.display = visibleItems > 0 ? '' : 'none';
    });
    glEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
}

glSearch.addEventListener('input', applyFilter);
glTags.forEach(tag => {
    tag.addEventListener('click', e => {
        e.preventDefault();
        glTags.forEach(t => t.classList.remove('active'));
        tag.classList.add('active');
        activeFilter = tag.dataset.filter;
        applyFilter();
    });
});
</script>
@endsection
