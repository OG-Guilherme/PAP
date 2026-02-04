<?php
session_start();
require_once '../important/conexao.php';

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

$tipo = $_GET['tipo'] ?? '';

$sql = "SELECT * FROM cursos WHERE ativo = 1";
$params = [];

if($tipo) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo;
}

$sql .= " ORDER BY ordem, nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Cursos - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
</head>
<body class="<?php echo $theme === 'light' ? 'tema-claro' : 'tema-escuro'; ?>">
    <header>
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
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="perfil.php">Perfil</a>
                        <?php if(isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin'): ?>
                            <a href="admin/">Admin</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php">Entrar</a>
                    <?php endif; ?>
                    <form method="POST" style="display: inline; margin: 0;">
                        <button type="submit" name="toggle_theme" class="theme-toggle">
                            <?php echo $theme === 'light' ? '🌙' : '☀️'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
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
                    <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb" class="logo">
                </a>
            </div>
        </div>
    </header>

    <script>
    window.addEventListener('load', function() {
        document.body.classList.add('slide-in');
        setTimeout(function() { document.body.classList.remove('slide-in'); }, 300);
    });
    </script>

    <div class="container">
        <h2>Nossos Cursos</h2>
        
        <div class="filtros">
            <a href="cursos.php" class="btn">Todos</a>
            <a href="cursos.php?tipo=Regular" class="btn">Regulares</a>
            <a href="cursos.php?tipo=Profissional" class="btn">Profissionais</a>
            <a href="cursos.php?tipo=CEF" class="btn">CEF</a>
        </div>

        <div class="grid">
            <?php if(empty($cursos)): ?>
                <p>Nenhum curso disponível.</p>
            <?php else: ?>
                <?php foreach($cursos as $c): ?>
                <div class="card">
                    <?php if($c['imagem']): ?>
                        <img src="uploads/<?php echo $c['imagem']; ?>" alt="">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($c['nome']); ?> (<?php echo htmlspecialchars($c['sigla']); ?>)</h3>
                    <p class="meta">
                        📚 <?php echo htmlspecialchars($c['tipo']); ?><br>
                        ⏱️ <?php echo $c['duracao_anos']; ?> anos
                    </p>
                    <p><?php echo htmlspecialchars(substr($c['descricao'], 0, 120)); ?>...</p>
                    <a href="curso.php?id=<?php echo $c['id']; ?>">Saber mais →</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>