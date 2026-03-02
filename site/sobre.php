<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

// Números reais da base de dados
$total_cursos     = $pdo->query("SELECT COUNT(*) FROM cursos WHERE ativo=1")->fetchColumn();
$total_alunos     = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo='aluno' AND ativo=1")->fetchColumn();
$total_professores= $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo='professor' AND ativo=1")->fetchColumn();
$total_noticias   = $pdo->query("SELECT COUNT(*) FROM noticias WHERE publicado=1")->fetchColumn();

$paginaActiva = 'sobre';
$tituloBase   = 'Sobre Nós';

$extraCSS = <<<'CSS'
<style>
@import url("https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=DM+Sans:wght@300;400;500;600&display=swap");

/* ── Sobre — estilos exclusivos ── */
.sobre-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 40px 80px;
    font-family: "DM Sans", sans-serif;
}

/* Hero da página Sobre */
.sobre-hero {
    padding: 72px 40px 60px;
    max-width: 1100px;
    margin: 0 auto;
}
.sobre-hero-label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--cor-principal);
    margin-bottom: 16px;
}
.sobre-hero h1 {
    font-family: "Playfair Display", Georgia, serif;
    font-size: clamp(2.2rem, 5vw, 3.6rem);
    font-weight: 700;
    line-height: 1.1;
    color: var(--cor-texto);
    margin-bottom: 20px;
    max-width: 700px;
}
.sobre-hero p {
    font-size: 1.1rem;
    line-height: 1.75;
    color: var(--cor-texto-claro);
    max-width: 600px;
}

/* Linha divisória decorativa */
.sobre-divider {
    height: 1px;
    background: linear-gradient(90deg, var(--cor-principal), transparent);
    max-width: 1100px;
    margin: 0 auto 60px;
    padding: 0 40px;
    box-sizing: border-box;
}

/* Stats em destaque */
.sobre-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--cor-borda);
    border: 1px solid var(--cor-borda);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 72px;
}
.sobre-stat {
    background: var(--cor-fundo-alt);
    padding: 36px 28px;
    text-align: center;
    transition: background 0.2s;
}
.sobre-stat:hover { background: var(--cor-fundo); }
.sobre-stat-num {
    font-family: "Playfair Display", serif;
    font-size: 3rem;
    font-weight: 700;
    color: var(--cor-principal);
    line-height: 1;
    margin-bottom: 8px;
    display: block;
}
.sobre-stat-label {
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--cor-texto-claro);
}

/* Secções em 2 colunas */
.sobre-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
    margin-bottom: 72px;
}
.sobre-2col.reverse { direction: rtl; }
.sobre-2col.reverse > * { direction: ltr; }

.sobre-text-block h2 {
    font-family: "Playfair Display", serif;
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--cor-texto);
    margin-bottom: 18px;
    line-height: 1.2;
}
.sobre-text-block p {
    font-size: 0.97rem;
    line-height: 1.8;
    color: var(--cor-texto-claro);
    margin-bottom: 14px;
}
.sobre-text-block .label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--cor-principal);
    margin-bottom: 12px;
    display: block;
}

/* Foto placeholder das instalações */
.sobre-foto {
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--cor-principal) 0%, var(--cor-secundaria) 100%);
    aspect-ratio: 4/3;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.sobre-foto img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.sobre-foto-placeholder {
    color: rgba(255,255,255,0.7);
    text-align: center;
    padding: 20px;
}
.sobre-foto-placeholder svg { width: 3rem; height: 3rem; margin-bottom: 12px; opacity: 0.6; }
.sobre-foto-placeholder p { font-size: 0.85rem; opacity: 0.7; margin: 0; }

/* Valores — grid */
.valores-section { margin-bottom: 72px; }
.valores-section h2 {
    font-family: "Playfair Display", serif;
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--cor-texto);
    margin-bottom: 32px;
}
.valores-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.valor-item {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 14px;
    padding: 28px 24px;
    transition: all 0.22s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.valor-item:hover {
    border-color: var(--cor-principal);
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}
.valor-icon {
    width: 44px; height: 44px;
    background: rgba(244,164,66,0.1);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: var(--cor-principal);
    flex-shrink: 0;
    transition: background 0.2s;
}
.valor-item:hover .valor-icon { background: rgba(244,164,66,0.18); }
.valor-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--cor-texto);
}
.valor-desc {
    font-size: 0.85rem;
    color: var(--cor-texto-claro);
    line-height: 1.6;
}

/* Instalações */
.instalacoes-section { margin-bottom: 72px; }
.instalacoes-section h2 {
    font-family: "Playfair Display", serif;
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--cor-texto);
    margin-bottom: 32px;
}
.instalacoes-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}
.instalacao-card {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    transition: all 0.2s ease;
    cursor: default;
}
.instalacao-card:hover {
    border-color: var(--cor-principal);
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.instalacao-card svg { color: var(--cor-principal); }
.instalacao-card strong {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--cor-texto);
    line-height: 1.3;
}

/* Equipa */
.equipa-section { margin-bottom: 72px; }
.equipa-section h2 {
    font-family: "Playfair Display", serif;
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--cor-texto);
    margin-bottom: 8px;
}
.equipa-section > p {
    font-size: 0.97rem;
    color: var(--cor-texto-claro);
    margin-bottom: 36px;
}
.equipa-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.equipa-card {
    background: var(--cor-fundo-alt);
    border: 1.5px solid var(--cor-borda);
    border-radius: 14px;
    padding: 28px 20px;
    text-align: center;
    transition: all 0.22s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.equipa-card:hover {
    border-color: var(--cor-principal);
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.1);
}
.equipa-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--cor-principal), var(--cor-secundaria));
    display: flex; align-items: center; justify-content: center;
    font-family: "Playfair Display", serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    overflow: hidden;
}
.equipa-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.equipa-nome {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--cor-texto);
}
.equipa-cargo {
    font-size: 0.8rem;
    color: var(--cor-principal);
    font-weight: 500;
    letter-spacing: 0.02em;
}
.equipa-area {
    font-size: 0.78rem;
    color: var(--cor-texto-claro);
}

/* CTA */
.sobre-cta {
    background: var(--cor-principal);
    border-radius: 20px;
    padding: 56px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.sobre-cta::before {
    content: "";
    position: absolute;
    inset: -50%;
    background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 70%);
}
.sobre-cta-inner { position: relative; z-index: 1; }
.sobre-cta h2 {
    font-family: "Playfair Display", serif;
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 12px;
}
.sobre-cta p {
    font-size: 1rem;
    color: rgba(255,255,255,0.85);
    margin-bottom: 30px;
}
.sobre-cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.cta-btn-white {
    background: white; color: var(--cor-principal);
    padding: 13px 28px; border-radius: 10px;
    font-weight: 700; font-size: 0.92rem;
    text-decoration: none; transition: all 0.2s;
    font-family: "DM Sans", sans-serif;
}
.cta-btn-white:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
.cta-btn-outline {
    background: transparent; color: white;
    padding: 12px 24px; border-radius: 10px;
    border: 2px solid rgba(255,255,255,0.5);
    font-weight: 600; font-size: 0.92rem;
    text-decoration: none; transition: all 0.2s;
    font-family: "DM Sans", sans-serif;
}
.cta-btn-outline:hover { border-color: white; background: rgba(255,255,255,0.1); }

@media (max-width: 900px) {
    .sobre-wrap, .sobre-hero { padding-left: 20px; padding-right: 20px; }
    .sobre-stats { grid-template-columns: 1fr 1fr; }
    .sobre-2col  { grid-template-columns: 1fr; gap: 30px; }
    .valores-grid   { grid-template-columns: 1fr 1fr; }
    .instalacoes-grid { grid-template-columns: 1fr 1fr; }
    .equipa-grid    { grid-template-columns: 1fr 1fr; }
    .sobre-cta      { padding: 40px 20px; }
}
@media (max-width: 560px) {
    .sobre-stats { grid-template-columns: 1fr 1fr; }
    .valores-grid { grid-template-columns: 1fr; }
    .instalacoes-grid { grid-template-columns: 1fr 1fr; }
    .equipa-grid  { grid-template-columns: 1fr 1fr; }
}
</style>
CSS;

require_once '_header.php';
?>

<!-- ══ Hero ══════════════════════════════════════════════ -->
<div class="sobre-hero">
    <div class="sobre-hero-label">A nossa escola</div>
    <h1>Uma comunidade educativa construída<br><em>para o futuro</em></h1>
    <p>O EduWeb une alunos, professores e famílias numa plataforma pensada para tornar a educação mais acessível, transparente e eficaz.</p>
</div>

<div class="sobre-divider"></div>

<!-- ══ Stats reais da BD ════════════════════════════════ -->
<div class="sobre-wrap">

    <div class="sobre-stats">
        <div class="sobre-stat">
            <span class="sobre-stat-num"><?= $total_alunos > 0 ? $total_alunos.'+' : '500+' ?></span>
            <span class="sobre-stat-label">Alunos ativos</span>
        </div>
        <div class="sobre-stat">
            <span class="sobre-stat-num"><?= $total_cursos > 0 ? $total_cursos : '15+' ?></span>
            <span class="sobre-stat-label">Cursos disponíveis</span>
        </div>
        <div class="sobre-stat">
            <span class="sobre-stat-num"><?= $total_professores > 0 ? $total_professores.'+' : '50+' ?></span>
            <span class="sobre-stat-label">Professores</span>
        </div>
        <div class="sobre-stat">
            <span class="sobre-stat-num">95%</span>
            <span class="sobre-stat-label">Taxa de sucesso</span>
        </div>
    </div>

    <!-- ══ Missão + Foto ══════════════════════════════════ -->
    <div class="sobre-2col">
        <div class="sobre-text-block">
            <span class="label">Quem somos</span>
            <h2>Educação de qualidade, acessível a todos</h2>
            <p>O EduWeb nasceu com um objetivo simples: tornar a informação escolar clara e disponível para toda a comunidade. Alunos, professores e famílias num só sítio.</p>
            <p>Combinamos tecnologia com práticas pedagógicas modernas para criar um ambiente onde aprender é natural — não uma obrigação burocrática.</p>
        </div>
        <div class="sobre-foto">
            <div class="sobre-foto-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <p>Fotografia das instalações</p>
            </div>
        </div>
    </div>

    <!-- ══ História + Foto 2 ══════════════════════════════ -->
    <div class="sobre-2col reverse">
        <div class="sobre-text-block">
            <span class="label">A nossa história</span>
            <h2>Décadas de excelência educativa</h2>
            <p>Fundada com o compromisso de servir a comunidade da Amadora, a escola tem crescido e adaptado-se às necessidades de cada geração — sem perder a identidade e os valores que a definem.</p>
            <p>Hoje somos uma referência local, com um corpo docente experiente e uma comunidade de alunos que se orgulham do percurso aqui feito.</p>
        </div>
        <div class="sobre-foto">
            <div class="sobre-foto-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <p>Fotografia histórica da escola</p>
            </div>
        </div>
    </div>

    <!-- ══ Valores ════════════════════════════════════════ -->
    <div class="valores-section">
        <span style="font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);display:block;margin-bottom:10px;">Os nossos valores</span>
        <h2>O que nos orienta</h2>
        <div class="valores-grid">
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M6 9H3.5a2.5 2.5 0 0 0 0 5H6"/><path d="M18 9h2.5a2.5 2.5 0 0 1 0 5H18"/><path d="M6 3h12v11a6 6 0 0 1-12 0V3z"/></svg>
                </div>
                <div class="valor-title">Excelência Académica</div>
                <div class="valor-desc">Comprometemo-nos com os mais altos padrões de qualidade no ensino e na formação de cada aluno.</div>
            </div>
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M9 21h6"/><path d="M12 3a6 6 0 0 1 6 6c0 2.22-1.21 4.16-3 5.2V18H9v-3.8A6 6 0 0 1 6 9a6 6 0 0 1 6-6z"/></svg>
                </div>
                <div class="valor-title">Inovação</div>
                <div class="valor-desc">Incentivamos o pensamento crítico e a criatividade como ferramentas essenciais para o futuro.</div>
            </div>
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="valor-title">Comunidade</div>
                <div class="valor-desc">Uma escola é feita de pessoas. Cuidamos das relações entre alunos, professores e famílias.</div>
            </div>
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <div class="valor-title">Inclusão</div>
                <div class="valor-desc">Todos têm lugar aqui. Promovemos um ambiente respeitador da diversidade e da individualidade.</div>
            </div>
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div class="valor-title">Desenvolvimento Integral</div>
                <div class="valor-desc">Formamos não só profissionais, mas pessoas com competências sociais, emocionais e técnicas.</div>
            </div>
            <div class="valor-item">
                <div class="valor-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:1.3rem;height:1.3rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="valor-title">Responsabilidade</div>
                <div class="valor-desc">Agimos com integridade, transparência e compromisso com o bem-estar da nossa comunidade.</div>
            </div>
        </div>
    </div>

    <!-- ══ Instalações ════════════════════════════════════ -->
    <div class="instalacoes-section">
        <span style="font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);display:block;margin-bottom:10px;">Espaços</span>
        <h2>As nossas instalações</h2>
        <div class="instalacoes-grid">
            <?php
            $instalacoes = [
                ['Salas Tecnológicas', '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
                ['Laboratórios',       '<path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11m0 0H5a2 2 0 0 0-2 2v1m6-3h10m0 0v1a2 2 0 0 1-2 2M19 14V9"/>'],
                ['Biblioteca',         '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
                ['Pavilhão Desportivo','<circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/>'],
                ['Salas Multimédia',   '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>'],
                ['Auditório',          '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
                ['Espaços de Convívio','<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                ['Cantina',            '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><line x1="7" y1="2" x2="7" y2="11"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>'],
            ];
            foreach ($instalacoes as [$nome, $path]): ?>
            <div class="instalacao-card">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="width:2.2rem;height:2.2rem;"><?= $path ?></svg>
                <strong><?= $nome ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ══ Equipa ═════════════════════════════════════════ -->
    <div class="equipa-section">
        <span style="font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--cor-principal);display:block;margin-bottom:10px;">Pessoas</span>
        <h2>Direção e Coordenação</h2>
        <p>A equipa que orienta a escola no dia-a-dia.</p>
        <div class="equipa-grid">
            <?php
            $equipa = [
                ['Maria Silva',    'Diretora',            'Gestão Escolar',     'MS'],
                ['João Ferreira',  'Subdiretor',          'Pedagógico',         'JF'],
                ['Ana Costa',      'Coordenadora',        'Ensino Secundário',  'AC'],
                ['Pedro Sousa',    'Coordenador TIC',     'Tecnologias',        'PS'],
                ['Carla Nunes',    'Responsável Admin.',  'Secretaria',         'CN'],
                ['Rui Almeida',    'Coordenador CEF',     'Form. Profissional', 'RA'],
                ['Sofia Martins',  'Psicóloga',           'Apoio ao Aluno',     'SM'],
                ['Luís Rodrigues', 'Coordenador Desp.',   'Desporto Escolar',   'LR'],
            ];
            foreach ($equipa as [$nome, $cargo, $area, $iniciais]): ?>
            <div class="equipa-card">
                <div class="equipa-avatar"><?= $iniciais ?></div>
                <div class="equipa-nome"><?= $nome ?></div>
                <div class="equipa-cargo"><?= $cargo ?></div>
                <div class="equipa-area"><?= $area ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ══ CTA final ══════════════════════════════════════ -->
    <div class="sobre-cta">
        <div class="sobre-cta-inner">
            <h2>Faz parte da nossa comunidade</h2>
            <p>Explora os cursos disponíveis ou entra em contacto connosco para saber mais.</p>
            <div class="sobre-cta-btns">
                <a href="cursos.php" class="cta-btn-white">Ver Cursos</a>
                <a href="contactos.php" class="cta-btn-outline">Contactar a Escola</a>
            </div>
        </div>
    </div>

</div><!-- .sobre-wrap -->

<?php require_once '_footer.php'; ?>