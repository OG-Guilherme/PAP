<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$paginaActiva = 'faq';
$tituloBase   = 'FAQ — Perguntas Frequentes';

$svgQ = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;flex-shrink:0;color:var(--cor-icone,var(--cor-principal))"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';

$extraCSS = <<<'ENDCSS'
<style>
.faq-container{max-width:760px;margin:0 auto;padding:56px 0 80px;}
.faq-container h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);margin-bottom:8px;display:flex;align-items:center;gap:12px;}
.faq-container h1 svg{color:var(--cor-principal);flex-shrink:0;}
.faq-container>p{color:var(--cor-texto-claro);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;margin-bottom:40px;font-size:.97rem;}
.faq-section-title{font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);margin:36px 0 14px;padding-bottom:8px;border-bottom:1px solid var(--cor-borda);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.faq-item{border:1px solid var(--cor-borda);border-radius:10px;margin-bottom:8px;overflow:hidden;transition:border-color .15s;}
.faq-item.open{border-color:var(--cor-principal);}
.faq-question{width:100%;background:var(--cor-fundo-alt);border:none;padding:16px 20px;text-align:left;font-size:.93rem;font-weight:600;color:var(--cor-texto);cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:background .15s;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
.faq-question:hover{background:var(--cor-fundo);}
.faq-item.open .faq-question{background:var(--cor-fundo);color:var(--cor-principal);}
.faq-chevron{width:1rem;height:1rem;flex-shrink:0;transition:transform .2s;color:var(--cor-texto-claro);}
.faq-item.open .faq-chevron{transform:rotate(180deg);color:var(--cor-principal);}
.faq-answer{display:none;padding:0 20px 18px;font-size:.9rem;line-height:1.75;color:var(--cor-texto-claro);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--cor-fundo);}
.faq-item.open .faq-answer{display:block;}
</style>
ENDCSS;
require_once '_header.php';
?>

<div class="container" style="padding-top:50px;padding-bottom:70px;">
<div class="faq-container">

    <h1 style="font-family:Georgia,serif;font-size:2.2rem;color:var(--cor-principal);margin-bottom:8px;">
        <?= $svgQ ?> Perguntas Frequentes
    </h1>
    <p style="color:var(--cor-texto-claro);font-family:sans-serif;margin-bottom:40px;font-size:0.97rem;">
        Encontra aqui as respostas às dúvidas mais comuns sobre o EduWeb.
        Se não encontrares o que procuras, <a href="contactos.php" style="color:var(--cor-principal);">contacta-nos</a>.
    </p>

    <!-- ── INSCRIÇÕES ─────────────────────────────────────────────── -->
    <div class="faq-section-title">Inscrições e Admissões</div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como me posso inscrever no EduWeb?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Podes criar uma conta gratuitamente clicando em <strong>"Criar Conta"</strong> no menu superior. Basta preencher o teu nome, email e uma password. Depois de criada a conta, podes fazer login e aceder a todos os recursos disponíveis para o teu nível de utilizador.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Quais são os tipos de conta disponíveis?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Existem quatro tipos de conta: <strong>Visitante</strong> (acesso básico à informação pública), <strong>Aluno</strong> (acesso a conteúdos académicos e comunicação interna), <strong>Professor</strong> (acesso a ferramentas de gestão de turmas e publicação de conteúdos) e <strong>Administrador</strong> (acesso total ao painel de gestão da plataforma).
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como posso alterar o meu tipo de conta de Visitante para Aluno?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            A alteração de tipo de conta é feita pelos administradores da plataforma. Após te inscreveres como aluno numa instituição parceira, a tua conta será atualizada automaticamente ou deves contactar o suporte em <a href="contactos.php" style="color:var(--cor-principal);">Contactos</a> com comprovativo de matrícula.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Existe algum custo para criar conta no EduWeb?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Não. A criação de conta e o acesso à plataforma EduWeb são totalmente <strong>gratuitos</strong>. Todos os conteúdos públicos (notícias, eventos, informações de cursos) estão acessíveis mesmo sem conta.
        </div>
    </div>

    <!-- ── CURSOS ──────────────────────────────────────────────────── -->
    <div class="faq-section-title">Cursos e Formação</div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Que tipos de cursos estão disponíveis no EduWeb?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            O EduWeb disponibiliza informação sobre três tipos de cursos: <strong>Cursos Regulares</strong> (plano de estudos tradicional), <strong>Cursos Profissionais</strong> (orientados para o mercado de trabalho) e <strong>Cursos CEF</strong> (Cursos de Educação e Formação para jovens e adultos). Podes ver todos os cursos disponíveis na secção <a href="cursos.php" style="color:var(--cor-principal);">Cursos</a>.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como posso saber quais as disciplinas de um curso específico?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Na página de cada curso (clicando em "Saber mais" na listagem de cursos) podes encontrar a descrição completa, objetivos, saídas profissionais e o plano de estudos detalhado com todas as disciplinas organizadas por ano e semestre.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Qual a duração dos cursos oferecidos?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            A duração varia consoante o tipo: os cursos regulares têm tipicamente <strong>3 anos</strong>, os profissionais <strong>3 anos</strong> e os CEF podem ter entre <strong>1 e 2 anos</strong>. A duração exata está indicada na página de cada curso.
        </div>
    </div>

    <!-- ── CONTA E PLATAFORMA ──────────────────────────────────────── -->
    <div class="faq-section-title">Conta e Plataforma</div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Esqueci-me da minha password. Como posso recuperá-la?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Na página de login, clica em <strong>"Esqueceu a password?"</strong>. Insere o teu email e receberás uma password temporária que podes usar para entrar e depois alterar no teu perfil.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como posso alterar o tema (claro/escuro) da plataforma?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Clica no ícone de <strong>lua (🌙) ou sol (☀️)</strong> no canto superior direito da barra de navegação. A preferência fica guardada para as próximas visitas.
        </div>
    </div>



    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Os meus dados pessoais estão seguros?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Sim. As passwords são armazenadas de forma encriptada (hash bcrypt) e nunca em texto simples. A plataforma cumpre as boas práticas de segurança web. Para questões específicas sobre privacidade, contacta-nos através da página de <a href="contactos.php" style="color:var(--cor-principal);">Contactos</a>.
        </div>
    </div>

    <!-- ── EVENTOS E NOTÍCIAS ──────────────────────────────────────── -->
    <div class="faq-section-title">Eventos e Notícias</div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como posso comentar numa notícia ou evento?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Para comentar, precisas de ter uma conta e estar com sessão iniciada. Na página da notícia ou evento, no final encontras a secção de comentários com uma caixa de texto. Os comentários são moderados antes de serem publicados.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Como me posso inscrever num evento?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Quando as inscrições estão abertas, a página do evento mostra um botão ou formulário de inscrição. Se não houver essa opção, significa que o evento não requer inscrição prévia ou que as inscrições ainda não abriram. Para dúvidas específicas, contacta o organizador do evento.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Posso pesquisar eventos por data ou categoria?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Sim! Na página de <a href="eventos.php" style="color:var(--cor-principal);">Eventos</a> encontras filtros por palavra-chave, categoria, data de início e data de fim. Basta preencher os campos e clicar em "Filtrar". O mesmo se aplica às <a href="noticias.php" style="color:var(--cor-principal);">Notícias</a>.
        </div>
    </div>

    <!-- ── SUPORTE ─────────────────────────────────────────────────── -->
    <div class="faq-section-title">Suporte</div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Não encontrei resposta à minha dúvida. O que faço?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Podes contactar-nos diretamente através da página de <a href="contactos.php" style="color:var(--cor-principal);">Contactos</a>. Responderemos ao teu email o mais brevemente possível, normalmente em 1 a 2 dias úteis.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Encontrei um erro ou problema técnico na plataforma. Como reporto?
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="faq-answer">
            Usa o formulário de <a href="contactos.php" style="color:var(--cor-principal);">Contacto</a> com o assunto "Erro técnico" e descreve o problema com o máximo de detalhe possível (página onde ocorreu, o que estavas a fazer, mensagem de erro se houver). Isso ajuda-nos a resolver o problema rapidamente.
        </div>
    </div>

</div>
</div>

<script>
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    // Fecha todos os outros
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    // Abre este se estava fechado
    if (!isOpen) item.classList.add('open');
}
</script>

<?php require_once '_footer.php'; ?>