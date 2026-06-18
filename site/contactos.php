<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = $_POST['nome']     ?? '';
    $email   = $_POST['email']    ?? '';
    $assunto = $_POST['assunto']  ?? '';
    $msg     = $_POST['mensagem'] ?? '';
    $mensagem = ($nome && $email && $assunto && $msg)
        ? '<div class="mensagem success">✅ Mensagem enviada com sucesso! Entraremos em contacto em breve.</div>'
        : '<div class="mensagem error">⚠️ Por favor preencha todos os campos.</div>';
}

$paginaActiva = '';
$tituloBase   = 'Contactos';
$extraCSS = '<style>
.contacto-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin:36px 0;}
.contacto-card{background:var(--cor-fundo-alt);padding:24px;border-radius:12px;border:1px solid var(--cor-borda);}
.contacto-card h3{margin-bottom:10px;color:var(--cor-icone,var(--cor-principal));}
</style>';
require_once '_header.php';
?>

<div class="container" style="padding:40px 24px;">
    <h2>Contacte a Escola</h2>

    <div class="contacto-grid">
        <div class="contacto-card"><h3>📧 Email</h3><p><?= SITE_EMAIL ?></p></div>
        <div class="contacto-card"><h3>📞 Telefone</h3><p><?= SITE_TELEFONE ?></p></div>
        <div class="contacto-card"><h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.1rem;height:1.1rem;vertical-align:middle;display:inline-block;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Morada</h3><p><?= SITE_MORADA ?></p></div>
    </div>

    <?= $mensagem ?>

    <h3 style="margin:32px 0 20px;">Envie uma Mensagem à Escola</h3>
    <form method="POST">
        <div class="form-group"><label>Nome</label><input type="text" name="nome" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Assunto</label><input type="text" name="assunto" required></div>
        <div class="form-group"><label>Mensagem</label><textarea name="mensagem" required></textarea></div>
        <button type="submit" class="btn">Enviar Mensagem</button>
    </form>
</div>

<?php require_once '_footer.php'; ?>
