<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

requireLogin();

$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$mensagem = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($nome && $email) {
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $mensagem = 'Este email já está em uso.'; $tipo_msg = 'error';
        } else {
            $pdo->prepare("UPDATE utilizadores SET nome=?, email=? WHERE id=?")->execute([$nome, $email, $_SESSION['user_id']]);
            $_SESSION['user_nome'] = $nome; $_SESSION['user_email'] = $email;
            $mensagem = 'Dados actualizados!'; $tipo_msg = 'success';
            $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]); $user = $stmt->fetch();
        }
    } else { $mensagem = 'Preencha todos os campos.'; $tipo_msg = 'error'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar_pass'])) {
    $atual     = $_POST['password_atual'] ?? '';
    $nova      = $_POST['password_nova'] ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';
    if (password_verify($atual, $user['password'])) {
        if ($nova === $confirmar && strlen($nova) >= 6) {
            $pdo->prepare("UPDATE utilizadores SET password=? WHERE id=?")->execute([password_hash($nova, PASSWORD_DEFAULT), $_SESSION['user_id']]);
            $mensagem = 'Password alterada!'; $tipo_msg = 'success';
        } else {
            $mensagem = strlen($nova) < 6 ? 'Mínimo 6 caracteres.' : 'Passwords não coincidem.'; $tipo_msg = 'error';
        }
    } else { $mensagem = 'Password actual incorrecta.'; $tipo_msg = 'error'; }
}

$badgeClass = match($user['tipo']) { 'admin'=>'badge-admin','professor'=>'badge-professor','aluno'=>'badge-aluno', default=>'badge-visitante' };

$paginaActiva = '';
$tituloBase   = 'O Meu Perfil';
$extraCSS = '<style>
.perfil-section{background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:14px;padding:28px;margin-bottom:24px;}
.perfil-section h3{margin-bottom:20px;font-size:1rem;font-family:sans-serif;display:flex;align-items:center;gap:8px;}
.admin-card{background:linear-gradient(135deg,#1e3c72,#2a5298);color:white;border-radius:14px;padding:28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;}
.admin-card p{opacity:.85;font-size:.9rem;margin-top:4px;}
.admin-card .btn{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);}
.admin-card .btn:hover{background:rgba(255,255,255,.35);}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:600px){.form-row{grid-template-columns:1fr;}}
</style>';
require_once '_header.php';
?>

<div class="container" style="padding-top:50px;padding-bottom:60px;">
    <div class="perfil-container">

        <div class="perfil-header">
            <div class="perfil-avatar">
                <?php if (!empty($user['foto_perfil'])): ?>
                    <img src="uploads/<?= $user['foto_perfil'] ?>" alt="Foto">
                <?php else: ?>
                    <?= mb_strtoupper(mb_substr($user['nome'],0,1)) ?>
                <?php endif; ?>
            </div>
            <div class="perfil-info">
                <h2><?= sanitize($user['nome']) ?></h2>
                <p><?= sanitize($user['email']) ?></p>
                <span class="badge-tipo <?= $badgeClass ?>"><?= ucfirst($user['tipo']) ?></span>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
        <div class="admin-card">
            <div>
                <h3 style="color:white;margin:0;font-family:sans-serif;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;vertical-align:middle;display:inline-block;flex-shrink:0;"></svg> Painel de Administração</h3>
                <p>Gerir notícias, eventos, cursos e utilizadores.</p>
            </div>
            <a href="../admin/" class="btn">Abrir Painel →</a>
        </div>
        <?php endif; ?>

        <div class="perfil-section">
            <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;vertical-align:middle;display:inline-block;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar Informações</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" value="<?= sanitize($user['nome']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= sanitize($user['email']) ?>" required>
                    </div>
                </div>
                <button type="submit" name="atualizar" class="btn">Guardar</button>
            </form>
        </div>

        <div class="perfil-section">
            <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;vertical-align:middle;display:inline-block;flex-shrink:0;"></svg> Alterar Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Password Actual</label>
                    <input type="password" name="password_atual" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nova Password</label>
                        <input type="password" name="password_nova" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmar</label>
                        <input type="password" name="password_confirmar" minlength="6" required>
                    </div>
                </div>
                <button type="submit" name="alterar_pass" class="btn">Alterar</button>
            </form>
        </div>

        <div style="text-align:center;margin-top:16px;">
            <a href="logout.php" style="color:#dc2626;font-family:sans-serif;font-size:.9rem;text-decoration:none;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;vertical-align:middle;display:inline-block;flex-shrink:0;"></svg> Terminar Sessão</a>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>
