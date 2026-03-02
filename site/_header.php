<?php
/**
 * _header.php — Header reutilizável EduWeb
 * Variáveis: $paginaActiva, $tituloBase
 */
?>
<!DOCTYPE html>
<html lang="pt" class="<?= $temaClasse ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloBase) ? htmlspecialchars($tituloBase) . ' - ' : '' ?>EduWeb</title>
    <link rel="stylesheet" href="../important/style.css?v=9">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body class="<?= $temaClasse ?>">

<div class="header-sticky-wrap">
<header class="header-unified">
    <div class="header-unified-inner">

        <!-- LOGO — esquerda -->
        <a href="index.php" class="header-logo-link">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" class="header-logo" id="site-logo">
        </a>

        <!-- NAV PRINCIPAL — centro -->
        <nav class="header-nav" id="header-nav">
            <a href="index.php"<?= ($paginaActiva??'')==='inicio'    ? ' class="active"':'' ?>>Início</a>
            <a href="cursos.php"<?= ($paginaActiva??'')==='cursos'    ? ' class="active"':'' ?>>Cursos</a>
            <a href="noticias.php"<?= ($paginaActiva??'')==='noticias' ? ' class="active"':'' ?>>Notícias</a>
            <a href="eventos.php"<?= ($paginaActiva??'')==='eventos'   ? ' class="active"':'' ?>>Eventos</a>
            <a href="galeria.php"<?= ($paginaActiva??'')==='galeria' ? ' class="active"':'' ?>>Galeria</a>
            <a href="sobre.php"<?= ($paginaActiva??'')==='sobre'       ? ' class="active"':'' ?>>Sobre</a>
            <a href="faq.php"<?= ($paginaActiva??'')==='faq'           ? ' class="active"':'' ?>>FAQ</a>
            <a href="contactos.php"<?= ($paginaActiva??'')==='contactos' ? ' class="active"':'' ?>>Contactos</a>
        </nav>

        <!-- AÇÕES — direita -->
        <div class="header-actions">

            <!-- Botão tema -->
            <button class="header-icon-btn" onclick="toggleTheme()" title="Mudar tema">
                <?= $_SESSION['tema'] === 'claro'
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
                ?>
            </button>

            <!-- Conta dropdown -->
            <div class="conta-dropdown">
                <?php if (isLoggedIn()): ?>
                    <button class="header-conta-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?= sanitize($_SESSION['user_nome']) ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="conta-dropdown-menu">
                        <div class="conta-user-info">
                            <strong><?= sanitize($_SESSION['user_nome']) ?></strong>
                            <small><?= sanitize($_SESSION['user_email'] ?? '') ?></small>
                        </div>
                        <a href="perfil.php"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> O Meu Perfil</a>
                        <?php if (isAdmin()): ?>
                        <a href="../admin/" style="color:var(--cor-principal);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Painel Admin</a>
                        <?php endif; ?>
                        <a href="logout.php" style="color:#dc2626;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Terminar Sessão</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="header-conta-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Entrar
                    </a>
                <?php endif; ?>
            </div>

        </div><!-- .header-actions -->
    </div><!-- .header-unified-inner -->
</header>
</div><!-- .header-sticky-wrap -->

<script>
// ── Tema ──────────────────────────────────────────────────────────────
function toggleTheme() {
    document.body.style.transition = 'opacity 0.18s ease';
    document.body.style.opacity    = '0';
    setTimeout(() => { window.location = 'index.php?toggle_theme=1'; }, 170);
}

// Sombra no scroll
window.addEventListener('scroll', () => {
    document.querySelector('.header-unified')?.classList.toggle('scrolled', window.scrollY > 8);
}, { passive: true });
</script>
