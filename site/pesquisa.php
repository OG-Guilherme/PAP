<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$q      = trim($_GET['q'] ?? '');
$filtro = $_GET['tipo'] ?? 'tudo';

$resultados = ['cursos' => [], 'noticias' => [], 'eventos' => []];
$total = 0;

if ($q !== '') {
    $like = "%$q%";

    if ($filtro === 'tudo' || $filtro === 'cursos') {
        $s = $pdo->prepare("SELECT id, nome AS titulo, sigla AS subtitulo, tipo AS categoria, 'cursos' AS _tipo FROM cursos WHERE ativo=1 AND (nome LIKE ? OR sigla LIKE ? OR descricao LIKE ?) ORDER BY nome LIMIT 10");
        $s->execute([$like, $like, $like]);
        $resultados['cursos'] = $s->fetchAll();
        $total += count($resultados['cursos']);
    }

    if ($filtro === 'tudo' || $filtro === 'noticias') {
        $s = $pdo->prepare("SELECT n.id, n.titulo, n.categoria, n.data_publicacao AS data, u.nome AS subtitulo, 'noticias' AS _tipo FROM noticias n JOIN utilizadores u ON n.autor_id=u.id WHERE n.publicado=1 AND (n.titulo LIKE ? OR n.conteudo LIKE ? OR n.resumo LIKE ?) ORDER BY n.data_publicacao DESC LIMIT 10");
        $s->execute([$like, $like, $like]);
        $resultados['noticias'] = $s->fetchAll();
        $total += count($resultados['noticias']);
    }

    if ($filtro === 'tudo' || $filtro === 'eventos') {
        $s = $pdo->prepare("SELECT e.id, e.titulo, e.categoria, e.data_evento AS data, e.local AS subtitulo, 'eventos' AS _tipo FROM eventos e WHERE e.publicado=1 AND (e.titulo LIKE ? OR e.descricao LIKE ?) ORDER BY e.data_evento DESC LIMIT 10");
        $s->execute([$like, $like]);
        $resultados['eventos'] = $s->fetchAll();
        $total += count($resultados['eventos']);
    }
}

$paginaActiva = '';
$tituloBase   = 'Pesquisa';

$extraCSS = <<<'ENDCSS'
<style>
.pesquisa-wrap{max-width:800px;margin:0 auto;padding:56px 24px 80px;}
.pesquisa-hero{margin-bottom:40px;padding-bottom:32px;border-bottom:1px solid var(--cor-borda);}
.pesquisa-hero h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);margin-bottom:24px;}
.pesquisa-form{display:flex;gap:8px;}
.pesquisa-form input{flex:1;font-size:1rem;padding:12px 16px;}
.pesquisa-form button{white-space:nowrap;padding:12px 24px;}
.pesquisa-filtros{display:flex;gap:6px;flex-wrap:wrap;margin-top:16px;}
.p-tab{padding:5px 13px;border-radius:100px;font-size:.8rem;font-weight:500;text-decoration:none;border:1.5px solid var(--cor-borda);color:var(--cor-texto-claro);transition:all .15s;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.p-tab:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.p-tab.active{background:var(--cor-principal);border-color:var(--cor-principal);color:white;}
.pesquisa-meta{font-size:.85rem;color:var(--cor-texto-claro);margin-bottom:32px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.pesquisa-meta strong{color:var(--cor-texto);}
.result-group{margin-bottom:40px;}
.result-group-title{font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:14px;display:flex;align-items:center;gap:10px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.result-group-title::after{content:'';flex:1;height:1px;background:var(--cor-borda);}
.result-item{display:flex;align-items:flex-start;gap:14px;padding:16px 0;border-bottom:1px solid var(--cor-borda);text-decoration:none;color:inherit;transition:all .15s;}
.result-item:last-child{border-bottom:none;}
.result-item:hover .result-title{color:var(--cor-principal);}
.result-icon{width:40px;height:40px;border-radius:10px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);display:flex;align-items:center;justify-content:center;color:var(--cor-principal);flex-shrink:0;transition:background .15s;}
.result-item:hover .result-icon{background:var(--cor-principal);color:white;border-color:var(--cor-principal);}
.result-title{font-weight:700;font-size:.97rem;color:var(--cor-texto);margin-bottom:4px;line-height:1.3;transition:color .15s;}
.result-sub{font-size:.8rem;color:var(--cor-texto-claro);display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
.result-cat{display:inline-block;font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--cor-principal);border:1px solid var(--cor-principal);border-radius:100px;padding:1px 7px;}
.result-cta{font-size:.78rem;color:var(--cor-principal);font-weight:600;margin-left:auto;flex-shrink:0;}
.pesquisa-empty{text-align:center;padding:60px 20px;color:var(--cor-texto-claro);}
.pesquisa-empty svg{width:3rem;height:3rem;margin:0 auto 16px;display:block;opacity:.35;}
.highlight{background:rgba(244,164,66,.25);border-radius:3px;padding:0 2px;}
.tema-escuro .highlight{background:rgba(139,92,246,.3);}
</style>
ENDCSS;
require_once '_header.php';

// Highlight function
function hl(string $text, string $q): string {
    if (!$q) return htmlspecialchars($text);
    return preg_replace('/(' . preg_quote(htmlspecialchars($q), '/') . ')/iu',
        '<mark class="highlight">$1</mark>',
        htmlspecialchars($text));
}

$icones = [
    'cursos'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
    'noticias' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>',
    'eventos'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
];
$labels = ['cursos' => 'Cursos', 'noticias' => 'Notícias', 'eventos' => 'Eventos'];
$links  = ['cursos' => 'curso.php', 'noticias' => 'noticia.php', 'eventos' => 'evento.php'];
?>

<div class="pesquisa-wrap">
  <div class="pesquisa-hero">
    <h1><?= $q ? 'Resultados para "' . htmlspecialchars($q) . '"' : 'Pesquisa' ?></h1>
    <form class="pesquisa-form" method="GET">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Pesquisar cursos, notícias, eventos…" autofocus>
      <?php if ($filtro !== 'tudo'): ?><input type="hidden" name="tipo" value="<?= htmlspecialchars($filtro) ?>"><?php endif; ?>
      <button type="submit" class="btn">Pesquisar</button>
    </form>
    <div class="pesquisa-filtros">
      <?php foreach (['tudo' => 'Tudo', 'cursos' => 'Cursos', 'noticias' => 'Notícias', 'eventos' => 'Eventos'] as $val => $lbl): ?>
        <a href="pesquisa.php?q=<?= urlencode($q) ?>&tipo=<?= $val ?>" class="p-tab <?= $filtro===$val ? 'active' : '' ?>"><?= $lbl ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($q === ''): ?>
    <div class="pesquisa-empty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <p>Escreve algo para pesquisar no site.</p>
    </div>
  <?php elseif ($total === 0): ?>
    <div class="pesquisa-empty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <p>Nenhum resultado para <strong>"<?= htmlspecialchars($q) ?>"</strong>.<br>Tenta palavras diferentes ou mais curtas.</p>
    </div>
  <?php else: ?>
    <p class="pesquisa-meta"><strong><?= $total ?></strong> resultado<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?></p>

    <?php foreach ($resultados as $tipo => $items): ?>
      <?php if (empty($items)) continue; ?>
      <div class="result-group">
        <div class="result-group-title"><?= $labels[$tipo] ?> (<?= count($items) ?>)</div>
        <?php foreach ($items as $r): ?>
        <a href="<?= $links[$tipo] ?>?id=<?= $r['id'] ?>" class="result-item">
          <div class="result-icon"><?= $icones[$tipo] ?></div>
          <div style="flex:1;min-width:0;">
            <div class="result-title"><?= hl($r['titulo'], $q) ?></div>
            <div class="result-sub">
              <?php if (!empty($r['categoria'])): ?><span class="result-cat"><?= htmlspecialchars($r['categoria']) ?></span><?php endif; ?>
              <?php if (!empty($r['subtitulo'])): ?><span><?= htmlspecialchars($r['subtitulo']) ?></span><?php endif; ?>
              <?php if (!empty($r['data'])): ?><span><?= date('d M Y', strtotime($r['data'])) ?></span><?php endif; ?>
            </div>
          </div>
          <span class="result-cta">Ver →</span>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once '_footer.php'; ?>