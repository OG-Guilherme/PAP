<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id = ? AND ativo = 1");
$stmt->execute([$id]);
$curso = $stmt->fetch();
if (!$curso) { header('Location: cursos.php'); exit; }

$stmt = $pdo->prepare("SELECT d.*, cd.ano, cd.semestre 
                       FROM disciplinas d 
                       JOIN curso_disciplinas cd ON d.id = cd.disciplina_id 
                       WHERE cd.curso_id = ? 
                       ORDER BY cd.ano, cd.semestre, d.nome");
$stmt->execute([$id]);
$disciplinas = $stmt->fetchAll();

// Agrupar por ano
$por_ano = [];
foreach ($disciplinas as $d) {
    $por_ano[$d['ano']][] = $d;
}

$paginaActiva = 'cursos';
$tituloBase   = sanitize($curso['nome']);
$extraCSS = '<style>
.curso-wrap{max-width:860px;margin:0 auto;padding:40px 24px 80px;}
.curso-back{display:inline-flex;align-items:center;gap:6px;color:var(--cor-texto-claro);font-size:.85rem;text-decoration:none;margin-bottom:32px;transition:color .15s;}
.curso-back:hover{color:var(--cor-principal);}
.curso-hero-img{width:100%;max-height:400px;object-fit:cover;border-radius:16px;margin-bottom:32px;}
.curso-titulo{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:700;letter-spacing:-.02em;margin-bottom:8px;}
.curso-sigla{font-size:1rem;font-weight:400;color:var(--cor-texto-claro);}
.curso-meta-chips{display:flex;flex-wrap:wrap;gap:10px;margin:20px 0 36px;}
.chip{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:100px;font-size:.82rem;font-weight:500;color:var(--cor-texto-claro);}
.chip svg{color:var(--cor-principal);}
.section-label{font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:10px;}
.curso-section{margin-bottom:40px;}
.curso-section h2{font-size:1.25rem;font-weight:700;margin-bottom:14px;}
.curso-section p{font-size:.97rem;line-height:1.8;color:var(--cor-texto);}
.plano-ano{margin-bottom:32px;}
.plano-ano-label{font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-texto-claro);border-bottom:1px solid var(--cor-borda);padding-bottom:8px;margin-bottom:14px;}
.disciplina-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:14px 16px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-left:3px solid var(--cor-principal);border-radius:8px;margin-bottom:8px;transition:border-color .15s;}
.disciplina-row:hover{border-left-color:var(--cor-secundaria);}
.disciplina-nome{font-weight:600;font-size:.92rem;color:var(--cor-texto);}
.disciplina-sigla{font-size:.8rem;color:var(--cor-texto-claro);margin-top:2px;}
.disciplina-horas{font-size:.8rem;color:var(--cor-texto-claro);white-space:nowrap;flex-shrink:0;}
</style>';
require_once '_header.php';
?>

<div class="curso-wrap">
    <a href="cursos.php" class="curso-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar aos cursos
    </a>

    <?php if ($curso['imagem']): ?>
        <img src="uploads/<?= $curso['imagem'] ?>" alt="" class="curso-hero-img" loading="lazy">
    <?php endif; ?>

    <h1 class="curso-titulo">
        <?= sanitize($curso['nome']) ?>
        <span class="curso-sigla">(<?= sanitize($curso['sigla']) ?>)</span>
    </h1>

    <div class="curso-meta-chips">
        <span class="chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <?= sanitize($curso['tipo']) ?>
        </span>
        <span class="chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?= $curso['duracao_anos'] ?> anos
        </span>
    </div>

    <div class="curso-section">
        <div class="section-label">Descrição</div>
        <p><?= nl2br(sanitize($curso['descricao'])) ?></p>
    </div>

    <?php if ($curso['objetivos']): ?>
    <div class="curso-section">
        <div class="section-label">Objetivos</div>
        <p><?= nl2br(sanitize($curso['objetivos'])) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($curso['saidas_profissionais']): ?>
    <div class="curso-section">
        <div class="section-label">Saídas Profissionais</div>
        <p><?= nl2br(sanitize($curso['saidas_profissionais'])) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($por_ano)): ?>
    <div class="curso-section">
        <div class="section-label">Plano Curricular</div>
        <h2 style="margin-bottom:24px;">Disciplinas</h2>
        <?php foreach ($por_ano as $ano => $discs): ?>
        <div class="plano-ano">
            <div class="plano-ano-label"><?= $ano ?>.º Ano</div>
            <?php foreach ($discs as $d): ?>
            <div class="disciplina-row">
                <div>
                    <div class="disciplina-nome"><?= sanitize($d['nome']) ?></div>
                    <?php if ($d['sigla']): ?>
                        <div class="disciplina-sigla"><?= sanitize($d['sigla']) ?></div>
                    <?php endif; ?>
                    <?php if ($d['descricao']): ?>
                        <div style="font-size:.82rem;color:var(--cor-texto-claro);margin-top:6px;"><?= sanitize($d['descricao']) ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($d['carga_horaria']): ?>
                    <div class="disciplina-horas"><?= $d['carga_horaria'] ?>h</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once '_footer.php'; ?>