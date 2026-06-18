<?php
// site/nova_password.php
// Página para onde o link do email aponta.
// Valida o token e permite definir nova password.

session_start();
require_once '../important/config.php';
require_once '_tema.php';

if (isLoggedIn()) { redirect('index.php'); }

$token     = trim($_GET['token'] ?? '');
$mensagem  = '';
$tipo      = '';
$tokenValido = false;
$user        = null;

// ── Validar token ────────────────────────────────────────
if ($token) {
    $stmt = $pdo->prepare("
        SELECT pr.*, u.nome, u.email
        FROM password_resets pr
        JOIN utilizadores u ON pr.user_id = u.id
        WHERE pr.token = ?
          AND pr.usado = 0
          AND pr.expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $resetRow = $stmt->fetch();

    if ($resetRow) {
        $tokenValido = true;
        $user        = $resetRow;
    } else {
        $mensagem = 'Este link é inválido ou já expirou. Pede um novo link de recuperação.';
        $tipo     = 'error';
    }
} else {
    redirect('recuperar.php');
}

// ── Processar nova password ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido) {
    $nova      = $_POST['password']         ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';

    if (strlen($nova) < 6) {
        $mensagem = 'A password deve ter pelo menos 6 caracteres.';
        $tipo     = 'error';
    } elseif ($nova !== $confirmar) {
        $mensagem = 'As passwords não coincidem.';
        $tipo     = 'error';
    } else {
        // Atualizar password
        $hash = password_hash($nova, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE utilizadores SET password = ? WHERE id = ?")
            ->execute([$hash, $user['user_id']]);

        // Marcar token como usado
        $pdo->prepare("UPDATE password_resets SET usado = 1 WHERE token = ?")
            ->execute([$token]);

        $mensagem    = 'Password alterada com sucesso! Já podes fazer login com a nova password.';
        $tipo        = 'success';
        $tokenValido = false; // esconder o formulário
    }
}

$paginaActiva = '';
$tituloBase   = 'Nova Password';
require_once '_header.php';
?>

<div class="container">
    <div class="login-container">
        <div class="login-header">
            <img src="logo-<?= $logoImg ?>.png" alt="EduWeb" onerror="this.style.display='none'">
            <h2>Definir Nova Password</h2>
        </div>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= $tipo ?>">
                <?= htmlspecialchars($mensagem) ?>
                <?php if ($tipo === 'success'): ?>
                    <br><a href="login.php" style="color:#166534;font-weight:bold;">Ir para o login →</a>
                <?php elseif ($tipo === 'error' && !$tokenValido): ?>
                    <br><a href="recuperar.php" style="color:#991b1b;font-weight:bold;">Pedir novo link →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($tokenValido): ?>
            <p style="margin-bottom:20px;font-size:.9rem;font-family:sans-serif;color:var(--cor-texto-claro);">
                A definir nova password para <strong><?= htmlspecialchars($user['email']) ?></strong>.
            </p>
            <form method="POST" style="max-width:none;">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label for="password">Nova Password</label>
                    <input type="password" id="password" name="password"
                           required minlength="6" autofocus autocomplete="new-password">
                    <small style="color:var(--cor-texto-claro);font-size:.8rem;font-family:sans-serif;">
                        Mínimo 6 caracteres
                    </small>
                </div>

                <div class="form-group">
                    <label for="password_confirmar">Confirmar Password</label>
                    <input type="password" id="password_confirmar" name="password_confirmar"
                           required minlength="6" autocomplete="new-password">
                </div>

                <!-- Indicador de força da password -->
                <div style="margin-bottom:20px;">
                    <div id="strength-bar" style="height:4px;border-radius:2px;background:var(--cor-borda);transition:all .3s;">
                        <div id="strength-fill" style="height:100%;width:0;border-radius:2px;transition:all .3s;"></div>
                    </div>
                    <small id="strength-label" style="font-size:.78rem;font-family:sans-serif;color:var(--cor-texto-claro);"></small>
                </div>

                <button type="submit" class="btn-login">Guardar Nova Password</button>
            </form>

            <script>
            const pwInput = document.getElementById('password');
            const fill    = document.getElementById('strength-fill');
            const label   = document.getElementById('strength-label');

            pwInput.addEventListener('input', () => {
                const v = pwInput.value;
                let score = 0;
                if (v.length >= 6)  score++;
                if (v.length >= 10) score++;
                if (/[A-Z]/.test(v)) score++;
                if (/[0-9]/.test(v)) score++;
                if (/[^A-Za-z0-9]/.test(v)) score++;

                const levels = [
                    { pct: '0%',   color: 'transparent', text: '' },
                    { pct: '25%',  color: '#ef4444',      text: 'Muito fraca' },
                    { pct: '50%',  color: '#f59e0b',      text: 'Fraca' },
                    { pct: '75%',  color: '#3b82f6',      text: 'Boa' },
                    { pct: '90%',  color: '#10b981',      text: 'Forte' },
                    { pct: '100%', color: '#059669',      text: 'Muito forte' },
                ];
                const l = levels[score] || levels[0];
                fill.style.width  = l.pct;
                fill.style.background = l.color;
                label.textContent = l.text;
                label.style.color = l.color;
            });
            </script>
        <?php endif; ?>

        <div class="login-footer" style="margin-top:20px;">
            <a href="login.php">← Voltar ao login</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>