{{--
  Moteur partage des QCM FeGArtisan.
  Variables attendues :
    $config       : metadonnees du QCM (qcm_key, title, subtitle, badge, color, description, is_exam, duration)
    $chapters     : liste des chapitres (title, num, lesson HTML)
    $allQuestions : liste des questions (chapter, question, options[], answer, explanation)
    $mode         : 'quiz' (defaut), 'fiche' (toutes lecons enchainees), 'flashcards' (cartes aleatoires)
--}}
@php $mode = $mode ?? 'quiz'; @endphp

@if($mode === 'fiche')

{{-- ========== MODE FICHE DE REVISION ========== --}}
<div class="container fiche-mode">
    <div class="fiche-header">
        <div class="fiche-badge" style="background:{{ $config['color'] }}">{{ $config['badge'] }}</div>
        <h1>{{ $config['title'] }} <small>&mdash; Fiche de revision</small></h1>
        <p class="subtitle">{{ count($chapters) }} chapitres . {{ count($allQuestions) }} questions resumees ci-dessous</p>
        <div class="fiche-actions">
            <a href="/quiz/{{ basename(request()->path()) }}" class="btn btn-restart">Passer au QCM</a>
            <button class="btn btn-primary" onclick="window.print()">Imprimer / PDF</button>
            <a href="/parcours" class="btn btn-restart">Retour parcours</a>
        </div>
    </div>

    @foreach($chapters as $i => $ch)
        <article class="fiche-chapter">
            <header>
                <div class="chapter-num">Chapitre {{ $ch['num'] }} sur {{ count($chapters) }}</div>
                <h2>{{ $ch['title'] }}</h2>
            </header>
            <div class="lesson-card">{!! $ch['lesson'] !!}</div>

            @php
                $chapterQs = array_values(array_filter($allQuestions, fn($q) => ($q['chapter'] ?? 0) === $i));
            @endphp
            @if(count($chapterQs))
                <details class="fiche-questions">
                    <summary>Voir les {{ count($chapterQs) }} questions et reponses de ce chapitre</summary>
                    <ol>
                        @foreach($chapterQs as $q)
                            <li>
                                <div class="fq-question">{!! $q['question'] !!}</div>
                                <div class="fq-answer"><strong>Reponse :</strong> {!! $q['options'][$q['answer']] !!}</div>
                                @if(!empty($q['explanation']))
                                    <div class="fq-explanation">{!! $q['explanation'] !!}</div>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </details>
            @endif
        </article>
    @endforeach

    <div class="fiche-footer">
        <a href="/quiz/{{ basename(request()->path()) }}" class="btn btn-primary">Passer au QCM maintenant &rarr;</a>
    </div>
</div>

<style>
.fiche-mode { max-width:900px; }
.fiche-header { background:var(--bg-card); border-radius:14px; padding:24px; margin-bottom:20px; border:1px solid var(--border-subtle); text-align:center; }
.fiche-badge { display:inline-block; padding:6px 18px; border-radius:20px; color:#fff; font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:12px; }
.fiche-header h1 { color:{{ $config['color'] }}; font-size:24px; margin-bottom:4px; }
.fiche-header h1 small { color:var(--text-muted); font-size:14px; font-weight:normal; display:block; margin-top:4px; }
.fiche-header .subtitle { color:var(--text-muted); font-size:13px; margin-bottom:16px; }
.fiche-actions { display:flex; justify-content:center; gap:10px; flex-wrap:wrap; }
.fiche-chapter { margin-bottom:28px; }
.fiche-chapter header { margin-bottom:12px; padding-left:6px; border-left:4px solid {{ $config['color'] }}; }
.fiche-chapter .chapter-num { color:{{ $config['color'] }}; font-size:11px; text-transform:uppercase; letter-spacing:2px; font-weight:700; }
.fiche-chapter h2 { font-size:20px; color:var(--text-main); margin-top:2px; }
.fiche-questions { background:var(--bg-card); border:1px solid var(--border-subtle); border-radius:10px; padding:16px; margin-top:12px; }
.fiche-questions summary { cursor:pointer; font-weight:700; color:{{ $config['color'] }}; font-size:14px; padding:4px 0; }
.fiche-questions ol { padding-left:22px; margin-top:14px; }
.fiche-questions li { margin-bottom:14px; padding-bottom:12px; border-bottom:1px dashed var(--border-subtle); }
.fiche-questions li:last-child { border-bottom:none; }
.fq-question { font-size:14px; color:var(--text-main); margin-bottom:6px; }
.fq-answer { font-size:13.5px; color:#4A7C59; margin-bottom:4px; }
.fq-explanation { font-size:13px; color:var(--text-muted); font-style:italic; }
.fiche-footer { text-align:center; padding:28px 0 12px; }
@media print {
    body { background:#fff !important; color:#000 !important; }
    .topbar, .sidebar, .sidebar-overlay, .global-progress, .fiche-actions, .fiche-footer { display:none !important; }
    .main-content { margin:0 !important; padding:0 !important; max-width:100% !important; }
    .fiche-header { box-shadow:none; border:1px solid #ccc; }
    .fiche-questions[open] summary { display:block; }
    .fiche-questions { page-break-inside:avoid; }
    .fiche-chapter { page-break-inside:avoid; }
}
</style>

@elseif($mode === 'flashcards')

{{-- ========== MODE FLASHCARDS ========== --}}
<div class="container fc-container">
    <div class="fc-header">
        <div class="fiche-badge" style="background:{{ $config['color'] }}">{{ $config['badge'] }}</div>
        <h1>{{ $config['title'] }} <small>&mdash; Flashcards</small></h1>
        <p class="subtitle">Memorisation active . Lisez, repondez mentalement, retournez</p>
    </div>

    <div class="fc-counter" id="fc-counter">Carte 1 / <span id="fc-total"></span></div>

    <div class="fc-card" id="fc-card">
        <div class="fc-side fc-front">
            <div class="fc-label">QUESTION</div>
            <div class="fc-content" id="fc-question"></div>
            <button class="btn btn-primary fc-reveal" onclick="flipCard()">Voir la reponse</button>
        </div>
        <div class="fc-side fc-back" style="display:none">
            <div class="fc-label fc-label-answer">REPONSE</div>
            <div class="fc-content fc-answer" id="fc-answer"></div>
            <div class="fc-content fc-explanation" id="fc-explanation"></div>
            <div class="fc-self-eval">
                <button class="btn btn-fail" onclick="markAndNext(false)">Je n\'ai pas su</button>
                <button class="btn btn-ok" onclick="markAndNext(true)">J\'ai su</button>
            </div>
        </div>
    </div>

    <div class="fc-actions">
        <button class="btn btn-restart" onclick="prevCard()">&larr; Precedente</button>
        <button class="btn btn-restart" onclick="shuffleCards()">Melanger</button>
        <button class="btn btn-restart" onclick="nextCard()">Suivante &rarr;</button>
    </div>

    <div class="fc-stats" id="fc-stats">
        <div><strong id="fc-known">0</strong><span>Sues</span></div>
        <div><strong id="fc-unknown">0</strong><span>Ratees</span></div>
        <div><strong id="fc-progress">0%</strong><span>Avancement</span></div>
    </div>

    <div style="text-align:center; margin-top:24px;">
        <a href="/quiz/{{ basename(request()->path()) }}" class="btn btn-primary">Passer au QCM officiel</a>
        <a href="/parcours" class="btn btn-restart">Retour parcours</a>
    </div>
</div>

<style>
.fc-container { max-width:760px; }
.fc-header { text-align:center; margin-bottom:18px; }
.fc-header h1 { color:{{ $config['color'] }}; font-size:24px; }
.fc-header h1 small { color:var(--text-muted); font-size:13px; font-weight:normal; display:block; margin-top:4px; }
.fc-counter { text-align:center; font-size:13px; color:var(--text-muted); margin-bottom:14px; font-weight:600; letter-spacing:1px; }
.fc-card { background:var(--bg-card); border:2px solid {{ $config['color'] }}33; border-radius:16px; padding:36px 28px; min-height:340px; display:flex; flex-direction:column; justify-content:center; box-shadow:0 8px 24px rgba(107,45,14,0.08); margin-bottom:20px; }
.fc-side { display:flex; flex-direction:column; align-items:center; text-align:center; gap:16px; }
.fc-label { font-size:11px; letter-spacing:3px; color:var(--text-muted); font-weight:800; }
.fc-label-answer { color:#4A7C59; }
.fc-content { font-size:17px; line-height:1.7; color:var(--text-main); }
.fc-answer { color:{{ $config['color'] }}; font-weight:700; font-size:18px; }
.fc-explanation { color:var(--text-muted); font-size:14px; font-style:italic; max-width:600px; margin:0 auto; }
.fc-reveal { margin-top:14px; }
.fc-self-eval { display:flex; gap:14px; margin-top:14px; flex-wrap:wrap; justify-content:center; }
.btn-ok { background:#4A7C59; color:#fff; padding:11px 22px; border:none; border-radius:8px; font-weight:700; cursor:pointer; }
.btn-fail { background:#C94A3A; color:#fff; padding:11px 22px; border:none; border-radius:8px; font-weight:700; cursor:pointer; }
.fc-actions { display:flex; justify-content:center; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
.fc-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; background:var(--bg-card); border-radius:12px; padding:16px; border:1px solid var(--border-subtle); }
.fc-stats > div { text-align:center; }
.fc-stats strong { display:block; font-size:24px; color:{{ $config['color'] }}; font-weight:800; }
.fc-stats span { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; }
@media (max-width:600px) { .fc-card { padding:24px 18px; min-height:280px; } .fc-content { font-size:15px; } .fc-answer { font-size:16px; } }
</style>

<script>
const FC_QUESTIONS = @json($allQuestions);
let fcDeck = [...FC_QUESTIONS];
let fcIdx = 0;
let fcKnown = 0, fcUnknown = 0;
let fcSeen = new Set();

function fcShuffleInPlace(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
}

function renderCard() {
    if (fcDeck.length === 0) return;
    const q = fcDeck[fcIdx];
    document.getElementById('fc-question').innerHTML = q.question;
    document.getElementById('fc-answer').innerHTML = q.options[q.answer];
    document.getElementById('fc-explanation').innerHTML = q.explanation || '';
    document.querySelector('.fc-front').style.display = 'flex';
    document.querySelector('.fc-back').style.display = 'none';
    document.getElementById('fc-counter').innerHTML = 'Carte ' + (fcIdx + 1) + ' / <span>' + fcDeck.length + '</span>';
}

function flipCard() {
    document.querySelector('.fc-front').style.display = 'none';
    document.querySelector('.fc-back').style.display = 'flex';
}

function markAndNext(known) {
    const q = fcDeck[fcIdx];
    const key = q.question;
    if (!fcSeen.has(key)) {
        fcSeen.add(key);
        if (known) fcKnown++; else fcUnknown++;
    }
    updateStats();
    nextCard();
}

function nextCard() {
    fcIdx = (fcIdx + 1) % fcDeck.length;
    renderCard();
}
function prevCard() {
    fcIdx = (fcIdx - 1 + fcDeck.length) % fcDeck.length;
    renderCard();
}
function shuffleCards() {
    fcShuffleInPlace(fcDeck);
    fcIdx = 0;
    renderCard();
}
function updateStats() {
    document.getElementById('fc-known').textContent = fcKnown;
    document.getElementById('fc-unknown').textContent = fcUnknown;
    const pct = fcSeen.size === 0 ? 0 : Math.round((fcKnown / fcSeen.size) * 100);
    document.getElementById('fc-progress').textContent = pct + '%';
}
document.getElementById('fc-total').textContent = fcDeck.length;
fcShuffleInPlace(fcDeck);
renderCard();
</script>

@else

{{-- ========== MODE QUIZ STANDARD ========== --}}
<div class="container">
    <h1>{{ $config['title'] }}</h1>
    <p class="subtitle">{{ $config['subtitle'] }}</p>

    <div id="start-screen" class="start-screen">
        <div class="js-logo">{{ $config['badge'] }}</div>
        <p>{!! $config['description'] !!}</p>

        @if(!($config['is_exam'] ?? false))
        <div class="roadmap">
            @foreach($chapters as $i => $ch)
                <div class="step" onclick="startQuiz({{ $i }},0,0)"><span class="dot">{{ $i+1 }}</span> {{ $ch['title'] }}</div>
            @endforeach
        </div>
        @endif

        <div class="btn-container" style="margin-top:30px">
            <button class="btn btn-primary" onclick="startQuiz()">Commencer</button>
            @php $slug = basename(request()->path()); @endphp
            @if($slug !== 'exam' && $slug !== 'oral')
            <a href="/fiche/{{ $slug }}" class="btn btn-restart">Fiche de revision</a>
            <a href="/flashcards/{{ $slug }}" class="btn btn-restart">Flashcards</a>
            @endif
        </div>
        <div id="resume-banner" style="display:none; margin-top:20px; background:var(--bg-card); border:2px solid {{ $config['color'] }}; border-radius:12px; padding:20px; text-align:center;">
            <p style="margin-bottom:12px; font-size:16px;">Vous avez une progression en cours : <strong id="resume-info"></strong></p>
            <button class="btn btn-primary" id="btn-resume">Continuer ou j'en etais</button>
            <button class="btn btn-restart" onclick="startQuiz()" style="margin-left:10px">Recommencer</button>
        </div>
    </div>

    <div id="quiz-area" style="display:none">
        <div class="progress-text" id="progress-text"></div>
        <div class="progress-bar"><div class="progress-fill" id="progress-fill"></div></div>
        <div class="timer" id="timer">00:00</div>
        <div id="content-area"></div>
        <div class="btn-container">
            <button class="btn btn-primary" id="btn-validate" onclick="validateAnswer()" disabled style="display:none">Valider</button>
            <button class="btn btn-primary" id="btn-next" onclick="next()" style="display:none">Suivant</button>
            <button class="btn btn-primary" id="btn-start-chapter" onclick="startChapter()" style="display:none">Commencer les questions</button>
        </div>
    </div>

    <div id="results" class="results"></div>
</div>

<script>
const QCM_CONFIG = @json($config);
const chapters = @json($chapters);
const allQuestions = @json($allQuestions);

let currentChapter = 0;
let currentQInChapter = 0;
let chapterQuestions = [];
let chapterScore = 0;
let chapterTotal = 0;
let globalQIndex = 0;
let score = 0;
let answers = [];
let state = 'start';
let selectedOption = -1;
let answered = false;
let timerSeconds = 0;
let timerInterval = null;
let examDeadline = null;

function getChapterQuestions(chIdx) {
    return allQuestions.filter(q => q.chapter === chIdx);
}

function startTimer() {
    timerSeconds = 0;
    const t = document.getElementById('timer');
    if (QCM_CONFIG.is_exam) {
        examDeadline = Date.now() + (QCM_CONFIG.duration * 1000);
    }
    timerInterval = setInterval(() => {
        if (QCM_CONFIG.is_exam) {
            const remain = Math.max(0, Math.round((examDeadline - Date.now()) / 1000));
            const m = String(Math.floor(remain / 60)).padStart(2, '0');
            const s = String(remain % 60).padStart(2, '0');
            t.textContent = m + ':' + s;
            timerSeconds = QCM_CONFIG.duration - remain;
            if (remain <= 0) { stopTimer(); showResults(); }
        } else {
            timerSeconds++;
            const m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
            const s = String(timerSeconds % 60).padStart(2, '0');
            t.textContent = m + ':' + s;
        }
    }, 1000);
}
function stopTimer() { if (timerInterval) clearInterval(timerInterval); timerInterval = null; }

function updateProgress() {
    const total = allQuestions.length;
    let pct;
    if (state === 'lesson') pct = (globalQIndex / total) * 100;
    else pct = ((globalQIndex + currentQInChapter) / total) * 100;
    document.getElementById('progress-fill').style.width = pct + '%';
    if (QCM_CONFIG.is_exam) {
        document.getElementById('progress-text').textContent = 'Question ' + (globalQIndex + 1) + ' / ' + total;
    } else {
        document.getElementById('progress-text').textContent = 'Chapitre ' + (currentChapter + 1) + ' / ' + chapters.length + ' . Question ' + Math.min(currentQInChapter + 1, chapterTotal || 1) + ' / ' + (chapterTotal || '...');
    }
}

function startQuiz(startCh, startScore, startGlobal) {
    document.getElementById('start-screen').style.display = 'none';
    document.getElementById('quiz-area').style.display = 'block';
    document.getElementById('results').style.display = 'none';
    currentChapter = startCh || 0;
    score = startScore || 0;
    globalQIndex = startGlobal || 0;
    answers = [];
    startTimer();
    if (QCM_CONFIG.is_exam) {
        chapterQuestions = [...allQuestions].sort(() => Math.random() - 0.5);
        chapterTotal = chapterQuestions.length;
        currentQInChapter = 0;
        showQuestion();
    } else {
        showLesson();
    }
}

function hideAllButtons() {
    document.getElementById('btn-validate').style.display = 'none';
    document.getElementById('btn-next').style.display = 'none';
    document.getElementById('btn-start-chapter').style.display = 'none';
}

function showLesson() {
    state = 'lesson';
    chapterScore = 0;
    chapterQuestions = getChapterQuestions(currentChapter);
    chapterTotal = chapterQuestions.length;
    currentQInChapter = 0;
    updateProgress();
    const ch = chapters[currentChapter];
    document.getElementById('content-area').innerHTML = `
        <div class="lesson-card">
            <div class="chapter-num">Chapitre ${ch.num} sur ${chapters.length}</div>
            <h2>${ch.title}</h2>
            ${ch.lesson}
        </div>`;
    hideAllButtons();
    document.getElementById('btn-start-chapter').style.display = 'inline-block';
}

function startChapter() { showQuestion(); }

function showQuestion() {
    state = 'question';
    selectedOption = -1;
    answered = false;
    updateProgress();
    const q = chapterQuestions[currentQInChapter];
    const badgeLabel = QCM_CONFIG.is_exam ? 'Examen' : chapters[currentChapter].title;
    let html = '<div class="question-card">';
    html += '<span class="category-badge">' + badgeLabel + '</span>';
    html += '<div class="question-text">' + q.question + '</div>';
    html += '<ul class="options">';
    q.options.forEach((opt, i) => { html += `<li onclick="selectOption(${i})" id="opt-${i}">${opt}</li>`; });
    html += '</ul>';
    html += '<div class="explanation" id="explanation">' + (q.explanation || '') + '</div>';
    html += '</div>';
    document.getElementById('content-area').innerHTML = html;
    hideAllButtons();
    document.getElementById('btn-validate').style.display = 'inline-block';
    document.getElementById('btn-validate').disabled = true;
}

function selectOption(i) {
    if (answered) return;
    selectedOption = i;
    document.getElementById('btn-validate').disabled = false;
    document.querySelectorAll('.options li').forEach((el, idx) => { el.classList.toggle('selected', idx === i); });
}

function validateAnswer() {
    if (selectedOption === -1 || answered) return;
    answered = true;
    const q = chapterQuestions[currentQInChapter];
    const isCorrect = selectedOption === q.answer;
    if (isCorrect) { score++; chapterScore++; }
    answers.push({ question: q, chapter: QCM_CONFIG.is_exam ? 0 : currentChapter, selected: selectedOption, correct: isCorrect });
    globalQIndex++;
    document.querySelectorAll('.options li').forEach((el, idx) => {
        el.classList.add('disabled');
        if (idx === q.answer) el.classList.add('correct');
        if (idx === selectedOption && !isCorrect) el.classList.add('wrong');
    });
    if (q.explanation) document.getElementById('explanation').style.display = 'block';
    hideAllButtons();
    document.getElementById('btn-next').style.display = 'inline-block';
}

function next() {
    currentQInChapter++;
    if (currentQInChapter >= chapterTotal) {
        if (QCM_CONFIG.is_exam) { showResults(); return; }
        if (currentChapter < chapters.length - 1) showPause();
        else showResults();
    } else {
        showQuestion();
    }
}

function showPause() {
    state = 'pause';
    updateProgress();
    const pct = Math.round(chapterScore / chapterTotal * 100);
    let color = '#C94A3A', emoji = 'Relisez la lecon.';
    if (pct >= 80) { color = '#4A7C59'; emoji = 'Excellent !'; }
    else if (pct >= 60) { color = '#0468D7'; emoji = 'Bien !'; }
    else if (pct >= 40) { color = '#E8A020'; emoji = 'A revoir.'; }
    const nextCh = chapters[currentChapter + 1];
    document.getElementById('content-area').innerHTML = `
        <div class="lesson-card">
            <div class="chapter-num">Fin du chapitre ${currentChapter + 1}</div>
            <h2>Pause &mdash; Bilan</h2>
            <div class="chapter-score">
                <div class="score-box"><div class="num" style="color:${color}">${chapterScore}/${chapterTotal}</div><div class="lbl">Bonnes reponses</div></div>
                <div class="score-box"><div class="num" style="color:${color}">${pct}%</div><div class="lbl">${emoji}</div></div>
            </div>
            <p style="text-align:center;margin-top:20px">Prochain : <strong>${nextCh.title}</strong></p>
            <div style="display:flex;justify-content:center;gap:14px;margin-top:28px;flex-wrap:wrap">
                <button class="btn btn-primary" id="btn-continue-chapter">Chapitre suivant</button>
                <button class="btn btn-restart" id="btn-stop-here">Arreter ici</button>
            </div>
        </div>`;
    hideAllButtons();
    document.getElementById('btn-continue-chapter').onclick = function() { currentChapter++; showLesson(); };
    document.getElementById('btn-stop-here').onclick = function() {
        fetch('/api/progress', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify({
                qcm_name: QCM_CONFIG.qcm_key,
                chapter_completed: currentChapter + 1,
                total_chapters: chapters.length,
                score_so_far: score,
                total_so_far: globalQIndex
            })
        }).then(() => { window.location.href = '/dashboard'; });
    };
}

function showResults() {
    stopTimer();
    document.getElementById('quiz-area').style.display = 'none';
    const resultsDiv = document.getElementById('results');
    resultsDiv.style.display = 'block';
    const total = allQuestions.length;
    const pct = Math.round(score / total * 100);

    let levelClass, message, detail;
    if (pct >= 80) {
        levelClass = 'level-excellent';
        message = 'Excellent ! Vous maitrisez ce theme.';
        detail = 'Vous etes pret pour la soutenance sur ce sujet. Pensez aussi a tester l\'examen final transverse.';
    } else if (pct >= 60) {
        levelClass = 'level-good';
        message = 'Bon travail ! Les bases sont la.';
        detail = 'Relisez les chapitres ou vous avez eu des difficultes puis recommencez.';
    } else if (pct >= 40) {
        levelClass = 'level-average';
        message = 'C\'est un debut. Continuez !';
        detail = 'Reprenez les lecons en detail. Bien comprendre le pourquoi avant de retenir le comment.';
    } else {
        levelClass = 'level-weak';
        message = 'Restez motive !';
        detail = 'Relisez la documentation FeGArtisan attentivement et relancez le QCM.';
    }

    let catHtml = '';
    if (!QCM_CONFIG.is_exam) {
        catHtml = '<div class="cat-scores">';
        chapters.forEach((ch, idx) => {
            const chAnswers = answers.filter(a => a.chapter === idx);
            const chCorrect = chAnswers.filter(a => a.correct).length;
            const chT = chAnswers.length;
            const p = chT > 0 ? Math.round(chCorrect / chT * 100) : 0;
            let c = '#C94A3A';
            if (p >= 80) c = '#4A7C59'; else if (p >= 60) c = '#0468D7'; else if (p >= 40) c = '#E8A020';
            catHtml += `<div class="cat-score-card"><div class="cat-name">Ch.${idx+1} ${ch.title.split(':')[0]}</div><div class="cat-pct" style="color:${c}">${p}%</div><div class="cat-detail">${chCorrect}/${chT}</div></div>`;
        });
        catHtml += '</div>';
    }

    resultsDiv.innerHTML = `
        <div class="score-circle ${levelClass}">${pct}%<span class="label">${score}/${total}</span></div>
        <div class="level-message">${message}</div>
        <div class="level-detail">${detail}</div>
        ${catHtml}
        <div class="btn-container" style="margin-top:30px">
            <button class="btn btn-primary" onclick="startQuiz()">Recommencer</button>
            <button class="btn btn-restart" onclick="retryFailed()" style="margin-left:10px">Retravailler mes erreurs</button>
            <button class="btn btn-restart" onclick="location.href='/dashboard'" style="margin-left:10px">Accueil</button>
        </div>`;

    fetch('/api/progress', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({ qcm_name: QCM_CONFIG.qcm_key, chapter_completed: -1, total_chapters: chapters.length })
    });

    fetch('/api/scores', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({ qcm_name: QCM_CONFIG.qcm_key, score: score, total: total, percentage: pct, duration_seconds: timerSeconds })
    });
}

function retryFailed() {
    const failed = answers.filter(a => !a.correct);
    if (failed.length === 0) { alert('Aucune erreur !'); return; }
    allQuestions.length = 0;
    failed.forEach(f => { const q = Object.assign({}, f.question); q.chapter = 0; allQuestions.push(q); });
    chapters.length = 0;
    chapters.push({title: 'Revision des erreurs', num: 1, lesson: '<p>Vous allez revoir les <strong>' + failed.length + ' questions</strong> ratees.</p>'});
    startQuiz();
}

fetch('/api/progress/' + QCM_CONFIG.qcm_key)
    .then(r => r.json())
    .then(data => {
        if (data.found && data.chapter_completed < data.total_chapters) {
            document.getElementById('resume-banner').style.display = 'block';
            document.getElementById('resume-info').textContent = 'Chapitre ' + data.chapter_completed + ' / ' + data.total_chapters + ' (' + data.score_so_far + '/' + data.total_so_far + ' bonnes)';
            document.getElementById('btn-resume').onclick = function() {
                startQuiz(data.chapter_completed, data.score_so_far, data.total_so_far);
            };
        }
    });
</script>

@endif
