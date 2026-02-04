<?php
require_once '../important/config.php';

if(!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = 'claro';
}

if(isset($_GET['toggle_theme'])) {
    $_SESSION['tema'] = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
    header('Location: contactos.php');
    exit;
}

$mensagem = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
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
<html lang="pt" class="<?= getThemeClass() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
    <style>
        .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .contacto-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin: 40px 0; }
        .contacto-card { background: var(--cor-fundo-alt); padding: 25px; border-radius: 8px; border: 1px solid var(--cor-borda); }
        .contacto-card h3 { margin-bottom: 15px; color: var(--cor-principal); }
    </style>
</head>
<body class="<?= getThemeClass() ?>">
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo-area">
                    <img src="logo-<?= $_SESSION['tema'] ?>.png" alt="EduWeb" class="logo">
                    <span class="logo-text">EduWeb</span>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="cursos.php">Cursos</a></li>
                        <li><a href="noticias.php">Notícias</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="contactos.php">Contactos</a></li>
                        <?php if(isLoggedIn()): ?>
                            <li><a href="perfil.php">Perfil</a></li>
                            <?php if(isAdmin()): ?><li><a href="admin/">Admin</a></li><?php endif; ?>
                            <li><a href="logout.php">Sair</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Entrar</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <button class="theme-toggle" onclick="window.location='?toggle_theme=1'">
                    <?= $_SESSION['tema'] === 'claro' ? '🌙' : '☀️' ?>
                </button>
            </div>
        </div>
    </header>

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