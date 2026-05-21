@php
    $score = $userScores[$qcmName] ?? null;
    $hasProgress = isset($userProgress[$qcmName]) && ($userProgress[$qcmName]->chapter_completed ?? 0) > 0;
@endphp
@if($score)
    @php
        $best = (float)$score->best;
        $attempts = (int)$score->attempts;
        if ($best >= 80) $cls = 'score-green';
        elseif ($best >= 60) $cls = 'score-blue';
        elseif ($best >= 40) $cls = 'score-orange';
        else $cls = 'score-red';
    @endphp
    <span class="score-badge {{ $cls }}">{{ round($best) }}%</span>
    <span class="score-attempts">{{ $attempts }} tentative{{ $attempts > 1 ? 's' : '' }}</span>
@elseif(!$hasProgress)
    <span class="score-badge score-none">Pas encore commence</span>
@endif
