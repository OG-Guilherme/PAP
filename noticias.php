<?php
session_start();
require_once 'conexao.php';

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
    <style>
        :root[data-theme="light"] {
            --bg: #ffffff;
            --text: #000000;
            --card-bg: #f5f5f5;
            --border: #ddd;
            --primary: #f4a442;
        }
        
        :root[data-theme="dark"] {
            --bg: #1a1a1a;
            --text: #ffffff;
            --card-bg: #2a2a2a;
            --border: #444;
            --primary: #8b5cf6;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        
        header {
            background: var(--primary);
            padding: 15px 20px;
            color: white;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        
        .theme-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .filtros {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        input, select {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg);
            color: var(--text);
        }
        
        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 20px;
            border-radius: 8px;
        }
        
        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .meta {
            font-size: 0.9em;
            color: #888;
            margin: 10px 0;
        }
        
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div>
                <h1>EduWeb</h1>
            </div>
            <nav>
                <a href="index.php">Início</a>
                <a href="noticias.php">Notícias</a>
                <a href="eventos.php">Eventos</a>
                <a href="contactos.php">Contactos</a>
            </nav>
            <form method="POST" style="display: inline;">
                <button type="submit" name="toggle_theme" class="theme-btn">
                    <?php echo $theme === 'light' ? '🌙' : '☀️'; ?>
                </button>
            </form>
        </div>
    </header>

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