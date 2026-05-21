@extends('layouts.app')
@section('title', 'Presentation FeGArtisan')

@php
    $config = [
        'qcm_key' => 'fega-intro',
        'title' => 'Presentation du projet FeGArtisan',
        'subtitle' => '25 questions . 5 chapitres . Mini-lecons tirees du memoire',
        'badge' => 'INTRO',
        'color' => '#8B3D1A',
        'description' => 'Le cadre institutionnel, la problematique, le sondage realise sur le terrain, les objectifs et les cibles du projet. <br>Tout ce qui ouvre la soutenance.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Cadre institutionnel : CallConnect', 'num' => 1, 'lesson' => '
            <p><strong>CallConnect Benin (CallConnect SARL)</strong> est la structure d\'accueil du stage. Il s\'agit d\'une societe basee a Cotonou, specialisee dans les solutions telephoniques et dans l\'<em>externalisation de services</em> (sous-traitance d\'activites a distance, ici principalement vers la France).</p>
            <p><strong>Date de creation officielle :</strong> 30 mai 2023 (verifiable via le <em>RCCM</em>, Registre du Commerce et du Credit Mobilier qui repertorie les entreprises au Benin).</p>
            <p><strong>Localisation :</strong> Parcelle C, maison IDY SEIDOU BACHAROU, quartier Gbedjromede, 6e arrondissement de Cotonou, departement du Littoral.</p>
            <p><strong>Organisation interne :</strong> Direction Generale, Pole Developpement (lieu effectif du stage), Pole Design UI/UX (<em>User Interface / User Experience</em>, conception des interfaces et de l\'experience utilisateur), Pole Formation, Pole Commercial.</p>
            <div class="tip">L\'entreprise se positionne comme un <strong>Webcenter</strong> (centre de services numeriques en ligne) novateur, qui fournit talents techniques et solutions numeriques aux <em>solopreneurs</em> (entrepreneurs individuels), PME et organisations. Vision : exploiter l\'<em>IA</em> (Intelligence Artificielle) pour <strong>renforcer</strong> le potentiel humain plutot que le remplacer.</div>
            <p>Un reseau d\'<em>assistants virtuels offshore</em> (collaborateurs a distance bases dans des pays a moindre cout horaire) reparti dans 4 pays : <strong>Benin, Maroc, Philippines, Madagascar</strong>.</p>
        '],
        ['title' => 'Problematique et etat des lieux', 'num' => 2, 'lesson' => '
            <p>Le <em>secteur artisanal</em> (ensemble des metiers manuels independants : couturiers, plombiers, maçons, etc.) au Benin represente une part importante de l\'<em>emploi informel</em> (activite economique non declaree officiellement), mais souffre d\'un <strong>manque de visibilite et d\'organisation</strong>.</p>
            <p><strong>Cote clients :</strong> trouver un artisan qualifie repose principalement sur le <em>bouche-a-oreille</em> (recommandations orales entre proches) — d\'ou des pertes de temps et des risques sur la qualite des prestations.</p>
            <p><strong>Cote artisans :</strong> absence d\'outils numeriques adaptes — la visibilite reste limitee a la zone geographique immediate, et il est difficile de gerer la relation client ou de mettre en avant ses realisations.</p>
            <p><strong>Chiffre cle</strong> (source <em>Banque Mondiale</em>, institution financiere internationale qui publie des statistiques de developpement) : plus de <code>80 %</code> des emplois en Afrique subsaharienne relevent du secteur informel.</p>
            <p>Le memoire identifie <strong>4 problemes concrets</strong> :</p>
            <ul>
                <li>Difficulte de trouver rapidement un artisan a la fois competent, disponible et geographiquement proche</li>
                <li>Manque de visibilite en ligne pour les artisans</li>
                <li>Absence de canaux de communication etablis entre artisans et clients</li>
                <li>Confiance faible (aucun moyen de verifier objectivement les competences ou la reputation)</li>
            </ul>
            <div class="tip">L\'etude des plateformes existantes (analyse <em>concurrentielle</em>, etude du marche actuel) a montre qu\'elles sont soit des <strong>sites web peu adaptes au mobile</strong>, soit des applications limitees a certaines fonctions (demande de <em>devis</em> uniquement, c\'est-a-dire estimation de prix avant prestation), avec parfois des <strong>barrieres linguistiques</strong> pour les francophones.</div>
        '],
        ['title' => 'Sondage et resultats terrain', 'num' => 3, 'lesson' => '
            <p>Un <em>sondage</em> (enquete d\'opinion par questionnaire) en ligne anonyme a ete realise <strong>du 05/05/2026 au 07/05/2026</strong>, totalisant <strong>71 repondants</strong> (50 clients + 21 artisans).</p>
            <p><strong>Validation du besoin :</strong></p>
            <ul>
                <li>Clients interesses : <strong>72 %</strong> (36 sur 50)</li>
                <li>Artisans interesses : <strong>66,7 %</strong> (14 sur 21)</li>
                <li>Interet global (toutes categories confondues) : <strong>70,4 %</strong></li>
            </ul>
            <p><strong>Top 3 difficultes cote clients :</strong></p>
            <ul>
                <li>Difficulte d\'evaluer la qualite avant de payer (46 %)</li>
                <li>Pas de prix fixe / negociation longue (36 %)</li>
                <li>Manque de fiabilite ou de ponctualite (36 %)</li>
            </ul>
            <p><strong>Top 3 obstacles cote artisans :</strong></p>
            <ul>
                <li>Manque de visibilite (95,2 %)</li>
                <li>Pas de presence en ligne (38,1 %)</li>
                <li>Difficulte a inspirer confiance (33,3 %)</li>
            </ul>
            <p><strong>Fonctionnalites les plus attendues cote clients :</strong> filtrer par localisation (66 %), filtrer par metier ou categorie (58 %), voir les photos de realisations dans un <em>portfolio</em> (collection des travaux passes de l\'artisan, 48 %).</p>
        '],
        ['title' => 'Objectifs et fonctionnalites principales', 'num' => 4, 'lesson' => '
            <p><strong>Objectif general :</strong> creer une application mobile facilitant la <em>mise en relation</em> (matchmaking : mecanisme d\'appariement entre demande et offre) rapide, efficace et securisee entre artisans et clients au Benin.</p>
            <p><strong>Objectifs specifiques (au nombre de 4) :</strong></p>
            <ul>
                <li>Creer une application <em>Android</em> (systeme d\'exploitation mobile Google majoritaire au Benin) permettant de chercher un artisan par categorie de metier et localisation</li>
                <li>Proposer aux artisans une application pour mettre en avant leurs services, partager leurs creations et gerer les echanges</li>
                <li>Etablir une <em>messagerie instantanee</em> (echange de messages en temps quasi reel) securisee</li>
                <li>Creer un <em>tableau de bord</em> (interface de pilotage et de supervision, ou <em>dashboard</em>) en ligne pour l\'administrateur</li>
            </ul>
            <p><strong>Fonctionnalites principales :</strong></p>
            <ul>
                <li><strong>Inscription &amp; authentification</strong> (verification d\'identite par email + mot de passe) securisees, avec validation email</li>
                <li><strong>Moteur de recherche multicriteres</strong> (recherche combinant plusieurs filtres simultanement : metier, ville, quartier)</li>
                <li><strong>Messagerie instantanee</strong> (photos, audio, documents)</li>
                <li><strong>Notifications push</strong> (alertes systeme envoyees au telephone meme app fermee) pour nouveaux messages et changements de statut</li>
                <li><strong>Gestion de profils</strong> complets pour artisan (photo, metier, description, tarifs, zones d\'intervention)</li>
                <li><strong>Tableau de bord artisan</strong> (statistiques, gestion des publications et messages)</li>
                <li><strong>Tableau de bord admin</strong> (<em>moderation</em> = controle et suppression des contenus inappropries, gestion des utilisateurs, statistiques globales)</li>
            </ul>
        '],
        ['title' => 'Cibles et apports du stage', 'num' => 5, 'lesson' => '
            <p><strong>3 types d\'utilisateurs cibles :</strong></p>
            <ul>
                <li><strong>Clients</strong> : particuliers ou entreprises a la recherche de services artisanaux rapides, fiables et locaux</li>
                <li><strong>Artisans</strong> : maçons, couturiers, electriciens, plombiers, menuisiers, coiffeurs, etc.</li>
                <li><strong>Administrateurs</strong> : responsables du bon fonctionnement de la plateforme et de la moderation</li>
            </ul>
            <div class="tip"><strong>Important :</strong> les clients et artisans utilisent <strong>uniquement</strong> l\'application <em>Flutter</em> (framework de Google pour developper des apps mobiles). L\'admin utilise <strong>uniquement</strong> le <em>dashboard</em> web Laravel (interface dans le navigateur). L\'admin ne peut PAS se connecter via l\'<em>API</em> Flutter (Application Programming Interface : ensemble des points d\'entree programmables exposes par le serveur) — c\'est un verrou de securite intentionnel.</div>
            <p><strong>Apports du stage (techniques) :</strong> maitrise de <em>Flutter / Dart</em> (langage de programmation de Google associe a Flutter) pour le multiplateformes, de <em>Laravel</em> et du modele <em>MVC</em> (Modele-Vue-Controleur, separation du code en 3 couches), de <em>Next.js</em> (framework React pour applications web), de <em>WordPress</em> (CMS, systeme de gestion de contenu), integration de <em>Firebase</em> (suite de services Google pour applications), <em>deploiement</em> (mise en production sur un serveur) chez Hostinger, Vercel, Render, et gestion de projet IT.</p>
            <p><strong>Apports humains :</strong> comprehension des enjeux de la <em>digitalisation</em> (transformation numerique des activites traditionnelles) du secteur informel et artisanal en Afrique de l\'Ouest, travail en equipe, communication professionnelle.</p>
            <p><strong>Principales difficultes rencontrees :</strong> respect du temps imparti, mise en oeuvre de la communication en temps reel (qui a conduit a l\'adoption du <em>polling</em>, technique de sondage periodique du serveur par requetes HTTP repetees), optimisation pour les <em>appareils d\'entree de gamme</em> (telephones a faibles capacites materielles, repandus au Benin), et adaptation a differentes tailles d\'ecran (<em>responsive design</em>).</p>
        '],
    ];

    $allQuestions = [
        // CHAPITRE 1
        ['chapter'=>0, 'question'=>"Quelle est la date officielle de creation de CallConnect SARL Benin ?", 'options'=>['1er janvier 2022', '30 mai 2023', '15 mars 2024', "Decembre 2021"], 'answer'=>1, 'explanation'=>"La date officielle est le <strong>30 mai 2023</strong> (verifiable via le RCCM)."],
        ['chapter'=>0, 'question'=>"Dans quel quartier de Cotonou se trouve CallConnect ?", 'options'=>['Akpakpa', 'Cocotomey', 'Gbedjromede', 'Calavi'], 'answer'=>2, 'explanation'=>"Quartier <strong>Gbedjromede</strong>, 6e arrondissement, departement du Littoral."],
        ['chapter'=>0, 'question'=>"Le stage s'est principalement deroule dans quel pole de l'entreprise ?", 'options'=>['Pole Commercial', 'Pole Formation', 'Pole Developpement', 'Direction Generale'], 'answer'=>2, 'explanation'=>"Le <strong>Pole Developpement</strong> regroupe les developpeurs web et mobile responsables de la conception et realisation des solutions logicielles."],
        ['chapter'=>0, 'question'=>"Comment CallConnect se positionne-t-il ?", 'options'=>["Comme un editeur de logiciels classique", "Comme un Webcenter novateur", "Comme un fournisseur d'access Internet", "Comme un SSII"], 'answer'=>1, 'explanation'=>"CallConnect se positionne comme un <strong>Webcenter novateur</strong> qui fournit talents techniques et solutions numeriques aux solopreneurs, PME et organisations."],
        ['chapter'=>0, 'question'=>"Combien de pays heberge le reseau d'assistants virtuels offshore de CallConnect ?", 'options'=>['2', '3', '4', '5'], 'answer'=>2, 'explanation'=>"<strong>4 pays</strong> : Benin, Maroc, Philippines, Madagascar."],

        // CHAPITRE 2
        ['chapter'=>1, 'question'=>"Selon le memoire, quel pourcentage des emplois en Afrique subsaharienne releve du secteur informel ?", 'options'=>['Plus de 40%', 'Plus de 60%', 'Plus de 80%', 'Plus de 95%'], 'answer'=>2, 'explanation'=>"Selon la Banque Mondiale, <strong>plus de 80%</strong> des emplois en Afrique subsaharienne relevent du secteur informel."],
        ['chapter'=>1, 'question'=>"Quel est l'un des 4 problemes specifiques identifies cote clients ?", 'options'=>["Trop d'artisans disponibles", "Difficulte de trouver un artisan competent, dispo et proche", "Tarifs imposes par l'Etat", "Manque de materiel"], 'answer'=>1, 'explanation'=>"La <strong>difficulte de trouver rapidement un artisan competent, disponible et proche</strong> est le premier probleme identifie."],
        ['chapter'=>1, 'question'=>"Quel constat le memoire fait-il sur les plateformes concurrentes existantes ?", 'options'=>["Toutes excellentes et adaptees", "Souvent sites web peu adaptes mobile ou apps limitees, avec barrieres linguistiques", "Inexistantes au Benin", "Reservees aux entreprises"], 'answer'=>1, 'explanation'=>"L'etude des plateformes existantes a revele qu'elles sont soit des <strong>sites web peu adaptes a l'usage mobile</strong>, soit des <strong>applications limitees</strong>, avec parfois des <strong>barrieres linguistiques</strong> pour francophones."],
        ['chapter'=>1, 'question'=>"Sur quelle base repose principalement la recherche d'artisan AVANT FeGArtisan ?", 'options'=>['Annuaire papier officiel', 'Sites web specialises', 'Bouche-a-oreille et recommandations non officielles', "Petites annonces presse"], 'answer'=>2, 'explanation'=>"Le memoire indique que trouver un artisan repose principalement sur le <strong>bouche-a-oreille et les recommandations non officielles</strong>, ce qui entraine pertes de temps et risques."],
        ['chapter'=>1, 'question'=>"Quel est l'un des 4 problemes identifies cote artisans ?", 'options'=>["Trop de clients", "Manque de visibilite et d'outils numeriques modernes", "Mauvaise qualite des outils traditionnels", "Trop forte concurrence des grands groupes"], 'answer'=>1, 'explanation'=>"Les artisans manquent de <strong>visibilite en ligne</strong> et ne disposent pas <strong>d'outils de gestion modernes</strong> qui repondent a leurs besoins."],

        // CHAPITRE 3
        ['chapter'=>2, 'question'=>"Combien de repondants au sondage FeGArtisan ?", 'options'=>['35', '50', '71', '120'], 'answer'=>2, 'explanation'=>"<strong>71 repondants</strong> au total : 50 clients + 21 artisans."],
        ['chapter'=>2, 'question'=>"Sur quelle periode le sondage a-t-il ete realise ?", 'options'=>['Janvier 2026', 'Mars 2026', "5 au 7 mai 2026", "Juin 2026"], 'answer'=>2, 'explanation'=>"Du <strong>5 au 7 mai 2026</strong>, sondage en ligne anonyme."],
        ['chapter'=>2, 'question'=>"Quel pourcentage de clients se declare interesse par FeGArtisan ?", 'options'=>['48%', '60%', '72%', '88%'], 'answer'=>2, 'explanation'=>"<strong>72% des clients</strong> (36 sur 50) interesses."],
        ['chapter'=>2, 'question'=>"Quel pourcentage des artisans interroges se declare interesse ?", 'options'=>['33%', '50%', '66.7%', '80%'], 'answer'=>2, 'explanation'=>"<strong>66.7%</strong> (14 sur 21) artisans interesses."],
        ['chapter'=>2, 'question'=>"Quel est le verdict global du sondage en termes d'interet ?", 'options'=>['35%', '52%', '70.4%', '90%'], 'answer'=>2, 'explanation'=>"<strong>70.4% d'interet global</strong> pour l'application FeGArtisan."],
        ['chapter'=>2, 'question'=>"Quel est l'obstacle numero 1 identifie cote artisans ?", 'options'=>["Concurrence informelle", "Saisonnalite", "Manque de visibilite (95.2%)", "Manque de materiel"], 'answer'=>2, 'explanation'=>"Le <strong>manque de visibilite</strong> est l'obstacle dominant avec <strong>95.2%</strong> des artisans qui le mentionnent."],
        ['chapter'=>2, 'question'=>"Quelle fonctionnalite est la plus attendue cote clients ?", 'options'=>["Paiement en ligne", "Filtrer par localisation (66%)", "Suivi GPS", "Devis automatique"], 'answer'=>1, 'explanation'=>"<strong>Filtrer par localisation/proximite</strong> arrive en tete cote clients avec <strong>66%</strong>."],

        // CHAPITRE 4
        ['chapter'=>3, 'question'=>"Quel est l'objectif general de FeGArtisan ?", 'options'=>["Vendre du materiel artisanal en ligne", "Mettre en relation artisans et clients de facon rapide, efficace, securisee", "Former les artisans aux outils numeriques", "Distribuer des subventions aux artisans"], 'answer'=>1, 'explanation'=>"L'objectif general est de creer une application mobile facilitant la <strong>mise en relation rapide, efficace et securisee entre artisans et clients au Benin</strong>."],
        ['chapter'=>3, 'question'=>"Combien d'objectifs specifiques sont definis dans le memoire ?", 'options'=>['2', '3', '4', '6'], 'answer'=>2, 'explanation'=>"<strong>4 objectifs specifiques</strong> : app Android pour clients, app pour artisans, messagerie instantanee securisee, tableau de bord admin."],
        ['chapter'=>3, 'question'=>"Quel role joue le moteur de recherche dans l'app ?", 'options'=>["Trouver des produits dans un catalogue", "Chercher par metier, ville ou quartier (multicriteres)", "Indexer Google", "Trouver des tutoriels"], 'answer'=>1, 'explanation'=>"Le moteur de recherche est <strong>multicriteres</strong> : par metier, par ville et par quartier."],
        ['chapter'=>3, 'question'=>"Quelles 3 types de fichiers peut-on envoyer dans la messagerie instantanee ?", 'options'=>["Texte seul", "Photos, fichiers audio et documents", "Videos UHD uniquement", "Liens externes uniquement"], 'answer'=>1, 'explanation'=>"La messagerie permet d'envoyer <strong>photos, fichiers audio et documents</strong>."],
        ['chapter'=>3, 'question'=>"Quel besoin non fonctionnel est cible cote performance ?", 'options'=>["Temps de reponse < 3 sec meme en 3G", "Temps de reponse < 100 ms toujours", "Temps illimite", "Aucun"], 'answer'=>0, 'explanation'=>"Performance ciblee : <strong>temps de reponse inferieur a 3 secondes meme en connexion 3G</strong>."],

        // CHAPITRE 5
        ['chapter'=>4, 'question'=>"Combien de types d'utilisateurs cibles FeGArtisan a-t-il ?", 'options'=>['2', '3', '4', '5'], 'answer'=>1, 'explanation'=>"<strong>3 types</strong> : Clients, Artisans et Administrateurs (le Super Admin etant une variante d'admin avec droits etendus)."],
        ['chapter'=>4, 'question'=>"Comment l'admin se connecte-t-il a FeGArtisan ?", 'options'=>["Via l'application Flutter", "Via un dashboard web Laravel dans le navigateur", "Via SSH uniquement", "Aucun acces"], 'answer'=>1, 'explanation'=>"L'admin utilise <strong>uniquement le dashboard web Laravel</strong>. Il ne peut PAS se connecter via l'API Flutter (intentionnellement bloque pour la securite)."],
        ['chapter'=>4, 'question'=>"Quelle difficulte technique majeure a conduit a l'adoption du polling ?", 'options'=>["Manque de developpeurs", "Mise en oeuvre de la communication temps reel", "Choix de Flutter", "Trop d'utilisateurs"], 'answer'=>1, 'explanation'=>"La mise en oeuvre de la <strong>communication temps reel</strong> sur Hostinger mutualise a conduit a abandonner les WebSockets pour adopter le <strong>polling HTTP</strong>."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
