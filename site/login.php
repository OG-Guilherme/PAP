<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nome']  = $user['nome'];
            $_SESSION['user_tipo']  = $user['tipo'];
            $_SESSION['user_email'] = $user['email'];
            header($user['tipo'] === 'admin' ? 'Location: ../admin/' : 'Location: index.php');
            exit;
        } else {
            $erro = 'Email ou password incorretos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}

$paginaActiva = '';
$tituloBase   = 'Entrar';
$extraCSS = '<style>
.auth-wrap{min-height:calc(100vh - 64px);display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--cor-fundo);}
.auth-card{width:100%;max-width:420px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:20px;padding:44px 40px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
.auth-logo{display:flex;justify-content:center;margin-bottom:28px;}
.auth-logo img{height:44px;}
.auth-title{font-size:1.5rem;font-weight:700;text-align:center;color:var(--cor-texto);margin-bottom:6px;letter-spacing:-0.01em;}
.auth-sub{text-align:center;color:var(--cor-texto-claro);font-size:.88rem;margin-bottom:32px;}
.auth-divider{height:1px;background:var(--cor-borda);margin:24px 0;position:relative;}
.auth-divider span{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:var(--cor-fundo-alt);padding:0 12px;font-size:.78rem;color:var(--cor-texto-claro);}
.auth-links{text-align:center;margin-top:24px;font-size:.88rem;color:var(--cor-texto-claro);line-height:2;}
.auth-links a{color:var(--cor-principal);text-decoration:none;font-weight:500;}
.auth-links a:hover{text-decoration:underline;}
@media(max-width:480px){.auth-card{padding:32px 24px;}}
</style>';
require_once '_header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="logo-<?= $_SESSION['tema'] === 'claro' ? 'claro' : 'escuro' ?>.png" alt="EduWeb">
        </div>
        <h1 class="auth-title">Bem-vindo de volta</h1>
        <p class="auth-sub">Entra na tua conta EduWeb</p>

        <?php if ($erro): ?>
            <div class="erro-msg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.95rem;height:.95rem;vertical-align:middle;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="email" placeholder="o.teu@email.pt">
            </div>
            <div class="form-group" style="margin-bottom:8px;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            <div style="text-align:right;margin-bottom:24px;">
                <a href="recuperar.php" style="font-size:.82rem;color:var(--cor-texto-claro);text-decoration:none;">Esqueceu a password?</a>
            </div>
            <button type="submit" name="login" class="btn-login">Entrar</button>
        </form>

        <div class="auth-links">
            Não tens conta? <a href="registar.php">Criar conta grátis</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>