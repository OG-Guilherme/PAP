<?php
session_start();
require_once '../important/conexao.php';

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

// Filtros
$pesquisa = $_GET['q'] ?? '';
$categoria = $_GET['cat'] ?? '';

// Query base
$sql = "SELECT n.*, u.nome as autor FROM noticias n 
        JOIN utilizadores u ON n.autor_id = u.id 
        WHERE n.publicado = 1";

$params = [];

if($pesquisa) {
    $sql .= " AND (n.titulo LIKE ? OR n.conteudo LIKE ?)";
    $params[] = "%$pesquisa%";
    $params[] = "%$pesquisa%";
}

if($categoria) {
    $sql .= " AND n.categoria = ?";
    $params[] = $categoria;
}

$sql .= " ORDER BY n.data_publicacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

// Buscar categorias
$cats = $pdo->query("SELECT DISTINCT categoria FROM noticias WHERE categoria IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Notícias - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
</head>
<body class="<?php echo $theme === 'light' ? 'tema-claro' : 'tema-escuro'; ?>">
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
                    <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb" class="logo">
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

    <div class="container">
        <h2>Notícias</h2>
        
        <form class="filtros" method="GET">
            <input type="text" name="q" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($pesquisa); ?>">
            <select name="cat">
                <option value="">Todas as categorias</option>
                <?php foreach($cats as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $c === $categoria ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filtrar</button>
            <a href="noticias.php"><button type="button">Limpar</button></a>
        </form>

        <div class="grid">
            <?php if(empty($noticias)): ?>
                <p>Nenhuma notícia encontrada.</p>
            <?php else: ?>
                <?php foreach($noticias as $n): ?>
                <div class="card">
                    <?php if($n['imagem_destaque']): ?>
                        <img src="uploads/<?php echo $n['imagem_destaque']; ?>" alt="">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
                    <p class="meta">
                        Por <?php echo htmlspecialchars($n['autor']); ?> - 
                        <?php echo date('d/m/Y', strtotime($n['data_publicacao'])); ?>
                        <?php if($n['categoria']): ?>
                            | Categoria: <?php echo htmlspecialchars($n['categoria']); ?>
                        <?php endif; ?>
                    </p>
                    <p><?php echo htmlspecialchars(substr($n['conteudo'], 0, 150)); ?>...</p>
                    <a href="noticia.php?id=<?php echo $n['id']; ?>">Ler mais →</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>