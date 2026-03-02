<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$tipo   = $_GET['tipo'] ?? '';
$sql    = "SELECT * FROM cursos WHERE ativo = 1";
$params = [];
if ($tipo) { $sql .= " AND tipo = ?"; $params[] = $tipo; }
$sql .= " ORDER BY ordem, nome";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$cursos = $stmt->fetchAll();
$tipos  = $pdo->query("SELECT DISTINCT tipo FROM cursos WHERE ativo=1 AND tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);

// Contar disciplinas por curso (para mostrar no card)
$disc_count = [];
foreach ($cursos as $c) {
    $s = $pdo->prepare("SELECT COUNT(*) FROM curso_disciplinas WHERE curso_id = ?");
    $s->execute([$c['id']]);
    $disc_count[$c['id']] = $s->fetchColumn();
}

$paginaActiva = 'cursos';
$tituloBase   = 'Cursos';
$extraCSS = <<<'ENDCSS'
<style>
.cursos-section{margin-bottom:56px;}
.cursos-section-header{display:flex;align-items:center;gap:14px;margin-bottom:24px;}
.cursos-section-title{font-size:1rem;font-weight:700;color:var(--cor-texto);}
.cursos-section-count{font-size:.78rem;color:var(--cor-texto-claro);background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:100px;padding:2px 10px;}
.cursos-table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid var(--cor-borda);border-radius:12px;overflow:hidden;}
.cursos-table th{background:var(--cor-fundo-alt);padding:10px 16px;text-align:left;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--cor-texto-claro);border-bottom:1px solid var(--cor-borda);}
.cursos-table td{padding:14px 16px;border-bottom:1px solid var(--cor-borda);font-size:.9rem;color:var(--cor-texto);vertical-align:middle;}
.cursos-table tr:last-child td{border-bottom:none;}
.cursos-table tr:hover td{background:var(--cor-fundo-alt);}
.cursos-table .nome-cell{font-weight:600;}
.cursos-table .sigla-badge{display:inline-block;font-size:.72rem;font-weight:700;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:4px;padding:2px 7px;color:var(--cor-texto-claro);margin-left:8px;}
.cursos-table .anos-badge{display:inline-flex;align-items:center;gap:4px;font-size:.82rem;color:var(--cor-texto-claro);}
.cursos-table .cta-btn{display:inline-flex;align-items:center;gap:4px;color:var(--cor-principal);font-size:.82rem;font-weight:600;text-decoration:none;padding:5px 12px;border:1.5px solid var(--cor-principal);border-radius:7px;transition:all .15s;white-space:nowrap;}
.cursos-table .cta-btn:hover{background:var(--cor-principal);color:white;}
.view-toggle{display:flex;gap:4px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:8px;padding:3px;}
.view-btn{width:32px;height:28px;display:flex;align-items:center;justify-content:center;border:none;border-radius:5px;background:transparent;color:var(--cor-texto-claro);cursor:pointer;transition:all .15s;}
.view-btn.active{background:var(--cor-principal);color:white;}
.tipo-tabs{display:flex;gap:6px;flex-wrap:wrap;}
.tipo-tab{padding:5px 13px;border-radius:100px;font-size:.8rem;font-weight:500;text-decoration:none;border:1.5px solid var(--cor-borda);color:var(--cor-texto-claro);transition:all .15s;}
.tipo-tab:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.tipo-tab.active{background:var(--cor-principal);border-color:var(--cor-principal);color:white;}
.cursos-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;}
.curso-card-v2{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:14px;overflow:hidden;transition:border-color .2s,transform .2s,box-shadow .2s;display:flex;flex-direction:column;}
.curso-card-v2:hover{border-color:var(--cor-principal);transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.08);}
.curso-card-v2-img{height:160px;overflow:hidden;background:linear-gradient(135deg,var(--cor-principal),var(--cor-secundaria));flex-shrink:0;}
.curso-card-v2-img img{width:100%;height:100%;object-fit:cover;}
.curso-card-v2-body{padding:20px;flex:1;display:flex;flex-direction:column;gap:10px;}
.curso-card-v2-title{font-size:.97rem;font-weight:700;color:var(--cor-texto);}
.chip-sm{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:var(--cor-fundo);border:1px solid var(--cor-borda);border-radius:100px;font-size:.75rem;color:var(--cor-texto-claro);}
.curso-card-v2-desc{font-size:.85rem;color:var(--cor-texto-claro);line-height:1.6;flex:1;}
.curso-card-v2-footer{display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid var(--cor-borda);}
.empty-state{text-align:center;padding:80px 20px;color:var(--cor-texto-claro);}
@media(max-width:640px){.cursos-table{display:none;}.cursos-cards{display:grid!important;}}
</style>
ENDCSS;
require_once '_header.php';

// Agrupar por tipo
$por_tipo = [];
foreach ($cursos as $c) {
    $por_tipo[$c['tipo'] ?: 'Outros'][] = $c;
}
?>

<div class="container" style="padding:56px 24px 80px;">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:40px;padding-bottom:32px;border-bottom:1px solid var(--cor-borda);">
        <div>
            <div style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:10px;">Oferta formativa</div>
            <h1 style="font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);">Cursos</h1>
        </div>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <div class="tipo-tabs">
                <a href="cursos.php" class="tipo-tab <?= !$tipo ? 'active' : '' ?>">Todos</a>
                <?php foreach ($tipos as $t): ?>
                    <a href="cursos.php?tipo=<?= urlencode($t) ?>" class="tipo-tab <?= $tipo===$t ? 'active' : '' ?>"><?= htmlspecialchars($t) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="view-toggle" id="view-toggle">
                <button class="view-btn active" id="btn-table" onclick="setView('table')" title="Vista tabela">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </button>
                <button class="view-btn" id="btn-cards" onclick="setView('cards')" title="Vista cards">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </button>
            </div>
        </div>
    </div>

    <?php if (empty($cursos)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="width:3rem;height:3rem;margin-bottom:16px;opacity:.3;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <p>Nenhum curso disponível.</p>
        </div>
    <?php else: ?>

        <?php foreach ($por_tipo as $tipoNome => $lista): ?>
        <?php if ($tipo && $tipoNome !== $tipo) continue; ?>
        <div class="cursos-section">
            <div class="cursos-section-header">
                <div class="cursos-section-title"><?= htmlspecialchars($tipoNome) ?></div>
                <div class="cursos-section-count"><?= count($lista) ?> curso<?= count($lista)>1?'s':'' ?></div>
                <div style="flex:1;height:1px;background:var(--cor-borda);"></div>
            </div>

            <!-- Vista tabela -->
            <div class="view-table">
                <table class="cursos-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Duração</th>
                            <th>Disciplinas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $c): ?>
                        <tr>
                            <td class="nome-cell">
                                <?= sanitize($c['nome']) ?>
                                <span class="sigla-badge"><?= sanitize($c['sigla']) ?></span>
                            </td>
                            <td>
                                <span class="anos-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.8rem;height:.8rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?= $c['duracao_anos'] ?> ano<?= $c['duracao_anos']>1?'s':'' ?>
                                </span>
                            </td>
                            <td style="color:var(--cor-texto-claro);font-size:.85rem;">
                                <?= ($disc_count[$c['id']] ?: '—') ?> disciplinas
                            </td>
                            <td><a href="curso.php?id=<?= $c['id'] ?>" class="cta-btn">Ver curso →</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista cards -->
            <div class="cursos-cards" style="display:none;">
                <?php foreach ($lista as $c): ?>
                <div class="curso-card-v2">
                    <div class="curso-card-v2-img">
                        <?php if ($c['imagem']): ?>
                            <img src="uploads/<?= $c['imagem'] ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="curso-card-v2-body">
                        <div class="curso-card-v2-title"><?= sanitize($c['nome']) ?> <span style="font-weight:400;color:var(--cor-texto-claro);font-size:.85em;">(<?= sanitize($c['sigla']) ?>)</span></div>
                        <div class="curso-card-v2-chips">
                            <span class="chip-sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= $c['duracao_anos'] ?> anos</span>
                            <?php if ($disc_count[$c['id']]): ?><span class="chip-sm"><?= $disc_count[$c['id']] ?> disciplinas</span><?php endif; ?>
                        </div>
                        <div class="curso-card-v2-desc"><?= sanitize(substr($c['descricao'],0,110)) ?>…</div>
                        <div class="curso-card-v2-footer">
                            <span class="curso-card-v2-discs"></span>
                            <a href="curso.php?id=<?= $c['id'] ?>" class="cta-btn">Ver curso →</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<script>
function setView(v) {
    const iTable = v==='table';
    document.querySelectorAll('.view-table').forEach(el => el.style.display = iTable ? 'block' : 'none');
    document.querySelectorAll('.cursos-cards').forEach(el => el.style.display = iTable ? 'none' : 'grid');
    document.getElementById('btn-table').classList.toggle('active', iTable);
    document.getElementById('btn-cards').classList.toggle('active', !iTable);
    localStorage.setItem('cursos_view', v);
}
// Restaurar preferência
const saved = localStorage.getItem('cursos_view') || 'table';
if (saved === 'cards') setView('cards');
</script>

<?php require_once '_footer.php'; ?>
