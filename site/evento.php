<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT e.*, u.nome as responsavel, u.foto_perfil FROM eventos e JOIN utilizadores u ON e.responsavel_id = u.id WHERE e.id = ? AND e.publicado = 1");
$stmt->execute([$id]);
$evento = $stmt->fetch();
if (!$evento) { header('Location: eventos.php'); exit; }

$pdo->prepare("UPDATE eventos SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);

$stmt = $pdo->prepare("SELECT c.*, u.nome, u.foto_perfil FROM comentarios c JOIN utilizadores u ON c.usuario_id = u.id WHERE c.tipo = 'evento' AND c.item_id = ? AND c.aprovado = 1 ORDER BY c.data_criacao DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'], $_SESSION['user_id'])) {
    $conteudo = trim($_POST['comentario']);
    if ($conteudo) {
        $pdo->prepare("INSERT INTO comentarios (tipo, item_id, usuario_id, conteudo) VALUES ('evento', ?, ?, ?)")
            ->execute([$id, $_SESSION['user_id'], $conteudo]);
        header("Location: evento.php?id=$id"); exit;
    }
}

$paginaActiva = 'eventos';
$tituloBase   = htmlspecialchars($evento['titulo']);
$extraCSS = '<style>
.article-wrap{max-width:760px;margin:0 auto;padding:40px 24px 80px;}
.article-back{display:inline-flex;align-items:center;gap:6px;color:var(--cor-texto-claro);font-size:.85rem;text-decoration:none;margin-bottom:36px;transition:color .15s;}
.article-back:hover{color:var(--cor-principal);}
.article-cat{font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:12px;}
.article-title{font-size:clamp(1.7rem,4vw,2.4rem);font-weight:700;line-height:1.2;letter-spacing:-.02em;margin-bottom:20px;color:var(--cor-texto);}
.evento-meta-row{display:flex;flex-wrap:wrap;gap:16px;padding:18px 0;border-top:1px solid var(--cor-borda);border-bottom:1px solid var(--cor-borda);margin-bottom:28px;}
.evento-meta-item{display:flex;align-items:center;gap:8px;font-size:.88rem;color:var(--cor-texto-claro);}
.evento-meta-item svg{color:var(--cor-principal);flex-shrink:0;}
.evento-organizer{display:flex;align-items:center;gap:12px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:12px;padding:14px 18px;margin-bottom:28px;}
.organizer-avatar{width:44px;height:44px;border-radius:50%;background:var(--cor-principal);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;overflow:hidden;}
.organizer-avatar img{width:100%;height:100%;object-fit:cover;}
.article-hero-img{width:100%;max-height:420px;object-fit:cover;border-radius:14px;margin-bottom:28px;}
.article-body{font-size:1.05rem;line-height:1.85;color:var(--cor-texto);margin-bottom:32px;}
.inscricoes-box{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-principal);border-radius:12px;padding:18px 22px;margin-bottom:32px;display:flex;align-items:center;gap:14px;}
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

$ts = strtotime($evento['data_evento']);
?>

<div class="article-wrap">
    <a href="eventos.php" class="article-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar aos eventos
    </a>

    <?php if ($evento['categoria']): ?>
        <div class="article-cat"><?= htmlspecialchars($evento['categoria']) ?></div>
    <?php endif; ?>

    <h1 class="article-title"><?= htmlspecialchars($evento['titulo']) ?></h1>

    <div class="evento-meta-row">
        <div class="evento-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <?= date('d/m/Y H:i', $ts) ?>
            <?php if ($evento['data_fim']): ?> → <?= date('d/m/Y H:i', strtotime($evento['data_fim'])) ?><?php endif; ?>
        </div>
        <?php if ($evento['local']): ?>
        <div class="evento-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= htmlspecialchars($evento['local']) ?>
        </div>
        <?php endif; ?>
        <div class="evento-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <?= $evento['visualizacoes'] ?> visualizações
        </div>
    </div>

    <div class="evento-organizer">
        <div class="organizer-avatar">
            <?php if ($evento['foto_perfil']): ?>
                <img src="uploads/<?= $evento['foto_perfil'] ?>" alt="">
            <?php else: ?>
                <?= mb_strtoupper(mb_substr($evento['responsavel'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div style="font-size:.9rem;">
            <div style="color:var(--cor-texto-claro);font-size:.75rem;margin-bottom:2px;">Organizado por</div>
            <div style="font-weight:600;color:var(--cor-texto);"><?= htmlspecialchars($evento['responsavel']) ?></div>
        </div>
    </div>

    <?php if ($evento['imagem_destaque']): ?>
        <img src="uploads/<?= $evento['imagem_destaque'] ?>" alt="" class="article-hero-img" loading="lazy">
    <?php endif; ?>

    <div class="article-body"><?= nl2br(htmlspecialchars($evento['descricao'])) ?></div>

    <?php if ($evento['inscricoes_abertas']): ?>
        <div class="inscricoes-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1.4rem;height:1.4rem;color:var(--cor-principal);flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>
                <div style="font-weight:700;font-size:.95rem;">Inscrições Abertas</div>
                <?php if ($evento['capacidade']): ?>
                    <div style="font-size:.82rem;color:var(--cor-texto-claro);margin-top:2px;">Capacidade: <?= $evento['capacidade'] ?> pessoas</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

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
                <a href="login.php">Inicia sessão</a> para comentar.
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