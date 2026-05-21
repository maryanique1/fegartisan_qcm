{{-- CSS partage par tous les QCM. Variable attendue : $color --}}
    * { box-sizing:border-box; margin:0; padding:0; }
    .container { overflow-wrap:break-word; max-width:820px; margin:0 auto; padding:30px 20px; }
    h1 { text-align:center; margin-bottom:10px; color:{{ $config['color'] }}; font-size:26px; }
    .subtitle { text-align:center; margin-bottom:30px; color:var(--text-muted); font-size:14px; }

    .progress-bar { background:var(--bg-card); border-radius:20px; height:12px; margin-bottom:10px; overflow:hidden; }
    .progress-fill { height:100%; background:linear-gradient(90deg,{{ $config['color'] }},{{ $config['color'] }}cc); border-radius:20px; transition:width .4s ease; }
    .progress-text { text-align:center; font-size:14px; margin-bottom:20px; color:var(--text-muted); }
    .timer { text-align:center; font-size:18px; font-family:'Consolas',monospace; color:{{ $config['color'] }}; margin-bottom:14px; font-weight:700; }

    .question-card { background:var(--bg-card); border-radius:12px; padding:30px; margin-bottom:20px; }
    .category-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:bold; margin-bottom:15px; text-transform:uppercase; background:{{ $config['color'] }}; color:#fff; }
    .question-text { font-size:18px; line-height:1.6; margin-bottom:20px; color:var(--text-main); }
    .question-text code { background:{{ $config['color'] }}22; padding:2px 8px; border-radius:4px; font-family:'Consolas',monospace; font-size:15px; color:{{ $config['color'] }}; }
    .code-block { background:var(--bg-code); border:1px solid var(--border-subtle); border-radius:8px; padding:15px; margin:15px 0; font-family:'Consolas',monospace; font-size:14px; line-height:1.6; overflow-x:auto; max-width:100%; white-space:pre; color:var(--text-main); }

    .options { list-style:none; }
    .options li { background:var(--bg-input); border:2px solid transparent; border-radius:8px; padding:14px 18px; margin-bottom:10px; cursor:pointer; transition:all .2s; font-size:15px; color:var(--text-main); }
    .options li:hover { border-color:{{ $config['color'] }}; background:{{ $config['color'] }}15; }
    .options li.selected { border-color:{{ $config['color'] }}; background:{{ $config['color'] }}22; }
    .options li.correct { border-color:#4A7C59; background:rgba(74,124,89,0.15); }
    .options li.wrong { border-color:#C94A3A; background:rgba(201,74,58,0.15); }
    .options li.disabled { cursor:default; opacity:0.7; } .options li.disabled.correct { opacity:1; }

    .explanation { margin-top:15px; padding:15px; border-radius:8px; background:var(--bg-code); border-left:4px solid {{ $config['color'] }}; font-size:14px; line-height:1.8; display:none; color:var(--text-main); }
    .explanation code { background:{{ $config['color'] }}22; padding:2px 6px; border-radius:4px; font-family:'Consolas',monospace; font-size:13px; color:{{ $config['color'] }}; }

    .btn { display:inline-block; padding:12px 30px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:background .2s; }
    .btn-primary { background:{{ $config['color'] }}; color:#fff; font-weight:bold; }
    .btn-primary:hover { filter:brightness(1.1); }
    .btn-primary:disabled { background:#555; color:#999; cursor:not-allowed; }
    .btn-restart { background:var(--bg-input); color:var(--text-main); border:1px solid var(--border-subtle); }
    .btn-restart:hover { background:{{ $config['color'] }}22; }
    .btn-container { text-align:center; margin-top:20px; }

    .lesson-card { background:linear-gradient(135deg,var(--bg-card),var(--bg-input)); border:2px solid {{ $config['color'] }}33; border-radius:16px; padding:35px; margin-bottom:20px; }
    .lesson-card h2 { color:{{ $config['color'] }}; margin-bottom:8px; font-size:22px; }
    .lesson-card .chapter-num { color:{{ $config['color'] }}; font-size:13px; text-transform:uppercase; letter-spacing:2px; margin-bottom:5px; }
    .lesson-card p { color:var(--text-main); line-height:1.8; margin:12px 0; font-size:15px; }
    .lesson-card code { background:{{ $config['color'] }}22; padding:2px 6px; border-radius:4px; font-family:'Consolas',monospace; font-size:13px; color:{{ $config['color'] }}; }
    .lesson-card ul, .lesson-card ol { margin:8px 0 12px 24px; }
    .lesson-card li { margin:4px 0; color:var(--text-main); font-size:14px; line-height:1.7; }
    .lesson-card .code-example { background:var(--bg-code); border:1px solid {{ $config['color'] }}33; border-radius:8px; padding:15px; margin:15px 0; font-family:'Consolas',monospace; font-size:13px; line-height:1.7; color:var(--text-main); white-space:pre; overflow-x:auto; max-width:100%; }
    .lesson-card .code-example .comment { color:#7a8b9e; }
    .lesson-card .code-example .keyword { color:{{ $config['color'] }}; }
    .lesson-card .code-example .string { color:#7dc78f; }
    .lesson-card .code-example .number { color:#E8B088; }
    .lesson-card .tip { background:{{ $config['color'] }}15; border-left:3px solid {{ $config['color'] }}; padding:10px 15px; border-radius:0 6px 6px 0; margin:15px 0; font-size:14px; color:var(--text-main); }

    .chapter-score { display:flex; align-items:center; justify-content:center; gap:20px; margin:20px 0; flex-wrap:wrap; }
    .chapter-score .score-box { background:var(--bg-code); border-radius:10px; padding:15px 25px; text-align:center; }
    .chapter-score .score-box .num { font-size:32px; font-weight:bold; }
    .chapter-score .score-box .lbl { color:var(--text-muted); font-size:12px; }

    .results { display:none; padding:10px 0; }
    .score-circle { width:180px; height:180px; border-radius:50%; margin:20px auto; display:flex; flex-direction:column; align-items:center; justify-content:center; font-size:48px; font-weight:bold; }
    .score-circle .label { font-size:14px; font-weight:normal; color:var(--text-muted); }
    .level-excellent { background:linear-gradient(135deg,#1a3e2a,#4A7C59); color:#7dc78f; }
    .level-good { background:linear-gradient(135deg,#1a2e44,#0468D7); color:#7ab7f0; }
    .level-average { background:linear-gradient(135deg,#3e3a1a,#E8A020); color:#f0c87a; }
    .level-weak { background:linear-gradient(135deg,#3e1a1a,#C94A3A); color:#f08070; }
    .level-message { text-align:center; font-size:22px; font-weight:bold; margin:15px 0; color:var(--text-main); }
    .level-detail { text-align:center; color:var(--text-muted); margin-bottom:30px; line-height:1.6; }
    .cat-scores { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:15px; margin:25px 0; }
    .cat-score-card { background:var(--bg-card); border-radius:10px; padding:15px; text-align:center; }
    .cat-score-card .cat-name { font-size:12px; font-weight:bold; margin-bottom:8px; color:{{ $config['color'] }}; }
    .cat-score-card .cat-pct { font-size:28px; font-weight:bold; }
    .cat-score-card .cat-detail { font-size:12px; color:var(--text-muted); margin-top:4px; }

    .start-screen { overflow-wrap:break-word; text-align:center; padding:40px 20px; }
    .start-screen p { color:var(--text-muted); margin:15px 0; line-height:1.6; }
    .js-logo { font-size:30px; font-weight:bold; color:#fff; background:linear-gradient(135deg,{{ $config['color'] }},{{ $config['color'] }}aa); padding:0 22px; min-width:120px; height:90px; display:inline-flex; align-items:center; justify-content:center; border-radius:14px; margin:0 auto 20px; box-shadow:0 8px 24px rgba(0,0,0,0.25); }
    .roadmap { text-align:left; max-width:520px; margin:25px auto; }
    .roadmap .step { background:var(--bg-card); border:1px solid var(--border-subtle); border-radius:8px; padding:12px 16px; margin-bottom:8px; cursor:pointer; transition:border-color .2s,background .2s; font-size:14px; color:var(--text-main); display:flex; align-items:center; gap:12px; }
    .roadmap .step:hover { border-color:{{ $config['color'] }}; background:{{ $config['color'] }}11; }
    .roadmap .step .dot { width:28px; height:28px; border-radius:50%; background:{{ $config['color'] }}; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; }

    @media (max-width:600px) {
        .container { padding:18px 12px; }
        .question-card { padding:20px 16px; } .question-text { font-size:16px; }
        .options li { padding:10px 12px; font-size:13px; }
        .code-block { font-size:11px; padding:10px; }
        .category-badge { font-size:10px; padding:3px 10px; }
        .progress-text { font-size:12px; } .timer { font-size:16px; }
        .level-message { font-size:18px; } .level-detail { font-size:13px; }
        .score-circle { width:120px; height:120px; font-size:30px; }
        .cat-scores { grid-template-columns:1fr 1fr; gap:10px; }
        .cat-score-card { padding:10px; } .cat-score-card .cat-pct { font-size:20px; }
        h1 { font-size:22px; } .lesson-card { padding:20px 16px; }
        .lesson-card h2 { font-size:18px; } .lesson-card p, .lesson-card li { font-size:13px; }
    }
