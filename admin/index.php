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
$stmt = $pdo->query("SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem LIMIT 6");
$cursos = $stmt->fetchAll();

// Estatísticas
$stats = [
    'cursos' => $pdo->query("SELECT COUNT(*) FROM cursos WHERE ativo = 1")->fetchColumn(),
    'alunos' => $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo = 'aluno'")->fetchColumn(),
    'professores' => $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo = 'professor'")->fetchColumn(),
    'noticias' => $pdo->query("SELECT COUNT(*) FROM noticias WHERE publicado = 1")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="pt" class="<?= getThemeClass() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduWeb - Plataforma Educativa de Excelência</title>
    <link rel="stylesheet" href="style.css?v=3">
    <style>
        /* Estilos adicionais para o hero melhorado */
        .hero {
            background: linear-gradient(135deg, var(--cor-principal), var(--cor-secundaria));
            color: white;
            padding: 100px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 1.4rem;
            opacity: 0.95;
            font-weight: 300;
            margin-bottom: 30px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .hero-btn {
            padding: 15px 35px;
            font-size: 1.1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .hero-btn-primary {
            background: white;
            color: var(--cor-principal);
        }
        
        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .hero-btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }
        
        .hero-btn-secondary:hover {
            background: white;
            color: var(--cor-principal);
        }
        
        /* Estatísticas */
        .stats-section {
            background: var(--cor-fundo-alt);
            padding: 60px 20px;
            margin: -40px 0 60px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .stat-card {
            background: var(--cor-fundo);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--cor-principal);
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1rem;
            color: var(--cor-texto-claro);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Seção sobre */
        .about-section {
            background: linear-gradient(135deg, var(--cor-principal) 0%, var(--cor-secundaria) 100%);
            color: white;
            padding: 80px 20px;
            margin: 60px 0;
        }
        
        .about-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        
        .about-content h2 {
            font-size: 2.5rem;
            margin-bottom: 25px;
            color: white;
        }
        
        .about-content p {
            font-size: 1.2rem;
            line-height: 1.8;
            opacity: 0.95;
            margin-bottom: 30px;
        }
        
        /* Features */
        .features-section {
            padding: 40px 0 60px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-card {
            background: var(--cor-fundo-alt);
            padding: 35px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid var(--cor-borda);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            border-color: var(--cor-principal);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--cor-texto);
        }
        
        .feature-card p {
            color: var(--cor-texto-claro);
            line-height: 1.6;
        }
        
        /* Seções */
        section {
            margin-bottom: 80px;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .section-header p {
            font-size: 1.1rem;
            color: var(--cor-texto-claro);
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
            border-radius: 16px;
            margin: 60px 0;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
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
        <div class="hero-content">
            <h1>Bem-vindo ao EduWeb</h1>
            <p>A plataforma educativa que transforma o futuro através da excelência académica e inovação pedagógica</p>
            <div class="hero-buttons">
                <a href="cursos.php" class="hero-btn hero-btn-primary">Explorar Cursos</a>
                <a href="sobre.php" class="hero-btn hero-btn-secondary">Conhecer EduWeb</a>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['cursos'] ?>+</div>
                <div class="stat-label">Cursos Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['alunos'] ?>+</div>
                <div class="stat-label">Alunos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['professores'] ?>+</div>
                <div class="stat-label">Professores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['noticias'] ?>+</div>
                <div class="stat-label">Notícias</div>
            </div>
        </div>
    </section>

    <main class="container">
        <!-- Features -->
        <section class="features-section">
            <div class="section-header">
                <h2>Porque Escolher o EduWeb?</h2>
                <p>Oferecemos uma experiência educativa completa e de excelência</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎓</div>
                    <h3>Educação de Qualidade</h3>
                    <p>Programas académicos rigorosos e atualizados, ministrados por profissionais qualificados</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💻</div>
                    <h3>Tecnologia Avançada</h3>
                    <p>Plataforma moderna com recursos digitais para potenciar a aprendizagem</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌟</div>
                    <h3>Apoio Personalizado</h3>
                    <p>Acompanhamento individualizado para garantir o sucesso de cada aluno</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h3>Comunidade Ativa</h3>
                    <p>Ambiente colaborativo que promove o networking e partilha de conhecimentos</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Recursos Completos</h3>
                    <p>Biblioteca digital, materiais de estudo e ferramentas de apoio disponíveis 24/7</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Preparação Profissional</h3>
                    <p>Formação orientada para o mercado de trabalho e desenvolvimento de competências</p>
                </div>
            </div>
        </section>

        <!-- Sobre -->
        <section class="about-section">
            <div class="about-content">
                <h2>Sobre o EduWeb</h2>
                <p>O EduWeb é mais do que uma plataforma educativa - é um ecossistema completo de aprendizagem que une tradição académica e inovação tecnológica. Acreditamos que a educação de qualidade deve ser acessível, envolvente e preparar os alunos para os desafios do século XXI.</p>
                <p>Com uma equipa dedicada de professores e profissionais, oferecemos cursos diversificados que combinam rigor académico com metodologias modernas de ensino, garantindo uma formação integral e preparação sólida para o futuro.</p>
                <a href="sobre.php" class="hero-btn hero-btn-secondary" style="margin-top: 20px;">Saber Mais Sobre Nós</a>
            </div>
        </section>

        <!-- Cursos -->
        <?php if(!empty($cursos)): ?>
        <section class="cursos">
            <div class="section-header">
                <h2>Nossos Cursos</h2>
                <p>Descubra os programas formativos que vão impulsionar a sua carreira</p>
            </div>
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

        <!-- Notícias -->
        <section class="noticias">
            <div class="section-header">
                <h2>Últimas Notícias</h2>
                <p>Fique a par das novidades e acontecimentos da nossa comunidade</p>
            </div>
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
                <div style="text-align: center; margin-top: 40px;">
                    <a href="noticias.php" class="btn">Ver todas as notícias</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Eventos -->
        <section class="eventos">
            <div class="section-header">
                <h2>Próximos Eventos</h2>
                <p>Participe nos eventos e atividades da nossa comunidade educativa</p>
            </div>
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
                <div style="text-align: center; margin-top: 40px;">
                    <a href="eventos.php" class="btn">Ver todos os eventos</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- CTA -->
        <section class="cta-section">
            <h2>Pronto para Começar?</h2>
            <p>Junte-se à nossa comunidade e dê o primeiro passo rumo ao seu futuro de sucesso</p>
            <div class="hero-buttons">
                <a href="registar.php" class="hero-btn hero-btn-primary">Criar Conta</a>
                <a href="contactos.php" class="hero-btn hero-btn-secondary">Falar Connosco</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EduWeb</h3>
                    <p>Plataforma educativa inovadora dedicada à excelência no ensino e formação</p>
                </div>
                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <a href="sobre.php">Sobre Nós</a>
                    <a href="cursos.php">Cursos</a>
                    <a href="noticias.php">Notícias</a>
                    <a href="eventos.php">Eventos</a>
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