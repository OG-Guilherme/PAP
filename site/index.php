<?php
session_start();
require_once '../important/config.php';

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

// Buscar últimas notícias (apenas 2)
$stmt = $pdo->query("SELECT n.*, u.nome as autor_nome 
                     FROM noticias n 
                     JOIN utilizadores u ON n.autor_id = u.id 
                     WHERE n.publicado = 1 
                     ORDER BY n.data_publicacao DESC LIMIT 2");
$noticias = $stmt->fetchAll();

// Buscar próximos eventos (apenas 2)
$stmt = $pdo->query("SELECT e.*, u.nome as responsavel_nome 
                     FROM eventos e 
                     JOIN utilizadores u ON e.responsavel_id = u.id 
                     WHERE e.publicado = 1 AND e.data_evento >= NOW() 
                     ORDER BY e.data_evento ASC LIMIT 2");
$eventos = $stmt->fetchAll();

// Buscar cursos em destaque (apenas 3)
$stmt = $pdo->query("SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem LIMIT 3");
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt" class="<?= getThemeClass() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduWeb - Início</title>
    <link rel="stylesheet" href="../important/style.css?v=3">
    <style>
        /* Vídeo Background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .video-background video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }
        
        .video-background video.active {
            opacity: 0.15;
        }
        
        .tema-escuro .video-background video.active {
            opacity: 0.08;
        }
        
        /* Hero com overlay */
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(244, 164, 66, 0.9), rgba(255, 140, 66, 0.9));
            padding: 100px 20px;
        }
        
        .tema-escuro .hero {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(109, 40, 217, 0.9));
        }
        
        /* Seção "Sobre Nós" resumida */
        .sobre-preview {
            background: var(--cor-fundo-alt);
            padding: 60px 40px;
            border-radius: 16px;
            margin: 60px auto;
            max-width: 1000px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            border-left: 6px solid var(--cor-principal);
        }
        
        .sobre-preview h2 {
            font-size: 2.2rem;
            margin-bottom: 25px;
            color: var(--cor-principal);
        }
        
        .sobre-preview p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--cor-texto);
            margin-bottom: 20px;
        }
        
        .valores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .valor-item {
            text-align: center;
            padding: 20px;
            background: var(--cor-fundo);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        
        .valor-item:hover {
            transform: translateY(-5px);
        }
        
        .valor-item .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .saiba-mais-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 40px;
            background: var(--cor-principal);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .saiba-mais-btn:hover {
            background: var(--cor-secundaria);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        /* Estatísticas rápidas */
        .stats-section {
            background: linear-gradient(135deg, var(--cor-principal), var(--cor-secundaria));
            padding: 50px 20px;
            margin: 60px 0;
            border-radius: 16px;
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        /* Ajustes nas seções de conteúdo */
        section {
            margin-bottom: 40px;
        }
        
        section h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body class="<?= getThemeClass() ?>">
    <!-- Vídeo Background com rotação -->
    <div class="video-background">
        <video id="video1" loop muted playsinline>
            <source src="../videos/video1.mp4" type="video/mp4">
        </video>
        <video id="video2" loop muted playsinline>
            <source src="../videos/video2.mp4" type="video/mp4">
        </video>
        <video id="video3" loop muted playsinline>
            <source src="../videos/video3.mp4" type="video/mp4">
        </video>
        <video id="video4" loop muted playsinline>
            <source src="../videos/video4.mp4" type="video/mp4">
        </video>
        <video id="video5" loop muted playsinline>
            <source src="../videos/video5.mp4" type="video/mp4">
        </video>
    </div>

    <header>
        <!-- Linha superior vermelha escura -->
        <div class="header-top">
            <div class="header-top-content">
                <nav class="top-nav">
                    <ul>
                        <li><a href="noticias.php">Notícias</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                    
                    </ul>
                </nav>
                <div class="top-actions">
                    <?php if(isLoggedIn()): ?>
                        <a href="perfil.php">Perfil</a>
                        <?php if(isAdmin()): ?>
                            <a href="admin/">Admin</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="contactos.php">Contactos</a>
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
                    <img src="../site/<?= $_SESSION['tema'] === 'claro' ? 'logo-escuro' : 'logo-claro' ?>.png" alt="EduWeb" class="logo">
                </a>
            </div>
        </div>
    </header>

    <script>
    // Sistema de rotação de vídeos com fade
    let videos = ['video1', 'video2', 'video3', 'video4', 'video5'];
    let currentVideoIndex = 0;
    
    function rotateVideos() {
        // Esconder vídeo atual
        document.getElementById(videos[currentVideoIndex]).classList.remove('active');
        
        // Próximo vídeo
        currentVideoIndex = (currentVideoIndex + 1) % videos.length;
        
        // Mostrar próximo vídeo
        let nextVideo = document.getElementById(videos[currentVideoIndex]);
        nextVideo.classList.add('active');
        nextVideo.play();
    }
    
    // Iniciar primeiro vídeo
    window.addEventListener('load', function() {
        document.body.classList.add('slide-in');
        setTimeout(function() {
            document.body.classList.remove('slide-in');
        }, 300);
        
        // Iniciar primeiro vídeo
        let firstVideo = document.getElementById(videos[0]);
        firstVideo.classList.add('active');
        firstVideo.play();
        
        // Rotação a cada 15 segundos
        setInterval(rotateVideos, 15000);
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
            <h1 style="font-size: 3.5rem; margin-bottom: 20px;">Bem-vindo ao EduWeb</h1>
            <p style="font-size: 1.5rem;">A plataforma educativa que transforma o futuro</p>
            <p style="font-size: 1.1rem; margin-top: 15px; opacity: 0.95;">Excelência, inovação e compromisso com a educação</p>
        </div>
    </section>

    <main class="container">
        <!-- SEÇÃO SOBRE NÓS PREVIEW -->
        <section class="sobre-preview">
            <h2>🎓 Sobre o EduWeb</h2>
            <p>O EduWeb é uma plataforma educativa moderna que tem como objetivo facilitar o acesso à informação e promover a comunicação entre alunos, professores e toda a comunidade educativa.</p>
            
            <p>Proporcionar uma educação de qualidade, acessível e inovadora, preparando os nossos alunos para os desafios do futuro através de metodologias modernas e um ambiente de aprendizagem inclusivo.</p>
            
            <div class="valores-grid">
                <div class="valor-item">
                    <div class="icon">🏆</div>
                    <strong>Excelência Académica</strong>
                </div>
                <div class="valor-item">
                    <div class="icon">💡</div>
                    <strong>Inovação</strong>
                </div>
                <div class="valor-item">
                    <div class="icon">🤝</div>
                    <strong>Inclusão</strong>
                </div>
                <div class="valor-item">
                    <div class="icon">🌍</div>
                    <strong>Responsabilidade Social</strong>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="sobre.php" class="saiba-mais-btn">Quer saber mais? Veja Sobre Nós! →</a>
            </div>
        </section>

        <!-- ESTATÍSTICAS -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Alunos Ativos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Professores</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Cursos Oferecidos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Taxa de Sucesso</div>
                </div>
            </div>
        </div>

        <!-- CURSOS EM DESTAQUE -->
        <?php if(!empty($cursos)): ?>
        <section class="cursos">
            <h2>📚 Cursos em Destaque</h2>
            <div class="grid">
                <?php foreach($cursos as $c): ?>
                <div class="card">
                    <?php if($c['imagem']): ?>
                        <img src="../uploads/<?= $c['imagem'] ?>" alt="<?= sanitize($c['nome']) ?>">
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

        <!-- ÚLTIMAS NOTÍCIAS -->
        <section class="noticias">
            <h2>📰 Últimas Notícias</h2>
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
                            <img src="../uploads/<?= $n['imagem_destaque'] ?>" alt="<?= sanitize($n['titulo']) ?>">
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

        <!-- PRÓXIMOS EVENTOS -->
        <section class="eventos">
            <h2>📅 Próximos Eventos</h2>
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
                            <img src="../uploads/<?= $e['imagem_destaque'] ?>" alt="<?= sanitize($e['titulo']) ?>">
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