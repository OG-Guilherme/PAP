<?php
session_start();
require_once 'conexao.php';

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

$mensagem = '';
$tipo = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {
    $email = $_POST['email'] ?? '';
    
    if($email) {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user) {
            // Gerar nova password temporária
            $nova_password = bin2hex(random_bytes(4)); // 8 caracteres
            $password_hash = password_hash($nova_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE utilizadores SET password = ? WHERE id = ?");
            $stmt->execute([$password_hash, $user['id']]);
            
            // Aqui normalmente enviarias por email, mas vamos mostrar na tela
            $mensagem = "Nova password temporária: <strong>$nova_password</strong><br>Use esta password para entrar e altere-a no seu perfil.";
            $tipo = 'success';
        } else {
            $mensagem = 'Email não encontrado no sistema.';
            $tipo = 'error';
        }
    } else {
        $mensagem = 'Por favor, insira o seu email.';
        $tipo = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Recuperar Password - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
    <style>
        .recuperar-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 40px;
            background: var(--cor-fundo-alt);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .recuperar-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .recuperar-header img {
            height: 80px;
            margin-bottom: 15px;
        }
        .mensagem {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .mensagem.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensagem.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
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

    <div class="container">
        <div class="recuperar-container">
            <div class="recuperar-header">
                <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb">
                <h2>Recuperar Password</h2>
            </div>
            
            <div class="info-box">
                ℹ️ Insira o seu email e receberá uma nova password temporária.
            </div>
            
            <?php if($mensagem): ?>
                <div class="mensagem <?php echo $tipo; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>
                
                <button type="submit" name="recuperar" class="btn-login">Recuperar Password</button>
            </form>
            
            <div class="login-footer">
                <a href="login.php">← Voltar ao login</a>
            </div>
        </div>
    </div>
</body>
</html>