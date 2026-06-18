<?php
/**
 * _header.php — Header institucional EduWeb
 * Variáveis esperadas: $paginaActiva, $tituloBase, $temaClasse, $logoImg
 */
?>
<!DOCTYPE html>
<html lang="pt" class="<?= $temaClasse ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloBase) ? htmlspecialchars($tituloBase) . ' — ' : '' ?>EduWeb</title>
    <link rel="stylesheet" href="../important/style.css?v=10">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body class="<?= $temaClasse ?>">

<!-- ══ Barra superior institucional ══════════════════════════
     Morada · Telefone · Email · Horário — como as escolas têm
     ══════════════════════════════════════════════════════════ -->
<div class="escola-topbar">
    <div class="escola-topbar-inner">
        <div class="escola-topbar-left">
            <span class="escola-topbar-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?= defined('SITE_MORADA') ? SITE_MORADA : 'Amadora, Lisboa' ?>
            </span>
            <span style="opacity:.3;">|</span>
            <span class="escola-topbar-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.83a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.72 16z"/></svg>
                <?= defined('SITE_TELEFONE') ? SITE_TELEFONE : '+351 21 XXX XXXX' ?>
            </span>
        </div>
        <div class="escola-topbar-right">
            <span class="escola-topbar-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <a href="mailto:<?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?>"><?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?></a>
            </span>
            <span style="opacity:.3;">|</span>
            <span class="escola-topbar-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Seg–Sex: 8h30–17h30
            </span>
        </div>
    </div>
</div>

<!-- ══ Header principal ══════════════════════════════════════ -->
<div class="header-sticky-wrap">
<header class="header-unified">
    <div class="header-unified-inner">

        <!-- Logo + nome da escola -->
        <a href="index.php" class="header-logo-link">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" class="header-logo" id="site-logo">
            <div class="header-escola-nome">
                <strong>EduWeb</strong>
                <span>Escola Secundária</span>
            </div>
        </a>

        <!-- Nav -->
        <nav class="header-nav" id="header-nav">
            <a href="index.php"<?=    ($paginaActiva??'')==='inicio'    ? ' class="active"':'' ?>>Início</a>
            <a href="cursos.php"<?=   ($paginaActiva??'')==='cursos'    ? ' class="active"':'' ?>>Cursos</a>
            <a href="noticias.php"<?= ($paginaActiva??'')==='noticias'  ? ' class="active"':'' ?>>Notícias</a>
            <a href="eventos.php"<?=  ($paginaActiva??'')==='eventos'   ? ' class="active"':'' ?>>Eventos</a>
            <a href="galeria.php"<?=  ($paginaActiva??'')==='galeria'   ? ' class="active"':'' ?>>Galeria</a>
            <a href="sobre.php"<?=    ($paginaActiva??'')==='sobre'     ? ' class="active"':'' ?>>Sobre</a>
            <a href="faq.php"<?=      ($paginaActiva??'')==='faq'       ? ' class="active"':'' ?>>FAQ</a>
            <a href="contactos.php"<?=($paginaActiva??'')==='contactos' ? ' class="active"':'' ?>>Contactos</a>
        </nav>

        <!-- Ações -->
        <div class="header-actions">

            <!-- Tema -->
            <button class="header-icon-btn" onclick="toggleTheme()" title="Mudar tema">
                <?= $_SESSION['tema'] === 'claro'
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
                ?>
            </button>

            <!-- Conta -->
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
                            <small><?= sanitize($_SESSION['user_email'] ?? '') ?> &middot; <?= ucfirst($_SESSION['user_tipo'] ?? '') ?></small>
                        </div>
                        <a href="perfil.php">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            O Meu Perfil
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="../admin/" style="color:var(--cor-principal);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Painel Admin
                        </a>
                        <?php endif; ?>
                        <a href="logout.php" style="color:#dc2626;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;flex-shrink:0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Terminar Sessão
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="header-conta-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Entrar
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>
</div>

<script>
function toggleTheme() {
    document.body.style.transition = 'opacity .18s ease';
    document.body.style.opacity = '0';
    setTimeout(() => { window.location = 'index.php?toggle_theme=1'; }, 170);
}
window.addEventListener('scroll', () => {
    document.querySelector('.header-sticky-wrap')?.classList.toggle('scrolled', window.scrollY > 4);
}, { passive: true });
</script>