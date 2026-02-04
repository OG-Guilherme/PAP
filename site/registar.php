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

$mensagem = '';
$tipo = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registar'])) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $tipo_user = $_POST['tipo'] ?? 'visitante';
    
    if($nome && $email && $password && $password_confirm) {
        if($password === $password_confirm) {
            if(strlen($password) >= 6) {
                // Verificar se email já existe
                $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
                $stmt->execute([$email]);
                
                if($stmt->fetch()) {
                    $mensagem = 'Este email já está registado!';
                    $tipo = 'error';
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, tipo) VALUES (?, ?, ?, ?)");
                    
                    if($stmt->execute([$nome, $email, $password_hash, $tipo_user])) {
                        $mensagem = 'Conta criada com sucesso! Pode agora fazer login.';
                        $tipo = 'success';
                    } else {
                        $mensagem = 'Erro ao criar conta. Tente novamente.';
                        $tipo = 'error';
                    }
                }
            } else {
                $mensagem = 'A password deve ter pelo menos 6 caracteres!';
                $tipo = 'error';
            }
        } else {
            $mensagem = 'As passwords não coincidem!';
            $tipo = 'error';
        }
    } else {
        $mensagem = 'Preencha todos os campos!';
        $tipo = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="utf-8">
    <title>Criar Conta - EduWeb</title>
    <link rel="stylesheet" href="../important/style.css">
    <style>
        .registar-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            background: var(--cor-fundo-alt);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .registar-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .registar-header img {
            height: 70px;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        <div class="registar-container">
            <div class="registar-header">
                <img src="logo-<?php echo $theme === 'light' ? 'escuro' : 'claro'; ?>.png" alt="EduWeb">
                <h2>Criar Conta no EduWeb</h2>
            </div>
            
            <?php if($mensagem): ?>
                <div class="mensagem <?php echo $tipo; ?>">
                    <?php echo $mensagem; ?>
                    <?php if($tipo === 'success'): ?>
                        <br><a href="login.php" style="color: #155724; font-weight: bold;">Ir para o login →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Conta:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="visitante">Visitante</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required minlength="6">
                        <small style="color: var(--cor-texto-claro);">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Password:</label>
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                    </div>
                </div>
                
                <button type="submit" name="registar" class="btn-login">Criar Conta</button>
            </form>
            
            <div class="login-footer">
                Já tem conta? <a href="login.php">Entrar</a> | 
                <a href="index.php">Voltar ao início</a>
            </div>
        </div>
    </div>
</body>
</html>