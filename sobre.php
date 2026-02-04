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
    <style>
        .sobre-hero {
            background: linear-gradient(135deg, var(--cor-principal), var(--cor-secundaria));
            color: white;
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .sobre-hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .sobre-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
        }
        
        .sobre-content {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .sobre-section {
            margin-bottom: 50px;
        }
        
        .sobre-section h2 {
            color: var(--cor-principal);
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .sobre-section p, .sobre-section ul {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--cor-texto);
            margin-bottom: 20px;
        }
        
        .sobre-section ul {
            margin-left: 30px;
        }
        
        .sobre-section ul li {
            margin-bottom: 10px;
        }
        
        .valores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        .valor-card {
            background: var(--cor-fundo-alt);
            padding: 30px;
            border-radius: 12px;
            border-left: 4px solid var(--cor-principal);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .valor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .valor-card h3 {
            color: var(--cor-principal);
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .valor-card p {
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }
        
        .instalacoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .instalacao-item {
            background: var(--cor-fundo-alt);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: background 0.3s ease;
        }
        
        .instalacao-item:hover {
            background: var(--cor-principal);
            color: white;
        }
        
        .instalacao-item .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .cta-box {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 50px;
            border-radius: 12px;
            text-align: center;
            margin: 60px 0;
        }
        
        .cta-box h2 {
            color: white;
            margin-bottom: 20px;
        }
        
        .cta-box p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }
        
        .cta-box .btn {
            background: white;
            color: #1e3c72;
            padding: 15px 40px;
            font-size: 1.1rem;
        }
        
        .cta-box .btn:hover {
            background: #f0f0f0;
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
    window.addEventListener('load', function() {
        document.body.classList.add('slide-in');
        setTimeout(function() {
            document.body.classList.remove('slide-in');
        }, 300);
    });

    function toggleTheme() {
        document.body.classList.add('slide-out');
        setTimeout(function() {
            window.location='?toggle_theme=1';
        }, 300);
    }
    </script>

    <section class="sobre-hero">
        <div class="container">
            <h1>Sobre o EduWeb</h1>
            <p>Conheça a nossa história, valores e compromisso com a educação de excelência</p>
        </div>
    </section>

    <main class="container">
        <div class="sobre-content">
            <div class="sobre-section">
                <h2>Quem Somos</h2>
                <p>O EduWeb é uma plataforma educativa moderna e inovadora que nasceu com o objetivo de transformar a forma como a educação é entregue e experienciada. Combinamos tradição académica com tecnologia de ponta para criar um ambiente de aprendizagem único, acessível e eficaz.</p>
                <p>Desde a nossa fundação, temos o compromisso de proporcionar educação de qualidade, preparando os nossos alunos não apenas para os desafios académicos, mas também para se destacarem no mercado de trabalho e na vida.</p>
            </div>

            <div class="sobre-section">
                <h2>Nossa Missão</h2>
                <p>Proporcionar uma educação de qualidade superior, acessível e inovadora, que prepare os nossos alunos para os desafios do futuro através de metodologias modernas, tecnologia avançada e um ambiente de aprendizagem inclusivo e estimulante.</p>
                <p>Acreditamos que cada aluno tem potencial único e merece oportunidades para desenvolver as suas competências, descobrir os seus talentos e alcançar os seus objetivos pessoais e profissionais.</p>
            </div>

            <div class="sobre-section">
                <h2>Nossa Visão</h2>
                <p>Ser reconhecidos como referência em educação inovadora, formando profissionais competentes, éticos e preparados para liderar as transformações do século XXI, contribuindo ativamente para o desenvolvimento da sociedade.</p>
            </div>

            <div class="sobre-section">
                <h2>Nossos Valores</h2>
                <div class="valores-grid">
                    <div class="valor-card">
                        <h3>🎯 Excelência Académica</h3>
                        <p>Compromisso com os mais altos padrões de qualidade no ensino e formação</p>
                    </div>
                    <div class="valor-card">
                        <h3>💡 Inovação e Criatividade</h3>
                        <p>Incentivo ao pensamento crítico e à busca constante por soluções inovadoras</p>
                    </div>
                    <div class="valor-card">
                        <h3>🤝 Inclusão e Diversidade</h3>
                        <p>Respeito pela individualidade e promoção de um ambiente acolhedor para todos</p>
                    </div>
                    <div class="valor-card">
                        <h3>🌍 Responsabilidade Social</h3>
                        <p>Compromisso com o desenvolvimento sustentável e o bem-estar da comunidade</p>
                    </div>
                    <div class="valor-card">
                        <h3>📚 Aprendizagem Contínua</h3>
                        <p>Cultura de desenvolvimento permanente e atualização constante de conhecimentos</p>
                    </div>
                    <div class="valor-card">
                        <h3>🎓 Desenvolvimento Integral</h3>
                        <p>Formação que abrange competências técnicas, sociais e emocionais</p>
                    </div>
                </div>
            </div>

            <div class="sobre-section">
                <h2>Nossa História</h2>
                <p>Fundada com o compromisso de oferecer educação de excelência, o EduWeb tem crescido e evoluído constantemente ao longo dos anos. Adaptamo-nos às necessidades da sociedade moderna, incorporando as melhores práticas pedagógicas e tecnológicas para garantir uma experiência educativa completa e relevante.</p>
                <p>Hoje, somos uma comunidade vibrante de alunos, professores e profissionais dedicados, unidos pelo objetivo comum de promover a educação transformadora que prepara para o futuro.</p>
            </div>

            <div class="sobre-section">
                <h2>Nossas Instalações</h2>
                <p>Dispomos de instalações modernas e bem equipadas, projetadas para proporcionar o melhor ambiente de aprendizagem:</p>
                <div class="instalacoes-grid">
                    <div class="instalacao-item">
                        <div class="icon">💻</div>
                        <strong>Salas Tecnológicas</strong>
                    </div>
                    <div class="instalacao-item">
                        <div class="icon">🔬</div>
                        <strong>Laboratórios</strong>
                    </div>
                    <div class="instalacao-item">
                        <div class="icon">📚</div>
                        <strong>Biblioteca Digital</strong>
                    </div>
                    <div class="instalacao-item">
                        <div class="icon">⚽</div>
                        <strong>Pavilhão Desportivo</strong>
                    </div>
                    <div class="instalacao-item">
                        <div class="icon">🎨</div>
                        <strong>Salas Multimédia</strong>
                    </div>
                    <div class="instalacao-item">
                        <div class="icon">☕</div>
                        <strong>Espaços de Convívio</strong>
                    </div>
                </div>
            </div>

            <div class="sobre-section">
                <h2>Equipa Docente</h2>
                <p>A nossa equipa é composta por professores qualificados, experientes e apaixonados pelo ensino. Todos os nossos docentes são cuidadosamente selecionados não apenas pelas suas competências técnicas, mas também pela sua capacidade de inspirar, motivar e acompanhar os alunos na sua jornada de aprendizagem.</p>
                <p>Investimos continuamente na formação dos nossos professores, garantindo que estão sempre atualizados com as mais recentes metodologias e tecnologias educativas.</p>
            </div>

            <div class="cta-box">
                <h2>Pronto para Fazer Parte do EduWeb?</h2>
                <p>Junte-se a uma comunidade que valoriza a educação, a inovação e o seu futuro</p>
                <a href="cursos.php" class="btn">Explorar Cursos</a>
                <a href="contactos.php" class="btn" style="background: transparent; border: 2px solid white; color: white; margin-left: 10px;">Falar Connosco</a>
            </div>
        </div>
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