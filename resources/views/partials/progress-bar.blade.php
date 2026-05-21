@php
    $p = $userProgress[$qcmName] ?? null;
@endphp
@if($p && (int)$p->chapter_completed > 0 && (int)$p->chapter_completed < (int)$p->total_chapters)
    @php
        $ch = (int)$p->chapter_completed;
        $tot = (int)$p->total_chapters;
        $pct = round($ch / $tot * 100);
    @endphp
    <div class="card-progress">
        <div class="card-progress-label">
            <span>Progression : Chapitre {{ $ch }} / {{ $tot }}</span>
            <span>{{ $pct }}%</span>
        </div>
        <div class="card-progress-bar">
            <div class="card-progress-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
        </div>
        <a href="/quiz/{{ $slug }}" class="btn-continue" style="background:{{ $color }}">Continuer</a>
    </div>
@endif
