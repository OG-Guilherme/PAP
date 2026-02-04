<?php
session_start();
require_once '../important/conexao.php';

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT e.*, u.nome as responsavel, u.foto_perfil 
                       FROM eventos e 
                       JOIN utilizadores u ON e.responsavel_id = u.id 
                       WHERE e.id = ? AND e.publicado = 1");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if(!$evento) {
    header('Location: eventos.php');
    exit;
}

$pdo->prepare("UPDATE eventos SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);

$stmt = $pdo->prepare("SELECT c.*, u.nome, u.foto_perfil 
                       FROM comentarios c 
                       JOIN utilizadores u ON c.usuario_id = u.id 
                       WHERE c.tipo = 'evento' AND c.item_id = ? AND c.aprovado = 1 
                       ORDER BY c.data_criacao DESC");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && isset($_SESSION['user_id'])) {
    $conteudo = $_POST['comentario'];
    $stmt = $pdo->prepare("INSERT INTO comentarios (tipo, item_id, usuario_id, conteudo) VALUES ('evento', ?, ?, ?)");
    $stmt->execute([$id, $_SESSION['user_id'], $conteudo]);
    header("Location: evento.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($evento['titulo']); ?> - EduWeb</title>
    <style>
        :root[data-theme="light"] {
            --bg: #ffffff; --text: #000000; --card-bg: #f5f5f5; --border: #ddd; --primary: #f4a442;
        }
        :root[data-theme="dark"] {
            --bg: #1a1a1a; --text: #ffffff; --card-bg: #2a2a2a; --border: #444; --primary: #8b5cf6;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: var(--bg); color: var(--text); }
        
        header { background: var(--primary); padding: 15px 20px; color: white; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin: 0 15px; }
        .theme-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .evento-header img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin: 20px 0; }
        .meta { color: #888; margin: 15px 0; }
        .responsavel { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .responsavel img { width: 50px; height: 50px; border-radius: 50%; }
        .descricao { line-height: 1.8; margin: 30px 0; }
        
        .comentarios { margin-top: 50px; }
        .comentario { background: var(--card-bg); padding: 15px; border-radius: 8px; margin: 15px 0; }
        .comentario-header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .comentario-header img { width: 40px; height: 40px; border-radius: 50%; }
        
        textarea { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 5px; background: var(--bg); color: var(--text); min-height: 100px; }
        button { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        a { color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>EduWeb</h1>
            <nav>
                <a href="index.php">Início</a>
                <a href="eventos.php">Eventos</a>
            </nav>
            <form method="POST" style="display: inline;">
                <button type="submit" name="toggle_theme" class="theme-btn"><?php echo $theme === 'light' ? '🌙' : '☀️'; ?></button>
            </form>
        </div>
    </header>

    <div class="container">
        <a href="eventos.php">← Voltar aos eventos</a>
        
        <div class="evento-header">
            <h1><?php echo htmlspecialchars($evento['titulo']); ?></h1>
            <p class="meta">
                📅 <?php echo date('d/m/Y H:i', strtotime($evento['data_evento'])); ?>
                <?php if($evento['data_fim']): ?>
                    até <?php echo date('d/m/Y H:i', strtotime($evento['data_fim'])); ?>
                <?php endif; ?>
                <br>📍 <?php echo htmlspecialchars($evento['local']); ?>
                <br>👁️ <?php echo $evento['visualizacoes']; ?> visualizações
            </p>
            
            <div class="responsavel">
                <?php if($evento['foto_perfil']): ?>
                    <img src="uploads/<?php echo $evento['foto_perfil']; ?>" alt="">
                <?php endif; ?>
                <strong>Organizado por <?php echo htmlspecialchars($evento['responsavel']); ?></strong>
            </div>
            
            <?php if($evento['imagem_destaque']): ?>
                <img src="uploads/<?php echo $evento['imagem_destaque']; ?>" alt="">
            <?php endif; ?>
        </div>
        
        <div class="descricao">
            <?php echo nl2br(htmlspecialchars($evento['descricao'])); ?>
        </div>
        
        <?php if($evento['inscricoes_abertas']): ?>
            <div style="background: var(--card-bg); padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3>✅ Inscrições Abertas</h3>
                <?php if($evento['capacidade']): ?>
                    <p>Capacidade: <?php echo $evento['capacidade']; ?> pessoas</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="comentarios">
            <h2>Comentários (<?php echo count($comentarios); ?>)</h2>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <form method="POST">
                    <textarea name="comentario" placeholder="Escreve o teu comentário..." required></textarea>
                    <button type="submit">Enviar Comentário</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Inicia sessão</a> para comentar.</p>
            <?php endif; ?>
            
            <?php foreach($comentarios as $c): ?>
                <div class="comentario">
                    <div class="comentario-header">
                        <?php if($c['foto_perfil']): ?>
                            <img src="uploads/<?php echo $c['foto_perfil']; ?>" alt="">
                        <?php endif; ?>
                        <div>
                            <strong><?php echo htmlspecialchars($c['nome']); ?></strong>
                            <small style="display: block; color: #888;">
                                <?php echo date('d/m/Y H:i', strtotime($c['data_criacao'])); ?>
                            </small>
                        </div>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($c['conteudo'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>