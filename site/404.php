<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$paginaActiva = '';
$tituloBase   = 'Página não encontrada';
http_response_code(404);

$extraCSS = <<<'ENDCSS'
<style>
.error-wrap{min-height:calc(100vh - 64px);display:flex;align-items:center;justify-content:center;padding:40px 24px;}
.error-card{max-width:560px;width:100%;text-align:center;}
.error-code{font-size:clamp(5rem,18vw,9rem);font-weight:800;letter-spacing:-.04em;color:var(--cor-principal);line-height:1;margin-bottom:8px;opacity:.15;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.error-icon{margin:-40px auto 24px;width:80px;height:80px;background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:20px;display:flex;align-items:center;justify-content:center;color:var(--cor-principal);}
.error-title{font-size:1.6rem;font-weight:700;color:var(--cor-texto);letter-spacing:-.02em;margin-bottom:12px;}
.error-desc{font-size:.97rem;color:var(--cor-texto-claro);line-height:1.7;margin-bottom:36px;}
.error-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
.error-search{margin-top:48px;padding-top:32px;border-top:1px solid var(--cor-borda);}
.error-search p{font-size:.85rem;color:var(--cor-texto-claro);margin-bottom:12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.error-search-row{display:flex;gap:8px;max-width:360px;margin:0 auto;}
.error-search-row input{flex:1;min-width:0;}
.quick-links{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-top:32px;}
.quick-link{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1.5px solid var(--cor-borda);border-radius:100px;font-size:.82rem;font-weight:500;color:var(--cor-texto-claro);text-decoration:none;transition:all .15s;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.quick-link:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
</style>
ENDCSS;
require_once '_header.php';
?>
<div class="error-wrap">
  <div class="error-card">
    <div class="error-code">404</div>
    <div class="error-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="width:2rem;height:2rem;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="12"/><line x1="11" y1="16" x2="11.01" y2="16"/></svg>
    </div>
    <h1 class="error-title">Página não encontrada</h1>
    <p class="error-desc">O endereço que tentaste aceder não existe ou foi removido.<br>Verifica se o URL está correto ou usa a pesquisa abaixo.</p>
    <div class="error-actions">
      <a href="index.php" class="btn">← Ir para o início</a>
      <button onclick="history.back()" class="btn btn-outline">Voltar atrás</button>
    </div>
    <div class="error-search">
      <p>Ou pesquisa no site:</p>
      <form action="pesquisa.php" method="GET">
        <div class="error-search-row">
          <input type="text" name="q" placeholder="Pesquisar…">
          <button type="submit" class="btn">Pesquisar</button>
        </div>
      </form>
    </div>
    <div class="quick-links">
      <a href="cursos.php" class="quick-link">Cursos</a>
      <a href="noticias.php" class="quick-link">Notícias</a>
      <a href="eventos.php" class="quick-link">Eventos</a>
      <a href="faq.php" class="quick-link">FAQ</a>
      <a href="contactos.php" class="quick-link">Contactos</a>
    </div>
  </div>
</div>
<?php require_once '_footer.php'; ?>