<?php
/**
 * _footer.php — Footer reutilizável para páginas em /site/
 * Usar no final de cada página antes de </body></html>
 *
 * Uso: require_once '_footer.php';
 */
?>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>EduWeb</h3>
                <p>Plataforma educativa inovadora</p>
            </div>
            <div class="footer-section">
                <h3>Links Rápidos</h3>
                <a href="sobre.php">Sobre Nós</a>
                <a href="cursos.php">Cursos</a>
                <a href="noticias.php">Notícias</a>
                <a href="eventos.php">Eventos</a>
                <a href="contactos.php">Contactos</a>
            </div>
            <div class="footer-section">
                <h3>Conta</h3>
                <?php if (isLoggedIn()): ?>
                    <a href="perfil.php">O Meu Perfil</a>
                    <a href="logout.php">Terminar Sessão</a>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                    <a href="registar.php">Criar Conta</a>
                <?php endif; ?>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <p>📧 <?= defined('SITE_EMAIL')    ? SITE_EMAIL    : '' ?></p>
                <p>📞 <?= defined('SITE_TELEFONE') ? SITE_TELEFONE : '' ?></p>
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:0.85rem;height:0.85rem;vertical-align:middle;display:inline-block;flex-shrink:0;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> <?= defined('SITE_MORADA')   ? SITE_MORADA   : '' ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> EduWeb. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

</body>
</html>
