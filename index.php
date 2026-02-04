<?php
session_start();
require_once 'config.php';

// Definir tema padrão se não existir
if(!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = 'claro';
}

// Trocar tema
if(isset($_GET['toggle_theme'])) {
    $_SESSION['tema'] = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
    header('Location: index.php');
    exit;
}

// Buscar últimas notícias
$stmt = $pdo->query("SELECT n.*, u.nome as autor_nome 
                     FROM noticias n 
                     JOIN utilizadores u ON n.autor_id = u.id 
                     WHERE n.publicado = 1 
                     ORDER BY n.data_publicacao DESC LIMIT 3");
$noticias = $stmt->fetchAll();

// Buscar próximos eventos
$stmt = $pdo->query("SELECT e.*, u.nome as responsavel_nome 
                     FROM eventos e 
                     JOIN utilizadores u ON e.responsavel_id = u.id 
                     WHERE e.publicado = 1 AND e.data_evento >= NOW() 
                     ORDER BY e.data_evento ASC LIMIT 3");
$eventos = $stmt->fetchAll();

// Buscar cursos em destaque
$stmt = $pdo->query("SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem LIMIT 4");
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt" class="<?= getThemeClass() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduWeb - Início</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body class="<?= getThemeClass() ?>">
    <header>
        <!-- Linha superior vermelha escura -->
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
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <?= $_SESSION['tema'] === 'claro' ? '🌙' : '☀️' ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Linha principal com logo -->
        <div class="header-main">
            <div class="header-content">
                <!-- Nav esquerda -->
                <nav class="nav-left main-nav">
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="sobre.php">Sobre Nós</a></li>
                    </ul>
                </nav>
                
                <!-- Espaço vazio central para o logo -->
                <div style="width: 200px;"></div>
                
                <!-- Nav direita -->
                <nav class="nav-right main-nav">
                    <ul>
                        <li><a href="cursos.php">Cursos</a></li>
                        <li><a href="sobre.php">Admissões</a></li>
                    </ul>
                </nav>
            </div>
            
            <!-- Logo sobreposto -->
            <div class="logo-area">
                <a href="index.php">
                    <img src="logo-<?= $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro' ?>.png" alt="EduWeb" class="logo">
                </a>
            </div>
        </div>
    </header>

    <script>
    // Adiciona animação de entrada quando a página carrega
    window.addEventListener('load', function() {
        document.body.classList.add('slide-in');
        setTimeout(function() {
            document.body.classList.remove('slide-in');
        }, 300);
    });

    function toggleTheme() {
        // Animação de saída
        document.body.classList.add('slide-out');
        
        // Redireciona após animação
        setTimeout(function() {
            window.location='?toggle_theme=1';
        }, 300);
    }
    </script>

    <section class="hero">
        <div class="container">
            <h1>Bem-vindo ao EduWeb</h1>
            <p>A plataforma educativa que transforma o futuro</p>
        </div>
    </section>

    <main class="container">
        <section class="noticias">
            <h2>Últimas Notícias</h2>
            <?php if(empty($noticias)): ?>
                <div style="text-align: center; padding: 60px 20px; background: var(--cor-fundo-alt); border-radius: 12px;">
                    <p style="font-size: 1.1rem; color: var(--cor-texto-claro); margin-bottom: 20px;">📰 Ainda não há notícias disponíveis</p>
                    <p style="color: var(--cor-texto-claro);">Em breve teremos novidades para partilhar!</p>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach($noticias as $n): ?>
                    <div class="card">
                        <?php if($n['imagem_destaque']): ?>
                            <img src="uploads/<?= $n['imagem_destaque'] ?>" alt="<?= sanitize($n['titulo']) ?>">
                        <?php endif; ?>
                        <h3><?= sanitize($n['titulo']) ?></h3>
                        <p class="meta">Por <?= sanitize($n['autor_nome']) ?> • <?= formatDate($n['data_publicacao']) ?></p>
                        <p><?= sanitize(substr($n['resumo'] ?? $n['conteudo'], 0, 150)) ?>...</p>
                        <a href="noticia.php?id=<?= $n['id'] ?>">Ler mais →</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div style="text-align: center; margin-top: 40px;">
                <a href="noticias.php" class="btn">Ver todas as notícias</a>
            </div>
        </section>

        <section class="eventos">
            <h2>Próximos Eventos</h2>
            <?php if(empty($eventos)): ?>
                <div style="text-align: center; padding: 60px 20px; background: var(--cor-fundo-alt); border-radius: 12px;">
                    <p style="font-size: 1.1rem; color: var(--cor-texto-claro); margin-bottom: 20px;">📅 Nenhum evento agendado</p>
                    <p style="color: var(--cor-texto-claro);">Fique atento para novos eventos em breve!</p>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach($eventos as $e): ?>
                    <div class="card">
                        <?php if($e['imagem_destaque']): ?>
                            <img src="uploads/<?= $e['imagem_destaque'] ?>" alt="<?= sanitize($e['titulo']) ?>">
                        <?php endif; ?>
                        <h3><?= sanitize($e['titulo']) ?></h3>
                        <p class="meta">📅 <?= formatDateTime($e['data_evento']) ?><br>📍 <?= sanitize($e['local']) ?></p>
                        <p><?= sanitize(substr($e['descricao'], 0, 120)) ?>...</p>
                        <a href="evento.php?id=<?= $e['id'] ?>">Ver detalhes →</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div style="text-align: center; margin-top: 40px;">
                <a href="eventos.php" class="btn">Ver todos os eventos</a>
            </div>
        </section>

        <?php if(!empty($cursos)): ?>
        <section class="cursos">
            <h2>Nossos Cursos</h2>
            <div class="grid">
                <?php foreach($cursos as $c): ?>
                <div class="card">
                    <?php if($c['imagem']): ?>
                        <img src="uploads/<?= $c['imagem'] ?>" alt="<?= sanitize($c['nome']) ?>">
                    <?php endif; ?>
                    <h3><?= sanitize($c['nome']) ?></h3>
                    <p class="meta">📚 <?= sanitize($c['tipo']) ?> • ⏱️ <?= $c['duracao_anos'] ?> anos</p>
                    <p><?= sanitize(substr($c['descricao'], 0, 130)) ?>...</p>
                    <a href="curso.php?id=<?= $c['id'] ?>">Saber mais →</a>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="cursos.php" class="btn">Ver todos os cursos</a>
            </div>
        </section>
        <?php endif; ?>
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