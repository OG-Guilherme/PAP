<?php

session_start();
require_once '../important/config.php';
require_once '_tema.php';

$stmt = $pdo->query("SELECT n.*, u.nome as autor_nome 
                     FROM noticias n 
                     JOIN utilizadores u ON n.autor_id = u.id 
                     WHERE n.publicado = 1 
                     ORDER BY n.data_publicacao DESC LIMIT 3");
$noticias = $stmt->fetchAll();

$stmt = $pdo->query("SELECT e.*, u.nome as responsavel_nome 
                     FROM eventos e 
                     JOIN utilizadores u ON e.responsavel_id = u.id 
                     WHERE e.publicado = 1 AND e.data_evento >= NOW() 
                     ORDER BY e.data_evento ASC LIMIT 3");
$eventos = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem LIMIT 3");
$cursos = $stmt->fetchAll();

// Contagens para os stats
$total_cursos  = $pdo->query("SELECT COUNT(*) FROM cursos WHERE ativo=1")->fetchColumn();
$total_noticias = $pdo->query("SELECT COUNT(*) FROM noticias WHERE publicado=1")->fetchColumn();
$total_eventos  = $pdo->query("SELECT COUNT(*) FROM eventos WHERE publicado=1")->fetchColumn();

// Galeria preview
$stmt = $pdo->query("
    SELECT imagem_destaque AS imagem, titulo, 'noticia' AS tipo, id
    FROM noticias WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    UNION ALL
    SELECT imagem_destaque AS imagem, titulo, 'evento' AS tipo, id
    FROM eventos WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    ORDER BY RAND() LIMIT 6
");
$galeria_preview = $stmt->fetchAll();

$paginaActiva = 'inicio';
$tituloBase   = 'Início';

$extraCSS = '<style>
/* ═══════════════ INDEX — ESTILOS EXCLUSIVOS ═══════════════ */

/* Importar fonte display elegante */
@import url("https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap");

/* ── HERO ──────────────────────────────────────────────── */
.hero-index {
    position: relative;
    min-height: 92vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: var(--hero-bg, #0a0a0f);
}

/* Fundo animado — mesh gradient */
.hero-index::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 20% 50%, rgba(244,164,66,0.18) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 20%, rgba(255,100,80,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 50% 50% at 60% 80%, rgba(244,164,66,0.08) 0%, transparent 50%);
    animation: heroMesh 12s ease-in-out infinite alternate;
}
.tema-escuro .hero-index::before {
    background:
        radial-gradient(ellipse 80% 60% at 20% 50%, rgba(139,92,246,0.22) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 20%, rgba(99,102,241,0.15) 0%, transparent 60%),
        radial-gradient(ellipse 50% 50% at 60% 80%, rgba(139,92,246,0.1) 0%, transparent 50%);
}
@keyframes heroMesh {
    0%   { opacity: 1; transform: scale(1)   rotate(0deg); }
    100% { opacity: 0.7; transform: scale(1.08) rotate(2deg); }
}

/* Grade decorativa no fundo */
.hero-index::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}
.tema-claro .hero-index {
    background: #faf8f5;
}
.tema-claro .hero-index::after {
    background-image:
        linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px);
}

.hero-inner {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 48px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

/* Texto do hero */
.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(244,164,66,0.12);
    border: 1px solid rgba(244,164,66,0.3);
    color: var(--cor-principal);
    font-family: "DM Sans", sans-serif;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 100px;
    margin-bottom: 28px;
    animation: fadeUp 0.6s ease 0.1s both;
}
.hero-eyebrow::before {
    content: "";
    width: 6px; height: 6px;
    background: var(--cor-principal);
    border-radius: 50%;
    animation: pulse 2s ease infinite;
}
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:0.5; transform:scale(1.4); }
}

.hero-title {
    font-family: "Playfair Display", Georgia, serif;
    font-size: clamp(2.8rem, 5vw, 4.2rem);
    font-weight: 900;
    line-height: 1.08;
    letter-spacing: -0.02em;
    color: var(--cor-texto);
    margin-bottom: 24px;
    animation: fadeUp 0.6s ease 0.2s both;
}
.hero-title em {
    font-style: italic;
    color: var(--cor-principal);
    position: relative;
}
.hero-title em::after {
    content: "";
    position: absolute;
    bottom: 4px; left: 0; right: 0;
    height: 3px;
    background: var(--cor-principal);
    opacity: 0.3;
    border-radius: 2px;
}

.hero-sub {
    font-family: "DM Sans", sans-serif;
    font-size: 1.15rem;
    font-weight: 300;
    line-height: 1.7;
    color: var(--cor-texto-claro);
    margin-bottom: 40px;
    max-width: 480px;
    animation: fadeUp 0.6s ease 0.3s both;
}

.hero-actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    animation: fadeUp 0.6s ease 0.4s both;
}

.btn-hero-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--cor-principal);
    color: white;
    text-decoration: none;
    font-family: "DM Sans", sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 14px 28px;
    border-radius: 10px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 20px rgba(244,164,66,0.3);
}
.btn-hero-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(244,164,66,0.45);
}
.tema-escuro .btn-hero-primary {
    box-shadow: 0 4px 20px rgba(139,92,246,0.35);
}
.tema-escuro .btn-hero-primary:hover {
    box-shadow: 0 8px 30px rgba(139,92,246,0.5);
}

.btn-hero-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--cor-texto);
    text-decoration: none;
    font-family: "DM Sans", sans-serif;
    font-size: 0.95rem;
    font-weight: 500;
    padding: 14px 24px;
    border-radius: 10px;
    border: 1.5px solid var(--cor-borda);
    transition: all 0.2s ease;
}
.btn-hero-secondary:hover {
    border-color: var(--cor-principal);
    color: var(--cor-principal);
    background: rgba(244,164,66,0.05);
}

/* Lado direito do hero — Cards de destaque */
.hero-right {
    animation: fadeLeft 0.7s ease 0.3s both;
}
@keyframes fadeLeft {
    from { opacity:0; transform:translateX(30px); }
    to   { opacity:1; transform:translateX(0); }
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}

/* Mini-cards de quem é o site */
.para-quem-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.para-quem-card {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 14px;
    padding: 22px 20px;
    text-decoration: none;
    color: var(--cor-texto);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    overflow: hidden;
}
.para-quem-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--pq-cor, var(--cor-principal)), transparent 70%);
    opacity: 0;
    transition: opacity 0.25s ease;
    border-radius: inherit;
}
.para-quem-card:hover { 
    border-color: var(--pq-cor, var(--cor-principal));
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.1);
}
.para-quem-card:hover::before { opacity: 0.06; }

.pq-icon {
    width: 40px; height: 40px;
    background: rgba(244,164,66,0.1);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--pq-cor, var(--cor-principal));
    flex-shrink: 0;
    transition: background 0.2s;
}
.para-quem-card:hover .pq-icon {
    background: rgba(244,164,66,0.18);
}
.pq-titulo {
    font-family: "DM Sans", sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--cor-texto);
}
.pq-desc {
    font-family: "DM Sans", sans-serif;
    font-size: 0.78rem;
    color: var(--cor-texto-claro);
    line-height: 1.5;
}

/* ── STATS BAR ────────────────────────────────────────── */
.stats-bar {
    background: var(--cor-principal);
    padding: 0;
    overflow: hidden;
}
.stats-bar-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 48px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.stat-bar-item {
    padding: 28px 20px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,0.15);
    color: white;
}
.stat-bar-item:last-child { border-right: none; }
.stat-bar-num {
    font-family: "Playfair Display", serif;
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 6px;
    display: block;
}
.stat-bar-label {
    font-family: "DM Sans", sans-serif;
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.85;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

/* ── SECÇÕES DE CONTEÚDO ─────────────────────────────── */
.index-section {
    padding: 80px 0;
}
.index-section + .index-section {
    border-top: 1px solid var(--cor-borda);
}
.section-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 48px;
}

.section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 48px;
    gap: 20px;
}
.section-label {
    font-family: "DM Sans", sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--cor-principal);
    margin-bottom: 8px;
}
.section-title {
    font-family: "Playfair Display", serif;
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 700;
    color: var(--cor-texto);
    line-height: 1.15;
    margin: 0;
}
.section-link {
    font-family: "DM Sans", sans-serif;
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--cor-principal);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    flex-shrink: 0;
    padding-bottom: 4px;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s;
}
.section-link:hover { border-color: var(--cor-principal); }

/* Grid de cursos — 3 cols */
.cursos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Card moderno */
.idx-card {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: var(--cor-texto);
}
.idx-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.12);
    border-color: var(--cor-principal);
}
.idx-card-img {
    height: 180px;
    background: linear-gradient(135deg, var(--cor-principal) 0%, var(--cor-secundaria) 100%);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}
.idx-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}
.idx-card:hover .idx-card-img img { transform: scale(1.05); }
.idx-card-badge {
    position: absolute;
    top: 12px; left: 12px;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(8px);
    color: white;
    font-family: "DM Sans", sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 100px;
}
.idx-card-body {
    padding: 22px 22px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.idx-card-title {
    font-family: "DM Sans", sans-serif;
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--cor-texto);
    margin-bottom: 8px;
    line-height: 1.3;
}
.idx-card-meta {
    font-family: "DM Sans", sans-serif;
    font-size: 0.8rem;
    color: var(--cor-texto-claro);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}
.idx-card-desc {
    font-family: "DM Sans", sans-serif;
    font-size: 0.875rem;
    color: var(--cor-texto-claro);
    line-height: 1.65;
    flex: 1;
    margin-bottom: 18px;
}
.idx-card-cta {
    font-family: "DM Sans", sans-serif;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--cor-principal);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: gap 0.2s ease;
}
.idx-card:hover .idx-card-cta { gap: 10px; }

/* Noticias — layout editorial (1 grande + 2 pequenas) */
.noticias-editorial {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 24px;
}
.noticia-principal {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: var(--cor-texto);
    transition: all 0.25s ease;
}
.noticia-principal:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.12);
    border-color: var(--cor-principal);
}
.noticia-principal-img {
    height: 260px;
    background: linear-gradient(135deg, var(--cor-principal) 0%, var(--cor-secundaria) 100%);
    overflow: hidden;
    position: relative;
}
.noticia-principal-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}
.noticia-principal:hover .noticia-principal-img img { transform: scale(1.04); }
.noticia-principal-body { padding: 28px; }
.noticia-principal-cat {
    font-family: "DM Sans", sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--cor-principal);
    margin-bottom: 10px;
}
.noticia-principal-title {
    font-family: "Playfair Display", serif;
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1.3;
    color: var(--cor-texto);
    margin-bottom: 12px;
}
.noticia-principal-meta {
    font-family: "DM Sans", sans-serif;
    font-size: 0.8rem;
    color: var(--cor-texto-claro);
    margin-bottom: 14px;
}
.noticia-principal-excerpt {
    font-family: "DM Sans", sans-serif;
    font-size: 0.9rem;
    color: var(--cor-texto-claro);
    line-height: 1.7;
}

.noticias-secundarias {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.noticia-mini {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 12px;
    padding: 18px 20px;
    text-decoration: none;
    color: var(--cor-texto);
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: all 0.2s ease;
}
.noticia-mini:hover {
    border-color: var(--cor-principal);
    transform: translateX(4px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.noticia-mini-title {
    font-family: "DM Sans", sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.35;
    color: var(--cor-texto);
}
.noticia-mini-meta {
    font-family: "DM Sans", sans-serif;
    font-size: 0.76rem;
    color: var(--cor-texto-claro);
}
.noticia-mini-cat {
    font-family: "DM Sans", sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--cor-principal);
}

/* Eventos — lista horizontal */
.eventos-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.evento-card {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 14px;
    padding: 24px;
    text-decoration: none;
    color: var(--cor-texto);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
    overflow: hidden;
}
.evento-card::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--cor-principal);
    border-radius: 4px 0 0 4px;
    transform: scaleY(0);
    transition: transform 0.25s ease;
    transform-origin: top;
}
.evento-card:hover::before { transform: scaleY(1); }
.evento-card:hover {
    border-color: var(--cor-principal);
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,0.1);
    padding-left: 28px;
}
.evento-data-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(244,164,66,0.1);
    border: 1px solid rgba(244,164,66,0.25);
    color: var(--cor-principal);
    font-family: "DM Sans", sans-serif;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 100px;
    width: fit-content;
}
.evento-title {
    font-family: "DM Sans", sans-serif;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
    color: var(--cor-texto);
}
.evento-local {
    font-family: "DM Sans", sans-serif;
    font-size: 0.8rem;
    color: var(--cor-texto-claro);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ── CTA FINAL ───────────────────────────────────────── */
.cta-final {
    background: var(--cor-principal);
    padding: 80px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cta-final::before {
    content: "";
    position: absolute;
    inset: -50%;
    background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.08) 0%, transparent 70%);
    animation: ctaPulse 6s ease-in-out infinite alternate;
}
@keyframes ctaPulse {
    0%   { transform: scale(0.9); }
    100% { transform: scale(1.1); }
}
.cta-final-content { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
.cta-final h2 {
    font-family: "Playfair Display", serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 900;
    color: white;
    margin-bottom: 16px;
    line-height: 1.15;
}
.cta-final p {
    font-family: "DM Sans", sans-serif;
    font-size: 1.05rem;
    color: rgba(255,255,255,0.85);
    margin-bottom: 36px;
    line-height: 1.6;
}
.cta-final-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.btn-cta-white {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: var(--cor-principal);
    text-decoration: none;
    font-family: "DM Sans", sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 14px 28px;
    border-radius: 10px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.btn-cta-white:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
.btn-cta-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: white;
    text-decoration: none;
    font-family: "DM Sans", sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 13px 24px;
    border-radius: 10px;
    border: 2px solid rgba(255,255,255,0.5);
    transition: all 0.2s ease;
}
.btn-cta-outline:hover { border-color: white; background: rgba(255,255,255,0.1); }

/* Empty state minimalista */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--cor-texto-claro);
    font-family: "DM Sans", sans-serif;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 900px) {
    .hero-inner { grid-template-columns: 1fr; gap: 48px; padding: 60px 24px; }
    .para-quem-grid { grid-template-columns: 1fr 1fr; }
    .stats-bar-inner { grid-template-columns: 1fr 1fr; padding: 0 24px; }
    .cursos-grid { grid-template-columns: 1fr; }
    .noticias-editorial { grid-template-columns: 1fr; }
    .eventos-list { grid-template-columns: 1fr; }
    .section-wrap { padding: 0 24px; }
    .cta-final { padding: 60px 24px; }
}
</style>';

require_once '_header.php';
?>

<!-- ═══════════════════════════════════════════
     HERO — proposta de valor clara e imediata
     ═══════════════════════════════════════════ -->
<section class="hero-index">
    <div class="hero-inner">

        <!-- Esquerda: texto -->
        <div class="hero-text">
            <div class="hero-eyebrow">Escola Amadora — Lisboa</div>

            <h1 class="hero-title">
                O lugar onde o<br>
                futuro começa<br>
                <em>a ganhar forma</em>
            </h1>

            <p class="hero-sub">
                Cursos, notícias, eventos e muito mais — tudo numa plataforma pensada para alunos, professores e famílias da nossa comunidade educativa.
            </p>

            <div class="hero-actions">
                <a href="cursos.php" class="btn-hero-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Ver Cursos
                </a>
                <a href="sobre.php" class="btn-hero-secondary">
                    Sobre a Escola
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:0.9rem;height:0.9rem;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>

        <!-- Direita: para quem é o site -->
        <div class="hero-right">
            <div class="para-quem-grid">

                <a href="cursos.php" class="para-quem-card" style="--pq-cor: #f4a442;">
                    <div class="pq-icon" style="background:rgba(244,164,66,0.12);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <div class="pq-titulo">Para Alunos</div>
                    <div class="pq-desc">Cursos, horários, notas e recursos de aprendizagem</div>
                </a>

                <a href="sobre.php" class="para-quem-card" style="--pq-cor: #3b82f6;">
                    <div class="pq-icon" style="background:rgba(59,130,246,0.12); color:#3b82f6;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="pq-titulo">Para Professores</div>
                    <div class="pq-desc">Gestão de turmas, publicação de conteúdo e comunicados</div>
                </a>

                <a href="contactos.php" class="para-quem-card" style="--pq-cor: #10b981;">
                    <div class="pq-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </div>
                    <div class="pq-titulo">Para Famílias</div>
                    <div class="pq-desc">Informações de admissão, eventos e contacto direto</div>
                </a>

                <a href="noticias.php" class="para-quem-card" style="--pq-cor: #8b5cf6;">
                    <div class="pq-icon" style="background:rgba(139,92,246,0.12); color:#8b5cf6;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="pq-titulo">Comunidade</div>
                    <div class="pq-desc">Notícias, eventos e tudo o que acontece na escola</div>
                </a>

            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════
     BARRA DE ESTATÍSTICAS
     ═══════════════════════ -->
<div class="stats-bar">
    <div class="stats-bar-inner">
        <div class="stat-bar-item">
            <span class="stat-bar-num" data-count="500">500+</span>
            <span class="stat-bar-label">Alunos Ativos</span>
        </div>
        <div class="stat-bar-item">
            <span class="stat-bar-num"><?= $total_cursos ?>+</span>
            <span class="stat-bar-label">Cursos</span>
        </div>
        <div class="stat-bar-item">
            <span class="stat-bar-num">50+</span>
            <span class="stat-bar-label">Professores</span>
        </div>
        <div class="stat-bar-item">
            <span class="stat-bar-num">95%</span>
            <span class="stat-bar-label">Taxa de Sucesso</span>
        </div>
    </div>
</div>

<!-- ═══════════════════════
     PESQUISA RÁPIDA
     ═══════════════════════ -->
<div style="background:var(--cor-fundo);border-bottom:1px solid var(--cor-borda);padding:20px 24px;">
    <form action="pesquisa.php" method="GET" style="max-width:560px;margin:0 auto;display:flex;gap:8px;">
        <div style="flex:1;position:relative;display:flex;align-items:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;position:absolute;left:14px;color:var(--cor-texto-claro);pointer-events:none;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" placeholder="Pesquisar cursos, notícias, eventos…" style="width:100%;padding:11px 16px 11px 40px;font-size:.93rem;">
        </div>
        <button type="submit" class="btn-hero-secondary" style="padding:11px 22px;font-size:.88rem;border-radius:10px;">Pesquisar</button>
    </form>
</div>

<!-- ═══════════════════════
     CURSOS EM DESTAQUE
     ═══════════════════════ -->
<?php if (!empty($cursos)): ?>
<section class="index-section">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-label">O que podes aprender</div>
                <h2 class="section-title">Cursos em Destaque</h2>
            </div>
            <a href="cursos.php" class="section-link">
                Ver todos os cursos
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <div class="cursos-grid">
            <?php foreach ($cursos as $c): ?>
            <a href="curso.php?id=<?= $c['id'] ?>" class="idx-card">
                <div class="idx-card-img">
                    <?php if ($c['imagem']): ?>
                        <img src="uploads/<?= $c['imagem'] ?>" alt="<?= sanitize($c['nome']) ?>">
                    <?php endif; ?>
                    <span class="idx-card-badge"><?= sanitize($c['tipo']) ?></span>
                </div>
                <div class="idx-card-body">
                    <div class="idx-card-title"><?= sanitize($c['nome']) ?></div>
                    <div class="idx-card-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;color:var(--cor-principal);flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?= $c['duracao_anos'] ?> anos
                    </div>
                    <div class="idx-card-desc"><?= sanitize(substr($c['descricao'], 0, 110)) ?>…</div>
                    <div class="idx-card-cta">
                        Saber mais
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════
     ÚLTIMAS NOTÍCIAS
     ═══════════════════════ -->
<section class="index-section" style="background:var(--cor-fundo-alt);">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-label">Fique a par</div>
                <h2 class="section-title">Últimas Notícias</h2>
            </div>
            <a href="noticias.php" class="section-link">
                Ver todas
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="empty-state">Ainda não há notícias. Em breve!</div>
        <?php else: ?>
            <div class="noticias-editorial">
                <!-- Notícia principal — a mais recente -->
                <a href="noticia.php?id=<?= $noticias[0]['id'] ?>" class="noticia-principal">
                    <div class="noticia-principal-img">
                        <?php if ($noticias[0]['imagem_destaque']): ?>
                            <img src="uploads/<?= $noticias[0]['imagem_destaque'] ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="noticia-principal-body">
                        <?php if ($noticias[0]['categoria']): ?>
                            <div class="noticia-principal-cat"><?= sanitize($noticias[0]['categoria']) ?></div>
                        <?php endif; ?>
                        <div class="noticia-principal-title"><?= sanitize($noticias[0]['titulo']) ?></div>
                        <div class="noticia-principal-meta">
                            <?= sanitize($noticias[0]['autor_nome']) ?> · <?= formatDate($noticias[0]['data_publicacao']) ?>
                        </div>
                        <div class="noticia-principal-excerpt">
                            <?= sanitize(substr($noticias[0]['resumo'] ?? $noticias[0]['conteudo'], 0, 160)) ?>…
                        </div>
                    </div>
                </a>

                <!-- Notícias secundárias -->
                <div class="noticias-secundarias">
                    <?php foreach (array_slice($noticias, 1) as $n): ?>
                    <a href="noticia.php?id=<?= $n['id'] ?>" class="noticia-mini">
                        <?php if ($n['categoria']): ?>
                            <div class="noticia-mini-cat"><?= sanitize($n['categoria']) ?></div>
                        <?php endif; ?>
                        <div class="noticia-mini-title"><?= sanitize($n['titulo']) ?></div>
                        <div class="noticia-mini-meta"><?= sanitize($n['autor_nome']) ?> · <?= formatDate($n['data_publicacao']) ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════
     PRÓXIMOS EVENTOS
     ═══════════════════════ -->
<section class="index-section">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-label">Agenda</div>
                <h2 class="section-title">Próximos Eventos</h2>
            </div>
            <a href="eventos.php" class="section-link">
                Ver agenda completa
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <?php if (empty($eventos)): ?>
            <div class="empty-state">Sem eventos agendados de momento.</div>
        <?php else: ?>
            <div class="eventos-list">
                <?php foreach ($eventos as $e): ?>
                <a href="evento.php?id=<?= $e['id'] ?>" class="evento-card">
                    <div class="evento-data-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= formatDateTime($e['data_evento']) ?>
                    </div>
                    <div class="evento-title"><?= sanitize($e['titulo']) ?></div>
                    <div class="evento-local">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;flex-shrink:0;color:var(--cor-principal);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= sanitize($e['local']) ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════
     GALERIA PRÉVIA
     ═══════════════════════ -->
<?php if (!empty($galeria_preview)): ?>
<section class="index-section" style="background:var(--cor-fundo-alt);">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-label">Memórias</div>
                <h2 class="section-title">Galeria</h2>
            </div>
            <a href="galeria.php" class="section-link">
                Ver galeria completa
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
            <?php foreach ($galeria_preview as $foto): ?>
            <a href="<?= $foto['tipo'] === 'noticia' ? 'noticia' : 'evento' ?>.php?id=<?= $foto['id'] ?>"
               style="display:block;height:160px;border-radius:10px;overflow:hidden;background:var(--cor-fundo);border:1px solid var(--cor-borda);">
                <img src="uploads/<?= htmlspecialchars($foto['imagem']) ?>"
                     alt="<?= htmlspecialchars($foto['titulo']) ?>"
                     loading="lazy"
                     style="width:100%;height:100%;object-fit:cover;transition:transform .35s;"
                     onmouseover="this.style.transform='scale(1.06)'"
                     onmouseout="this.style.transform=''">
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════
     CTA FINAL
     ═══════════════════════ -->
<?php if (!isLoggedIn()): ?>
<div class="cta-final">
    <div class="cta-final-content">
        <h2>Pronto para começar?</h2>
        <p>Cria a tua conta gratuitamente e acede a todos os recursos da plataforma EduWeb.</p>
        <div class="cta-final-btns">
            <a href="registar.php" class="btn-cta-white">
                Criar conta gratuita
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="login.php" class="btn-cta-outline">Já tenho conta</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '_footer.php'; ?>