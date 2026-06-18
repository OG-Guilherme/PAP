<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$mensagem = ''; $tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registar'])) {
    $nome      = $_POST['nome']             ?? '';
    $email     = $_POST['email']            ?? '';
    $password  = $_POST['password']         ?? '';
    $password2 = $_POST['password_confirm'] ?? '';
    $tipo_user = $_POST['tipo']             ?? 'visitante';

    if ($nome && $email && $password && $password2) {
        if ($password !== $password2) {
            $mensagem = 'As passwords não coincidem!'; $tipo = 'error';
        } elseif (strlen($password) < 6) {
            $mensagem = 'A password deve ter pelo menos 6 caracteres!'; $tipo = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $mensagem = 'Este email já está registado!'; $tipo = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, tipo) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$nome, $email, password_hash($password, PASSWORD_DEFAULT), $tipo_user])) {
                    $mensagem = 'Conta criada com sucesso! Pode agora fazer login.'; $tipo = 'success';
                } else {
                    $mensagem = 'Erro ao criar conta. Tente novamente.'; $tipo = 'error';
                }
            }
        }
    } else {
        $mensagem = 'Preencha todos os campos!'; $tipo = 'error';
    }
}

$paginaActiva = '';
$tituloBase   = 'Criar Conta';
$extraCSS = '<style>.form-row{display:grid;grid-template-columns:1fr 1fr;gap:15px;}@media(max-width:600px){.form-row{grid-template-columns:1fr;}}</style>';
require_once '_header.php';
?>

<div class="container">
    <div class="login-container" style="max-width:520px;">
        <div class="login-header">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" onerror="this.style.display='none'">
            <h2>Criar Conta</h2>
        </div>

        <?php if($mensagem): ?>
            <div class="mensagem <?= $tipo ?>">
                <?= htmlspecialchars($mensagem) ?>
                <?php if($tipo === 'success'): ?>
                    <br><a href="login.php" style="color:#166534;font-weight:bold;">Ir para o login →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required autofocus>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Tipo de Conta</label>
                <select name="tipo" required>
                    <option value="visitante">Visitante</option>
                    <option value="aluno">Aluno</option>
                    <option value="professor">Professor</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required minlength="6">
                    <small style="color:var(--cor-texto-claro);font-size:.8rem;">Mínimo 6 caracteres</small>
                </div>
                <div class="form-group">
                    <label>Confirmar Password</label>
                    <input type="password" name="password_confirm" required minlength="6">
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

<?php require_once '_footer.php'; ?>
