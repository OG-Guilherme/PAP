<?php
session_start();
require_once '../conexao.php';

$mensagem = '';

// Adicionar notícia
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $resumo = $_POST['resumo'];
    $categoria = $_POST['categoria'];
    $data_pub = $_POST['data_publicacao'];
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($titulo));
    
    // Upload imagem
    $imagem = null;
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'noticias/' . uniqid() . '.' . $ext;
        $dir = '../uploads/noticias/';
        if(!is_dir($dir)) mkdir($dir, 0777, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../uploads/' . $imagem);
    }
    
    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, slug, resumo, conteudo, imagem_destaque, autor_id, categoria, data_publicacao, publicado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    if($stmt->execute([$titulo, $slug, $resumo, $conteudo, $imagem, $_SESSION['user_id'], $categoria, $data_pub])) {
        // Log
        $pdo->prepare("INSERT INTO logs_admin (usuario_id, acao, tipo, tabela, item_id, descricao) VALUES (?, 'Criar notícia', 'criar', 'noticias', ?, ?)")->execute([$_SESSION['user_id'], $pdo->lastInsertId(), $titulo]);
        $mensagem = '<div class="alert success">✅ Notícia criada com sucesso!</div>';
    }
}

// Eliminar
if(isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("SELECT titulo FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();
    
    $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$id]);
    $pdo->prepare("INSERT INTO logs_admin (usuario_id, acao, tipo, tabela, item_id, descricao) VALUES (?, 'Eliminar notícia', 'eliminar', 'noticias', ?, ?)")->execute([$_SESSION['user_id'], $id, $noticia['titulo']]);
    $mensagem = '<div class="alert success">🗑️ Notícia eliminada!</div>';
}

// Listar
$noticias = $pdo->query("SELECT n.*, u.nome as autor FROM noticias n JOIN utilizadores u ON n.autor_id = u.id ORDER BY n.data_publicacao DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Gestão de Notícias - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .admin-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px; }
        .admin-header h1 { font-size: 1.5rem; }
        
        .admin-container { display: flex; min-height: calc(100vh - 80px); }
        
        .sidebar { width: 250px; background: #2c3e50; color: white; padding: 20px 0; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 12px 20px; transition: background 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; }
        
        .main-content { flex: 1; padding: 30px; }
        
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .card h2 { margin-bottom: 20px; color: #333; }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        textarea { min-height: 200px; }
        
        button, .btn { background: #2a5298; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        button:hover { background: #1e3c72; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f8f9fa; }
        table a { color: #2a5298; text-decoration: none; margin: 0 5px; }
        table a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📰 Gestão de Notícias</h1>
    </div>

    <div class="admin-container">
        <div class="sidebar">
            <a href="../site/index.php">📊 Dashboard</a>
            <a href="../site/noticias.php" class="active">📰 Notícias</a>
            <a href="../site/eventos.php">📅 Eventos</a>
            <a href="../site/cursos.php">📚 Cursos</a>
            <a href="../site/utilizadores.php">👥 Utilizadores</a>
            <a href="../site/comentarios.php">💬 Comentários</a>
            <a href="logs.php">📋 Logs</a>
            <hr style="border: none; border-top: 1px solid #34495e; margin: 15px 0;">
            <a href="../site/index.php">🏠 Ver Site</a>
            <a href="../site/logout.php">🚪 Sair</a>
        </div>

        <div class="main-content">
            <?php echo $mensagem; ?>
            
            <div class="card">
                <h2>➕ Adicionar Nova Notícia</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="titulo">Título:</label>
                        <input type="text" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="resumo">Resumo:</label>
                        <input type="text" id="resumo" name="resumo" maxlength="200" placeholder="Breve resumo (máx 200 caracteres)">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <select id="categoria" name="categoria">
                            <option value="">Sem categoria</option>
                            <option value="Académico">Académico</option>
                            <option value="Desporto">Desporto</option>
                            <option value="Cultura">Cultura</option>
                            <option value="Avisos">Avisos</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="conteudo">Conteúdo:</label>
                        <textarea id="conteudo" name="conteudo" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="imagem">Imagem de Destaque:</label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_publicacao">Data de Publicação:</label>
                        <input type="datetime-local" id="data_publicacao" name="data_publicacao" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    
                    <button type="submit" name="adicionar">Adicionar Notícia</button>
                </form>
            </div>

            <div class="card">
                <h2>📋 Notícias Existentes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoria</th>
                            <th>Data</th>
                            <th>Visualizações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($noticias as $n): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($n['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($n['autor']); ?></td>
                            <td><?php echo $n['categoria'] ?: '-'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($n['data_publicacao'])); ?></td>
                            <td><?php echo $n['visualizacoes']; ?></td>
                            <td>
                                <a href="../noticia.php?id=<?php echo $n['id']; ?>" target="_blank">Ver</a>
                                <a href="?eliminar=<?php echo $n['id']; ?>" onclick="return confirm('Eliminar esta notícia?')" style="color: #dc3545;">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>