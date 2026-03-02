<?php
session_start();
require_once '../important/config.php';
requireAdmin();

$active_page = 'dashboard';
$page_title  = 'Dashboard';

$stats['noticias']     = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$stats['eventos']      = $pdo->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
$stats['cursos']       = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
$stats['utilizadores'] = $pdo->query("SELECT COUNT(*) FROM utilizadores")->fetchColumn();
$stats['comentarios']  = $pdo->query("SELECT COUNT(*) FROM comentarios WHERE aprovado = 0")->fetchColumn();
$logs = $pdo->query("SELECT l.*, u.nome FROM logs_admin l JOIN utilizadores u ON l.usuario_id = u.id ORDER BY l.data_criacao DESC LIMIT 10")->fetchAll();

$verifyPath  = __DIR__ . '/verify';
$verifyFiles = [];
if (is_dir($verifyPath)) {
    foreach (scandir($verifyPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fp = $verifyPath . '/' . $item;
        $verifyFiles[] = ['name'=>$item,'size'=>is_file($fp)?filesize($fp):null,'mtime'=>filemtime($fp),'is_dir'=>is_dir($fp)];
    }
    usort($verifyFiles, fn($a,$b) => $b['mtime']-$a['mtime']);
}

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>📊 Dashboard</h2>
        <p>Visão geral do EduWeb</p>
    </div>
</div>

<!-- Stat cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:28px;">
    <?php
    $statCards = [
        ['label'=>'NOTÍCIAS',     'val'=>$stats['noticias'],     'color'=>'#3b82f6', 'href'=>'noticias.php'],
        ['label'=>'EVENTOS',      'val'=>$stats['eventos'],      'color'=>'#f59e0b', 'href'=>'eventos.php'],
        ['label'=>'CURSOS',       'val'=>$stats['cursos'],       'color'=>'#10b981', 'href'=>'cursos.php'],
        ['label'=>'UTILIZADORES', 'val'=>$stats['utilizadores'], 'color'=>'#8b5cf6', 'href'=>'utilizadores.php'],
        ['label'=>'PENDENTES',    'val'=>$stats['comentarios'],  'color'=>'#ef4444', 'href'=>'comentarios.php'],
    ];
    foreach ($statCards as $sc): ?>
    <a href="<?= $sc['href'] ?>" style="text-decoration:none;">
        <div class="card" style="border-top:3px solid <?= $sc['color'] ?>;padding:20px;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="font-size:.7rem;color:var(--text-muted);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;font-weight:600;"><?= $sc['label'] ?></div>
            <div style="font-size:2.4rem;font-weight:800;color:var(--text);line-height:1;"><?= $sc['val'] ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Pasta Verify -->
<?php if (!empty($verifyFiles)): ?>
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <h3 class="card-title" style="margin:0;">🔍 Pasta Verify</h3>
        <a href="verify.php" style="color:var(--admin-accent);font-size:.85rem;">Ver todos →</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
        <?php foreach (array_slice($verifyFiles, 0, 6) as $vf):
            $ext  = $vf['is_dir'] ? 'dir' : strtolower(pathinfo($vf['name'], PATHINFO_EXTENSION));
            $icon = match(true) {
                $vf['is_dir']                                     => '📁',
                $ext === 'php'                                    => '🐘',
                $ext === 'pdf'                                    => '📄',
                in_array($ext,['jpg','jpeg','png','gif','webp'])  => '🖼️',
                in_array($ext,['txt','md'])                       => '📝',
                default                                           => '📎'
            };
            $size = $vf['size'] !== null ? ($vf['size'] > 1024 ? round($vf['size']/1024,1).'KB' : $vf['size'].'B') : 'pasta';
        ?>
        <a href="verify.php?file=<?= urlencode($vf['name']) ?>"
           style="border:1px solid var(--border);border-radius:10px;padding:14px;display:flex;align-items:center;gap:10px;text-decoration:none;color:var(--text);background:var(--surface2);transition:all .2s;"
           onmouseover="this.style.borderColor=getComputedStyle(document.documentElement).getPropertyValue('--admin-accent');this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='';this.style.transform=''">
            <span style="font-size:1.6rem;flex-shrink:0;"><?= $icon ?></span>
            <div>
                <div style="font-weight:600;font-size:.85rem;word-break:break-all;"><?= htmlspecialchars($vf['name']) ?></div>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;"><?= $size ?> · <?= date('d/m/Y', $vf['mtime']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Últimas atividades -->
<div class="card">
    <h3 class="card-title">📋 Últimas Atividades</h3>
    <?php if (empty($logs)): ?>
        <p style="color:var(--text-muted);font-style:italic;margin-top:10px;">Sem atividades registadas.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Utilizador</th><th>Ação</th><th>Tipo</th><th>Data</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['nome']) ?></td>
                        <td><?= htmlspecialchars($l['acao']) ?></td>
                        <td><span class="badge badge-<?= $l['tipo'] ?>"><?= strtoupper($l['tipo']) ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($l['data_criacao'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require '_footer.php'; ?>