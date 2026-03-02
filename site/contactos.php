<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = trim($_POST['nome']     ?? '');
    $email   = trim($_POST['email']    ?? '');
    $assunto = trim($_POST['assunto']  ?? '');
    $msg     = trim($_POST['mensagem'] ?? '');
    if ($nome && $email && $assunto && $msg) {
        $mensagem = 'success';
    } else {
        $mensagem = 'error';
    }
}

$paginaActiva = 'contactos';
$tituloBase   = 'Contactos';
$extraCSS = '<style>
.contactos-wrap{max-width:1000px;margin:0 auto;padding:56px 24px 80px;}
.page-hero-label{font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:10px;}
.page-hero h1{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:var(--cor-texto);letter-spacing:-.02em;margin-bottom:10px;}
.page-hero p{color:var(--cor-texto-claro);font-size:1rem;margin-bottom:48px;max-width:520px;}
.contactos-layout{display:grid;grid-template-columns:1fr 1.5fr;gap:48px;align-items:start;}
.contacto-cards{display:flex;flex-direction:column;gap:14px;}
.contacto-card{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:14px;padding:20px 22px;display:flex;align-items:flex-start;gap:16px;transition:border-color .2s;}
.contacto-card:hover{border-color:var(--cor-principal);}
.contacto-icon{width:42px;height:42px;border-radius:10px;background:rgba(244,164,66,.1);display:flex;align-items:center;justify-content:center;color:var(--cor-principal);flex-shrink:0;}
.contacto-card-label{font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--cor-texto-claro);margin-bottom:4px;}
.contacto-card-value{font-size:.95rem;font-weight:500;color:var(--cor-texto);}
.horario-box{background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:14px;padding:20px 22px;margin-top:14px;}
.horario-box h4{font-size:.82rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--cor-texto-claro);margin-bottom:14px;}
.horario-row{display:flex;justify-content:space-between;font-size:.88rem;padding:6px 0;border-bottom:1px solid var(--cor-borda);}
.horario-row:last-child{border:none;}
.horario-row .dias{color:var(--cor-texto);}
.horario-row .horas{color:var(--cor-texto-claro);}
.form-section h3{font-size:1.1rem;font-weight:700;margin-bottom:22px;color:var(--cor-texto);}
@media(max-width:760px){.contactos-layout{grid-template-columns:1fr;}}
</style>';
require_once '_header.php';
?>

<div class="contactos-wrap">
    <div class="page-hero-label">Fala connosco</div>
    <h1 style="font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:var(--cor-texto);letter-spacing:-.02em;margin-bottom:10px;">Entre em contacto</h1>
    <p style="color:var(--cor-texto-claro);font-size:1rem;margin-bottom:48px;max-width:520px;">Temos todo o gosto em responder às tuas dúvidas. Usa o formulário ou contacta-nos diretamente.</p>

    <div class="contactos-layout">
        <!-- Info -->
        <div>
            <div class="contacto-cards">
                <div class="contacto-card">
                    <div class="contacto-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div class="contacto-card-label">Email</div>
                        <div class="contacto-card-value"><?= SITE_EMAIL ?></div>
                    </div>
                </div>
                <div class="contacto-card">
                    <div class="contacto-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-1.94a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div>
                        <div class="contacto-card-label">Telefone</div>
                        <div class="contacto-card-value"><?= SITE_TELEFONE ?></div>
                    </div>
                </div>
                <div class="contacto-card">
                    <div class="contacto-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <div class="contacto-card-label">Morada</div>
                        <div class="contacto-card-value"><?= SITE_MORADA ?></div>
                    </div>
                </div>
            </div>

            <div class="horario-box">
                <h4>Horário de Atendimento</h4>
                <div class="horario-row"><span class="dias">Segunda a Sexta</span><span class="horas">08h00 – 17h30</span></div>
                <div class="horario-row"><span class="dias">Sábado</span><span class="horas">09h00 – 13h00</span></div>
                <div class="horario-row"><span class="dias">Domingo</span><span class="horas">Encerrado</span></div>
            </div>
        </div>

        <!-- Formulário -->
        <div>
            <div class="form-section">
                <h3>Envie uma mensagem</h3>

                <?php if ($mensagem === 'success'): ?>
                    <div class="mensagem success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.9rem;height:.9rem;vertical-align:middle;margin-right:6px;"><polyline points="20 6 9 17 4 12"/></svg>
                        Mensagem enviada! Entraremos em contacto em breve.
                    </div>
                <?php elseif ($mensagem === 'error'): ?>
                    <div class="mensagem error">Preencha todos os campos.</div>
                <?php endif; ?>

                <form method="POST" style="max-width:none;">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" required placeholder="O seu nome completo">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="o.seu@email.pt">
                    </div>
                    <div class="form-group">
                        <label>Assunto</label>
                        <input type="text" name="assunto" required placeholder="Em que podemos ajudar?">
                    </div>
                    <div class="form-group">
                        <label>Mensagem</label>
                        <textarea name="mensagem" required placeholder="Descreva a sua questão…" style="min-height:140px;"></textarea>
                    </div>
                    <button type="submit" class="btn">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>