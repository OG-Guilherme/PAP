<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'logs';
$page_title  = 'Logs';

// Export
if (isset($_GET['exportar'])) {
    $rows = $pdo->query(
        "SELECT l.*, u.nome FROM logs_admin l JOIN utilizadores u ON l.usuario_id = u.id ORDER BY l.data_criacao DESC"
    )->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="logs_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($f, ['ID', 'Utilizador', 'Ação', 'Tipo', 'Tabela', 'Data', 'IP']);
    foreach ($rows as $r) {
        fputcsv($f, [$r['id'], $r['nome'], $r['acao'], $r['tipo'],
            $r['tabela'] ?? '-', date('d/m/Y H:i', strtotime($r['data_criacao'])), $r['ip_address'] ?? '-']);
    }
    fclose($f); exit;
}

$logs = $pdo->query(
    "SELECT l.*, u.nome FROM logs_admin l JOIN utilizadores u ON l.usuario_id = u.id ORDER BY l.data_criacao DESC LIMIT 200"
)->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>📋 Logs de Atividade</h2>
        <p>Últimas <?php echo count($logs); ?> ações registadas</p>
    </div>
    <a href="?exportar=1" class="btn btn-success">⬇️ Exportar CSV</a>
</div>

<div class="card">
    <h2>📋 Histórico</h2>
    <?php if (empty($logs)): ?>
        <p style="color:var(--text-muted); text-align:center; padding:30px 0;">Sem logs.</p>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Utilizador</th><th>Ação</th><th>Tipo</th><th>Tabela</th><th>Data</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.78rem;"><?php echo $log['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($log['nome']); ?></strong></td>
                    <td><?php echo htmlspecialchars($log['acao']); ?></td>
                    <td><span class="badge badge-<?php echo $log['tipo']; ?>"><?php echo strtoupper($log['tipo']); ?></span></td>
                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo htmlspecialchars($log['tabela'] ?? '—'); ?></td>
                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo date('d/m/Y H:i', strtotime($log['data_criacao'])); ?></td>
                    <td style="color:var(--text-muted); font-size:0.75rem; font-family:monospace;"><?php echo htmlspecialchars($log['ip_address'] ?? '—'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require '_footer.php'; ?>
