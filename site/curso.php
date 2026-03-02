<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id = ? AND ativo = 1");
$stmt->execute([$id]);
$curso = $stmt->fetch();
if (!$curso) { header('Location: cursos.php'); exit; }

// Buscar disciplinas do curso
$stmt = $pdo->prepare("SELECT d.*, cd.ano, cd.semestre 
                       FROM disciplinas d 
                       JOIN curso_disciplinas cd ON d.id = cd.disciplina_id 
                       WHERE cd.curso_id = ? 
                       ORDER BY cd.ano, cd.semestre, d.nome");
$stmt->execute([$id]);
$disciplinas = $stmt->fetchAll();

$paginaActiva = 'cursos';
$tituloBase   = htmlspecialchars($curso['nome']);
require_once '_header.php';
?>

<div class="container" style="padding:40px 24px;">
    <a href="cursos.php" style="color:var(--cor-principal);text-decoration:none;font-family:sans-serif;font-size:.9rem;">← Voltar aos cursos</a>

    <div style="margin:24px 0;">
        <h1 style="font-size:clamp(1.6rem,4vw,2.4rem);margin-bottom:8px;">
            <?= sanitize($curso['nome']) ?>
            <span style="font-size:1rem;color:var(--cor-texto-claro);font-weight:400;">(<?= sanitize($curso['sigla']) ?>)</span>
        </h1>

        <?php if($curso['imagem']): ?>
            <img src="uploads/<?= $curso['imagem'] ?>" alt="" 
                 loading="lazy" decoding="async"
                 style="width:100%;max-height:420px;object-fit:cover;border-radius:12px;margin:20px 0;">
        <?php endif; ?>
    </div>

    <!-- Info box -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:36px;">
        <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:10px;padding:18px;">
            <div style="font-size:.75rem;color:var(--cor-texto-claro);text-transform:uppercase;letter-spacing:.06em;font-family:sans-serif;margin-bottom:4px;">Tipo</div>
            <div style="font-weight:600;"><?= sanitize($curso['tipo']) ?></div>
        </div>
        <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:10px;padding:18px;">
            <div style="font-size:.75rem;color:var(--cor-texto-claro);text-transform:uppercase;letter-spacing:.06em;font-family:sans-serif;margin-bottom:4px;">Duração</div>
            <div style="font-weight:600;"><?= $curso['duracao_anos'] ?> anos</div>
        </div>
    </div>

    <h2>Descrição</h2>
    <p style="line-height:1.8;margin:16px 0 32px;color:var(--cor-texto);">
        <?= nl2br(sanitize($curso['descricao'])) ?>
    </p>

    <?php if($curso['objetivos']): ?>
        <h2>Objetivos</h2>
        <p style="line-height:1.8;margin:16px 0 32px;color:var(--cor-texto);">
            <?= nl2br(sanitize($curso['objetivos'])) ?>
        </p>
    <?php endif; ?>

    <?php if($curso['saidas_profissionais']): ?>
        <h2>Saídas Profissionais</h2>
        <p style="line-height:1.8;margin:16px 0 32px;color:var(--cor-texto);">
            <?= nl2br(sanitize($curso['saidas_profissionais'])) ?>
        </p>
    <?php endif; ?>

    <?php if(!empty($disciplinas)): ?>
        <h2>Plano Curricular</h2>
        <?php
        $ano_atual = 0;
        foreach($disciplinas as $d):
            if($d['ano'] != $ano_atual) {
                if($ano_atual > 0) echo '</div>';
                echo '<h3 style="margin:28px 0 14px;font-size:1rem;font-family:sans-serif;color:var(--cor-texto-claro);text-transform:uppercase;letter-spacing:.06em;">' . $d['ano'] . '.º Ano</h3>';
                echo '<div style="display:grid;gap:10px;">';
                $ano_atual = $d['ano'];
            }
        ?>
            <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-left:4px solid var(--cor-principal);border-radius:8px;padding:16px;">
                <strong><?= sanitize($d['nome']) ?></strong>
                <?php if($d['sigla']): ?>
                    <span style="color:var(--cor-texto-claro);font-size:.85rem;"> (<?= sanitize($d['sigla']) ?>)</span>
                <?php endif; ?>
                <?php if($d['carga_horaria']): ?>
                    <span style="float:right;font-size:.82rem;color:var(--cor-texto-claro);font-family:sans-serif;"><?= $d['carga_horaria'] ?>h</span>
                <?php endif; ?>
                <?php if($d['descricao']): ?>
                    <p style="margin-top:8px;font-size:.88rem;color:var(--cor-texto-claro);"><?= sanitize($d['descricao']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '_footer.php'; ?>
