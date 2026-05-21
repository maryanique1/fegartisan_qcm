@extends('layouts.app')
@section('title', 'Epreuve 8 : Frontend, UX, design & outils')

@php
    $config = [
        'qcm_key' => 'fega-8',
        'title' => 'Epreuve 8 : Frontend, UX, design & outils',
        'subtitle' => '18 questions . Soutenance . Design system, responsive, outils techniques',
        'badge' => 'UX',
        'color' => '#0468D7',
        'description' => 'Justifier les choix de design (Material 3, palette terra cotta, design system tokens), le responsive, l\'accessibilite, et les outils utilises (Postman/Thunder Client, Draw.io, StarUML, Git/GitHub, VS Code, Android Studio).',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Frontend, UX et outils techniques', 'num' => 1, 'lesson' => '
            <p>Cette epreuve cible toutes les questions du jury sur le <strong>design</strong> (apparence visuelle), l\'<strong>UX</strong> (User Experience : qualite percue de l\'experience utilisateur) et les <strong>outils techniques</strong> employes au quotidien dans le projet.</p>
            <p><strong>Design system FeGArtisan</strong> (systeme de design : ensemble de regles visuelles centralisees pour garantir la coherence de l\'interface) : tout est regroupe dans <code>lib/onboarding/core/theme/</code> autour de 3 fichiers cles :</p>
            <ul>
                <li><code>app_colors.dart</code> : palette terra cotta (couleurs <em>tokens</em> = valeurs nommees reutilisables : primary, accent, success, danger, etc.).</li>
                <li><code>app_tokens.dart</code> : barreme de <em>spacing</em> (espacements) en 4/8 px, <em>radius</em> (rayons de coins arrondis), classe <code>AppText</code> (echelle typographique).</li>
                <li><code>app_theme.dart</code> : <em>ThemeData</em> global Flutter en <em>Material 3</em> (derniere version du langage de design Google).</li>
            </ul>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Pourquoi avoir choisi la palette terra cotta (brun-orange) pour FeGArtisan ?", 'options'=>['Tendance moderne', 'Couleur traditionnelle de l\'artisanat (poterie, terre cuite) qui evoque le savoir-faire local', 'Imposee par HECM', 'Choix esthetique aleatoire'], 'answer'=>1, 'explanation'=>"La <strong>terre cuite</strong> evoque l'<strong>artisanat traditionnel</strong> (poterie, briques, materiaux beninois). Elle differencie la marque des concurrents tech bleus/verts et cree un univers chaleureux coherent avec la cible."],
        ['chapter'=>0, 'question'=>"Quel design system Material est utilise ?", 'options'=>['Material 2', 'Material 3 (useMaterial3: true)', 'Cupertino', 'Aucun, custom seulement'], 'answer'=>1, 'explanation'=>"<code>useMaterial3: true</code> dans ThemeData. Material 3 (Material You) offre des composants plus modernes et des color schemes generes automatiquement."],
        ['chapter'=>0, 'question'=>"Comment garantir la coherence du spacing dans l'app Flutter ?", 'options'=>['Valeurs en dur partout', 'AppSpacing tokens (xs=4, sm=8, md=12, lg=16, xl=20...) + helper Gap', 'Random', 'CSS-in-Dart'], 'answer'=>1, 'explanation'=>"Bareme 4/8 px via <code>AppSpacing.xs / sm / md / lg / xl / xxl / xxxl / huge</code>. Helper <code>const Gap(AppSpacing.lg)</code> remplace les <code>SizedBox</code> manuels."],
        ['chapter'=>0, 'question'=>"Quelle echelle typographique est definie ?", 'options'=>['Aucune', 'AppText.display / h1 / h2 / h3 / body / bodyStrong / bodySmall / label / button / caption / overline', 'Tailwind classes', 'Material default seul'], 'answer'=>1, 'explanation'=>"<strong>11 presets typo</strong> via AppText.* . Polices Playfair Display (display, h1, h2) et Inter (h3+, body, label, button). Garantit un rendu coherent partout."],
        ['chapter'=>0, 'question'=>"L'application mobile est-elle responsive ?", 'options'=>['Non, taille fixe', 'Oui, Flutter calcule via MediaQuery + LayoutBuilder pour s\'adapter a toutes tailles d\'ecran Android', 'Seulement en tablette', 'Iframe responsive'], 'answer'=>1, 'explanation'=>"Flutter est nativement responsive. Tests effectues sur Samsung A03 core, Tecno Spark 10, Infinix Hot 12 (gammes basses tres differentes)."],
        ['chapter'=>0, 'question'=>"Comment l'accessibilite est-elle prise en compte ?", 'options'=>['Pas du tout', 'Tailles de touche >= 48 dp (boutons 52 px), contrastes verifies, labels semantiques pour lecteurs d\'ecran', 'Seulement les couleurs', 'Plugin a installer'], 'answer'=>1, 'explanation'=>"Boutons 52 px (au-dessus du minimum WCAG 48 dp), contrastes terra cotta/cream OK, semantics Flutter pour TalkBack."],
        ['chapter'=>0, 'question'=>"Pourquoi avoir choisi Flutter plutot que natif Android/Kotlin ?", 'options'=>['Effet de mode', 'Cross-platform (Android + iOS depuis 1 seul code), hot reload tres rapide, performances natives, large communaute', 'Plus simple a apprendre', 'Free'], 'answer'=>1, 'explanation'=>"Une seule base de code pour Android + iOS, <strong>hot reload</strong> en dev, compilation native (pas WebView), Google supporte. Critique pour un MVP avec ressources limitees."],
        ['chapter'=>0, 'question'=>"Pourquoi VS Code comme IDE principal ?", 'options'=>['Tendance', 'Gratuit, leger, extensions Laravel/PHP/Flutter excellentes (Intelephense, Laravel Extension Pack, Dart, Flutter)', 'Obligatoire', 'Pas de raison'], 'answer'=>1, 'explanation'=>"<strong>Gratuit + leger + extensible</strong>. Extensions : PHP Intelephense, Laravel Extension Pack pour le backend ; Dart et Flutter pour mobile ; Thunder Client pour tester l'API."],
        ['chapter'=>0, 'question'=>"Pourquoi Android Studio en complement de VS Code ?", 'options'=>['Pour debugger', 'IDE officiel Android : gere l\'emulateur, le SDK Android, le debug avance, l\'inspection layout', 'Aucune raison', 'Pour iOS'], 'answer'=>1, 'explanation'=>"IDE officiel Android Studio. Gere SDK Manager, Virtual Device Manager (emulateur), debugger avance Flutter, et l\'inspecteur de widgets."],
        ['chapter'=>0, 'question'=>"Quel outil de test API est utilise et pourquoi ?", 'options'=>['Postman seulement', 'Thunder Client (extension VS Code, sans compte) recommande ; Postman optionnel avec compte', 'Curl seulement', 'Aucun'], 'answer'=>1, 'explanation'=>"<strong>Thunder Client</strong> recommande car gratuit, sans compte, integree a VS Code. Postman necessite desormais un compte pour importer. Curl reste utilisable en CLI."],
        ['chapter'=>0, 'question'=>"Pourquoi Draw.io (diagrams.net) pour les diagrammes UML ?", 'options'=>['Beau', 'Gratuit, open source, support UML 2.5, export PDF/PNG, AI generation, interface intuitive', 'Impose par le jury', 'Cher mais bien'], 'answer'=>1, 'explanation'=>"Gratuit, open source, UML 2.5, export PDF/PNG, AI integration. Adapte aux contextes academiques sans license."],
        ['chapter'=>0, 'question'=>"Pourquoi StarUML pour le diagramme de classes seulement ?", 'options'=>['Plus joli', 'Environnement professionnel dedie classe, meilleur rendu des cardinalites/methodes complexes', 'Aucune raison', 'Force par HECM'], 'answer'=>1, 'explanation'=>"StarUML est plus adapte aux diagrammes de classes complexes. Les autres diagrammes (cas, sequence, activite) sont plus simples a faire dans Draw.io."],
        ['chapter'=>0, 'question'=>"Pourquoi Git + GitHub pour la gestion de version ?", 'options'=>['Effet de mode', 'Standard de l\'industrie, gratuit, travail collaboratif en branche, sauvegarde, integration CI/CD (GitHub Actions deploiement automatique)', 'Pas de raison', 'Pour le CV'], 'answer'=>1, 'explanation'=>"Standard. Permet travail en equipe (branches), historique complet, et integration GitHub Actions pour le deploiement automatique sur Hostinger."],
        ['chapter'=>0, 'question'=>"L'application Flutter supporte-t-elle le mode sombre ?", 'options'=>['Non', 'Oui via ThemeMode.system, choix dark/light gere par AppColors et tokens', 'Light only', 'Random'], 'answer'=>1, 'explanation'=>"Le ThemeData est defini avec colors light + dark. Le user peut switcher via le bouton theme du dashboard QCM (FeGArtisan QCM). L'app mobile FeGArtisan peut etendre cela facilement."],
        ['chapter'=>0, 'question'=>"Comment le video player est-il habille pour le plein ecran ?", 'options'=>['UI custom', 'Package chewie autour de video_player (controles seek, plein ecran, son, vitesse)', 'WebView YouTube', 'Aucun'], 'answer'=>1, 'explanation'=>"<code>chewie</code> ajoute l'UI complete (seek bar, plein ecran, son, vitesse) par-dessus <code>video_player</code>. En feed, auto-play muet ; au tap, ChewieController prend la main."],
        ['chapter'=>0, 'question'=>"Comment Flutter cache-t-il les images du serveur pour eviter le re-download ?", 'options'=>['Cache navigateur', 'cached_network_image : cache disque automatique avec placeholders et fallback', 'Aucun cache', 'IndexedDB'], 'answer'=>1, 'explanation'=>"<code>cached_network_image</code> stocke les images sur disque. Au prochain affichage, lecture instantanee sans request. Critique sur connexion 3G beninoise."],
        ['chapter'=>0, 'question'=>"Quelle font sur le dashboard web admin (Blade) ?", 'options'=>['Comic Sans', 'Segoe UI / Arial system stack (zero web font a charger, rapide en 3G)', 'Google Roboto', 'Times New Roman'], 'answer'=>1, 'explanation'=>"Stack systeme <code>Segoe UI, Arial, sans-serif</code>. Zero web font a charger = page admin instantanee meme en 3G."],
        ['chapter'=>0, 'question'=>"Pourquoi un layout admin separe (Blade) du frontend mobile (Flutter) ?", 'options'=>['Sans raison', 'Besoins UX tres differents : admin = densite info + tableau + clavier ; mobile = touch + lecture sequentielle', 'Cout licence', 'Force par Laravel'], 'answer'=>1, 'explanation'=>"L'admin a besoin de <strong>tableaux denses</strong>, raccourcis clavier, multi-onglets. Le mobile vise un usage <strong>touch sequentiel</strong>. Deux UX = deux stacks adaptees."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
