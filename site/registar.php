<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$mensagem = ''; $tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registar'])) {
    $nome      = trim($_POST['nome']             ?? '');
    $email     = trim($_POST['email']            ?? '');
    $password  = $_POST['password']              ?? '';
    $password2 = $_POST['password_confirm']      ?? '';
    $tipo_user = $_POST['tipo']                  ?? 'visitante';

    if ($nome && $email && $password && $password2) {
        if ($password !== $password2) {
            $mensagem = 'As passwords não coincidem.'; $tipo = 'error';
        } elseif (strlen($password) < 6) {
            $mensagem = 'A password deve ter pelo menos 6 caracteres.'; $tipo = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $mensagem = 'Este email já está registado.'; $tipo = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, tipo) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$nome, $email, password_hash($password, PASSWORD_DEFAULT), $tipo_user])) {
                    $mensagem = 'Conta criada! Podes agora fazer login.'; $tipo = 'success';
                } else {
                    $mensagem = 'Erro ao criar conta. Tente novamente.'; $tipo = 'error';
                }
            }
        }
    } else {
        $mensagem = 'Preencha todos os campos.'; $tipo = 'error';
    }
}

$paginaActiva = '';
$tituloBase   = 'Criar Conta';
$extraCSS = '<style>
.auth-wrap{min-height:calc(100vh - 64px);display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--cor-fundo);}
.auth-card{width:100%;max-width:480px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:20px;padding:44px 40px;box-shadow:0 8px 40px rgba(0,0,0,0.08);}
.auth-logo{display:flex;justify-content:center;margin-bottom:28px;}
.auth-logo img{height:44px;}
.auth-title{font-size:1.5rem;font-weight:700;text-align:center;color:var(--cor-texto);margin-bottom:6px;letter-spacing:-0.01em;}
.auth-sub{text-align:center;color:var(--cor-texto-claro);font-size:.88rem;margin-bottom:32px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.auth-links{text-align:center;margin-top:24px;font-size:.88rem;color:var(--cor-texto-claro);line-height:2;}
.auth-links a{color:var(--cor-principal);text-decoration:none;font-weight:500;}
.tipo-options{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:4px;}
.tipo-option{display:none;}
.tipo-label{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px;border:1.5px solid var(--cor-borda);border-radius:10px;cursor:pointer;font-size:.82rem;font-weight:500;color:var(--cor-texto-claro);text-align:center;transition:all .15s;}
.tipo-label svg{color:var(--cor-texto-claro);transition:color .15s;}
.tipo-option:checked + .tipo-label{border-color:var(--cor-principal);color:var(--cor-principal);background:rgba(244,164,66,.07);}
.tipo-option:checked + .tipo-label svg{color:var(--cor-principal);}
@media(max-width:480px){.auth-card{padding:32px 20px;}.form-row{grid-template-columns:1fr;}}
</style>';
require_once '_header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="logo-<?= $_SESSION['tema'] === 'claro' ? 'claro' : 'escuro' ?>.png" alt="EduWeb">
        </div>
        <h1 class="auth-title">Criar conta</h1>
        <p class="auth-sub">Junta-te à comunidade EduWeb</p>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= $tipo ?>">
                <?= htmlspecialchars($mensagem) ?>
                <?php if ($tipo === 'success'): ?>
                    <br><a href="login.php" style="color:#166534;font-weight:600;">Ir para o login →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="nome" required autofocus placeholder="O teu nome">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="o.teu@email.pt">
            </div>

            <div class="form-group">
                <label style="margin-bottom:12px;">Tipo de conta</label>
                <div class="tipo-options">
                    <label>
                        <input type="radio" name="tipo" value="visitante" class="tipo-option" checked>
                        <span class="tipo-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Visitante
                        </span>
                    </label>
                    <label>
                        <input type="radio" name="tipo" value="aluno" class="tipo-option">
                        <span class="tipo-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            Aluno
                        </span>
                    </label>
                    <label>
                        <input type="radio" name="tipo" value="professor" class="tipo-option">
                        <span class="tipo-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            Professor
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="Mín. 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar</label>
                    <input type="password" name="password_confirm" required minlength="6" placeholder="Repetir password">
                </div>
            </div>

            <button type="submit" name="registar" class="btn-login">Criar Conta</button>
        </form>

        <div class="auth-links">
            Já tens conta? <a href="login.php">Entrar</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>