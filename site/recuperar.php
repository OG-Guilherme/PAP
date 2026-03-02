<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$mensagem = ''; $tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {
    $email = $_POST['email'] ?? '';
    if ($email) {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $nova = bin2hex(random_bytes(4));
            $pdo->prepare("UPDATE utilizadores SET password=? WHERE id=?")->execute([password_hash($nova, PASSWORD_DEFAULT), $user['id']]);
            $mensagem = "Nova password temporária: <strong>$nova</strong><br>Use esta password para entrar e altere-a no perfil.";
            $tipo = 'success';
        } else {
            $mensagem = 'Email não encontrado.'; $tipo = 'error';
        }
    } else {
        $mensagem = 'Insira o seu email.'; $tipo = 'error';
    }
}

$paginaActiva = '';
$tituloBase   = 'Recuperar Password';
require_once '_header.php';
?>

<div class="container">
    <div class="login-container">
        <div class="login-header">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" onerror="this.style.display='none'">
            <h2>Recuperar Password</h2>
        </div>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;padding:14px;border-radius:8px;margin-bottom:20px;font-size:.9rem;font-family:sans-serif;">
            ℹ️ Insira o seu email para receber uma nova password temporária.
        </div>

        <?php if($mensagem): ?>
            <div class="mensagem <?= $tipo ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus>
            </div>
            <button type="submit" name="recuperar" class="btn-login">Recuperar Password</button>
        </form>

        <div class="login-footer">
            <a href="login.php">← Voltar ao login</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>
