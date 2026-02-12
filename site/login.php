<?php
session_start();
require_once '../important/conexao.php';

if(isset($_SESSION['user_id'])) {
    header('Location: ../site/index.php');
    exit;
}

if(isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

$erro = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo'];
            $_SESSION['user_email'] = $user['email'];
            
            if($user['tipo'] === 'admin') {
                header('Location: ../admin/index.php');
            } else {
                header('Location: ../site/index.php');
            }
            exit;
        } else {
            $erro = 'Email ou password incorretos!';
        }
    } else {
        $erro = 'Preencha todos os campos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Login - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
    <style>
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 40px;
            background: var(--cor-fundo-alt);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            height: 80px;
            margin-bottom: 15px;
        }
        .login-header h2 {
            margin: 0;
            color: var(--cor-texto);
        }
        .erro-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--cor-texto);
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--cor-borda);
            border-radius: 6px;
            background: var(--cor-fundo);
            color: var(--cor-texto);
            font-size: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--cor-principal);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: var(--cor-secundaria);
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .login-footer a {
            color: var(--cor-principal);
            text-decoration: none;
        }
    </style>
</head>
<body class="<?php echo $theme === 'light' ? 'tema-claro' : 'tema-escuro'; ?>">
    <header>
        <div class="header-top">
            <div class="header-top-content">
                <nav class="top-nav">
                    <ul>
                        <li><a href="../site/noticias.php">Notícias</a></li>
                        <li><a href="../site/eventos.php">Eventos</a></li>
                        <li><a href="../site/contactos.php">Contactos</a></li>
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
                        <li><a href="../site/index.php">Início</a></li>
                        <li><a href="../site/sobre.php">Sobre Nós</a></li>
                    </ul>
                </nav>
                <div style="width: 200px;"></div>
                <nav class="nav-right main-nav">
                    <ul>
                        <li><a href="../site/cursos.php">Cursos</a></li>
                        <li><a href="../site/sobre.php">Admissões</a></li>
                    </ul>
                </nav>
            </div>
            <div class="logo-area">
                <a href="../site/index.php">
                    <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb" class="logo">
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb">
                <h2>Entrar no EduWeb</h2>
            </div>
            
            <?php if($erro): ?>
                <div class="erro-msg">⚠️ <?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn-login">Entrar</button>
            </form>
            
            <div class="login-footer">
                <a href="../site/recuperar.php">Esqueceu a password?</a><br>
                <p style="margin-top: 15px; color: var(--cor-texto-claro);">
                    Não tem conta? <a href="registar.php">Criar conta</a>
                </p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EduWeb</h3>
                    <p>Plataforma educativa inovadora</p>
                </div>
                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <a href="../site/sobre.php">Sobre Nós</a>
                    <a href="../site/cursos.php">Cursos</a>
                    <a href="../site/contactos.php">Contactos</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> EduWeb. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>