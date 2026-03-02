<?php
/**
 * _header.php - Admin shared header
 * Include at top of each admin page AFTER session_start() and auth check.
 * Requires $pdo, $_SESSION['user_nome'], $active_page (string like 'dashboard')
 */

// Read theme from session — inherit from main site
// Main site uses $_SESSION['tema'] ('claro'/'escuro')
// Admin also supports $_SESSION['admin_theme'] for override
// We sync: if main site changed, admin follows
$admin_theme = 'light';
if (isset($_SESSION['tema'])) {
    $admin_theme = $_SESSION['tema'] === 'escuro' ? 'dark' : 'light';
}
if (isset($_SESSION['admin_theme'])) {
    $admin_theme = $_SESSION['admin_theme'];
}

// Handle toggle
if (isset($_GET['toggle_admin_theme'])) {
    $admin_theme = ($admin_theme === 'light') ? 'dark' : 'light';
    $_SESSION['admin_theme'] = $admin_theme;
    // Also sync main site theme
    $_SESSION['tema'] = ($admin_theme === 'dark') ? 'escuro' : 'claro';
    $redirect_to = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $redirect_to);
    exit;
}

$pending_comments = $pdo->query("SELECT COUNT(*) FROM comentarios WHERE aprovado = 0")->fetchColumn();

$nav_items = [
    'dashboard'    => ['icon' => '📊', 'label' => 'Dashboard',     'href' => 'index.php'],
    'noticias'     => ['icon' => '📰', 'label' => 'Notícias',      'href' => 'noticias.php'],
    'eventos'      => ['icon' => '📅', 'label' => 'Eventos',       'href' => 'eventos.php'],
    'cursos'       => ['icon' => '📚', 'label' => 'Cursos',        'href' => 'cursos.php'],
    'utilizadores' => ['icon' => '👥', 'label' => 'Utilizadores',  'href' => 'utilizadores.php'],
    'comentarios'  => ['icon' => '💬', 'label' => 'Comentários',   'href' => 'comentarios.php', 'badge' => $pending_comments],
    'logs'         => ['icon' => '📋', 'label' => 'Logs',          'href' => 'logs.php'],
    'verify'       => ['icon' => '🔍', 'label' => 'Pasta Verify',  'href' => 'verify.php'],
];

$groups = [
    'CONTEÚDO'   => ['dashboard', 'noticias', 'eventos', 'cursos'],
    'GESTÃO'     => ['utilizadores', 'comentarios', 'logs'],
    'VERIFICAÇÃO'=> ['verify'],
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin'); ?> - EduWeb Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?php echo $admin_theme === 'dark' ? 'admin-dark' : ''; ?>">

<!-- Top Header -->
<div class="admin-header">
    <div class="admin-header-left">
        <!-- Logo imagem — a mesma do site principal -->
        <a href="../site/index.php" style="display:flex;align-items:center;text-decoration:none;">
            <img src="../site/logo-claro.png"
                 alt="EduWeb"
                 class="admin-logo-img"
                 id="admin-logo-img"
                 onerror="this.style.display='none'">
        </a>
        <div>
            <h1>Painel de Administração — EduWeb</h1>
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_nome'] ?? 'Admin'); ?>!</p>
        </div>
    </div>
    <div class="admin-header-right">
        <a href="pap/site/perfil.php" style="color:white; text-decoration:none; font-size:0.85rem; opacity:0.85;">
            👤 Perfil
        </a>
        <a href="../site/index.php" style="color:white; text-decoration:none; font-size:0.85rem; opacity:0.85;">
            🏠 Ver Site
        </a>
        <button class="theme-btn-admin" onclick="toggleAdminTheme()" id="admin-theme-btn">
            <?php if ($admin_theme === 'dark'): ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;vertical-align:middle;flex-shrink:0;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                Claro
            <?php else: ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;vertical-align:middle;flex-shrink:0;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                Escuro
            <?php endif; ?>
        </button>
        <a href="../site/logout.php" style="color:white; text-decoration:none; font-size:0.85rem; background:rgba(255,255,255,0.12); padding:6px 14px; border-radius:6px;">
            🚪 Sair
        </a>
    </div>
</div>

<div class="admin-wrapper">
<!-- Sidebar -->
<div class="sidebar">
<?php foreach ($groups as $group_label => $group_items): ?>
    <div class="sidebar-section"><?php echo $group_label; ?></div>
    <?php foreach ($group_items as $key): 
        $item = $nav_items[$key];
        $is_active = ($active_page ?? '') === $key;
    ?>
    <a href="<?php echo $item['href']; ?>" class="<?php echo $is_active ? 'active' : ''; ?>">
        <span class="icon"><?php echo $item['icon']; ?></span>
        <span class="label"><?php echo $item['label']; ?></span>
        <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
            <span class="badge-count"><?php echo $item['badge']; ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
<?php endforeach; ?>
    <hr>
    <a href="../site/index.php"><span class="icon">🏠</span><span class="label">Ver Site</span></a>
    <a href="../logout.php"><span class="icon">🚪</span><span class="label">Terminar Sessão</span></a>
</div>

<!-- Main content starts here — close </div></div></body></html> in footer -->
<div class="main-content">