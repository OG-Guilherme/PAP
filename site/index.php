<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$noticias_stmt = $pdo->query("SELECT n.*, u.nome as autor_nome 
                     FROM noticias n 
                     JOIN utilizadores u ON n.autor_id = u.id 
                     WHERE n.publicado = 1 
                     ORDER BY n.data_publicacao DESC LIMIT 4");
$noticias = $noticias_stmt->fetchAll();

$eventos_stmt = $pdo->query("SELECT e.*, u.nome as responsavel_nome 
                     FROM eventos e 
                     JOIN utilizadores u ON e.responsavel_id = u.id 
                     WHERE e.publicado = 1 AND e.data_evento >= NOW() 
                     ORDER BY e.data_evento ASC LIMIT 4");
$eventos = $eventos_stmt->fetchAll();

$cursos_stmt = $pdo->query("SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem LIMIT 6");
$cursos = $cursos_stmt->fetchAll();

$total_cursos   = $pdo->query("SELECT COUNT(*) FROM cursos WHERE ativo=1")->fetchColumn();
$total_noticias = $pdo->query("SELECT COUNT(*) FROM noticias WHERE publicado=1")->fetchColumn();
$total_eventos  = $pdo->query("SELECT COUNT(*) FROM eventos WHERE publicado=1")->fetchColumn();

$galeria_stmt = $pdo->query("
    SELECT imagem_destaque AS imagem, titulo, 'noticia' AS tipo, id
    FROM noticias WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    UNION ALL
    SELECT imagem_destaque AS imagem, titulo, 'evento' AS tipo, id
    FROM eventos WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    ORDER BY RAND() LIMIT 6
");
$galeria_preview = $galeria_stmt->fetchAll();

// Avisos urgentes
$avisos_stmt = $pdo->query("SELECT * FROM noticias WHERE publicado=1 AND categoria='Aviso' ORDER BY data_publicacao DESC LIMIT 3");
$avisos = $avisos_stmt->fetchAll();

$paginaActiva = 'inicio';
$tituloBase   = 'Início';

$extraCSS = <<<'ENDCSS'
<style>
@import url("https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap");

/* BARRA DE AVISOS */
.avisos-bar { background: var(--cor-principal); padding: 10px 0; }
.avisos-bar-inner { max-width:1200px; margin:0 auto; padding:0 48px; display:flex; align-items:center; gap:16px; }
.avisos-bar-label { font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:white; background:rgba(0,0,0,0.2); padding:4px 10px; border-radius:4px; white-space:nowrap; flex-shrink:0; display:flex; align-items:center; gap:6px; }
.avisos-bar-text { font-family:"DM Sans",sans-serif; font-size:.85rem; color:white; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.avisos-bar-text a { color:white; text-decoration:underline; text-underline-offset:2px; margin-right:28px; }
.avisos-bar-text a:hover { opacity:.85; }

/* HERO */
.hero-escola { position:relative; min-height:500px; display:flex; align-items:center; overflow:hidden; background:var(--cor-fundo); border-bottom:1px solid var(--cor-borda); }
.hero-escola::before { content:""; position:absolute; inset:0; background:linear-gradient(135deg, rgba(244,164,66,0.07) 0%, transparent 50%, rgba(244,164,66,0.03) 100%); pointer-events:none; }
.tema-escuro .hero-escola::before { background:linear-gradient(135deg, rgba(139,92,246,0.09) 0%, transparent 50%, rgba(139,92,246,0.04) 100%); }
.hero-escola::after { content:""; position:absolute; inset:0; background-image:linear-gradient(var(--cor-borda) 1px, transparent 1px), linear-gradient(90deg, var(--cor-borda) 1px, transparent 1px); background-size:40px 40px; opacity:.25; pointer-events:none; }
.hero-escola-inner { position:relative; z-index:2; max-width:1200px; margin:0 auto; padding:60px 48px; display:grid; grid-template-columns:1.1fr 1fr; gap:60px; align-items:center; width:100%; }
.hero-escola-badge { display:inline-flex; align-items:center; gap:7px; background:rgba(244,164,66,0.1); border:1px solid rgba(244,164,66,0.25); color:var(--cor-principal); font-family:"DM Sans",sans-serif; font-size:.75rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; padding:5px 12px; border-radius:4px; margin-bottom:18px; }
.hero-escola-nome { font-family:"Playfair Display",Georgia,serif; font-size:clamp(2rem,4vw,3rem); font-weight:900; line-height:1.1; color:var(--cor-texto); margin-bottom:12px; }
.hero-escola-nome span { color:var(--cor-principal); }
.hero-escola-sub { font-family:"DM Sans",sans-serif; font-size:1rem; color:var(--cor-texto-claro); line-height:1.65; margin-bottom:32px; max-width:440px; }
.hero-escola-btns { display:flex; gap:12px; flex-wrap:wrap; }
.btn-escola-primary { display:inline-flex; align-items:center; gap:8px; background:var(--cor-principal); color:white; text-decoration:none; font-family:"DM Sans",sans-serif; font-size:.9rem; font-weight:600; padding:12px 24px; border-radius:8px; transition:all .2s; box-shadow:0 3px 14px rgba(244,164,66,0.3); }
.btn-escola-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(244,164,66,0.4); }
.btn-escola-secondary { display:inline-flex; align-items:center; gap:8px; background:transparent; color:var(--cor-texto); text-decoration:none; font-family:"DM Sans",sans-serif; font-size:.9rem; font-weight:500; padding:11px 20px; border-radius:8px; border:1.5px solid var(--cor-borda); transition:all .2s; }
.btn-escola-secondary:hover { border-color:var(--cor-principal); color:var(--cor-principal); }

/* ACESSO RÁPIDO */
.hero-acesso-rapido { background:var(--cor-fundo-alt); border:1.5px solid var(--cor-borda); border-radius:14px; overflow:hidden; }
.hero-acesso-titulo { padding:14px 20px; border-bottom:1px solid var(--cor-borda); font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--cor-texto-claro); }
.acesso-item { display:flex; align-items:center; gap:14px; padding:13px 20px; text-decoration:none; color:var(--cor-texto); border-bottom:1px solid var(--cor-borda); transition:background .15s; }
.acesso-item:last-child { border-bottom:none; }
.acesso-item:hover { background:var(--cor-fundo); }
.acesso-item-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:rgba(244,164,66,0.1); color:var(--cor-principal); }
.acesso-item-titulo { font-family:"DM Sans",sans-serif; font-size:.86rem; font-weight:600; color:var(--cor-texto); display:block; }
.acesso-item-desc { font-family:"DM Sans",sans-serif; font-size:.74rem; color:var(--cor-texto-claro); }
.acesso-item-info { flex:1; }
.acesso-item-arrow { color:var(--cor-texto-claro); opacity:.4; flex-shrink:0; transition:all .15s; }
.acesso-item:hover .acesso-item-arrow { opacity:1; color:var(--cor-principal); }

/* STATS BAR */
.stats-bar { background:var(--cor-principal); padding:0; }
.stats-bar-inner { max-width:1200px; margin:0 auto; padding:0 48px; display:grid; grid-template-columns:repeat(4,1fr); }
.stat-bar-item { padding:22px 20px; text-align:center; border-right:1px solid rgba(255,255,255,0.15); color:white; }
.stat-bar-item:last-child { border-right:none; }
.stat-bar-num { font-family:"Playfair Display",serif; font-size:2rem; font-weight:900; line-height:1; margin-bottom:4px; display:block; }
.stat-bar-label { font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:500; opacity:.85; letter-spacing:.05em; text-transform:uppercase; }

/* PESQUISA */
.pesquisa-wrap { background:var(--cor-fundo-alt); border-bottom:1px solid var(--cor-borda); padding:16px 0; }
.pesquisa-inner { max-width:1200px; margin:0 auto; padding:0 48px; display:flex; align-items:center; gap:14px; }
.pesquisa-label { font-family:"DM Sans",sans-serif; font-size:.8rem; font-weight:600; color:var(--cor-texto-claro); white-space:nowrap; }
.pesquisa-form { flex:1; max-width:480px; display:flex; gap:8px; }
.pesquisa-input-wrap { flex:1; position:relative; display:flex; align-items:center; }
.pesquisa-input-wrap svg { position:absolute; left:12px; color:var(--cor-texto-claro); pointer-events:none; flex-shrink:0; }
.pesquisa-input-wrap input { width:100%; padding:9px 14px 9px 36px; font-size:.88rem; border-radius:8px; }
.btn-pesquisa { display:inline-flex; align-items:center; gap:6px; background:var(--cor-principal); color:white; border:none; font-family:"DM Sans",sans-serif; font-size:.85rem; font-weight:600; padding:9px 18px; border-radius:8px; cursor:pointer; transition:opacity .2s; }
.btn-pesquisa:hover { opacity:.9; }

/* SECÇÕES */
.escola-section { padding:60px 0; border-bottom:1px solid var(--cor-borda); }
.section-wrap { max-width:1200px; margin:0 auto; padding:0 48px; }
.section-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:32px; gap:20px; }
.section-eyebrow { font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--cor-principal); margin-bottom:6px; }
.section-title { font-family:"Playfair Display",serif; font-size:clamp(1.4rem,2.5vw,1.9rem); font-weight:700; color:var(--cor-texto); line-height:1.15; margin:0; }
.section-link { font-family:"DM Sans",sans-serif; font-size:.83rem; font-weight:500; color:var(--cor-principal); text-decoration:none; display:flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0; border-bottom:1px solid transparent; transition:border-color .2s; }
.section-link:hover { border-color:var(--cor-principal); }

/* NOTÍCIAS + AVISOS */
.noticias-avisos-grid { display:grid; grid-template-columns:1.6fr 1fr; gap:32px; }
.noticia-card { display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--cor-borda); text-decoration:none; color:var(--cor-texto); transition:all .15s; }
.noticia-card:first-child { padding-top:0; }
.noticia-card:last-child { border-bottom:none; padding-bottom:0; }
.noticia-card:hover .noticia-card-titulo { color:var(--cor-principal); }
.noticia-card-img { width:80px; height:64px; border-radius:8px; background:linear-gradient(135deg,var(--cor-principal),var(--cor-secundaria)); flex-shrink:0; overflow:hidden; }
.noticia-card-img img { width:100%; height:100%; object-fit:cover; }
.noticia-card-cat { font-family:"DM Sans",sans-serif; font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--cor-principal); margin-bottom:4px; }
.noticia-card-titulo { font-family:"DM Sans",sans-serif; font-size:.88rem; font-weight:600; line-height:1.35; color:var(--cor-texto); margin-bottom:5px; transition:color .15s; }
.noticia-card-meta { font-family:"DM Sans",sans-serif; font-size:.74rem; color:var(--cor-texto-claro); }
.avisos-col { background:var(--cor-fundo-alt); border:1.5px solid var(--cor-borda); border-radius:12px; overflow:hidden; height:fit-content; }
.avisos-col-header { padding:13px 18px; border-bottom:1px solid var(--cor-borda); display:flex; align-items:center; gap:8px; background:rgba(244,164,66,0.06); }
.avisos-col-titulo { font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--cor-principal); }
.aviso-item { padding:13px 18px; border-bottom:1px solid var(--cor-borda); text-decoration:none; display:block; transition:background .15s; }
.aviso-item:last-child { border-bottom:none; }
.aviso-item:hover { background:var(--cor-fundo); }
.aviso-item-titulo { font-family:"DM Sans",sans-serif; font-size:.84rem; font-weight:600; color:var(--cor-texto); margin-bottom:4px; line-height:1.3; }
.aviso-item-data { font-family:"DM Sans",sans-serif; font-size:.72rem; color:var(--cor-texto-claro); }
.aviso-empty { padding:22px 18px; font-family:"DM Sans",sans-serif; font-size:.84rem; color:var(--cor-texto-claro); text-align:center; }

/* CALENDÁRIO */
.calendario-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
.calendario-col h3 { font-family:"DM Sans",sans-serif; font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--cor-texto-claro); margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid var(--cor-borda); }
.cal-item { display:flex; align-items:flex-start; gap:14px; padding:11px 0; border-bottom:1px solid var(--cor-borda); text-decoration:none; color:inherit; }
.cal-item:last-child { border-bottom:none; }
.cal-data { min-width:44px; text-align:center; background:var(--cor-fundo-alt); border:1px solid var(--cor-borda); border-radius:8px; padding:5px 6px; flex-shrink:0; }
.cal-data-dia { font-family:"Playfair Display",serif; font-size:1.3rem; font-weight:700; color:var(--cor-principal); line-height:1; display:block; }
.cal-data-mes { font-family:"DM Sans",sans-serif; font-size:.62rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--cor-texto-claro); }
.cal-info-titulo { font-family:"DM Sans",sans-serif; font-size:.86rem; font-weight:600; color:var(--cor-texto); margin-bottom:2px; }
.cal-info-desc { font-family:"DM Sans",sans-serif; font-size:.76rem; color:var(--cor-texto-claro); }

/* CURSOS */
.cursos-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.curso-card { background:var(--cor-fundo-alt); border:1.5px solid var(--cor-borda); border-radius:12px; padding:20px 18px; text-decoration:none; color:var(--cor-texto); transition:all .22s; display:flex; flex-direction:column; gap:9px; }
.curso-card:hover { border-color:var(--cor-principal); transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,0.08); }
.curso-tipo-badge { display:inline-flex; align-items:center; width:fit-content; font-family:"DM Sans",sans-serif; font-size:.66rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--cor-principal); background:rgba(244,164,66,0.1); border:1px solid rgba(244,164,66,0.2); padding:3px 8px; border-radius:4px; }
.curso-nome { font-family:"DM Sans",sans-serif; font-size:.93rem; font-weight:600; color:var(--cor-texto); line-height:1.3; }
.curso-meta { font-family:"DM Sans",sans-serif; font-size:.76rem; color:var(--cor-texto-claro); display:flex; align-items:center; gap:5px; }
.curso-cta { font-family:"DM Sans",sans-serif; font-size:.78rem; font-weight:600; color:var(--cor-principal); display:flex; align-items:center; gap:5px; margin-top:auto; transition:gap .2s; }
.curso-card:hover .curso-cta { gap:8px; }

/* GALERIA */
.galeria-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:10px; }
.galeria-item { display:block; height:130px; border-radius:8px; overflow:hidden; background:var(--cor-fundo-alt); border:1px solid var(--cor-borda); transition:transform .2s,box-shadow .2s; }
.galeria-item:hover { transform:translateY(-2px); box-shadow:0 5px 18px rgba(0,0,0,0.1); }
.galeria-item img { width:100%; height:100%; object-fit:cover; transition:transform .35s; }
.galeria-item:hover img { transform:scale(1.05); }

/* CTA */
.cta-escola { background:var(--cor-principal); padding:60px 48px; text-align:center; position:relative; overflow:hidden; }
.cta-escola::before { content:""; position:absolute; inset:-50%; background:radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.08) 0%, transparent 70%); }
.cta-escola-inner { position:relative; z-index:1; max-width:560px; margin:0 auto; }
.cta-escola h2 { font-family:"Playfair Display",serif; font-size:clamp(1.6rem,3vw,2.3rem); font-weight:900; color:white; margin-bottom:12px; line-height:1.15; }
.cta-escola p { font-family:"DM Sans",sans-serif; font-size:.97rem; color:rgba(255,255,255,0.85); margin-bottom:26px; line-height:1.6; }
.cta-escola-btns { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
.btn-cta-white { display:inline-flex; align-items:center; gap:8px; background:white; color:var(--cor-principal); text-decoration:none; font-family:"DM Sans",sans-serif; font-size:.9rem; font-weight:700; padding:12px 24px; border-radius:8px; transition:all .2s; box-shadow:0 3px 12px rgba(0,0,0,0.15); }
.btn-cta-white:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,0.2); }
.btn-cta-outline { display:inline-flex; align-items:center; gap:8px; background:transparent; color:white; text-decoration:none; font-family:"DM Sans",sans-serif; font-size:.9rem; font-weight:600; padding:11px 20px; border-radius:8px; border:2px solid rgba(255,255,255,0.5); transition:all .2s; }
.btn-cta-outline:hover { border-color:white; background:rgba(255,255,255,0.1); }

.empty-state { text-align:center; padding:36px 20px; color:var(--cor-texto-claro); font-family:"DM Sans",sans-serif; font-size:.88rem; }

@media (max-width:960px) {
    .hero-escola-inner { grid-template-columns:1fr; gap:28px; padding:44px 24px; }
    .hero-acesso-rapido { display:none; }
    .stats-bar-inner { grid-template-columns:1fr 1fr; padding:0 24px; }
    .noticias-avisos-grid { grid-template-columns:1fr; }
    .calendario-grid { grid-template-columns:1fr; }
    .cursos-grid { grid-template-columns:1fr 1fr; }
    .section-wrap { padding:0 24px; }
    .pesquisa-inner { padding:0 24px; flex-wrap:wrap; }
    .avisos-bar-inner { padding:0 24px; }
    .cta-escola { padding:44px 24px; }
}
@media (max-width:600px) {
    .cursos-grid { grid-template-columns:1fr; }
    .galeria-grid { grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); }
}
</style>
ENDCSS;

require_once '_header.php';
?>

<?php if (!empty($avisos)): ?>
<div class="avisos-bar">
    <div class="avisos-bar-inner">
        <span class="avisos-bar-label">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Avisos
        </span>
        <div class="avisos-bar-text">
            <?php foreach ($avisos as $a): ?>
                <a href="noticia.php?id=<?= $a['id'] ?>"><?= sanitize($a['titulo']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- HERO -->
<div class="hero-escola">
    <div class="hero-escola-inner">
        <div>
            <div class="hero-escola-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Escola Secundária · Amadora
            </div>
            <h1 class="hero-escola-nome">Escola <span>EduWeb</span></h1>
            <p class="hero-escola-sub">Portal oficial da comunidade escolar. Aqui encontras notícias, agenda de eventos, oferta formativa e todos os recursos do portal da escola.</p>
            <div class="hero-escola-btns">
                <a href="cursos.php" class="btn-escola-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Oferta Formativa
                </a>
                <a href="contactos.php" class="btn-escola-secondary">Contactos</a>
                <a href="sobre.php" class="btn-escola-secondary">Sobre a Escola</a>
            </div>
        </div>

        <!-- Acesso rápido (visível só em desktop) -->
        <div class="hero-acesso-rapido">
            <div class="hero-acesso-titulo">Acesso Rápido</div>
            <a href="noticias.php" class="acesso-item">
                <div class="acesso-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
                <div class="acesso-item-info"><span class="acesso-item-titulo">Notícias e Comunicados</span><span class="acesso-item-desc"><?= $total_noticias ?> publicações</span></div>
                <svg class="acesso-item-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="eventos.php" class="acesso-item">
                <div class="acesso-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <div class="acesso-item-info"><span class="acesso-item-titulo">Agenda Escolar</span><span class="acesso-item-desc"><?= $total_eventos ?> eventos agendados</span></div>
                <svg class="acesso-item-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="cursos.php" class="acesso-item">
                <div class="acesso-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
                <div class="acesso-item-info"><span class="acesso-item-titulo">Oferta Formativa</span><span class="acesso-item-desc"><?= $total_cursos ?> cursos disponíveis</span></div>
                <svg class="acesso-item-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="faq.php" class="acesso-item">
                <div class="acesso-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
                <div class="acesso-item-info"><span class="acesso-item-titulo">Perguntas Frequentes</span><span class="acesso-item-desc">Matrículas, cursos, conta</span></div>
                <svg class="acesso-item-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="contactos.php" class="acesso-item">
                <div class="acesso-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6 6l.86-.86a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.4 16z"/></svg></div>
                <div class="acesso-item-info"><span class="acesso-item-titulo">Contactos da Escola</span><span class="acesso-item-desc">Secretaria, direção, email</span></div>
                <svg class="acesso-item-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>
</div>

<!-- PESQUISA -->
<div class="pesquisa-wrap">
    <div class="pesquisa-inner">
        <span class="pesquisa-label">Pesquisar no portal:</span>
        <form action="pesquisa.php" method="GET" class="pesquisa-form">
            <div class="pesquisa-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" placeholder="Pesquisar cursos, notícias, eventos…">
            </div>
            <button type="submit" class="btn-pesquisa">Pesquisar</button>
        </form>
    </div>
</div>

<!-- STATS -->
<div class="stats-bar">
    <div class="stats-bar-inner">
        <div class="stat-bar-item"><span class="stat-bar-num">500+</span><span class="stat-bar-label">Alunos Matriculados</span></div>
        <div class="stat-bar-item"><span class="stat-bar-num"><?= $total_cursos ?>+</span><span class="stat-bar-label">Cursos e Formações</span></div>
        <div class="stat-bar-item"><span class="stat-bar-num">50+</span><span class="stat-bar-label">Docentes</span></div>
        <div class="stat-bar-item"><span class="stat-bar-num">95%</span><span class="stat-bar-label">Taxa de Aprovação</span></div>
    </div>
</div>

<!-- NOTÍCIAS + AVISOS -->
<section class="escola-section">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-eyebrow">Informação escolar</div>
                <h2 class="section-title">Notícias e Comunicados</h2>
            </div>
            <a href="noticias.php" class="section-link">Ver todas <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="noticias-avisos-grid">
            <div>
                <?php if (empty($noticias)): ?>
                    <div class="empty-state">Ainda não há notícias publicadas.</div>
                <?php else: ?>
                    <?php foreach ($noticias as $n): ?>
                    <a href="noticia.php?id=<?= $n['id'] ?>" class="noticia-card">
                        <div class="noticia-card-img"><?php if ($n['imagem_destaque']): ?><img src="uploads/<?= htmlspecialchars($n['imagem_destaque']) ?>" alt=""><?php endif; ?></div>
                        <div>
                            <?php if ($n['categoria']): ?><div class="noticia-card-cat"><?= sanitize($n['categoria']) ?></div><?php endif; ?>
                            <div class="noticia-card-titulo"><?= sanitize($n['titulo']) ?></div>
                            <div class="noticia-card-meta"><?= sanitize($n['autor_nome']) ?> · <?= formatDate($n['data_publicacao']) ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="avisos-col">
                <div class="avisos-col-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;color:var(--cor-principal);flex-shrink:0;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="avisos-col-titulo">Avisos e Comunicados</span>
                </div>
                <?php if (empty($avisos)): ?>
                    <div class="aviso-empty">Sem avisos de momento.<br><small>Cria notícias com categoria "Aviso" para aparecerem aqui.</small></div>
                <?php else: ?>
                    <?php foreach ($avisos as $a): ?>
                    <a href="noticia.php?id=<?= $a['id'] ?>" class="aviso-item">
                        <div class="aviso-item-titulo"><?= sanitize($a['titulo']) ?></div>
                        <div class="aviso-item-data"><?= formatDate($a['data_publicacao']) ?></div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div style="padding:11px 18px;border-top:1px solid var(--cor-borda);">
                    <a href="noticias.php?categoria=Aviso" style="font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:600;color:var(--cor-principal);text-decoration:none;">Ver todos os avisos →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALENDÁRIO + EVENTOS -->
<section class="escola-section" style="background:var(--cor-fundo-alt);">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-eyebrow">Agenda escolar</div>
                <h2 class="section-title">Calendário e Próximos Eventos</h2>
            </div>
            <a href="eventos.php" class="section-link">Ver agenda <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="calendario-grid">
            <div class="calendario-col">
                <h3>Datas Importantes 2024/2025</h3>
                <?php
                $datas = [
                    ['03','Mar','Início do 2.º Período','2.º período letivo'],
                    ['20','Mar','Reuniões de Avaliação','Conselho de turma'],
                    ['11','Abr','Páscoa — Interrupção letiva','De 11 a 22 de abril'],
                    ['09','Jun','Exames Nacionais','Ensino Secundário'],
                    ['20','Jun','Fim do Ano Letivo','2024/2025'],
                ];
                foreach ($datas as $d): ?>
                <div class="cal-item">
                    <div class="cal-data"><span class="cal-data-dia"><?= $d[0] ?></span><span class="cal-data-mes"><?= $d[1] ?></span></div>
                    <div><div class="cal-info-titulo"><?= $d[2] ?></div><div class="cal-info-desc"><?= $d[3] ?></div></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="calendario-col">
                <h3>Próximos Eventos</h3>
                <?php if (empty($eventos)): ?>
                    <div class="empty-state" style="padding:16px 0;">Sem eventos agendados.</div>
                <?php else: ?>
                    <?php foreach ($eventos as $e): ?>
                    <a href="evento.php?id=<?= $e['id'] ?>" class="cal-item">
                        <div class="cal-data">
                            <span class="cal-data-dia"><?= date('d', strtotime($e['data_evento'])) ?></span>
                            <span class="cal-data-mes"><?= date('M', strtotime($e['data_evento'])) ?></span>
                        </div>
                        <div>
                            <div class="cal-info-titulo"><?= sanitize($e['titulo']) ?></div>
                            <div class="cal-info-desc"><?= sanitize($e['local']) ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- OFERTA FORMATIVA -->
<?php if (!empty($cursos)): ?>
<section class="escola-section">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-eyebrow">O que oferecemos</div>
                <h2 class="section-title">Oferta Formativa</h2>
            </div>
            <a href="cursos.php" class="section-link">Ver todos <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="cursos-grid">
            <?php foreach ($cursos as $c): ?>
            <a href="curso.php?id=<?= $c['id'] ?>" class="curso-card">
                <span class="curso-tipo-badge"><?= sanitize($c['tipo']) ?></span>
                <div class="curso-nome"><?= sanitize($c['nome']) ?></div>
                <div class="curso-meta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.72rem;height:.72rem;color:var(--cor-principal);flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?= $c['duracao_anos'] ?> anos<?php if (!empty($c['sigla'])): ?> · <?= sanitize($c['sigla']) ?><?php endif; ?>
                </div>
                <div class="curso-cta">Saber mais <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:.72rem;height:.72rem;"><polyline points="9 18 15 12 9 6"/></svg></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- GALERIA -->
<?php if (!empty($galeria_preview)): ?>
<section class="escola-section" style="background:var(--cor-fundo-alt);">
    <div class="section-wrap">
        <div class="section-header">
            <div>
                <div class="section-eyebrow">Vida escolar</div>
                <h2 class="section-title">Galeria de Fotos</h2>
            </div>
            <a href="galeria.php" class="section-link">Ver galeria <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><polyline points="9 18 15 12 9 6"/></svg></a>
        </div>
        <div class="galeria-grid">
            <?php foreach ($galeria_preview as $foto): ?>
            <a href="<?= $foto['tipo'] === 'noticia' ? 'noticia' : 'evento' ?>.php?id=<?= $foto['id'] ?>" class="galeria-item">
                <img src="uploads/<?= htmlspecialchars($foto['imagem']) ?>" alt="<?= htmlspecialchars($foto['titulo']) ?>" loading="lazy">
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA FINAL -->
<?php if (!isLoggedIn()): ?>
<div class="cta-escola">
    <div class="cta-escola-inner">
        <h2>Novo no portal?</h2>
        <p>Cria a tua conta para acederes a todos os recursos — comunicados, agenda escolar, perfil e muito mais.</p>
        <div class="cta-escola-btns">
            <a href="registar.php" class="btn-cta-white">Criar conta <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:.82rem;height:.82rem;"><polyline points="9 18 15 12 9 6"/></svg></a>
            <a href="login.php" class="btn-cta-outline">Já tenho conta</a>
            <a href="contactos.php" class="btn-cta-outline">Contactar a Escola</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '_footer.php'; ?>