@extends('layouts.app')
@section('title', 'Epreuve 3 : Routes API exhaustives')

@php
    $config = [
        'qcm_key' => 'fega-3',
        'title' => 'Epreuve 3 : Routes API exhaustives',
        'subtitle' => '20 questions . QCM transverse . Routes publiques + auth + artisan',
        'badge' => 'API',
        'color' => '#C17B4E',
        'description' => 'Connaitre la map exhaustive de l\'API REST FeGArtisan : publiques, authentifiees (Bearer), reservees artisan, et leurs middlewares.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Cartographie complete des routes API', 'num' => 1, 'lesson' => '
            <p>Une <em>route API</em> est un point d\'entree HTTP du backend Laravel. Chaque route associe un <em>verbe HTTP</em> (GET, POST, PUT, PATCH, DELETE) + un chemin (<em>endpoint</em>) a une methode de controller.</p>
            <p><strong>Base URL en developpement local :</strong> <code>http://127.0.0.1:8000/api</code></p>
            <p><strong>Base URL en production :</strong> <code>https://votre-domaine.com/api</code> (Hostinger).</p>
            <p>L\'API se decoupe en 3 groupes selon le niveau de protection : <em>routes publiques</em> (sans authentification), <em>routes authentifiees</em> (Bearer token Sanctum obligatoire), et <em>routes reservees artisan</em> (middleware <code>artisan.role</code> en plus).</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Endpoint d'inscription client ?", 'options'=>['POST /register', 'POST /api/register/client', 'POST /api/clients', 'POST /api/users/new'], 'answer'=>1, 'explanation'=>"POST /api/register/client (throttle 10/min)."],
        ['chapter'=>0, 'question'=>"Endpoint de login ?", 'options'=>['POST /api/login', 'POST /api/auth/signin', 'POST /api/sessions', 'POST /api/me'], 'answer'=>0, 'explanation'=>"POST /api/login. Throttle 5/min."],
        ['chapter'=>0, 'question'=>"Endpoint pour renvoyer le mail de verification ?", 'options'=>['POST /api/email/resend', 'POST /api/verify/resend', 'POST /api/me/resend-verify', 'GET /api/email/again'], 'answer'=>0, 'explanation'=>"POST /api/email/resend (corps : email). Throttle 5/min."],
        ['chapter'=>0, 'question'=>"Liste des categories de metiers ?", 'options'=>['POST /api/cats', 'GET /api/categories (publique)', 'GET /api/metiers', 'GET /api/artisans/categories'], 'answer'=>1, 'explanation'=>"GET /api/categories (publique, pour les filtres)."],
        ['chapter'=>0, 'question'=>"Recherche artisans avec filtres ?", 'options'=>['POST /api/search', 'GET /api/artisans (filtres ville/categorie/q via query params)', 'GET /api/find-artisan', 'POST /api/artisans/search'], 'answer'=>1, 'explanation'=>"GET /api/artisans. Masque les profils avec <code>profile_visible=false</code>."],
        ['chapter'=>0, 'question'=>"Fiche detaillee d'un artisan ?", 'options'=>['POST /api/artisans/{id}', 'GET /api/artisans/{id}', 'GET /api/profile/{id}', 'GET /api/users/{id}'], 'answer'=>1, 'explanation'=>"GET /api/artisans/{id}. 404 si <code>profile_visible=false</code>. Incremente <code>views_count</code> (sauf si proprietaire visite son propre profil)."],
        ['chapter'=>0, 'question'=>"Mon profil (authentifie) ?", 'options'=>['GET /api/profile', 'GET /api/me', 'GET /api/users/me', 'GET /api/auth/me'], 'answer'=>1, 'explanation'=>"GET /api/me. Appele au bootstrap + a chaque foreground pour rafraichir email_verified_at."],
        ['chapter'=>0, 'question'=>"Mettre a jour son profil ?", 'options'=>['PUT /api/me', 'POST /api/me', 'PATCH /api/profile', 'POST /api/me/update'], 'answer'=>1, 'explanation'=>"POST /api/me. Multipart pour avatar (image)."],
        ['chapter'=>0, 'question'=>"Supprimer son compte (RGPD) ?", 'options'=>['POST /api/me/delete', 'DELETE /api/me', 'POST /api/account/remove', 'DELETE /api/users/me'], 'answer'=>1, 'explanation'=>"DELETE /api/me. Accepte <code>reason</code> et <code>comment</code> optionnels. Soft-delete (corbeille 30j)."],
        ['chapter'=>0, 'question'=>"Logout (revoque le token courant) ?", 'options'=>['DELETE /api/token', 'POST /api/logout', 'POST /api/auth/logout', 'GET /api/end'], 'answer'=>1, 'explanation'=>"POST /api/logout."],
        ['chapter'=>0, 'question'=>"Toggle like sur publication ?", 'options'=>['POST /api/publications/{id}/like', 'PUT /api/likes/{id}', 'GET /api/like/{id}', 'POST /api/like'], 'answer'=>0, 'explanation'=>"POST /api/publications/{id}/like. Retourne <code>{ liked, likes_count }</code>."],
        ['chapter'=>0, 'question'=>"Commenter une publication ?", 'options'=>['POST /api/publications/{id}/comments', 'POST /api/comments', 'POST /api/publications/comment', 'PUT /api/c'], 'answer'=>0, 'explanation'=>"POST /api/publications/{id}/comments. Accepte un <code>parent_id</code> pour les replies."],
        ['chapter'=>0, 'question'=>"Toggle favori sur artisan ?", 'options'=>['POST /api/favorites/{artisanId}', 'POST /api/fav', 'POST /api/users/{id}/like', 'PUT /api/favorites'], 'answer'=>0, 'explanation'=>"POST /api/favorites/{artisanId}. Toggle (add ou remove)."],
        ['chapter'=>0, 'question'=>"Endpoint de polling messages ?", 'options'=>['GET /api/conversations/{id}/messages?after={id}', 'GET /api/poll', 'POST /api/messages/since', 'WebSocket'], 'answer'=>0, 'explanation'=>"GET /api/conversations/{id}/messages?after={lastId}, appele toutes les 3s."],
        ['chapter'=>0, 'question'=>"Envoyer un message ?", 'options'=>['POST /api/messages', 'POST /api/conversations/{id}/messages', 'POST /api/chat/send', 'PUT /api/m'], 'answer'=>1, 'explanation'=>"POST /api/conversations/{id}/messages. Accepte texte ou fichier (multipart)."],
        ['chapter'=>0, 'question'=>"Demarrer ou recuperer une conversation avec un artisan ?", 'options'=>['POST /api/conversations { artisan_id }', 'POST /api/chat/start', 'GET /api/conversations/new', 'PUT /api/conv'], 'answer'=>0, 'explanation'=>"POST /api/conversations avec body <code>{ artisan_id }</code>. Retourne la conversation existante ou la cree."],
        ['chapter'=>0, 'question'=>"Stats artisan (vues, likes, avis, messages) ?", 'options'=>['GET /api/artisan/dashboard', 'GET /api/me/stats', 'GET /api/dashboard', 'POST /api/artisan/stats'], 'answer'=>0, 'explanation'=>"GET /api/artisan/dashboard. Middleware <code>artisan.role</code>."],
        ['chapter'=>0, 'question'=>"Creer une publication artisan ?", 'options'=>['POST /api/publications', 'POST /api/artisan/publications (multipart media[])', 'POST /api/posts', 'PUT /api/publications/new'], 'answer'=>1, 'explanation'=>"POST /api/artisan/publications. Multipart avec champ <code>media[]</code> pour la galerie multi-fichiers."],
        ['chapter'=>0, 'question'=>"Modifier une publication en remplacant la galerie ?", 'options'=>['PUT /api/artisan/publications/{id}', 'PUT /api/artisan/publications/{id} avec replace_media=1', 'POST /api/publications/{id}/replace', 'DELETE puis POST'], 'answer'=>1, 'explanation'=>"PUT /api/artisan/publications/{id} avec <code>replace_media=1</code> dans le body pour ecraser la galerie."],
        ['chapter'=>0, 'question'=>"Endpoint pour signaler un contenu (publication, user, comment) ?", 'options'=>['POST /api/reports', 'POST /api/signal', 'POST /api/flag', 'POST /api/abuse'], 'answer'=>0, 'explanation'=>"POST /api/reports avec cible polymorphe (publication, user ou comment)."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
