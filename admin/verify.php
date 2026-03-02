<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'verify';
$page_title  = 'Pasta Verify';

// Path to the verify folder (relative to admin/)
$verify_dir = dirname(__DIR__) . '/verify/';
$files      = [];

if (is_dir($verify_dir)) {
    $all = scandir($verify_dir);
    foreach ($all as $f) {
        if ($f === '.' || $f === '..') continue;
        $full = $verify_dir . $f;
        if (is_file($full)) {
            $files[] = [
                'name'    => $f,
                'ext'     => strtolower(pathinfo($f, PATHINFO_EXTENSION)),
                'size'    => filesize($full),
                'mtime'   => filemtime($full),
                'path'    => $full,
            ];
        }
    }
    usort($files, fn($a, $b) => strcmp($a['name'], $b['name']));
}

// View a specific file
$view_file   = null;
$view_content= '';
if (isset($_GET['ver']) && !empty($files)) {
    $requested = basename($_GET['ver']); // sanitize
    foreach ($files as $f) {
        if ($f['name'] === $requested) {
            $view_file   = $f;
            $view_content= file_get_contents($f['path']);
            break;
        }
    }
}

// Extension → icon
function fileIcon($ext) {
    return match ($ext) {
        'php'  => '🐘',
        'html','htm' => '🌐',
        'css'  => '🎨',
        'js'   => '⚡',
        'sql'  => '🗃️',
        'txt'  => '📄',
        'json' => '📦',
        'md'   => '📝',
        'jpg','jpeg','png','gif','webp' => '🖼️',
        'pdf'  => '📕',
        default => '📁',
    };
}

function formatBytes($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes/1024, 1) . ' KB';
    return round($bytes/1048576, 1) . ' MB';
}

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>🔍 Pasta Verify</h2>
        <p><?php echo count($files); ?> ficheiro(s) encontrado(s) em <code style="font-size:0.78rem; background:var(--surface2); padding:2px 6px; border-radius:4px;">/verify/</code></p>
    </div>
    <?php if ($view_file): ?>
        <a href="verify.php" class="btn btn-ghost">← Voltar à lista</a>
    <?php endif; ?>
</div>

<?php if (!$view_file): ?>
<!-- FILE GRID -->
<?php if (empty($files)): ?>
    <div class="card" style="text-align:center; padding:50px; color:var(--text-muted);">
        <p style="font-size:2rem; margin-bottom:16px;">📂</p>
        <p>A pasta <code>/verify/</code> está vazia ou não existe.</p>
        <p style="margin-top:8px; font-size:0.85rem;">Crie a pasta <code><?php echo htmlspecialchars($verify_dir); ?></code> e coloque ficheiros lá.</p>
    </div>
<?php else: ?>
    <div class="file-list-grid" style="margin-bottom:24px;">
        <?php foreach ($files as $f): ?>
        <a href="verify.php?ver=<?php echo urlencode($f['name']); ?>" class="file-card">
            <span class="file-icon"><?php echo fileIcon($f['ext']); ?></span>
            <div>
                <div class="file-name"><?php echo htmlspecialchars($f['name']); ?></div>
                <div class="file-ext">
                    <?php echo strtoupper($f['ext'] ?: 'ficheiro'); ?> &middot; <?php echo formatBytes($f['size']); ?>
                    <br><?php echo date('d/m/Y H:i', $f['mtime']); ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php else: ?>
<!-- FILE VIEWER -->
<div class="card" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <span style="font-size:1.3rem;"><?php echo fileIcon($view_file['ext']); ?></span>
            <strong style="margin-left:8px;"><?php echo htmlspecialchars($view_file['name']); ?></strong>
            <span style="color:var(--text-muted); font-size:0.82rem; margin-left:12px;">
                <?php echo formatBytes($view_file['size']); ?> &middot; 
                Modificado: <?php echo date('d/m/Y H:i', $view_file['mtime']); ?>
            </span>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="../verify/<?php echo urlencode($view_file['name']); ?>" target="_blank" class="btn btn-ghost btn-sm">🔗 Abrir página</a>
            <a href="verify.php" class="btn btn-ghost btn-sm">← Lista</a>
        </div>
    </div>
</div>

<?php
// For PHP/HTML files, offer a rendered view in an iframe AND source view
$can_render = in_array($view_file['ext'], ['php', 'html', 'htm']);
$show_source = isset($_GET['fonte']) || !$can_render;
?>

<?php if ($can_render): ?>
<div style="margin-bottom: 16px; display:flex; gap:8px;">
    <a href="verify.php?ver=<?php echo urlencode($view_file['name']); ?>" 
       class="btn <?php echo !$show_source ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">🌐 Pré-visualização</a>
    <a href="verify.php?ver=<?php echo urlencode($view_file['name']); ?>&fonte=1" 
       class="btn <?php echo $show_source ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">📄 Código Fonte</a>
</div>
<?php endif; ?>

<?php if ($can_render && !$show_source): ?>
<!-- Iframe preview -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="file-viewer-header">
        <span>pré-visualização</span>
        <span class="filename"><?php echo htmlspecialchars($view_file['name']); ?></span>
    </div>
    <iframe src="../verify/<?php echo urlencode($view_file['name']); ?>"
            style="width:100%; height:650px; border:none; display:block; background:white;"
            sandbox="allow-same-origin allow-scripts allow-forms">
    </iframe>
</div>
<?php else: ?>
<!-- Source code viewer -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="file-viewer-header">
        <span>código fonte</span>
        <span class="filename"><?php echo htmlspecialchars($view_file['name']); ?></span>
    </div>
    <div class="file-viewer"><?php
        $lines = explode("\n", $view_content);
        $i     = 1;
        foreach ($lines as $line) {
            echo '<span style="color:#484f58; display:inline-block; width:36px; text-align:right; margin-right:16px; user-select:none;">' . $i . '</span>';
            echo htmlspecialchars($line) . "\n";
            $i++;
        }
    ?></div>
</div>
<?php endif; ?>

<?php endif; // view_file ?>

<?php require '_footer.php'; ?>
