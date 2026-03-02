<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$pesquisa  = trim($_GET['q']   ?? '');
$categoria = $_GET['cat']      ?? '';
$pagina    = max(1, (int)($_GET['p'] ?? 1));
$por_pag   = 9;

$sql    = "SELECT n.*, u.nome as autor FROM noticias n JOIN utilizadores u ON n.autor_id = u.id WHERE n.publicado = 1";
$params = [];
if ($pesquisa)  { $sql .= " AND (n.titulo LIKE ? OR n.conteudo LIKE ?)"; $params[] = "%$pesquisa%"; $params[] = "%$pesquisa%"; }
if ($categoria) { $sql .= " AND n.categoria = ?"; $params[] = $categoria; }
$sql .= " ORDER BY n.data_publicacao DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$todas = $stmt->fetchAll();
$total = count($todas);
$noticias = array_slice($todas, ($pagina-1)*$por_pag, $por_pag);
$cats = $pdo->query("SELECT DISTINCT categoria FROM noticias WHERE categoria IS NOT NULL AND publicado=1 ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

$paginaActiva = 'noticias';
$tituloBase   = 'Notícias';
$extraCSS = <<<'ENDCSS'
<style>
.news-featured{display:grid;grid-template-columns:1fr 1fr;gap:0;border-bottom:1px solid var(--cor-borda);margin-bottom:40px;text-decoration:none;color:inherit;}
.news-featured-img{height:380px;overflow:hidden;background:var(--cor-fundo-alt);}
.news-featured-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.news-featured:hover .news-featured-img img{transform:scale(1.03);}
.news-featured-body{padding:40px;display:flex;flex-direction:column;justify-content:center;border-left:1px solid var(--cor-borda);}
.news-featured-cat{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:14px;}
.news-featured-title{font-size:1.6rem;font-weight:700;line-height:1.25;letter-spacing:-.02em;color:var(--cor-texto);margin-bottom:14px;}
.news-featured-body .meta{font-size:.8rem;color:var(--cor-texto-claro);margin-bottom:16px;}
.news-featured-body .desc{font-size:.9rem;line-height:1.7;color:var(--cor-texto-claro);margin-bottom:24px;flex:1;}
.news-featured-cta{display:inline-flex;align-items:center;gap:6px;color:var(--cor-principal);font-size:.85rem;font-weight:700;text-decoration:none;transition:gap .15s;}
.news-featured:hover .news-featured-cta{gap:10px;}
.news-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1px;background:var(--cor-borda);border:1px solid var(--cor-borda);border-radius:12px;overflow:hidden;}
.news-item{background:var(--cor-fundo);padding:22px;display:flex;flex-direction:column;gap:10px;transition:background .15s;}
.news-item:hover{background:var(--cor-fundo-alt);}
.news-item-cat{font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);}
.news-item-title{font-size:.95rem;font-weight:700;color:var(--cor-texto);line-height:1.35;text-decoration:none;}
.news-item-title:hover{color:var(--cor-principal);}
.news-item-meta{font-size:.76rem;color:var(--cor-texto-claro);}
.news-item-img{height:160px;overflow:hidden;border-radius:8px;background:var(--cor-fundo-alt);margin-bottom:4px;}
.news-item-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s;}
.news-item:hover .news-item-img img{transform:scale(1.04);}
.search-row{display:flex;gap:8px;margin-bottom:16px;}
.search-row input{flex:1;min-width:0;}
.cat-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:32px;}
.cat-tab{padding:5px 13px;border-radius:100px;font-size:.8rem;font-weight:500;text-decoration:none;border:1.5px solid var(--cor-borda);color:var(--cor-texto-claro);transition:all .15s;}
.cat-tab:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.cat-tab.active{background:var(--cor-principal);border-color:var(--cor-principal);color:white;}
.empty-state{text-align:center;padding:80px 20px;color:var(--cor-texto-claro);}
@media(max-width:720px){.news-featured{grid-template-columns:1fr;}.news-featured-img{height:220px;}.news-featured-body{padding:24px;border-left:none;border-top:1px solid var(--cor-borda);}}
</style>
ENDCSS;
require_once '_header.php';
?>

<div class="container" style="padding:56px 24px 80px;">
    <div style="margin-bottom:40px;border-bottom:1px solid var(--cor-borda);padding-bottom:32px;">
        <div style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:10px;">Comunidade EduWeb</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);">Notícias</h1>
    </div>

    <form method="GET" style="max-width:none;">
        <div class="search-row">
            <input type="text" name="q" placeholder="Pesquisar notícias…" value="<?= htmlspecialchars($pesquisa) ?>">
            <?php if ($categoria): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($categoria) ?>"><?php endif; ?>
            <button type="submit" class="btn" style="white-space:nowrap;">Pesquisar</button>
            <?php if ($pesquisa || $categoria): ?>
                <a href="noticias.php" class="btn btn-outline" style="white-space:nowrap;">Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($cats)): ?>
    <div class="cat-tabs">
        <a href="noticias.php<?= $pesquisa ? '?q='.urlencode($pesquisa) : '' ?>" class="cat-tab <?= !$categoria ? 'active' : '' ?>">Todas</a>
        <?php foreach ($cats as $c): ?>
            <a href="noticias.php?cat=<?= urlencode($c) ?><?= $pesquisa ? '&q='.urlencode($pesquisa) : '' ?>" class="cat-tab <?= $c===$categoria ? 'active' : '' ?>"><?= htmlspecialchars($c) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($noticias)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="width:3rem;height:3rem;margin-bottom:16px;opacity:.3;"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
            <p>Nenhuma notícia encontrada.</p>
        </div>
    <?php else: ?>

        <?php $destaque = $noticias[0]; $resto = array_slice($noticias, 1); ?>

        <!-- Notícia em destaque -->
        <a href="noticia.php?id=<?= $destaque['id'] ?>" class="news-featured" style="text-decoration:none;">
            <div class="news-featured-img">
                <?php if ($destaque['imagem_destaque']): ?>
                    <img src="uploads/<?= $destaque['imagem_destaque'] ?>" alt="" loading="lazy">
                <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--cor-principal),var(--cor-secundaria));display:flex;align-items:center;justify-content:center;opacity:.3;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2" style="width:4rem;height:4rem;"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="news-featured-body">
                <?php if ($destaque['categoria']): ?>
                    <div class="news-featured-cat"><?= htmlspecialchars($destaque['categoria']) ?></div>
                <?php endif; ?>
                <div class="news-featured-title"><?= htmlspecialchars($destaque['titulo']) ?></div>
                <div class="meta"><?= htmlspecialchars($destaque['autor']) ?> · <?= date('d M Y', strtotime($destaque['data_publicacao'])) ?></div>
                <div class="desc"><?= htmlspecialchars(substr($destaque['resumo'] ?: $destaque['conteudo'], 0, 200)) ?>…</div>
                <span class="news-featured-cta">Ler artigo completo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </a>

        <?php if (!empty($resto)): ?>
        <div class="news-grid">
            <?php foreach ($resto as $n): ?>
            <div class="news-item">
                <?php if ($n['imagem_destaque']): ?>
                    <div class="news-item-img"><img src="uploads/<?= $n['imagem_destaque'] ?>" alt="" loading="lazy"></div>
                <?php endif; ?>
                <?php if ($n['categoria']): ?><div class="news-item-cat"><?= htmlspecialchars($n['categoria']) ?></div><?php endif; ?>
                <a href="noticia.php?id=<?= $n['id'] ?>" class="news-item-title"><?= htmlspecialchars($n['titulo']) ?></a>
                <div class="news-item-meta"><?= htmlspecialchars($n['autor']) ?> · <?= date('d M Y', strtotime($n['data_publicacao'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($total > $por_pag): ?>
        <div style="display:flex;justify-content:center;gap:8px;margin-top:40px;">
            <?php for ($i=1; $i<=ceil($total/$por_pag); $i++): ?>
                <a href="?p=<?= $i ?><?= $pesquisa ? '&q='.urlencode($pesquisa) : '' ?><?= $categoria ? '&cat='.urlencode($categoria) : '' ?>"
                   style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;text-decoration:none;font-size:.85rem;border:1.5px solid <?= $i===$pagina ? 'var(--cor-principal)' : 'var(--cor-borda)' ?>;background:<?= $i===$pagina ? 'var(--cor-principal)' : 'transparent' ?>;color:<?= $i===$pagina ? 'white' : 'var(--cor-texto-claro)' ?>;">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once '_footer.php'; ?>
