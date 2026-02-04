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
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$sql = "SELECT e.*, u.nome as responsavel FROM eventos e 
        JOIN utilizadores u ON e.responsavel_id = u.id 
        WHERE e.publicado = 1";
$params = [];

if($pesquisa) {
    $sql .= " AND (e.titulo LIKE ? OR e.descricao LIKE ?)";
    $params[] = "%$pesquisa%";
    $params[] = "%$pesquisa%";
}

if($categoria) {
    $sql .= " AND e.categoria = ?";
    $params[] = $categoria;
}

if($data_inicio) {
    $sql .= " AND e.data_evento >= ?";
    $params[] = $data_inicio;
}

if($data_fim) {
    $sql .= " AND e.data_evento <= ?";
    $params[] = $data_fim . ' 23:59:59';
}

$sql .= " ORDER BY e.data_evento ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll();

$cats = $pdo->query("SELECT DISTINCT categoria FROM eventos WHERE categoria IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Eventos - EduWeb</title>
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
        <h2>Eventos</h2>
        
        <form class="filtros" method="GET">
            <input type="text" name="q" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($pesquisa); ?>">
            <select name="cat">
                <option value="">Todas as categorias</option>
                <?php foreach($cats as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $c === $categoria ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" placeholder="Data início">
            <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" placeholder="Data fim">
            <button type="submit">Filtrar</button>
            <a href="eventos.php"><button type="button">Limpar</button></a>
        </form>

        <div class="grid">
            <?php if(empty($eventos)): ?>
                <p>Nenhum evento encontrado.</p>
            <?php else: ?>
                <?php foreach($eventos as $e): ?>
                <div class="card">
                    <?php if($e['imagem_destaque']): ?>
                        <img src="uploads/<?php echo $e['imagem_destaque']; ?>" alt="">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($e['titulo']); ?></h3>
                    <p class="meta">
                        📅 <?php echo date('d/m/Y H:i', strtotime($e['data_evento'])); ?>
                        <?php if($e['local']): ?>
                            <br>📍 <?php echo htmlspecialchars($e['local']); ?>
                        <?php endif; ?>
                        <?php if($e['categoria']): ?>
                            <br>🏷️ <?php echo htmlspecialchars($e['categoria']); ?>
                        <?php endif; ?>
                    </p>
                    <p><?php echo htmlspecialchars(substr($e['descricao'], 0, 100)); ?>...</p>
                    <a href="evento.php?id=<?php echo $e['id']; ?>">Ver mais →</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>