<?php
session_start();
require_once '../important/config.php';
require_once '_tema.php';

$pesquisa    = trim($_GET['q']           ?? '');
$categoria   = $_GET['cat']              ?? '';
$data_inicio = $_GET['data_inicio']      ?? '';
$data_fim    = $_GET['data_fim']         ?? '';
$vista       = $_GET['v'] ?? 'lista'; // lista | calendario

$sql    = "SELECT e.*, u.nome as responsavel FROM eventos e JOIN utilizadores u ON e.responsavel_id = u.id WHERE e.publicado = 1";
$params = [];
if ($pesquisa)    { $sql .= " AND (e.titulo LIKE ? OR e.descricao LIKE ?)"; $params[] = "%$pesquisa%"; $params[] = "%$pesquisa%"; }
if ($categoria)   { $sql .= " AND e.categoria = ?"; $params[] = $categoria; }
if ($data_inicio) { $sql .= " AND e.data_evento >= ?"; $params[] = $data_inicio; }
if ($data_fim)    { $sql .= " AND e.data_evento <= ?"; $params[] = $data_fim . ' 23:59:59'; }
$sql .= " ORDER BY e.data_evento ASC";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$eventos = $stmt->fetchAll();
$cats = $pdo->query("SELECT DISTINCT categoria FROM eventos WHERE categoria IS NOT NULL AND publicado=1 ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

// Separar passados/futuros
$hoje   = date('Y-m-d');
$proximos = array_filter($eventos, fn($e) => substr($e['data_evento'],0,10) >= $hoje);
$passados  = array_filter($eventos, fn($e) => substr($e['data_evento'],0,10) <  $hoje);

$paginaActiva = 'eventos';
$tituloBase   = 'Eventos';
$extraCSS = <<<'ENDCSS'
<style>
.agenda-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:32px;padding-bottom:20px;border-bottom:1px solid var(--cor-borda);}
.agenda-view-btns{display:flex;gap:4px;background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:8px;padding:3px;}
.agenda-view-btn{padding:6px 14px;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;background:transparent;color:var(--cor-texto-claro);transition:all .15s;}
.agenda-view-btn.active{background:var(--cor-principal);color:white;}
.timeline{position:relative;}
.tl-month{margin-bottom:32px;}
.tl-month-label{font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-texto-claro);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.tl-month-label::after{content:'';flex:1;height:1px;background:var(--cor-borda);}
.tl-items{display:flex;flex-direction:column;gap:10px;}
.tl-item{display:grid;grid-template-columns:64px 1fr;background:var(--cor-fundo-alt);border:1.5px solid var(--cor-borda);border-radius:12px;overflow:hidden;text-decoration:none;color:inherit;transition:border-color .2s,box-shadow .2s,transform .2s;}
.tl-item:hover{border-color:var(--cor-principal);box-shadow:0 4px 20px rgba(0,0,0,.08);transform:translateX(4px);}
.tl-date{background:var(--cor-principal);color:white;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px 8px;text-align:center;flex-shrink:0;}
.tl-date .day{font-size:1.4rem;font-weight:800;line-height:1;}
.tl-date .dow{font-size:.6rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;opacity:.85;margin-top:3px;}
.tl-body{padding:14px 18px;display:flex;align-items:flex-start;gap:14px;}
.tl-body-text{flex:1;min-width:0;}
.tl-title{font-weight:700;font-size:.95rem;color:var(--cor-texto);margin-bottom:5px;line-height:1.3;}
.tl-meta{font-size:.78rem;color:var(--cor-texto-claro);display:flex;flex-wrap:wrap;gap:10px;}
.tl-meta span{display:flex;align-items:center;gap:4px;}
.tl-tag{font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--cor-principal);border:1px solid var(--cor-principal);border-radius:100px;padding:2px 8px;white-space:nowrap;flex-shrink:0;}
.tl-img{width:72px;height:72px;border-radius:8px;object-fit:cover;flex-shrink:0;}
.tl-item.passado{opacity:.55;filter:grayscale(.4);}
.tl-item.passado .tl-date{background:var(--cor-texto-claro);}
.secao-passados-toggle{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.8rem;color:var(--cor-texto-claro);font-weight:600;padding:12px 0;border-top:1px solid var(--cor-borda);margin-top:8px;}
.secao-passados-toggle::after{content:'';flex:1;height:1px;background:var(--cor-borda);}
.cal-wrap{background:var(--cor-fundo-alt);border:1px solid var(--cor-borda);border-radius:14px;overflow:hidden;}
.cal-nav{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--cor-borda);}
.cal-nav-btn{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--cor-borda);border-radius:8px;background:var(--cor-fundo);cursor:pointer;color:var(--cor-texto-claro);transition:all .15s;}
.cal-nav-btn:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.cal-month-title{font-weight:700;font-size:1rem;color:var(--cor-texto);}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);}
.cal-dow{text-align:center;font-size:.72rem;font-weight:700;letter-spacing:.06em;color:var(--cor-texto-claro);padding:10px 4px;border-bottom:1px solid var(--cor-borda);}
.cal-day{border-right:1px solid var(--cor-borda);border-bottom:1px solid var(--cor-borda);padding:8px;min-height:80px;cursor:pointer;transition:background .12s;}
.cal-day:nth-child(7n){border-right:none;}
.cal-day:hover{background:var(--cor-fundo);}
.cal-day.other-month .cal-day-num{opacity:.3;}
.cal-day.today .cal-day-num{background:var(--cor-principal);color:white;border-radius:50%;}
.cal-day-num{width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:600;margin-bottom:4px;}
.cal-event-dot{font-size:.7rem;padding:2px 6px;border-radius:4px;background:var(--cor-principal);color:white;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-decoration:none;display:block;}
.eventos-toolbar{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;}
.eventos-toolbar input[type=text]{flex:1;min-width:180px;}
.eventos-toolbar input[type=date]{width:auto;}
.cat-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:28px;}
.cat-tab{padding:5px 13px;border-radius:100px;font-size:.8rem;font-weight:500;text-decoration:none;border:1.5px solid var(--cor-borda);color:var(--cor-texto-claro);transition:all .15s;}
.cat-tab:hover{border-color:var(--cor-principal);color:var(--cor-principal);}
.cat-tab.active{background:var(--cor-principal);border-color:var(--cor-principal);color:white;}
.empty-state{text-align:center;padding:80px 20px;color:var(--cor-texto-claro);}
</style>
ENDCSS;
require_once '_header.php';

$meses_pt = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
$dias_pt  = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

// Agrupar eventos por mês
$por_mes = [];
foreach ($proximos as $e) {
    $key = date('Y-m', strtotime($e['data_evento']));
    $por_mes[$key][] = $e;
}
?>

<div class="container" style="padding:56px 24px 80px;">
    <div class="agenda-header">
        <div>
            <div style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--cor-principal);margin-bottom:8px;">Agenda EduWeb</div>
            <h1 style="font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;letter-spacing:-.02em;color:var(--cor-texto);">Eventos</h1>
        </div>
        <div class="agenda-view-btns">
            <button class="agenda-view-btn <?= $vista==='lista' ? 'active' : '' ?>" onclick="setVista('lista')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;vertical-align:middle;margin-right:4px;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Lista
            </button>
            <button class="agenda-view-btn <?= $vista==='cal' ? 'active' : '' ?>" onclick="setVista('cal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;vertical-align:middle;margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Calendário
            </button>
        </div>
    </div>

    <!-- Toolbar de filtros -->
    <form method="GET" style="max-width:none;">
        <input type="hidden" name="v" value="<?= htmlspecialchars($vista) ?>">
        <div class="eventos-toolbar">
            <input type="text"  name="q"           placeholder="Pesquisar eventos…" value="<?= htmlspecialchars($pesquisa) ?>">
            <input type="date"  name="data_inicio" value="<?= $data_inicio ?>" title="Data início">
            <input type="date"  name="data_fim"    value="<?= $data_fim ?>"    title="Data fim">
            <?php if ($categoria): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($categoria) ?>"><?php endif; ?>
            <button type="submit" class="btn" style="white-space:nowrap;">Filtrar</button>
            <?php if ($pesquisa||$categoria||$data_inicio||$data_fim): ?>
                <a href="eventos.php" class="btn btn-outline" style="white-space:nowrap;">Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($cats)): ?>
    <div class="cat-tabs">
        <a href="eventos.php?v=<?= $vista ?><?= $pesquisa ? '&q='.urlencode($pesquisa) : '' ?>" class="cat-tab <?= !$categoria ? 'active' : '' ?>">Todos</a>
        <?php foreach ($cats as $c): ?>
            <a href="eventos.php?v=<?= $vista ?>&cat=<?= urlencode($c) ?><?= $pesquisa ? '&q='.urlencode($pesquisa) : '' ?>" class="cat-tab <?= $c===$categoria ? 'active' : '' ?>"><?= htmlspecialchars($c) ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- VISTA LISTA -->
    <div id="view-lista" style="display:<?= $vista==='lista' ? 'block' : 'none' ?>;">
        <?php if (empty($eventos)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="width:3rem;height:3rem;margin-bottom:16px;opacity:.3;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <p>Nenhum evento encontrado.</p>
            </div>
        <?php else: ?>
            <!-- Próximos -->
            <?php if (!empty($por_mes)): ?>
                <div class="timeline">
                    <?php foreach ($por_mes as $ym => $evts): 
                        [$y,$m] = explode('-', $ym); ?>
                        <div class="tl-month">
                            <div class="tl-month-label"><?= $meses_pt[(int)$m] ?> <?= $y ?></div>
                            <div class="tl-items">
                                <?php foreach ($evts as $e): $ts = strtotime($e['data_evento']); ?>
                                <a href="evento.php?id=<?= $e['id'] ?>" class="tl-item">
                                    <div class="tl-date">
                                        <div class="day"><?= date('d', $ts) ?></div>
                                        <div class="dow"><?= $dias_pt[date('w', $ts)] ?></div>
                                    </div>
                                    <div class="tl-body">
                                        <div class="tl-body-text">
                                            <div class="tl-title"><?= htmlspecialchars($e['titulo']) ?></div>
                                            <div class="tl-meta">
                                                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= date('H:i', $ts) ?></span>
                                                <?php if ($e['local']): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:.75rem;height:.75rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= htmlspecialchars($e['local']) ?></span><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($e['categoria']): ?><div class="tl-tag"><?= htmlspecialchars($e['categoria']) ?></div><?php endif; ?>
                                        <?php if ($e['imagem_destaque']): ?><img src="uploads/<?= $e['imagem_destaque'] ?>" alt="" class="tl-img" loading="lazy"><?php endif; ?>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Passados (colapsável) -->
            <?php if (!empty($passados)): ?>
                <details style="margin-top:16px;">
                    <summary class="secao-passados-toggle">
                        <?= count($passados) ?> evento<?= count($passados)>1?'s':'' ?> passado<?= count($passados)>1?'s':'' ?>
                    </summary>
                    <div class="timeline" style="margin-top:14px;opacity:.6;">
                        <div class="tl-items">
                            <?php foreach ($passados as $e): $ts = strtotime($e['data_evento']); ?>
                            <a href="evento.php?id=<?= $e['id'] ?>" class="tl-item passado">
                                <div class="tl-date">
                                    <div class="day"><?= date('d', $ts) ?></div>
                                    <div class="dow"><?= $dias_pt[date('w',$ts)] ?></div>
                                </div>
                                <div class="tl-body">
                                    <div class="tl-body-text">
                                        <div class="tl-title"><?= htmlspecialchars($e['titulo']) ?></div>
                                        <div class="tl-meta"><span><?= date('d/m/Y', $ts) ?></span><?php if ($e['local']): ?><span><?= htmlspecialchars($e['local']) ?></span><?php endif; ?></div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </details>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- VISTA CALENDÁRIO -->
    <div id="view-cal" style="display:<?= $vista==='cal' ? 'block' : 'none' ?>;">
        <div class="cal-wrap" id="cal-wrap"></div>
    </div>
</div>

<script>
function setVista(v) {
    document.getElementById('view-lista').style.display = v==='lista' ? 'block' : 'none';
    document.getElementById('view-cal').style.display   = v==='cal'   ? 'block' : 'none';
    document.querySelectorAll('.agenda-view-btn').forEach((b,i) => b.classList.toggle('active', (i===0&&v==='lista')||(i===1&&v==='cal')));
    const url = new URL(location.href);
    url.searchParams.set('v', v);
    history.replaceState({}, '', url);
    if (v==='cal') renderCal(calYear, calMonth);
}

// Dados de eventos para o calendário
const CAL_EVENTS = <?= json_encode(array_values(array_map(fn($e) => [
    'id'    => $e['id'],
    'title' => $e['titulo'],
    'date'  => substr($e['data_evento'],0,10),
], $eventos))) ?>;

let calYear  = new Date().getFullYear();
let calMonth = new Date().getMonth(); // 0-based

function renderCal(year, month) {
    calYear = year; calMonth = month;
    const meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const dias  = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
    const today = new Date().toISOString().slice(0,10);

    // Mapa data → eventos
    const map = {};
    CAL_EVENTS.forEach(e => { if (!map[e.date]) map[e.date] = []; map[e.date].push(e); });

    let html = `<div class="cal-nav">
        <button class="cal-nav-btn" onclick="renderCal(${month===0?year-1:year},${month===0?11:month-1})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="cal-month-title">${meses[month]} ${year}</div>
        <button class="cal-nav-btn" onclick="renderCal(${month===11?year+1:year},${month===11?0:month+1})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:.85rem;height:.85rem;"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div><div class="cal-grid">`;

    dias.forEach(d => html += `<div class="cal-dow">${d}</div>`);

    const first = new Date(year, month, 1).getDay();
    const days  = new Date(year, month+1, 0).getDate();
    const prevD = new Date(year, month, 0).getDate();

    for (let i=0; i<first; i++) {
        html += `<div class="cal-day other-month"><div class="cal-day-num">${prevD-first+1+i}</div></div>`;
    }
    for (let d=1; d<=days; d++) {
        const ds = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const evs = map[ds] || [];
        const isToday = ds === today;
        html += `<div class="cal-day${isToday?' today':''}">
            <div class="cal-day-num">${d}</div>
            ${evs.slice(0,2).map(e => `<a href="evento.php?id=${e.id}" class="cal-event-dot">${e.title}</a>`).join('')}
            ${evs.length>2 ? `<div style="font-size:.65rem;color:var(--cor-texto-claro);">+${evs.length-2}</div>` : ''}
        </div>`;
    }
    const remaining = 42 - first - days;
    for (let d=1; d<=remaining; d++) html += `<div class="cal-day other-month"><div class="cal-day-num">${d}</div></div>`;

    html += '</div>';
    document.getElementById('cal-wrap').innerHTML = html;
}

// Renderiza calendário se estiver visível
if (document.getElementById('view-cal').style.display !== 'none') renderCal(calYear, calMonth);
</script>

<?php require_once '_footer.php'; ?>
