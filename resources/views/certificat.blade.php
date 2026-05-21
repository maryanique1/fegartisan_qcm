@extends('layouts.app')
@section('title', 'Certificat — FeGArtisan QCM')

@section('styles')
    .container { max-width:840px; margin:0 auto; }
    .cert {
        background:linear-gradient(135deg,#FDF6EE,#fff7ee); color:#2C1A0E; border:12px double #C17B4E;
        border-radius:18px; padding:48px 36px; text-align:center; box-shadow:0 30px 80px rgba(107,45,14,0.18);
    }
    .cert .cert-logo { width:80px; height:80px; border-radius:18px; object-fit:cover; margin:0 auto 16px; box-shadow:0 10px 28px rgba(107,45,14,0.25); display:block; }
    .cert h2 { color:#8B3D1A; font-size:34px; letter-spacing:2px; font-family:Georgia,serif; margin-bottom:6px; }
    .cert .sub { color:#9A7A64; font-size:14px; letter-spacing:3px; text-transform:uppercase; margin-bottom:28px; }
    .cert .name { font-size:42px; font-family:Georgia,serif; color:#6B2D0E; border-bottom:2px solid #C17B4E; display:inline-block; padding:0 30px 6px; margin:18px 0 28px; }
    .cert p { color:#4A3424; font-size:15px; line-height:1.7; max-width:560px; margin:0 auto 18px; }
    .cert .pct { font-size:54px; font-weight:800; color:#8B3D1A; margin:8px 0; }
    .cert .footer-cert { margin-top:32px; display:flex; justify-content:space-between; padding-top:24px; border-top:1px solid #C17B4E55; font-size:13px; color:#9A7A64; }
    .actions { text-align:center; margin-top:24px; }
    .btn { padding:11px 26px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; }
    @media print {
        body { background:#fff; } .topbar, .sidebar, .sidebar-overlay, .global-progress, .actions { display:none !important; }
        .main-content { margin:0; padding:0; max-width:100%; }
        .cert { box-shadow:none; }
    }
    @media (max-width:600px) {
        .cert { padding:28px 18px; } .cert h2 { font-size:24px; } .cert .name { font-size:28px; padding:0 12px 4px; }
        .cert .pct { font-size:38px; } .cert .footer-cert { flex-direction:column; gap:8px; text-align:center; }
    }
@endsection

@section('content')
<div class="container">
    <div class="cert">
        <img src="/logo.jpeg" alt="FeGArtisan" class="cert-logo">
        <h2>Certificat de reussite</h2>
        <div class="sub">FeGArtisan QCM &mdash; Soutenance prep</div>
        <p>Ce certificat atteste que</p>
        <div class="name">{{ $user->nom ?? $user->name }}</div>
        <p>a valide avec succes l'examen final de la plateforme FeGArtisan QCM avec un score de</p>
        <div class="pct">{{ $bestPct }}%</div>
        <p>({{ $bestScore }} / {{ $total }} bonnes reponses)</p>
        <p>Demontrant ainsi sa maitrise de l'architecture, du backend Laravel, de l'application Flutter, de la messagerie en temps reel et de la conception du projet FeGArtisan.</p>
        <div class="footer-cert">
            <div>Delivre le {{ now()->format('d/m/Y') }}</div>
            <div>HECM &mdash; Licence SIL</div>
        </div>
    </div>
    <div class="actions">
        <button class="btn" onclick="window.print()">Imprimer le certificat</button>
    </div>
</div>
@endsection
