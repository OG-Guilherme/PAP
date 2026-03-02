<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

// Buscar imagens de eventos e notícias que tenham imagem_destaque
$fotos = $pdo->query("
    SELECT 'noticia' AS tipo, id, titulo, imagem_destaque AS imagem, data_publicacao AS data, categoria
    FROM noticias WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    UNION ALL
    SELECT 'evento' AS tipo, id, titulo, imagem_destaque AS imagem, data_evento AS data, categoria
    FROM eventos WHERE publicado=1 AND imagem_destaque IS NOT NULL AND imagem_destaque != ''
    ORDER BY data DESC
")->fetchAll();

$cats = array_unique(array_filter(array_column($fotos, 'categoria')));
sort($cats);
$filtro = $_GET['cat'] ?? '';

$paginaActiva = 'galeria';
$tituloBase   = 'Galeria';

$extraCSS = <<<'ENDCSS'
<style>
.galeria-wrap{max-width:1200px;margin:0 auto;padding:56px 24px 80px;}
.galeria-hero{margin-bottom:40px;padding-bottom:32px;border-bottom:1px solid var(--cor-borda);display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.galeria-hero h1{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);}
.galeria-hero-label{font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:10px;}
.cat-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:32px;}
.cat-tab{padding:5px 13px;border-radius:100px;font-size:.8rem;font-weight:500;text-decoration:none;border:1.5px solid var(--cor-borda);color:var(--cor-texto-claro);transition:all .15s;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.cat-tab:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.cat-tab.active{background:var(--cor-principal);border-color:var(--cor-principal);color:white;}
/* Masonry grid */
.galeria-grid{columns:4 220px;gap:12px;}
.galeria-item{break-inside:avoid;margin-bottom:12px;position:relative;overflow:hidden;border-radius:10px;cursor:pointer;background:var(--cor-fundo-alt);}
.galeria-item img{width:100%;display:block;transition:transform .35s ease;}
.galeria-item:hover img{transform:scale(1.04);}
.galeria-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.7) 0%,transparent 50%);opacity:0;transition:opacity .25s;display:flex;flex-direction:column;justify-content:flex-end;padding:14px;}
.galeria-item:hover .galeria-overlay{opacity:1;}
.galeria-overlay-title{color:white;font-size:.85rem;font-weight:700;line-height:1.3;margin-bottom:4px;}
.galeria-overlay-meta{display:flex;align-items:center;gap:8px;font-size:.72rem;color:rgba(255,255,255,.75);}
.galeria-overlay-cat{display:inline-block;background:var(--cor-principal);color:white;font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:4px;padding:2px 6px;}
.galeria-tipo-badge{position:absolute;top:10px;right:10px;background:rgba(0,0,0,.55);color:white;font-size:.65rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:100px;padding:3px 9px;backdrop-filter:blur(4px);}
/* Lightbox */
.lightbox{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.92);align-items:center;justify-content:center;padding:24px;}
.lightbox.open{display:flex;}
.lb-inner{position:relative;max-width:1000px;max-height:90vh;width:100%;display:flex;flex-direction:column;align-items:center;}
.lb-img{max-width:100%;max-height:75vh;object-fit:contain;border-radius:8px;}
.lb-caption{margin-top:16px;text-align:center;}
.lb-title{color:white;font-size:1rem;font-weight:600;margin-bottom:4px;}
.lb-meta{color:rgba(255,255,255,.55);font-size:.82rem;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.lb-close{position:absolute;top:-16px;right:-16px;width:40px;height:40px;background:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#333;font-size:1.2rem;transition:background .15s;}
.lb-close:hover{background:#f0f0f0;}
.lb-nav{position:absolute;top:50%;transform:translateY(-50%);display:flex;justify-content:space-between;width:calc(100% + 80px);left:-40px;pointer-events:none;}
.lb-btn{width:44px;height:44px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:white;pointer-events:all;transition:background .15s;backdrop-filter:blur(4px);}
.lb-btn:hover{background:rgba(255,255,255,.3);}
.lb-link{display:inline-flex;align-items:center;gap:6px;margin-top:14px;color:var(--cor-principal);font-size:.85rem;font-weight:600;text-decoration:none;border:1.5px solid var(--cor-principal);border-radius:8px;padding:6px 14px;transition:all .15s;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.lb-link:hover{background:var(--cor-principal);color:white;}
.empty-state{text-align:center;padding:80px 20px;color:var(--cor-texto-claro);}
.empty-state svg{width:3rem;height:3rem;margin:0 auto 16px;display:block;opacity:.35;}
</style>
ENDCSS;
require_once '_header.php';

$visiveis = $filtro ? array_filter($fotos, fn($f) => $f['categoria'] === $filtro) : $fotos;
$visiveis = array_values($visiveis);
?>

<div class="galeria-wrap">
  <div class="galeria-hero">
    <div>
      <div class="galeria-hero-label">Memórias EduWeb</div>
      <h1>Galeria</h1>
    </div>
    <div style="font-size:.88rem;color:var(--cor-texto-claro);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
      <?= count($visiveis) ?> foto<?= count($visiveis) !== 1 ? 's' : '' ?>
    </div>
  </div>

  <?php if (!empty($cats)): ?>
  <div class="cat-tabs">
    <a href="galeria.php" class="cat-tab <?= !$filtro ? 'active' : '' ?>">Todas</a>
    <?php foreach ($cats as $c): ?>
      <a href="galeria.php?cat=<?= urlencode($c) ?>" class="cat-tab <?= $c===$filtro ? 'active' : '' ?>"><?= htmlspecialchars($c) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($visiveis)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      <p>Nenhuma foto disponível ainda.</p>
    </div>
  <?php else: ?>
    <div class="galeria-grid" id="galeria-grid">
      <?php foreach ($visiveis as $i => $f): ?>
      <div class="galeria-item" onclick="openLb(<?= $i ?>)" data-index="<?= $i ?>">
        <img src="uploads/<?= htmlspecialchars($f['imagem']) ?>" alt="<?= htmlspecialchars($f['titulo']) ?>" loading="lazy">
        <div class="galeria-overlay">
          <?php if ($f['categoria']): ?><span class="galeria-overlay-cat"><?= htmlspecialchars($f['categoria']) ?></span><?php endif; ?>
          <div class="galeria-overlay-title"><?= htmlspecialchars($f['titulo']) ?></div>
          <div class="galeria-overlay-meta"><?= date('d M Y', strtotime($f['data'])) ?></div>
        </div>
        <div class="galeria-tipo-badge"><?= $f['tipo'] === 'noticia' ? 'Notícia' : 'Evento' ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLbOutside(event)">
  <div class="lb-inner">
    <button class="lb-close" onclick="closeLb()" title="Fechar">✕</button>
    <div class="lb-nav">
      <button class="lb-btn" onclick="navLb(-1)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="lb-btn" onclick="navLb(1)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
    <img class="lb-img" id="lb-img" src="" alt="">
    <div class="lb-caption">
      <div class="lb-title" id="lb-title"></div>
      <div class="lb-meta" id="lb-meta"></div>
      <a class="lb-link" id="lb-link" href="#">Ver página completa →</a>
    </div>
  </div>
</div>

<script>
const FOTOS = <?= json_encode(array_map(fn($f) => [
    'img'   => 'uploads/' . $f['imagem'],
    'title' => $f['titulo'],
    'meta'  => date('d M Y', strtotime($f['data'])) . ($f['categoria'] ? ' · ' . $f['categoria'] : ''),
    'link'  => ($f['tipo'] === 'noticia' ? 'noticia.php' : 'evento.php') . '?id=' . $f['id'],
], $visiveis)) ?>;

let cur = 0;
function openLb(i) {
    cur = i;
    renderLb();
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLb() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
function closeLbOutside(e) { if (e.target.id === 'lightbox') closeLb(); }
function navLb(dir) {
    cur = (cur + dir + FOTOS.length) % FOTOS.length;
    renderLb();
}
function renderLb() {
    const f = FOTOS[cur];
    document.getElementById('lb-img').src   = f.img;
    document.getElementById('lb-img').alt   = f.title;
    document.getElementById('lb-title').textContent = f.title;
    document.getElementById('lb-meta').textContent  = f.meta;
    document.getElementById('lb-link').href  = f.link;
}
document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('open')) return;
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowLeft')  navLb(-1);
    if (e.key === 'ArrowRight') navLb(1);
});
</script>

<?php require_once '_footer.php'; ?>