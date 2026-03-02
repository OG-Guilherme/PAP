<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

requireLogin();

$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$mensagem = ''; $tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($nome && $email) {
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $mensagem = 'Este email já está em uso.'; $tipo_msg = 'error';
        } else {
            $pdo->prepare("UPDATE utilizadores SET nome=?, email=? WHERE id=?")->execute([$nome, $email, $_SESSION['user_id']]);
            $_SESSION['user_nome'] = $nome; $_SESSION['user_email'] = $email;
            $mensagem = 'Dados atualizados com sucesso.'; $tipo_msg = 'success';
            $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]); $user = $stmt->fetch();
        }
    } else { $mensagem = 'Preencha todos os campos.'; $tipo_msg = 'error'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar_pass'])) {
    $atual     = $_POST['password_atual']     ?? '';
    $nova      = $_POST['password_nova']      ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';
    if (password_verify($atual, $user['password'])) {
        if ($nova === $confirmar && strlen($nova) >= 6) {
            $pdo->prepare("UPDATE utilizadores SET password=? WHERE id=?")->execute([password_hash($nova, PASSWORD_DEFAULT), $_SESSION['user_id']]);
            $mensagem = 'Password alterada com sucesso.'; $tipo_msg = 'success';
        } else {
            $mensagem = strlen($nova) < 6 ? 'Mínimo 6 caracteres.' : 'As passwords não coincidem.'; $tipo_msg = 'error';
        }
    } else { $mensagem = 'Password atual incorreta.'; $tipo_msg = 'error'; }
}

$tipo_badges = ['admin'=>['Admin','#7c3aed','#f5f3ff'],'professor'=>['Professor','#0369a1','#eff6ff'],'aluno'=>['Aluno','#059669','#f0fdf4'],'visitante'=>['Visitante','#64748b','#f1f5f9']];
$badge = $tipo_badges[$user['tipo']] ?? $tipo_badges['visitante'];

$paginaActiva = '';
$tituloBase   = 'O Meu Perfil';
$extraCSS = '<style>
.perfil-wrap{max-width:760px;margin:0 auto;padding:40px 24px 80px;}
.perfil-hero{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:20px;padding:32px;display:flex;align-items:center;gap:24px;margin-bottom:28px;}
.perfil-avatar-big{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--cor-principal),var(--cor-secundaria));display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;font-weight:700;flex-shrink:0;overflow:hidden;}
.perfil-avatar-big img{width:100%;height:100%;object-fit:cover;}
.perfil-hero-name{font-size:1.3rem;font-weight:700;color:var(--cor-texto);margin-bottom:4px;}
.perfil-hero-email{font-size:.88rem;color:var(--cor-texto-claro);margin-bottom:10px;}
.tipo-badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:100px;font-size:.75rem;font-weight:700;letter-spacing:.04em;}
.admin-banner{background:linear-gradient(135deg,#1e3c72,#2a5298);border-radius:14px;padding:22px 26px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.admin-banner h4{color:white;font-size:.95rem;font-weight:700;margin-bottom:4px;}
.admin-banner p{color:rgba(255,255,255,.75);font-size:.82rem;}
.admin-banner .btn{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:white;white-space:nowrap;}
.admin-banner .btn:hover{background:rgba(255,255,255,.3);}
.perfil-section{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:14px;padding:26px;margin-bottom:20px;}
.perfil-section-title{font-size:.82rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--cor-texto-claro);margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.perfil-section-title svg{color:var(--cor-principal);}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:600px){.perfil-hero{flex-direction:column;text-align:center;}.form-row-2{grid-template-columns:1fr;}.admin-banner{flex-direction:column;}}
</style>';
require_once '_header.php';
?>

<div class="perfil-wrap">

    <!-- Header do perfil -->
    <div class="perfil-hero">
        <div class="perfil-avatar-big">
            <?php if (!empty($user['foto_perfil'])): ?>
                <img src="uploads/<?= $user['foto_perfil'] ?>" alt="">
            <?php else: ?>
                <?= mb_strtoupper(mb_substr($user['nome'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <div class="perfil-hero-name"><?= sanitize($user['nome']) ?></div>
            <div class="perfil-hero-email"><?= sanitize($user['email']) ?></div>
            <span class="tipo-badge" style="background:<?= $badge[2] ?>;color:<?= $badge[1] ?>;"><?= $badge[0] ?></span>
        </div>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipo_msg ?>" style="margin-bottom:20px;"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <!-- Painel admin -->
    <?php if (isAdmin()): ?>
    <div class="admin-banner">
        <div>
            <h4>Painel de Administração</h4>
            <p>Gerir notícias, eventos, cursos e utilizadores.</p>
        </div>
        <a href="../admin/" class="btn">Abrir Painel →</a>
    </div>
    <?php endif; ?>

    <!-- Editar informações -->
    <div class="perfil-section">
        <div class="perfil-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Editar Informações
        </div>
        <form method="POST" style="max-width:none;">
            <div class="form-row-2">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?= sanitize($user['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= sanitize($user['email']) ?>" required>
                </div>
            </div>
            <button type="submit" name="atualizar" class="btn">Guardar alterações</button>
        </form>
    </div>

    <!-- Alterar password -->
    <div class="perfil-section">
        <div class="perfil-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Alterar Password
        </div>
        <form method="POST" style="max-width:none;">
            <div class="form-group">
                <label>Password atual</label>
                <input type="password" name="password_atual" required placeholder="A tua password atual">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label>Nova password</label>
                    <input type="password" name="password_nova" minlength="6" required placeholder="Mín. 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar</label>
                    <input type="password" name="password_confirmar" minlength="6" required placeholder="Repetir nova password">
                </div>
            </div>
            <button type="submit" name="alterar_pass" class="btn">Alterar Password</button>
        </form>
    </div>

    <!-- Terminar sessão -->
    <div style="text-align:center;margin-top:12px;">
        <a href="logout.php" style="display:inline-flex;align-items:center;gap:6px;color:#dc2626;font-size:.88rem;text-decoration:none;padding:8px 16px;border:1px solid #fecaca;border-radius:8px;transition:background .15s;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Terminar Sessão
        </a>
    </div>
</div>

<?php require_once '_footer.php'; ?>