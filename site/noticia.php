<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT n.*, u.nome as autor, u.foto_perfil 
                       FROM noticias n 
                       JOIN utilizadores u ON n.autor_id = u.id 
                       WHERE n.id = ? AND n.publicado = 1");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
if (!$noticia) { header('Location: noticias.php'); exit; }

// Incrementar visualizações
$pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);

// Comentários
$stmt = $pdo->prepare("SELECT c.*, u.nome, u.foto_perfil 
                       FROM comentarios c 
                       JOIN utilizadores u ON c.usuario_id = u.id 
                       WHERE c.tipo = 'noticia' AND c.item_id = ? AND c.aprovado = 1 
                       ORDER BY c.data_criacao DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && isLoggedIn()) {
    $conteudo = trim($_POST['comentario'] ?? '');
    if ($conteudo) {
        $pdo->prepare("INSERT INTO comentarios (tipo, item_id, usuario_id, conteudo) VALUES ('noticia', ?, ?, ?)")
            ->execute([$id, $_SESSION['user_id'], $conteudo]);
        header("Location: noticia.php?id=$id");
        exit;
    }
}

$paginaActiva = 'noticias';
$tituloBase   = sanitize($noticia['titulo']);
require_once '_header.php';
?>

<div class="container" style="padding:40px 24px;max-width:860px;">
    <a href="noticias.php" style="color:var(--cor-icone,var(--cor-principal));text-decoration:none;font-family:sans-serif;font-size:.9rem;">← Voltar às notícias</a>

    <article style="margin-top:24px;">
        <h1 style="font-size:clamp(1.6rem,4vw,2.4rem);line-height:1.25;margin-bottom:16px;">
            <?= sanitize($noticia['titulo']) ?>
        </h1>

        <!-- Meta -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
            <?php if($noticia['foto_perfil']): ?>
                <img src="uploads/<?= $noticia['foto_perfil'] ?>" alt=""
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div style="width:38px;height:38px;border-radius:50%;background:var(--cor-principal);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-family:sans-serif;">
                    <?= mb_strtoupper(mb_substr($noticia['autor'],0,1)) ?>
                </div>
            <?php endif; ?>
            <div style="font-family:sans-serif;font-size:.85rem;">
                <div style="font-weight:600;color:var(--cor-texto);"><?= sanitize($noticia['autor']) ?></div>
                <div style="color:var(--cor-texto-claro);">
                    <?= date('d \d\e F \d\e Y', strtotime($noticia['data_publicacao'])) ?>
                    <?php if($noticia['categoria']): ?>
                        · <span style="color:var(--cor-icone,var(--cor-principal));"><?= sanitize($noticia['categoria']) ?></span>
                    <?php endif; ?>
                    · <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:0.85rem;height:0.85rem;vertical-align:middle;display:inline-block;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> <?= $noticia['visualizacoes'] ?> visualizações
                </div>
            </div>
        </div>

        <?php if($noticia['imagem_destaque']): ?>
            <img src="uploads/<?= $noticia['imagem_destaque'] ?>" alt=""
                 loading="lazy" decoding="async"
                 style="width:100%;max-height:460px;object-fit:cover;border-radius:12px;margin-bottom:32px;">
        <?php endif; ?>

        <div style="line-height:1.9;font-size:1.05rem;color:var(--cor-texto);">
            <?= nl2br(sanitize($noticia['conteudo'])) ?>
        </div>
    </article>

    <!-- Comentários -->
    <section style="margin-top:60px;border-top:1px solid var(--cor-borda);padding-top:40px;">
        <h2 style="margin-bottom:24px;">Comentários (<?= count($comentarios) ?>)</h2>

        <?php if(isLoggedIn()): ?>
            <form method="POST" style="margin-bottom:32px;max-width:none;">
                <textarea name="comentario" placeholder="Escreve o teu comentário..." required
                          style="margin-bottom:10px;"></textarea>
                <button type="submit" class="btn">Publicar Comentário</button>
            </form>
        <?php else: ?>
            <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:10px;padding:20px;margin-bottom:28px;font-family:sans-serif;font-size:.9rem;">
                <a href="login.php" style="color:var(--cor-icone,var(--cor-principal));font-weight:600;">Inicia sessão</a> para comentar.
            </div>
        <?php endif; ?>

        <div style="display:grid;gap:16px;">
            <?php foreach($comentarios as $c): ?>
            <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:10px;padding:18px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <?php if($c['foto_perfil']): ?>
                        <img src="uploads/<?= $c['foto_perfil'] ?>" alt=""
                             style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:36px;height:36px;border-radius:50%;background:var(--cor-principal);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-family:sans-serif;font-size:.9rem;">
                            <?= mb_strtoupper(mb_substr($c['nome'],0,1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <strong style="font-family:sans-serif;font-size:.9rem;"><?= sanitize($c['nome']) ?></strong>
                        <small style="display:block;color:var(--cor-texto-claro);font-family:sans-serif;">
                            <?= date('d/m/Y H:i', strtotime($c['data_criacao'])) ?>
                        </small>
                    </div>
                </div>
                <p style="line-height:1.7;font-size:.95rem;"><?= nl2br(sanitize($c['conteudo'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once '_footer.php'; ?>
