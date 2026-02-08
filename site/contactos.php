<?php
session_start();
require_once '../important/config.php';

if(!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = 'claro';
}

if(isset($_POST['toggle_theme'])) {
    $_SESSION['tema'] = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
}

// Compatibilidade com o sistema de theme
$theme = $_SESSION['tema'] === 'claro' ? 'light' : 'dark';

$mensagem = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['toggle_theme'])) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $assunto = $_POST['assunto'] ?? '';
    $msg = $_POST['mensagem'] ?? '';
    
    if($nome && $email && $assunto && $msg) {
        // Aqui podias enviar email real, mas por agora apenas guarda
        $mensagem = '<div class="alert success">Mensagem enviada com sucesso! Entraremos em contacto em breve.</div>';
    } else {
        $mensagem = '<div class="alert error">Por favor preencha todos os campos.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - EduWeb</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body class="<?php echo getThemeClass(); ?>">
    <header>
        <!-- Linha superior -->
        <div class="header-top">
            <div class="header-top-content">
                <nav class="top-nav">
                    <ul>
                        <li><a href="noticias.php">Notícias</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="contactos.php">Contactos</a></li>
                    </ul>
                </nav>
                <div class="top-actions">
                    <?php if(isLoggedIn()): ?>
                        <a href="perfil.php">Perfil</a>
                        <?php if(isAdmin()): ?>
                            <a href="admin/">Admin</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php">Entrar</a>
                    <?php endif; ?>
                    <form method="POST" style="display: inline; margin: 0;">
                        <button type="submit" name="toggle_theme" class="theme-toggle">
                            <?php echo $_SESSION['tema'] === 'claro' ? '🌙' : '☀️'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Linha principal -->
        <div class="header-main">
            <div class="header-content">
                <nav class="nav-left main-nav">
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="sobre.php">Sobre Nós</a></li>
                    </ul>
                </nav>
                <div style="width: 200px;"></div>
                <nav class="nav-right main-nav">
                    <ul>
                        <li><a href="cursos.php">Cursos</a></li>
                        <li><a href="sobre.php">Admissões</a></li>
                    </ul>
                </nav>
            </div>
            <div class="logo-area">
                <a href="index.php">
                    <img src="logo-<?php echo $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb" class="logo">
                </a>
            </div>
        </div>
    </header>

    <script>
    window.addEventListener('load', function() {
        document.body.classList.add('slide-in');
        setTimeout(function() {
            document.body.classList.remove('slide-in');
        }, 300);
    });
    </script>

    <main class="container">
        <h2>Entre em Contacto</h2>
        
        <div class="contacto-info">
            <div class="contacto-card">
                <h3>📧 Email</h3>
                <p><?= SITE_EMAIL ?></p>
            </div>
            <div class="contacto-card">
                <h3>📞 Telefone</h3>
                <p><?= SITE_TELEFONE ?></p>
            </div>
            <div class="contacto-card">
                <h3>📍 Morada</h3>
                <p><?= SITE_MORADA ?></p>
            </div>
        </div>

        <?= $mensagem ?>

        <h3 style="margin: 40px 0 20px;">Envie-nos uma Mensagem</h3>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="assunto">Assunto:</label>
                <input type="text" id="assunto" name="assunto" required>
            </div>
            
            <div class="form-group">
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" required></textarea>
            </div>
            
            <button type="submit" class="btn">Enviar Mensagem</button>
        </form>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EduWeb</h3>
                    <p>Plataforma educativa inovadora</p>
                </div>
                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <a href="sobre.php">Sobre Nós</a>
                    <a href="cursos.php">Cursos</a>
                    <a href="contactos.php">Contactos</a>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p>📧 <?= SITE_EMAIL ?></p>
                    <p>📞 <?= SITE_TELEFONE ?></p>
                    <p>📍 <?= SITE_MORADA ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> EduWeb. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>