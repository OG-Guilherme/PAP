<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT n.*, u.nome as autor, u.foto_perfil FROM noticias n JOIN utilizadores u ON n.autor_id = u.id WHERE n.id = ? AND n.publicado = 1");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
if (!$noticia) { header('Location: noticias.php'); exit; }

$pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);

$stmt = $pdo->prepare("SELECT c.*, u.nome, u.foto_perfil FROM comentarios c JOIN utilizadores u ON c.usuario_id = u.id WHERE c.tipo = 'noticia' AND c.item_id = ? AND c.aprovado = 1 ORDER BY c.data_criacao DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && isLoggedIn()) {
    $conteudo = trim($_POST['comentario'] ?? '');
    if ($conteudo) {
        $pdo->prepare("INSERT INTO comentarios (tipo, item_id, usuario_id, conteudo) VALUES ('noticia', ?, ?, ?)")
            ->execute([$id, $_SESSION['user_id'], $conteudo]);
        header("Location: noticia.php?id=$id"); exit;
    }
}

$paginaActiva = 'noticias';
$tituloBase   = sanitize($noticia['titulo']);
$extraCSS = '<style>
.article-wrap{max-width:760px;margin:0 auto;padding:40px 24px 80px;}
.article-back{display:inline-flex;align-items:center;gap:6px;color:var(--cor-texto-claro);font-size:.85rem;text-decoration:none;margin-bottom:36px;transition:color .15s;}
.article-back:hover{color:var(--cor-principal);}
.article-cat{font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:12px;}
.article-title{font-size:clamp(1.7rem,4vw,2.4rem);font-weight:700;line-height:1.2;letter-spacing:-.02em;margin-bottom:20px;color:var(--cor-texto);}
.article-byline{display:flex;align-items:center;gap:12px;padding-bottom:20px;border-bottom:1px solid var(--cor-borda);margin-bottom:28px;}
.byline-avatar{width:40px;height:40px;border-radius:50%;background:var(--cor-principal);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.9rem;flex-shrink:0;overflow:hidden;}
.byline-avatar img{width:100%;height:100%;object-fit:cover;}
.byline-name{font-weight:600;font-size:.9rem;color:var(--cor-texto);}
.byline-date{font-size:.8rem;color:var(--cor-texto-claro);}
.article-img{width:100%;max-height:460px;object-fit:cover;border-radius:14px;margin-bottom:32px;}
.article-body{font-size:1.05rem;line-height:1.85;color:var(--cor-texto);}
.comments-section{margin-top:64px;padding-top:40px;border-top:1px solid var(--cor-borda);}
.comments-title{font-size:1.2rem;font-weight:700;margin-bottom:28px;}
.comment-form textarea{margin-bottom:12px;}
.comment-card{background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:12px;padding:18px;margin-bottom:14px;}
.comment-header{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.comment-avatar{width:36px;height:36px;border-radius:50%;background:var(--cor-principal);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.82rem;flex-shrink:0;overflow:hidden;}
.comment-avatar img{width:100%;height:100%;object-fit:cover;}
.comment-name{font-weight:600;font-size:.88rem;}
.comment-date{font-size:.76rem;color:var(--cor-texto-claro);}
.comment-body{font-size:.92rem;line-height:1.7;}
.login-prompt{background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:12px;padding:18px 22px;margin-bottom:24px;font-size:.9rem;color:var(--cor-texto-claro);}
.login-prompt a{color:var(--cor-principal);font-weight:600;text-decoration:none;}
</style>';
require_once '_header.php';
?>

<div class="article-wrap">
    <a href="noticias.php" class="article-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar às notícias
    </a>

    <?php if ($noticia['categoria']): ?>
        <div class="article-cat"><?= sanitize($noticia['categoria']) ?></div>
    <?php endif; ?>

    <h1 class="article-title"><?= sanitize($noticia['titulo']) ?></h1>

    <div class="article-byline">
        <div class="byline-avatar">
            <?php if ($noticia['foto_perfil']): ?>
                <img src="uploads/<?= $noticia['foto_perfil'] ?>" alt="">
            <?php else: ?>
                <?= mb_strtoupper(mb_substr($noticia['autor'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <div class="byline-name"><?= sanitize($noticia['autor']) ?></div>
            <div class="byline-date">
                <?= date('d \d\e F \d\e Y', strtotime($noticia['data_publicacao'])) ?>
                · <?= $noticia['visualizacoes'] ?> visualizações
            </div>
        </div>
    </div>

    <?php if ($noticia['imagem_destaque']): ?>
        <img src="uploads/<?= $noticia['imagem_destaque'] ?>" alt="" class="article-img" loading="lazy">
    <?php endif; ?>

    <div class="article-body">
        <?= nl2br(sanitize($noticia['conteudo'])) ?>
    </div>

    <!-- Comentários -->
    <div class="comments-section">
        <div class="comments-title">Comentários (<?= count($comentarios) ?>)</div>

        <?php if (isLoggedIn()): ?>
            <form method="POST" class="comment-form" style="max-width:none;margin-bottom:32px;">
                <textarea name="comentario" placeholder="Deixa o teu comentário…" required></textarea>
                <button type="submit" class="btn">Publicar</button>
            </form>
        <?php else: ?>
            <div class="login-prompt">
                <a href="login.php">Inicia sessão</a> para deixar um comentário.
            </div>
        <?php endif; ?>

        <?php foreach ($comentarios as $c): ?>
        <div class="comment-card">
            <div class="comment-header">
                <div class="comment-avatar">
                    <?php if ($c['foto_perfil']): ?>
                        <img src="uploads/<?= $c['foto_perfil'] ?>" alt="">
                    <?php else: ?>
                        <?= mb_strtoupper(mb_substr($c['nome'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="comment-name"><?= sanitize($c['nome']) ?></div>
                    <div class="comment-date"><?= date('d/m/Y H:i', strtotime($c['data_criacao'])) ?></div>
                </div>
            </div>
            <div class="comment-body"><?= nl2br(sanitize($c['conteudo'])) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '_footer.php'; ?>