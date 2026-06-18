<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = $_POST['email'] ?? '';
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
            $erro = 'Email ou password incorretos!';
        }
    } else {
        $erro = 'Preencha todos os campos!';
    }
}

$paginaActiva = '';
$tituloBase   = 'Entrar';
require_once '_header.php';
?>

<div class="container">
    <div class="login-container">
        <div class="login-header">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" onerror="this.style.display='none'">
            <h2>Acesso à Área Reservada</h2>
        </div>

        <?php if ($erro): ?>
            <div class="erro-msg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;vertical-align:middle;display:inline-block;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" name="login" class="btn-login">Entrar</button>
        </form>

        <div class="login-footer" style="margin-top:20px;">
            <a href="recuperar.php">Esqueceu a password?</a><br>
            <span style="color:var(--cor-texto-claro);margin-top:10px;display:block;">
                Ainda não tens conta? <a href="registar.php">Registar</a>
            </span>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>
