<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = 'claro';
}

if(isset($_GET['toggle_theme'])) {
    $_SESSION['tema'] = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
    header('Location: sobre.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt" class="<?= getThemeClass() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - EduWeb</title>
    <link rel="stylesheet" href="style.css">
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
        <h2>Sobre o EduWeb</h2>
        
        <div style="max-width: 800px;">
            <p style="margin-bottom: 20px;">O EduWeb é uma plataforma educativa moderna que tem como objetivo facilitar o acesso à informação e promover a comunicação entre alunos, professores e toda a comunidade educativa.</p>
            
            <h3 style="margin: 30px 0 15px;">Nossa Missão</h3>
            <p style="margin-bottom: 20px;">Proporcionar uma educação de qualidade, acessível e inovadora, preparando os nossos alunos para os desafios do futuro através de metodologias modernas e um ambiente de aprendizagem inclusivo.</p>
            
            <h3 style="margin: 30px 0 15px;">Nossos Valores</h3>
            <ul style="margin-left: 20px; margin-bottom: 20px;">
                <li>Excelência académica</li>
                <li>Inovação e criatividade</li>
                <li>Inclusão e diversidade</li>
                <li>Responsabilidade social</li>
                <li>Desenvolvimento integral dos alunos</li>
            </ul>
            
            <h3 style="margin: 30px 0 15px;">História</h3>
            <p style="margin-bottom: 20px;">Fundada com o compromisso de oferecer educação de excelência, o EduWeb tem crescido e evoluído ao longo dos anos, adaptando-se às necessidades da sociedade moderna e incorporando as melhores práticas pedagógicas.</p>
            
            <h3 style="margin: 30px 0 15px;">Instalações</h3>
            <p style="margin-bottom: 20px;">Dispomos de instalações modernas e bem equipadas, incluindo:</p>
            <ul style="margin-left: 20px; margin-bottom: 20px;">
                <li>Salas de aula tecnológicas</li>
                <li>Laboratórios de ciências e informática</li>
                <li>Biblioteca e centro de recursos</li>
                <li>Pavilhão desportivo</li>
                <li>Espaços de convívio</li>
            </ul>
        </div>
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