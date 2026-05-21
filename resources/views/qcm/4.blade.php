@extends('layouts.app')
@section('title', 'Epreuve 4 : Riverpod & state Flutter')

@php
    $config = [
        'qcm_key' => 'fega-4',
        'title' => 'Epreuve 4 : Riverpod & state management',
        'subtitle' => '16 questions . Avance . Providers, Notifiers, invalidation',
        'badge' => 'State',
        'color' => '#0468D7',
        'description' => 'QCM avance sur les types de providers Riverpod, les controllers Notifier vs AsyncNotifier, les invalidations et l\'optimistic update.',
        'is_exam' => false,
    ];

    $chapters = [
        ['title' => 'Riverpod en profondeur', 'num' => 1, 'lesson' => '
            <p><em>Riverpod</em> 2.x est la librairie de <strong>state management</strong> (gestion centralisee de l\'etat applicatif) exclusive de FeGArtisan cote Flutter. Le code applique strictement le <em>pattern</em> (modele d\'architecture) en cascade :</p>
            <p><strong>Provider</strong> (singleton, ex. ApiClient) =&gt; <strong>Repository</strong> (couche d\'acces aux donnees) =&gt; <strong>Notifier</strong> (gestionnaire d\'etat reactif) =&gt; <strong>UI</strong> (widget qui ecoute).</p>
            <p>Aucun ecran ne touche directement la librairie HTTP <code>Dio</code> : tout passe par cette chaine d\'abstraction qui rend chaque couche testable et remplacable.</p>
        '],
    ];

    $allQuestions = [
        ['chapter'=>0, 'question'=>"Quel widget enveloppe l'app pour activer Riverpod ?", 'options'=>['MaterialApp', 'ProviderScope', 'Riverpod()', 'StateScope'], 'answer'=>1, 'explanation'=>"<code>runApp(ProviderScope(child: const FeGArtisanApp()))</code>."],
        ['chapter'=>0, 'question'=>"Quel type Notifier est synchrone ?", 'options'=>['AsyncNotifier', 'Notifier', 'StreamNotifier', 'StateNotifier'], 'answer'=>1, 'explanation'=>"<code>Notifier&lt;T&gt;</code> est synchrone (build retourne T). <code>AsyncNotifier&lt;T&gt;</code> est asynchrone (build retourne FutureOr&lt;T&gt;)."],
        ['chapter'=>0, 'question'=>"Quelle methode invalide un provider precis ?", 'options'=>['ref.reset(provider)', 'ref.invalidate(provider)', 'ref.refresh(provider)', 'ref.dispose(provider)'], 'answer'=>1, 'explanation'=>"<code>ref.invalidate(provider)</code>. Cela force un re-build au prochain read."],
        ['chapter'=>0, 'question'=>"Difference entre invalidate et refresh ?", 'options'=>['Aucune', 'invalidate marque dirty (rebuild a la prochaine lecture) ; refresh force immediatement', 'refresh est obsolete', 'invalidate ne marche que sur AsyncNotifier'], 'answer'=>1, 'explanation'=>"<code>invalidate</code> marque dirty et le provider rebuild a la prochaine lecture. <code>refresh</code> force le rebuild immediatement et renvoie la nouvelle valeur."],
        ['chapter'=>0, 'question'=>"Quel pattern pour un provider parametre par ID ?", 'options'=>['Provider.id(...)', 'Provider.family((ref, id) => ...)', 'ParamProvider', 'IdProvider'], 'answer'=>1, 'explanation'=>"<code>FutureProvider.family&lt;Artisan, int&gt;((ref, id) async =&gt; ...)</code>. <code>family</code> permet le parametre dynamique."],
        ['chapter'=>0, 'question'=>"Quel modifier libere la memoire quand plus d'ecouteur ?", 'options'=>['autoDispose', 'autoFree', 'keepAlive(false)', 'ref.close()'], 'answer'=>0, 'explanation'=>"<code>FutureProvider.autoDispose</code>. Combine avec <code>family</code> = parfait pour fetch HTTP par ID."],
        ['chapter'=>0, 'question'=>"Comment un Widget lit-il l'etat d'un provider ?", 'options'=>['Provider.of(context)', 'ref.watch(provider)', 'context.read()', 'subscribe()'], 'answer'=>1, 'explanation'=>"<code>final state = ref.watch(authControllerProvider)</code>. Le widget reagit aux changements."],
        ['chapter'=>0, 'question'=>"Comment lire SANS reactivite (pour un onTap) ?", 'options'=>['ref.watch(provider)', 'ref.read(provider)', 'ref.listen(...)', 'aucun'], 'answer'=>1, 'explanation'=>"<code>ref.read(provider)</code>. Pour les actions ponctuelles (onPressed)."],
        ['chapter'=>0, 'question'=>"Pourquoi NE PAS invalider publicationsProvider apres un like ?", 'options'=>['Trop lent', "Detruit AnimationController du LikeBtn = scintillement", "Riverpod l'interdit", "Aucune raison"], 'answer'=>1, 'explanation'=>"Invalider detruit le widget et son anim. On utilise <strong>override local</strong> via <code>_likeOverrides[id] = updatedPub</code>."],
        ['chapter'=>0, 'question'=>"Quel pattern critique applique AuthController a chaque login/logout ?", 'options'=>['Refresh local', '_resetUserScopedState() invalide tous les providers dependants du user', 'Reload de l\'app', 'rien'], 'answer'=>1, 'explanation'=>"Critique pour la securite : invalide myPublications, dashboard, conversations, notifs, favoris, privacy. Empeche un nouveau user de voir le cache du precedent."],
        ['chapter'=>0, 'question'=>"AuthState peut prendre quels 3 etats principaux ?", 'options'=>['Loading, Success, Error', 'AuthUnknown, AuthUnauthenticated, AuthAuthenticated(user)', 'On, Off, Pending', 'Open, Closed, Locked'], 'answer'=>1, 'explanation'=>"AuthUnknown (init), AuthUnauthenticated (pas de token valide), AuthAuthenticated(user)."],
        ['chapter'=>0, 'question'=>"Pourquoi AuthController etend Notifier et pas AsyncNotifier ?", 'options'=>['Plus rapide', 'Son etat AuthState est synchrone (sealed class) - le _bootstrap() async tourne en background', 'Compatibilite', 'Plus simple'], 'answer'=>1, 'explanation'=>"L'etat AuthState est une sealed class synchrone. Le bootstrap async tourne en arriere-plan et update le state via <code>state = ...</code>."],
        ['chapter'=>0, 'question'=>"Quel type pour DashboardController qui charge des stats au mount ?", 'options'=>['Provider', 'NotifierProvider', 'AsyncNotifierProvider', 'StreamProvider'], 'answer'=>2, 'explanation'=>"<code>AsyncNotifierProvider&lt;DashboardController, DashboardStats&gt;</code>. build() async charge les stats au mount."],
        ['chapter'=>0, 'question'=>"Comment ecouter un changement de provider DANS un autre Notifier (pas un widget) ?", 'options'=>['ref.watch(provider) dans build()', 'ref.listen(provider, (prev, next) => ...) ailleurs', 'subscribe', 'aucun'], 'answer'=>1, 'explanation'=>"<code>ref.listen(provider, (prev, next) =&gt; { ... })</code>. Permet de reagir sans re-build."],
        ['chapter'=>0, 'question'=>"Apres un POST /api/publications, comment refleter cote app ?", 'options'=>['Recharger l\'app', 'state = AsyncData([newPub, ...current.value]) pour optimistic update', 'fetch tout', 'Rien'], 'answer'=>1, 'explanation'=>"Optimistic update : on prepend la nouvelle publication a la liste avant meme la confirmation backend. Si erreur, rollback."],
        ['chapter'=>0, 'question'=>"Quelle methode notifier accepte un getter pour modifier l'etat ?", 'options'=>['this.state = ...', 'state = ...', 'setState(...)', 'emit(...)'], 'answer'=>1, 'explanation'=>"Dans un Notifier, <code>state = newValue</code> (setter direct). Pas de <code>setState</code> ni <code>emit</code>."],
    ];
@endphp

@section('styles')
    @include('qcm._styles', ['config' => $config])
@endsection

@section('content')
    @include('qcm._engine', ['config' => $config, 'chapters' => $chapters, 'allQuestions' => $allQuestions])
@endsection
