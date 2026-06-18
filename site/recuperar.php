<?php
// site/recuperar.php
// Pede o email → gera token → envia email com link

session_start();
require_once '../important/config.php';
require_once '../important/mailer.php';
require_once '_tema.php';

if (isLoggedIn()) { redirect('index.php'); }

$mensagem = '';
$tipo     = '';
$debugLinkHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {
    $email = trim($_POST['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Insere um endereço de email válido.';
        $tipo     = 'error';
    } else {
        // Verificar se existe na BD
        $stmt = $pdo->prepare("SELECT id, nome FROM utilizadores WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Sempre mostrar a mesma mensagem de sucesso (evita enumerar emails)
        $mensagem = 'Se o email existir na nossa plataforma, receberás um link para redefinir a password nos próximos minutos. Verifica também a pasta de spam.';
        $tipo     = 'success';

        if ($user) {
            // Invalidar tokens anteriores deste utilizador
            $pdo->prepare("UPDATE password_resets SET usado = 1 WHERE user_id = ?")
                ->execute([$user['id']]);

            // Gerar token seguro
            $token     = bin2hex(random_bytes(32)); // 64 chars hex
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $pdo->prepare(
                "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)"
            )->execute([$user['id'], $token, $expiresAt]);

            // Construir link
            $baseUrl = rtrim(SITE_URL, '/');
            $link    = "{$baseUrl}/site/nova_password.php?token={$token}";

            // Enviar email
            $assunto = SITE_NAME . ' — Recuperação de Password';
            $corpo   = emailRecuperacaoTemplate($user['nome'], $link);
            $result  = sendMail($pdo, $email, $assunto, $corpo);

            if ($result !== true) {
                // Regista sempre no log de erros do PHP
                error_log("[EduWeb] Erro ao enviar email de recuperação para {$email}: {$result}");

                // Em modo debug (DEBUG_MODE=true em config.php), mostra o link
                // diretamente na página — útil para testar sem teres de configurar
                // nenhuma conta de email real. Em produção (DEBUG_MODE=false) isto
                // fica sempre escondido.
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    $debugLinkHtml = '<div style="margin-top:14px;padding:12px 14px;background:var(--cor-fundo-alt);'
                        . 'border:1px dashed var(--cor-borda);border-radius:8px;font-size:.82rem;'
                        . 'font-family:monospace;word-break:break-all;">'
                        . '[DEBUG] Não foi possível enviar o email (' . htmlspecialchars($result) . ').<br>'
                        . 'Link direto para testares: <a href="' . htmlspecialchars($link) . '">'
                        . htmlspecialchars($link) . '</a></div>';
                }
            }
        }
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

        <div style="background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:.9rem;font-family:sans-serif;color:var(--cor-texto-claro);">
            ℹ️ Insere o teu email e enviaremos um link para definires uma nova password.
        </div>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= $tipo ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>
        <?= $debugLinkHtml ?>

        <?php if ($tipo !== 'success'): ?>
        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autofocus autocomplete="email">
            </div>
            <button type="submit" name="recuperar" class="btn-login">Enviar Link de Recuperação</button>
        </form>
        <?php endif; ?>

        <div class="login-footer" style="margin-top:20px;">
            <a href="login.php">← Voltar ao login</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>