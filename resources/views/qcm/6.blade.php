@extends('layouts.app')
@section('title', 'Epreuve 6 : Diagrammes UML & conception')

@php
    $config = [
        'qcm_key' => 'fega-6',
        'title' => 'Epreuve 6 : Diagrammes UML & conception',
        'subtitle' => '14 questions . Cle . Acteurs, cas, classes, sequence, activite',
        'badge' => 'UML',
        'color' => '#8B3D1A',
        'description' => 'Le chapitre 2 du memoire detaille les diagrammes UML : contexte statique, cas d\'utilisation, classes, sequences (s\'authentifier, valider, rechercher) et activites correspondantes.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Conception UML du memoire', 'num' => 1, 'lesson' => '
            <p>L\'<em>UML</em> (Unified Modeling Language : langage de modelisation standardise pour les systemes orientes objet) est utilise dans le memoire pour formaliser la conception avant le codage.</p>
            <p>Les diagrammes UML sont realises avec deux outils :</p>
            <ul>
                <li><strong>Draw.io</strong> (egalement appele <em>diagrams.net</em>, editeur en ligne gratuit et open source) : pour les diagrammes de cas d\'utilisation, de contexte statique, de sequence et d\'activite.</li>
                <li><strong>StarUML</strong> (logiciel desktop dedie a UML) : pour le diagramme de classes, qui beneficie d\'un meilleur rendu des cardinalites et des methodes complexes.</li>
            </ul>
            <p>Cette epreuve teste les acteurs, les cas d\'utilisation, les cardinalites (<em>multiplicites</em> = nombre d\'instances en relation), les diagrammes de sequence (deroulement chronologique des echanges entre objets) et d\'activite (logique algorithmique).</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Quel outil principal pour les diagrammes UML du memoire ?", 'options'=>['StarUML uniquement', 'Draw.io (diagrams.net) + StarUML pour le diagramme de classes', 'Visio', 'PlantUML'], 'answer'=>1, 'explanation'=>"<strong>Draw.io</strong> pour la plupart, <strong>StarUML</strong> pour le diagramme de classes (besoin d'un outil professionnel pour cet exercice)."],
        ['chapter'=>0, 'question'=>"Combien d'acteurs principaux dans le contexte statique ?", 'options'=>['2', '3', '4 (Client, Artisan, Administrateur, Super Admin)', '5'], 'answer'=>2, 'explanation'=>"<strong>4 acteurs</strong> : Client, Artisan, Administrateur, Super Admin (variante avec droits complets)."],
        ['chapter'=>0, 'question'=>"Quelle est la multiplicite de l'acteur Administrateur sur le systeme ?", 'options'=>['0..*', '1..*', '1', '0..1'], 'answer'=>1, 'explanation'=>"<strong>1..*</strong> (au moins un admin pour gerer la plateforme)."],
        ['chapter'=>0, 'question'=>"Quelle multiplicite pour le Super Admin ?", 'options'=>['0..*', '1..*', '1 (unique)', '0..1'], 'answer'=>2, 'explanation'=>"<strong>1</strong> seul Super Admin. Il a les droits complets sur la plateforme (notamment gerer les autres admins)."],
        ['chapter'=>0, 'question'=>"Quel cas d'utilisation est commun aux 4 acteurs (include) ?", 'options'=>['Publier', 'Se connecter', 'Signaler', 'Liker'], 'answer'=>1, 'explanation'=>"<strong>Se connecter</strong> est en relation <code>include</code> avec tous les cas authentifies (s'inscrire, gerer profil, etc.)."],
        ['chapter'=>0, 'question'=>"Le diagramme de classes inclut-il une classe Categorie ?", 'options'=>['Non', 'Oui (id_categorie, nom, description, image, activite, dateCreation + methodes CRUD + active/desactive)', 'Seulement dans BDD', 'Optionnel'], 'answer'=>1, 'explanation'=>"<strong>Oui</strong>. Categorie est une entite a part entiere avec image (cover du profil artisan) et methodes ajouter/modifier/supprimer/activer/desactiver."],
        ['chapter'=>0, 'question'=>"Le diagramme de classes a une classe Signalement. Quel est son champ <em>type</em> ?", 'options'=>['Constant', 'Type de cible polymorphe (publication, user, comment)', 'Categorie', 'Severite'], 'answer'=>1, 'explanation'=>"Le champ <code>type</code> de Signalement est polymorphe : il indique le type de la cible (publication, user, comment)."],
        ['chapter'=>0, 'question'=>"Sequence 's'authentifier' : qui est le premier acteur a interagir ?", 'options'=>['BDD', 'Utilisateur (saisit email/password)', 'Auth Controleur', 'Page accueil'], 'answer'=>1, 'explanation'=>"L'utilisateur demande l'interface, la remplit, et la page envoie ensuite vers Auth Controleur qui valide via la BDD."],
        ['chapter'=>0, 'question'=>"Diagramme de sequence 'valider un artisan' : l'admin clique 'Approuver'. Que fait Validation Controleur ?", 'options'=>['supprime artisan', 'appelle mettreAJourStatut(valide) sur BDD + notifierArtisan(statut)', 'rien', 'envoie SMS'], 'answer'=>1, 'explanation'=>"<code>approuverArtisan(artisanId)</code> => <code>mettreAJourStatut(valide)</code> en BDD puis <code>notifierArtisan(statut)</code> (mail)."],
        ['chapter'=>0, 'question'=>"Sequence 'rechercher un artisan' : que sont les criteres principaux ?", 'options'=>['nom', 'categorie et quartier (deja enregistres dans le profil artisan)', 'age et sexe', 'photo'], 'answer'=>1, 'explanation'=>"Le client saisit <strong>categorie et quartier</strong>. Les autres infos viennent du profil artisan stocke."],
        ['chapter'=>0, 'question'=>"Que se passe-t-il dans l'alt 'aucun resultat' du diagramme de recherche ?", 'options'=>['crash', 'Message \"aucun artisan trouve\" et option de modifier les criteres', 'redirect', 'rien'], 'answer'=>1, 'explanation'=>"L'alt <em>aucun resultat</em> affiche \"aucun artisan trouve\" et propose de modifier les criteres. Sinon retour a la saisie."],
        ['chapter'=>0, 'question'=>"Diagramme d'activite 's'authentifier' : que se passe-t-il si 'Champs remplis ?' = Non ?", 'options'=>['Login', 'Message \"Champs obligatoires\" + retour a la saisie', 'Crash', 'Logout'], 'answer'=>1, 'explanation'=>"Boucle : si champs non remplis => message + retour saisie. Si remplis => valider identifiants => si corrects => creer session + dashboard."],
        ['chapter'=>0, 'question'=>"Diagramme d'activite 'valider artisan' : que peut faire l'admin si dossier incomplet ?", 'options'=>['Valider quand meme', 'Demander des complements a l\'artisan (boucle)', 'Supprimer immediatement', 'Ignorer'], 'answer'=>1, 'explanation'=>"Si dossier incomplet, l'admin peut <strong>demander des complements</strong> (boucle vers consulter le dossier). Sinon decision : valider ou rejeter."],
        ['chapter'=>0, 'question'=>"Quelle multiplicite entre User et Score (modele dev_learn / fegartisan_qcm) ?", 'options'=>['1..1', 'User 1 - 0..* Score (un user a plusieurs scores)', '0..1 - 0..1', 'many-many'], 'answer'=>1, 'explanation'=>"<strong>1 User - 0..* Score</strong>. Un utilisateur peut avoir plusieurs tentatives (scores) sur differents QCM."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
