@extends('layouts.app')
@section('title', 'Epreuve 7 : Tests & deploiement')

@php
    $config = [
        'qcm_key' => 'fega-7',
        'title' => 'Epreuve 7 : Tests & deploiement',
        'subtitle' => '12 questions . Intermediaire . Stack tests, Hostinger, APK signe',
        'badge' => 'Deploy',
        'color' => '#C17B4E',
        'description' => 'Strategie de tests (unitaires, integration, fonctionnels, securite, perf, compat), deploiement Hostinger (stack reelle, structure, CI/CD), generation APK signe et publication Play Store.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Strategie de tests et deploiement en production', 'num' => 1, 'lesson' => '
            <p>Le chapitre 3 du memoire detaille la <em>strategie de tests</em> (organisation des verifications avant livraison) et le deroulement du <em>deploiement</em> (mise en production sur un serveur accessible publiquement).</p>
            <p><strong>6 types de tests appliques :</strong> <em>tests unitaires</em> (verification de chaque fonction isolement), <em>tests d\'integration</em> (interactions entre modules), <em>tests fonctionnels</em> (parcours utilisateur complets), <em>tests de securite</em> (injection SQL, acces non autorises), <em>tests de performance</em> (temps de reponse en 3G), <em>tests de compatibilite</em> (Samsung A03, Tecno Spark 10, Infinix Hot 12).</p>
            <p><strong>Deploiement :</strong> Hostinger shared hosting + <em>CI/CD</em> (Continuous Integration / Continuous Deployment : automatisation des deploiements) via <em>GitHub Actions</em> (workflow declenche sur push vers main, SCP password vers Hostinger port 65002). Generation de l\'<em>APK</em> (Android Package Kit : fichier d\'installation Android) signe par <em>keytool</em> et de l\'<em>AAB</em> (Android App Bundle : format optimise pour le Play Store).</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Combien de types de tests sont appliques selon le memoire ?", 'options'=>['3', '6 (unitaires, integration, fonctionnels, securite, performance, compatibilite)', '10', '2'], 'answer'=>1, 'explanation'=>"<strong>6 types</strong>. Tests unitaires (12 cas), integration (8), fonctionnels (15), securite (6), performance (3), compatibilite (3 appareils)."],
        ['chapter'=>0, 'question'=>"Quel temps de reponse moyen vise les tests de performance en 3G ?", 'options'=>['<= 1 s', '<= 1.8 s mesure (cible 3G)', '<= 5 s', '<= 30 s'], 'answer'=>1, 'explanation'=>"Le tableau indique <strong>temps moyen 1.8s en 3G</strong>, dans la cible 3 secondes."],
        ['chapter'=>0, 'question'=>"Sur quels 3 appareils Android les tests de compat ont-ils ete faits ?", 'options'=>['Samsung S22, Pixel 7, iPhone', 'Samsung Galaxy A03 core, Tecno Spark 10, Infinix Hot 12', 'OnePlus, Xiaomi, Huawei', 'Tous les Android'], 'answer'=>1, 'explanation'=>"<strong>Samsung A03 core (Android 13), Tecno Spark 10 (Android 13), Infinix Hot 12 (Android 12)</strong>. Choix volontaire d'appareils d'entree de gamme pour cibler la realite beninoise."],
        ['chapter'=>0, 'question'=>"Quel hebergeur Laravel est utilise en production ?", 'options'=>['AWS EC2', 'Hostinger shared hosting (avec contraintes)', 'Heroku', 'Render'], 'answer'=>1, 'explanation'=>"<strong>Hostinger shared hosting</strong>. Choix cheap pour le MVP. Limitations : pas de WebSockets, pas de queue workers, exec() desactive."],
        ['chapter'=>0, 'question'=>"Quel mailer transactionnel est utilise ?", 'options'=>['SendGrid', 'Brevo SMTP', 'Mailgun', 'AWS SES'], 'answer'=>1, 'explanation'=>"<strong>Brevo SMTP</strong>. Limite gratuite suffisante pour l'usage actuel."],
        ['chapter'=>0, 'question'=>"Quel mecanisme CI/CD declenche le deploiement ?", 'options'=>['Push manuel', 'GitHub Actions on push to main : .github/workflows/deploy.yml (SCP password vers Hostinger port 65002)', 'CircleCI', 'Bitbucket Pipeline'], 'answer'=>1, 'explanation'=>"<strong>GitHub Actions</strong> sur push vers main. SCP password auth vers Hostinger (port 65002)."],
        ['chapter'=>0, 'question'=>"Pourquoi PHP 8.2 et pas PHP 8.3 ?", 'options'=>['Pas de raison', 'Compatibilite avec Hostinger et kreait/firebase-php ^7.16', 'Bug PHP 8.3', 'Disponibilite'], 'answer'=>1, 'explanation'=>"PHP 8.2 est la version stable Hostinger compatible avec kreait/firebase-php ^7.16. La 8.3+ exigerait kreait 8.x."],
        ['chapter'=>0, 'question'=>"Comment l'APK release est-il transfere sur Hostinger ?", 'options'=>['FTP', 'SCP vers public_html/downloads/fegartisan.apk', 'Upload via cPanel', 'Git'], 'answer'=>1, 'explanation'=>"SCP : <code>scp -P 65002 build/app/outputs/flutter-apk/app-release.apk user@host:/home/.../downloads/fegartisan.apk</code>. URL publique : APK_DOWNLOAD_URL dans .env."],
        ['chapter'=>0, 'question'=>"Pour signer un APK release, quelle commande genere le keystore ?", 'options'=>['flutter create-key', 'keytool -genkey -v -keystore android/app/fegartisan-release-key.jks ...', 'apksigner sign', 'gradle key'], 'answer'=>1, 'explanation'=>"<code>keytool -genkey -v -keystore android/app/fegartisan-release-key.jks -alias fegartisan -keyalg RSA -keysize 2048 -validity 10000</code>. Ne JAMAIS commiter le .jks."],
        ['chapter'=>0, 'question'=>"Format optimal pour Play Store ?", 'options'=>['APK', 'AAB (Android App Bundle, plus petit)', 'IPA', 'ZIP'], 'answer'=>1, 'explanation'=>"<strong>AAB</strong> (build via <code>flutter build appbundle --release</code>). Plus petit, optimise par device."],
        ['chapter'=>0, 'question'=>"Combien d'enquete/sondage cite le memoire dans la phase d'analyse ?", 'options'=>['0', '1 sondage (71 reponses)', '3', '10+'], 'answer'=>1, 'explanation'=>"<strong>1 sondage</strong> de 71 reponses (50 clients + 21 artisans), du 5 au 7 mai 2026."],
        ['chapter'=>0, 'question'=>"Quelle migration TiDB a ete effectuee (memoire) ?", 'options'=>['Aucune', 'us-east => eu-central (Frankfurt) en mai 2026 : dump donnees, switch .env, migrate sur nouveau cluster, import dump avec FK checks=0', 'NoSQL', 'PostgreSQL'], 'answer'=>1, 'explanation'=>"Migration <strong>us-east => eu-central</strong> en mai 2026 pour latence. Dump donnees seules (mysqldump --no-create-info), basculer .env, migrate sur nouveau (cree le schema propre), importer dump avec FK checks=0."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
