<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT e.*, u.nome as responsavel, u.foto_perfil
                       FROM eventos e
                       JOIN utilizadores u ON e.responsavel_id = u.id
                       WHERE e.id = ? AND e.publicado = 1");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if (!$evento) { header('Location: eventos.php'); exit; }

$pdo->prepare("UPDATE eventos SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);

$stmt = $pdo->prepare("SELECT c.*, u.nome, u.foto_perfil
                       FROM comentarios c
                       JOIN utilizadores u ON c.usuario_id = u.id
                       WHERE c.tipo = 'evento' AND c.item_id = ? AND c.aprovado = 1
                       ORDER BY c.data_criacao DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'], $_SESSION['user_id'])) {
    $stmt = $pdo->prepare("INSERT INTO comentarios (tipo, item_id, usuario_id, conteudo) VALUES ('evento', ?, ?, ?)");
    $stmt->execute([$id, $_SESSION['user_id'], trim($_POST['comentario'])]);
    header("Location: evento.php?id=$id");
    exit;
}

$paginaActiva = 'eventos';
$tituloBase   = htmlspecialchars($evento['titulo']);
$extraCSS = '<style>
.evento-hero img { width:100%; max-height:420px; object-fit:cover; border-radius:12px; margin:20px 0; }
.evento-meta { display:flex; flex-wrap:wrap; gap:14px; margin:16px 0 24px; font-size:.95rem; color:var(--cor-texto-claro); font-family:sans-serif; }
.evento-meta span { display:flex; align-items:center; gap:6px; }
.responsavel-box { display:flex; align-items:center; gap:12px; background:var(--cor-fundo-alt); border:1px solid var(--cor-borda); border-radius:10px; padding:14px 18px; margin:20px 0; }
.responsavel-box img { width:46px; height:46px; border-radius:50%; object-fit:cover; }
.inscricoes-box { background:var(--cor-fundo-alt); border:1px solid var(--cor-borda); border-left:4px solid var(--cor-principal); border-radius:10px; padding:18px 22px; margin:28px 0; font-family:sans-serif; }
.descricao { line-height:1.85; font-family:sans-serif; font-size:1.05rem; color:var(--cor-texto); margin:28px 0; }
.comentarios-section { margin-top:50px; }
.comentarios-section h2 { font-size:1.4rem; margin-bottom:20px; font-family:sans-serif; }
.comentario-form textarea { width:100%; padding:12px; border:1px solid var(--cor-borda); border-radius:8px; background:var(--cor-fundo-alt); color:var(--cor-texto); min-height:100px; font-family:sans-serif; font-size:.95rem; resize:vertical; }
.comentario-item { background:var(--cor-fundo-alt); border:1px solid var(--cor-borda); padding:16px; border-radius:10px; margin:14px 0; }
.comentario-header { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.comentario-header img { width:38px; height:38px; border-radius:50%; }
.comentario-header small { color:var(--cor-texto-claro); font-size:.8rem; display:block; }
</style>';
require_once '_header.php';

$svgCal = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
$svgPin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
$svgEye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
?>

<div class="container" style="padding-top:40px;padding-bottom:60px;max-width:860px;">
    <a href="eventos.php" style="color:var(--cor-principal);font-family:sans-serif;font-size:.9rem;text-decoration:none;">← Voltar aos eventos</a>

    <h1 style="font-family:Georgia,serif;font-size:2.2rem;margin:20px 0 8px;color:var(--cor-texto);">
        <?= htmlspecialchars($evento['titulo']) ?>
    </h1>
    <div class="evento-meta">
        <span><?= $svgCal ?> <?= date('d/m/Y H:i', strtotime($evento['data_evento'])) ?>
            <?php if ($evento['data_fim']): ?> → <?= date('d/m/Y H:i', strtotime($evento['data_fim'])) ?><?php endif; ?>
        </span>
        <?php if ($evento['local']): ?><span><?= $svgPin ?> <?= htmlspecialchars($evento['local']) ?></span><?php endif; ?>
        <span><?= $svgEye ?> <?= $evento['visualizacoes'] ?> visualizações</span>
    </div>
    <div class="responsavel-box">
        <?php if ($evento['foto_perfil']): ?><img src="uploads/<?= $evento['foto_perfil'] ?>" alt=""><?php endif; ?>
        <span style="font-family:sans-serif;">Organizado por <strong><?= htmlspecialchars($evento['responsavel']) ?></strong></span>
    </div>
    <?php if ($evento['imagem_destaque']): ?><img src="uploads/<?= $evento['imagem_destaque'] ?>" alt="" style="width:100%;max-height:420px;object-fit:cover;border-radius:12px;margin:20px 0;"><?php endif; ?>

    <div class="descricao"><?= nl2br(htmlspecialchars($evento['descricao'])) ?></div>

    <?php if ($evento['inscricoes_abertas']): ?>
        <div class="inscricoes-box">
            <strong>✅ Inscrições Abertas</strong>
            <?php if ($evento['capacidade']): ?><p style="margin-top:6px;color:var(--cor-texto-claro);">Capacidade: <?= $evento['capacidade'] ?> pessoas</p><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="comentarios-section">
        <h2>Comentários (<?= count($comentarios) ?>)</h2>
        <?php if (isLoggedIn()): ?>
            <form method="POST" style="margin-bottom:28px;">
                <textarea name="comentario" placeholder="Escreve o teu comentário..." required></textarea>
                <button type="submit" class="btn" style="margin-top:10px;">Enviar Comentário</button>
            </form>
        <?php else: ?>
            <p style="font-family:sans-serif;margin-bottom:20px;color:var(--cor-texto-claro);">
                <a href="login.php" style="color:var(--cor-principal);">Inicia sessão</a> para comentar.
            </p>
        <?php endif; ?>
        <?php foreach ($comentarios as $c): ?>
            <div class="comentario-item">
                <div class="comentario-header">
                    <?php if ($c['foto_perfil']): ?><img src="uploads/<?= $c['foto_perfil'] ?>" alt=""><?php endif; ?>
                    <div>
                        <strong style="font-family:sans-serif;"><?= htmlspecialchars($c['nome']) ?></strong>
                        <small><?= date('d/m/Y H:i', strtotime($c['data_criacao'])) ?></small>
                    </div>
                </div>
                <p style="font-family:sans-serif;line-height:1.7;"><?= nl2br(htmlspecialchars($c['conteudo'])) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '_footer.php'; ?>