<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$mensagem = ''; $tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $nova = bin2hex(random_bytes(4));
            $pdo->prepare("UPDATE utilizadores SET password=? WHERE id=?")
                ->execute([password_hash($nova, PASSWORD_DEFAULT), $user['id']]);
            $mensagem = "Password temporária gerada: <strong style='font-family:monospace;letter-spacing:.05em;'>$nova</strong><br><small>Entra com esta password e altera-a no teu perfil.</small>";
            $tipo = 'success';
        } else {
            $mensagem = 'Não encontrámos nenhuma conta com esse email.'; $tipo = 'error';
        }
    } else {
        $mensagem = 'Insere o teu email.'; $tipo = 'error';
    }
}

$paginaActiva = '';
$tituloBase   = 'Recuperar Password';
$extraCSS = '<style>
.auth-wrap{min-height:calc(100vh - 64px);display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--cor-fundo);}
.auth-card{width:100%;max-width:420px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:20px;padding:44px 40px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
.auth-logo{display:flex;justify-content:center;margin-bottom:28px;}
.auth-logo img{height:44px;}
.auth-title{font-size:1.5rem;font-weight:700;text-align:center;color:var(--cor-texto);margin-bottom:6px;letter-spacing:-0.01em;}
.auth-sub{text-align:center;color:var(--cor-texto-claro);font-size:.88rem;margin-bottom:32px;}
.auth-info{background:var(--cor-fundo);border:1px solid var(--cor-borda);border-radius:10px;padding:14px 16px;margin-bottom:24px;font-size:.85rem;color:var(--cor-texto-claro);display:flex;gap:10px;align-items:flex-start;}
.auth-links{text-align:center;margin-top:24px;font-size:.88rem;}
.auth-links a{color:var(--cor-principal);text-decoration:none;font-weight:500;}
@media(max-width:480px){.auth-card{padding:32px 20px;}}
</style>';
require_once '_header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="logo-<?= $_SESSION['tema'] === 'claro' ? 'claro' : 'escuro' ?>.png" alt="EduWeb">
        </div>
        <h1 class="auth-title">Recuperar password</h1>
        <p class="auth-sub">Vamos gerar uma password temporária</p>

        <div class="auth-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;margin-top:1px;color:var(--cor-principal);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Insere o email associado à tua conta. Receberás uma password temporária para aceder.</span>
        </div>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= $tipo ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus placeholder="o.teu@email.pt">
            </div>
            <button type="submit" name="recuperar" class="btn-login">Recuperar Password</button>
        </form>

        <div class="auth-links">
            <a href="login.php">← Voltar ao login</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>