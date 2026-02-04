<?php
session_start();
require_once '../important/conexao.php';

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id = ? AND ativo = 1");
$stmt->execute([$id]);
$curso = $stmt->fetch();

if(!$curso) {
    header('Location: cursos.php');
    exit;
}

// Buscar disciplinas do curso
$stmt = $pdo->prepare("SELECT d.*, cd.ano, cd.semestre 
                       FROM disciplinas d 
                       JOIN curso_disciplinas cd ON d.id = cd.disciplina_id 
                       WHERE cd.curso_id = ? 
                       ORDER BY cd.ano, cd.semestre, d.nome");
$stmt->execute([$id]);
$disciplinas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($curso['nome']); ?> - EduWeb</title>
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
        
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .curso-header img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin: 20px 0; }
        .info-box { background: var(--card-bg); padding: 20px; border-radius: 8px; margin: 20px 0; }
        .disciplinas { margin-top: 40px; }
        .disciplina { background: var(--card-bg); padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid var(--primary); }
        a { color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>EduWeb</h1>
            <nav>
                <a href="index.php">Início</a>
                <a href="cursos.php">Cursos</a>
            </nav>
            <form method="POST" style="display: inline;">
                <button type="submit" name="toggle_theme" class="theme-btn"><?php echo $theme === 'light' ? '🌙' : '☀️'; ?></button>
            </form>
        </div>
    </header>

    <div class="container">
        <a href="cursos.php">← Voltar aos cursos</a>
        
        <div class="curso-header">
            <h1><?php echo htmlspecialchars($curso['nome']); ?> (<?php echo htmlspecialchars($curso['sigla']); ?>)</h1>
            
            <?php if($curso['imagem']): ?>
                <img src="uploads/<?php echo $curso['imagem']; ?>" alt="">
            <?php endif; ?>
        </div>
        
        <div class="info-box">
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($curso['tipo']); ?></p>
            <p><strong>Duração:</strong> <?php echo $curso['duracao_anos']; ?> anos</p>
        </div>
        
        <h2>Descrição</h2>
        <p style="line-height: 1.8; margin: 20px 0;">
            <?php echo nl2br(htmlspecialchars($curso['descricao'])); ?>
        </p>
        
        <?php if($curso['objetivos']): ?>
            <h2>Objetivos</h2>
            <p style="line-height: 1.8; margin: 20px 0;">
                <?php echo nl2br(htmlspecialchars($curso['objetivos'])); ?>
            </p>
        <?php endif; ?>
        
        <?php if($curso['saidas_profissionais']): ?>
            <h2>Saídas Profissionais</h2>
            <p style="line-height: 1.8; margin: 20px 0;">
                <?php echo nl2br(htmlspecialchars($curso['saidas_profissionais'])); ?>
            </p>
        <?php endif; ?>
        
        <?php if(!empty($disciplinas)): ?>
            <div class="disciplinas">
                <h2>Disciplinas</h2>
                <?php 
                $ano_atual = 0;
                foreach($disciplinas as $d): 
                    if($d['ano'] != $ano_atual) {
                        if($ano_atual > 0) echo '</div>';
                        echo '<h3 style="margin-top: 30px;">'. $d['ano'] .'º Ano</h3>';
                        echo '<div>';
                        $ano_atual = $d['ano'];
                    }
                ?>
                    <div class="disciplina">
                        <strong><?php echo htmlspecialchars($d['nome']); ?></strong>
                        <?php if($d['sigla']): ?>
                            (<?php echo htmlspecialchars($d['sigla']); ?>)
                        <?php endif; ?>
                        <?php if($d['carga_horaria']): ?>
                            <br><small><?php echo $d['carga_horaria']; ?>h</small>
                        <?php endif; ?>
                        <?php if($d['descricao']): ?>
                            <p style="margin-top: 10px; font-size: 0.9em;">
                                <?php echo htmlspecialchars($d['descricao']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>