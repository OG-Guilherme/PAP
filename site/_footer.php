<?php
/**
 * _footer.php — Footer institucional EduWeb
 */
?>
<footer>
    <div class="container">
        <div class="footer-content">

            <div class="footer-section">
                <h3>EduWeb</h3>
                <p>Escola Secundária da Amadora.<br>Ao serviço da nossa comunidade educativa há décadas.</p>
                <div class="footer-horario">
                    <strong class="footer-horario-titulo">Secretaria</strong>
                    <p>Segunda a Sexta: 8h30 – 17h30</p>
                    <p>Sábado: 9h00 – 12h30</p>
                </div>
            </div>

            <div class="footer-section">
                <h3>Oferta Formativa</h3>
                <a href="cursos.php">Cursos Regulares</a>
                <a href="cursos.php?tipo=Profissional">Cursos Profissionais</a>
                <a href="cursos.php?tipo=CEF">Cursos CEF</a>
                <a href="sobre.php">Sobre a Escola</a>
                <a href="faq.php">Perguntas Frequentes</a>
            </div>

            <div class="footer-section">
                <h3>Comunidade</h3>
                <a href="noticias.php">Notícias</a>
                <a href="eventos.php">Eventos e Agenda</a>
                <a href="galeria.php">Galeria</a>
                <a href="contactos.php">Contactos</a>
                <?php if (isLoggedIn()): ?>
                    <a href="perfil.php">O Meu Perfil</a>
                    <a href="logout.php">Terminar Sessão</a>
                <?php else: ?>
                    <a href="login.php">Iniciar Sessão</a>
                    <a href="registar.php">Criar Conta</a>
                <?php endif; ?>
            </div>

            <div class="footer-section">
                <h3>Contacto</h3>
                <p>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;vertical-align:middle;display:inline-block;flex-shrink:0;margin-right:4px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?>
                </p>
                <p>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;vertical-align:middle;display:inline-block;flex-shrink:0;margin-right:4px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.83a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.72 16z"/></svg>
                    <?= defined('SITE_TELEFONE') ? SITE_TELEFONE : '' ?>
                </p>
                <p>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;vertical-align:middle;display:inline-block;flex-shrink:0;margin-right:4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= defined('SITE_MORADA') ? SITE_MORADA : '' ?>
                </p>
            </div>

        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> EduWeb — Escola Secundária, Amadora. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

</body>
</html>