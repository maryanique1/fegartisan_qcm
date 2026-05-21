@extends('layouts.app')
@section('title', 'Base de donnees FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-bdd',
        'title' => 'Base de donnees',
        'subtitle' => '20 questions . 4 chapitres . Migrations, modeles, TiDB, corbeille',
        'badge' => 'BDD',
        'color' => '#00BCD4',
        'description' => 'Schema relationnel, migrations Laravel, modeles Eloquent et relations, hebergement TiDB Cloud, soft-delete avec corbeille 30j et scheduler de purge.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Schema relationnel et tables principales', 'num' => 1, 'lesson' => '
            <p>Le <em>schema relationnel</em> (ensemble des tables et de leurs relations) de FeGArtisan comporte une vingtaine de tables. Voici les principales :</p>
            <ul>
                <li><strong>users</strong> : <code>first_name</code>, <code>last_name</code>, <code>name</code> (nom complet pour retrocompatibilite), <code>email</code> <em>unique</em> (contrainte d\'unicite : pas deux memes valeurs en base), <code>phone</code> unique, <code>role</code> <em>enum</em> (type a valeurs fixees) avec [client, artisan, admin], <code>avatar</code>, <code>ville</code>, <code>quartier</code>, <code>is_active</code>, <code>email_verified_at</code>, <code>password</code>, <code>fcm_token</code>, <code>last_seen_at</code>, <code>profile_visible</code>.</li>
                <li><strong>artisan_profiles</strong> : <code>user_id</code>, <code>category_id</code>, <code>metier</code>, <code>description</code>, <code>ville</code>, <code>quartier</code>, <code>proof_document</code>, <code>proof_type</code>, <code>validation_status</code> enum [pending, approved, rejected, suspended], <code>rejection_reason</code>, <code>validated_at</code>, <code>is_available</code>, <code>rating_avg</code>, <code>rating_count</code>, <code>views_count</code>.</li>
                <li><strong>categories</strong> : <code>name</code>, <code>slug</code> (identifiant URL : "macon" plutot que "Macon"), <code>icon</code>, <code>description</code>, <code>is_active</code>, <code>image</code> (couverture affichee sur le profil artisan).</li>
                <li><strong>publications</strong> : <code>artisan_profile_id</code>, <code>type</code> enum [text, image, video, mixed], <code>content</code>, <code>media_url</code> (premier media de la galerie, conserve pour retrocompatibilite avec anciens clients), <code>likes_count</code>, <code>comments_count</code>, <code>shares_count</code>, <code>views_count</code>, <code>is_active</code>.</li>
                <li><strong>publication_media</strong> : <code>publication_id</code>, <code>type</code> enum [image, video], <code>path</code>, <code>position</code> (ordre dans la galerie multi-media).</li>
                <li><strong>conversations</strong> : <code>client_id</code>, <code>artisan_id</code>, <code>last_message_at</code> (timestamp du dernier message, pour trier).</li>
                <li><strong>messages</strong> : <code>conversation_id</code>, <code>sender_id</code>, <code>content</code>, <code>type</code>, <code>media_url</code>, <code>read_at</code>.</li>
                <li><strong>message_reactions</strong> : <code>message_id</code>, <code>user_id</code>, <code>emoji</code>. <strong>Contrainte unique sur la paire (message_id, user_id)</strong> = un seul emoji par utilisateur par message.</li>
                <li><strong>profile_views</strong> : <code>artisan_profile_id</code>, <code>viewer_user_id</code>, <code>viewer_ip</code>, <code>viewed_at</code> (colonne <em>indexee</em> = avec un index pour acceler les recherches).</li>
                <li><strong>reviews</strong>, <strong>favorites</strong>, <strong>reports</strong>, <strong>comments</strong>, <strong>publication_likes</strong>, <strong>comment_likes</strong>, <strong>search_history</strong>, <strong>support_tickets</strong>, <strong>notifications</strong>.</li>
            </ul>
        '],
        ['title' => 'Migrations Laravel (versionnement du schema)', 'num' => 2, 'lesson' => '
            <p>Une <strong>migration</strong> est un fichier PHP qui decrit la structure d\'une table en code. C\'est le <em>plan de construction</em> versionne dans Git — chaque developpeur peut reproduire la meme base.</p>

            <p><strong>Generer une migration :</strong></p>
            <div class="code-example">php artisan make:migration create_categories_table --create=categories
php artisan make:migration add_fcm_token_to_users_table --table=users</div>
            <p>Le flag <code>--create=</code> indique qu\'on cree une nouvelle table, <code>--table=</code> qu\'on modifie une table existante (ajout/modification de colonne).</p>

            <p><strong>Types de colonnes les plus utilises (methodes Blueprint) :</strong></p>
            <ul>
                <li><code>$table->string("nom")</code> : VARCHAR(255) — chaine de caracteres courte.</li>
                <li><code>$table->text("contenu")</code> : TEXT — texte long (jusqu\'a 65 535 caracteres).</li>
                <li><code>$table->integer("qte")</code> : INT — entier signe 32 bits.</li>
                <li><code>$table->decimal("prix", 8, 2)</code> : DECIMAL avec <em>precision</em> (8 chiffres au total, 2 apres la virgule).</li>
                <li><code>$table->boolean("actif")</code> : TINYINT(1) — vrai/faux.</li>
                <li><code>$table->enum("role", ["a","b"])</code> : ENUM — valeur dans une liste fermee.</li>
                <li><code>$table->timestamp("visite_a")</code> : TIMESTAMP — date et heure UTC.</li>
                <li><code>$table->foreignId("user_id")->constrained()->cascadeOnDelete()</code> : <em>foreign key</em> (cle etrangere : lien vers une autre table) vers <code>users(id)</code>, avec suppression en cascade.</li>
                <li>Modifieurs : <code>->nullable()</code> (autorise NULL), <code>->default(...)</code> (valeur par defaut), <code>->unique()</code> (contrainte d\'unicite).</li>
            </ul>

            <p><strong>Commandes Artisan principales :</strong></p>
            <div class="code-example">php artisan migrate              # applique les nouvelles migrations
php artisan migrate:fresh        # ATTENTION : DROP tout + recree
php artisan migrate:fresh --seed # idem + remplit avec donnees de test
php artisan migrate:rollback     # annule la derniere migration
php artisan migrate:status       # affiche l\'etat (executees / en attente)</div>
            <div class="tip">Creez d\'abord les tables <em>sans</em> cles etrangeres (users, categories), puis celles <em>avec</em> cles etrangeres (artisan_profiles qui reference users). Le numero <em>timestamp</em> (horodatage) dans le nom du fichier de migration fixe l\'ordre d\'execution.</div>
        '],
        ['title' => 'Modeles Eloquent et relations', 'num' => 3, 'lesson' => '
            <p>Un <strong>modele Eloquent</strong> (composant <em>ORM</em> de Laravel = Object-Relational Mapping, qui traduit les tables en classes PHP) est une classe qui represente une table en base. Au lieu d\'ecrire du SQL, on manipule des objets PHP.</p>
            <div class="code-example">class Category extends Model {
    // $fillable = liste blanche des colonnes assignables en masse
    // (protection contre l\'injection de champs comme is_admin)
    protected $fillable = ["name","slug","icon","description","is_active","image"];

    // Definition d\'une relation : une categorie a plusieurs profils artisans
    public function artisanProfiles() {
        return $this->hasMany(ArtisanProfile::class);
    }
}</div>

            <p><strong>Relations principales du projet</strong> (<em>hasOne</em> = a un, <em>hasMany</em> = a plusieurs, <em>belongsTo</em> = appartient a) :</p>
            <ul>
                <li><code>User</code> : <em>hasOne</em> <code>ArtisanProfile</code> ; <em>hasMany</em> <code>Messages</code>, <code>FcmTokens</code>, <code>Favorites</code>.</li>
                <li><code>ArtisanProfile</code> : <em>belongsTo</em> <code>User</code> et <code>Category</code> ; <em>hasMany</em> <code>Publications</code>, <code>ProfileViews</code>.</li>
                <li><code>Publication</code> : <em>belongsTo</em> <code>ArtisanProfile</code> ; <em>hasMany</em> <code>Likes</code>, <code>Comments</code>, <code>Media</code>.</li>
                <li><code>Conversation</code> : <em>hasMany</em> <code>Messages</code> ; <em>belongsTo</em> deux fois <code>User</code> (un comme <code>client</code>, un comme <code>artisan</code>).</li>
                <li><code>Message</code> : <em>belongsTo</em> <code>Conversation</code> et <code>User</code> (sender) ; <em>hasMany</em> <code>Reactions</code>.</li>
                <li><code>MessageReaction</code> : <em>belongsTo</em> <code>Message</code> et <code>User</code> (avec contrainte unique sur la paire).</li>
            </ul>

            <p><strong><em>Hooks</em> de modeles</strong> (declencheurs automatiques places dans la methode <code>booted()</code> du modele) :</p>
            <ul>
                <li><code>User::saving</code> : avant chaque sauvegarde, concatene <code>first_name</code> + <code>last_name</code> dans la colonne <code>name</code> (retrocompatibilite).</li>
                <li><code>Review::saved/deleted</code> : recalcule la note moyenne via <code>ArtisanProfile::updateRating()</code> + <em>invalide le cache</em> (force le rechargement) des stats artisan.</li>
                <li><code>Publication::deleting</code> : supprime les fichiers media du disque AVANT la cascade DB (sinon orphelins).</li>
                <li><code>Comment::created/deleted</code> : incremente ou decremente <code>publications.comments_count</code> (compteur denormalise pour eviter un <code>COUNT(*)</code> a chaque affichage).</li>
            </ul>
        '],
        ['title' => 'TiDB Cloud et corbeille soft-delete', 'num' => 4, 'lesson' => '
            <p><strong>TiDB Cloud</strong> = base de donnees <em>MySQL distribue serverless</em> (sans serveur a gerer, facturee a l\'usage, scalable horizontalement). Gratuit jusqu\'a 5 Go (free tier).</p>

            <p><strong>Configuration <code>.env</code> :</strong></p>
            <div class="code-example">DB_CONNECTION=mysql
DB_HOST=gateway01.eu-central-1.prod.aws.tidbcloud.com
DB_PORT=4000           # port TiDB (pas 3306 standard MySQL)
DB_DATABASE=fegartisan
DB_USERNAME=...
DB_PASSWORD=...
MYSQL_ATTR_SSL_CA=ssl/ca.pem    # certificat SSL obligatoire</div>
            <p>Le fichier <code>ssl/ca.pem</code> est le <em>certificat SSL</em> (Certificate Authority, autorite qui garantit l\'identite du serveur) telecharge depuis l\'interface TiDB Cloud : <em>votre cluster</em> &gt; <em>Connect</em> &gt; <em>CA Certificate</em>. Sans lui, la connexion TLS echoue.</p>

            <p><strong>Corbeille a retention 30 jours :</strong> la migration <code>add_soft_deletes_to_trashable_tables</code> ajoute une colonne <code>deleted_at</code> (indexee) sur 6 modeles : <code>users</code>, <code>artisan_profiles</code>, <code>publications</code>, <code>comments</code>, <code>reviews</code>, <code>categories</code>. Le <em>soft-delete</em> (suppression douce) marque la ligne comme supprimee sans la retirer physiquement.</p>

            <p>Le trait <em>SoftDeletes</em> de Laravel ne <em>cascade</em> (propage la suppression aux modeles lies) PAS automatiquement entre modeles. On orchestre donc la cascade manuellement dans le hook <code>booted::deleting</code>, avec un test sur <code>isForceDeleting()</code> (qui distingue soft-delete et force-delete) :</p>
            <div class="code-example">User::deleting (soft)             => ArtisanProfile::delete()
                                  => Comment::delete()  (les siens)
                                  => Review::delete()   (donnes)

ArtisanProfile::deleting (soft)   => Publication::delete()
                                  => Review::delete()   (recus)

Publication::deleting (soft)      => Comment::delete()
                                  // PAS les fichiers media
                                  //   (gardes pour permettre restore)

Publication::deleting (FORCE)     => Storage::disk("public")->delete($path)
                                  // libere de l\'espace disque Hostinger</div>

            <p><strong>Commandes Artisan planifiees</strong> (executees par le scheduler via cron) :</p>
            <ul>
                <li><code>artisans:cleanup-pending</code> (quotidien 03:00) : a J+3 apres inscription incomplete, envoie un email de relance ; a J+7, soft-delete du compte (entree en corbeille).</li>
                <li><code>trash:purge --days=30</code> (quotidien 03:15) : <em>force-delete</em> (suppression definitive et irreversible) les enregistrements dont <code>deleted_at &lt; now() - 30j</code>. Ordre respecte les contraintes de cles etrangeres : commentaires &rarr; avis &rarr; publications &rarr; profils artisans &rarr; categories &rarr; utilisateurs.</li>
            </ul>
            <div class="tip">La <em>contrainte UNIQUE</em> sur <code>users.email</code> reste active meme sur les lignes <em>soft-deleted</em>. Consequence : un email present dans la corbeille bloque une nouvelle inscription avec le meme email pendant les 30 jours qui suivent. L\'admin peut purger manuellement via <code>/admin/trash</code> pour debloquer la situation.</div>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Quelle valeur d'enum prend role dans la table users ?", 'options'=>['public, private', 'client, artisan, admin', 'free, paid', 'visitor, member'], 'answer'=>1, 'explanation'=>"<code>enum('role', ['client','artisan','admin'])-&gt;default('client')</code>."],
        ['chapter'=>0, 'question'=>"Quels sont les statuts de validation d'un artisan ?", 'options'=>['active, inactive', 'pending, approved, rejected, suspended', 'new, validated', 'en attente, ok'], 'answer'=>1, 'explanation'=>"<code>enum('validation_status', ['pending','approved','rejected','suspended'])-&gt;default('pending')</code>."],
        ['chapter'=>0, 'question'=>"Pourquoi la table publication_media existe-t-elle separement ?", 'options'=>['Pour la perf SQL', 'Pour permettre une galerie multi-media (text + N images + N videos)', 'Pour le cache', 'Aucune raison'], 'answer'=>1, 'explanation'=>"Une publication peut combiner <strong>texte + N images + N videos</strong>. Chaque media a sa <code>position</code> pour l'ordre d'affichage. Le champ <code>publications.media_url</code> reste le premier media pour compat avec anciens clients."],
        ['chapter'=>0, 'question'=>"Quelle est la contrainte unique cruciale sur message_reactions ?", 'options'=>['emoji seul', 'unique(message_id, user_id)', 'unique(emoji, user_id)', 'aucune'], 'answer'=>1, 'explanation'=>"<code>unique(['message_id','user_id'])</code> garantit <strong>une seule reaction par user par message</strong>. Cle technique du toggle intelligent (add/replace/remove)."],
        ['chapter'=>0, 'question'=>"Quelle colonne est ajoutee specifiquement pour tracker les visites de profil artisan ?", 'options'=>['Aucune table', 'profile_views avec viewer_user_id, viewer_ip, viewed_at indexe', 'visits_log JSON', 'analytics_events'], 'answer'=>1, 'explanation'=>"Table dediee <code>profile_views</code>. L'artisan qui visite son propre profil n'est <strong>jamais</strong> compte. Deduplication par IP/24h."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Commande Artisan pour creer une migration qui ajoute une colonne ?", 'options'=>['php artisan make:migration ... --create=...', 'php artisan make:migration add_xxx_to_yyy --table=yyy', 'php artisan alter:table', 'php artisan db:column'], 'answer'=>1, 'explanation'=>"<code>php artisan make:migration add_fcm_token_to_users_table --table=users</code>. Le flag <code>--table=</code> pour modifier une table existante (vs <code>--create=</code> pour creer)."],
        ['chapter'=>1, 'question'=>"Quelle methode pour declarer une foreign key vers users avec cascade delete ?", 'options'=>['$table->foreign(\'user_id\')->references(\'id\')->on(\'users\')', '$table->foreignId(\'user_id\')->constrained()->cascadeOnDelete()', '$table->fk(\'user_id\')', 'Aucune'], 'answer'=>1, 'explanation'=>"<code>$table-&gt;foreignId('user_id')-&gt;constrained()-&gt;cascadeOnDelete()</code>. Convention : nomme la colonne <code>x_id</code> et appelle <code>constrained()</code>."],
        ['chapter'=>1, 'question'=>"ATTENTION : que fait <code>php artisan migrate:fresh</code> ?", 'options'=>['Rejoue les migrations en attente', 'DROP toutes les tables + recreate', 'Rollback la derniere', 'Affiche les migrations'], 'answer'=>1, 'explanation'=>"<strong>Supprime TOUTES les donnees</strong> et recree. A ne faire qu'en dev. Ajouter <code>--seed</code> pour les donnees test."],
        ['chapter'=>1, 'question'=>"Quelle commande affiche l'etat des migrations sans rien changer ?", 'options'=>['php artisan migrate:list', 'php artisan migrate:status', 'php artisan db:show', 'php artisan db:status'], 'answer'=>1, 'explanation'=>"<code>php artisan migrate:status</code> liste les migrations executees vs en attente."],
        ['chapter'=>1, 'question'=>"Quelle methode ajoute created_at + updated_at auto a une migration ?", 'options'=>['$table->autoTimestamps()', '$table->timestamps()', '$table->dates()', '$table->time()'], 'answer'=>1, 'explanation'=>"<code>$table-&gt;timestamps()</code> ajoute <code>created_at</code> et <code>updated_at</code> automatiquement geres par Eloquent."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Comment declarer la relation User hasMany Scores dans le modele User ?", 'options'=>['return $this->scoresMany()', 'return $this->hasMany(Score::class)', 'return Score::where(...)', 'return new HasMany(Score)'], 'answer'=>1, 'explanation'=>"<code>public function scores() { return $this-&gt;hasMany(Score::class); }</code>."],
        ['chapter'=>2, 'question'=>"Quelle relation entre Conversation et User ?", 'options'=>['hasOne uniquement', 'belongsTo (client + artisan)', 'hasMany', 'morphTo'], 'answer'=>1, 'explanation'=>"Une conversation a 2 belongsTo <code>User</code> : <code>client_id</code> et <code>artisan_id</code>. Plus hasMany Messages."],
        ['chapter'=>2, 'question'=>"Que fait le hook User::saving ?", 'options'=>["Crypter le password", "Concatener first_name + last_name dans la colonne name", "Logger les changements", "Envoyer un email"], 'answer'=>1, 'explanation'=>"Le hook concatene <code>first_name + last_name</code> dans <code>name</code> pour compatibilite avec les anciens scripts qui lisent <code>name</code>."],
        ['chapter'=>2, 'question'=>"Que fait le hook Publication::deleting (soft) ?", 'options'=>['Supprime les fichiers media', 'Cascade vers Comment::delete()', 'Supprime les avis', 'Rien'], 'answer'=>1, 'explanation'=>"Sur soft-delete : cascade vers <code>Comment::delete()</code>. Sur <strong>force-delete</strong> : supprime aussi les fichiers media du disque via <code>Storage::disk('public')-&gt;delete($path)</code>."],
        ['chapter'=>2, 'question'=>"Pourquoi pas $fillable dans un modele ?", 'options'=>['Pas necessaire', "Mass assignment serait dangereux (un attaquant peut injecter des champs non desires)", "Performance", "Conflict avec Eloquent"], 'answer'=>1, 'explanation'=>"<code>$fillable</code> est la liste blanche des colonnes qu'on peut remplir via <code>Model::create($request->all())</code>. Sans, un attaquant pourrait injecter <code>is_admin=true</code>."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"Quel SGBD est utilise en production FeGArtisan ?", 'options'=>['MySQL local', 'TiDB Cloud Serverless (compatible MySQL) en eu-central-1', 'PostgreSQL', 'SQLite'], 'answer'=>1, 'explanation'=>"<strong>TiDB Cloud Serverless</strong> region <strong>eu-central-1 (Frankfurt)</strong> pour minimiser la latence depuis Hostinger."],
        ['chapter'=>3, 'question'=>"Quel fichier doit etre present pour la connexion SSL a TiDB ?", 'options'=>['ssl/ca.pem (telecharge depuis TiDB Cloud)', 'ssl/key.pem', 'tidb.cert', '.env'], 'answer'=>0, 'explanation'=>"<code>ssl/ca.pem</code> a la racine du projet, telechargeable depuis TiDB Cloud => votre cluster => Connect => CA Certificate."],
        ['chapter'=>3, 'question'=>"Combien de jours dure la corbeille avant purge definitive ?", 'options'=>['7 jours', '30 jours', '90 jours', 'Illimite'], 'answer'=>1, 'explanation'=>"<strong>30 jours</strong>. La commande <code>trash:purge --days=30</code> tourne quotidiennement a 03:15."],
        ['chapter'=>3, 'question'=>"Sur combien de modeles le soft-delete est-il applique ?", 'options'=>['3', '6 (users, artisan_profiles, publications, comments, reviews, categories)', '10', '20'], 'answer'=>1, 'explanation'=>"<strong>6 modeles</strong> : users, artisan_profiles, publications, comments, reviews, categories. Migration <code>add_soft_deletes_to_trashable_tables</code> ajoute <code>deleted_at</code> indexe."],
        ['chapter'=>3, 'question'=>"L'UNIQUE constraint sur users.email reste-t-elle active sur les rows soft-deleted ?", 'options'=>['Non, MySQL les ignore', 'Oui, donc un email en corbeille bloque une nouvelle inscription 30 jours', 'Seulement en dev', 'Aleatoire'], 'answer'=>1, 'explanation'=>"<strong>Oui</strong>. Consequence : un user soft-deleted dont l'email est dans la corbeille empeche une nouvelle inscription avec le meme email pendant 30 jours."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
