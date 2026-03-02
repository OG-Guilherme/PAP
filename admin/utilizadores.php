<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'utilizadores';
$page_title  = 'Utilizadores';
$mensagem    = '';

// Export CSV
if (isset($_GET['exportar'])) {
    $rows = $pdo->query("SELECT id, nome, email, tipo, ativo FROM utilizadores ORDER BY id DESC")->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="utilizadores_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($f, ['ID', 'Nome', 'Email', 'Tipo', 'Registo', 'Ativo']);
    foreach ($rows as $r) {
        fputcsv($f, [$r['id'], $r['nome'], $r['email'], $r['tipo'],
            date('d/m/Y H:i', strtotime($r['data_criacao'] ?? $r['created_at'] ?? date('Y-m-d'))), $r['ativo'] ? 'Sim' : 'Não']);
    }
    fclose($f); exit;
}

// Toggle active
if (isset($_GET['toggle_ativo'])) {
    $id  = (int)$_GET['toggle_ativo'];
    $cur = $pdo->prepare("SELECT ativo, nome FROM utilizadores WHERE id = ?");
    $cur->execute([$id]);
    $u   = $cur->fetch();
    $novo= $u['ativo'] ? 0 : 1;
    $pdo->prepare("UPDATE utilizadores SET ativo = ? WHERE id = ?")->execute([$novo, $id]);
    $mensagem = ['type' => 'success', 'text' => 'Utilizador ' . ($novo ? 'ativado' : 'desativado') . '!'];
}

// Change role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_tipo'])) {
    $id   = (int)$_POST['user_id'];
    $tipo = $_POST['tipo'];
    $pdo->prepare("UPDATE utilizadores SET tipo = ? WHERE id = ?")->execute([$tipo, $id]);
    $mensagem = ['type' => 'success', 'text' => 'Tipo atualizado!'];
}

// Delete
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id !== (int)$_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM utilizadores WHERE id = ?")->execute([$id]);
        $mensagem = ['type' => 'success', 'text' => '🗑️ Utilizador eliminado!'];
    } else {
        $mensagem = ['type' => 'error', 'text' => '❌ Não pode eliminar a sua própria conta!'];
    }
}

$utilizadores = $pdo->query("SELECT * FROM utilizadores ORDER BY id DESC")->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>👥 Utilizadores</h2>
        <p><?php echo count($utilizadores); ?> utilizador(es) registado(s)</p>
    </div>
    <a href="?exportar=1" class="btn btn-success">⬇️ Exportar CSV</a>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?php echo $mensagem['type']; ?>"><?php echo $mensagem['text']; ?></div>
<?php endif; ?>

<div class="card">
    <h2>📋 Todos os Utilizadores</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Registo</th><th>Estado</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($utilizadores as $u): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?php echo $u['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($u['nome']); ?></strong></td>
                    <td style="font-size:0.82rem; color:var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <select name="tipo" onchange="this.form.submit()" style="padding:4px 8px; font-size:0.78rem; border-radius:4px; border:1px solid var(--border); background:var(--input-bg); color:var(--text);">
                                <?php foreach (['visitante','aluno','professor','admin'] as $t): ?>
                                    <option value="<?php echo $t; ?>" <?php echo $u['tipo'] === $t ? 'selected' : ''; ?>><?php echo ucfirst($t); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="change_tipo" value="1">
                        </form>
                    </td>
                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo '—'; ?></td>
                    <td>
                        <span class="badge <?php echo $u['ativo'] ? 'badge-criar' : 'badge-eliminar'; ?>">
                            <?php echo $u['ativo'] ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-links">
                            <a href="?toggle_ativo=<?php echo $u['id']; ?>" class="edit">
                                <?php echo $u['ativo'] ? 'Desativar' : 'Ativar'; ?>
                            </a>
                            <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                            <a href="?eliminar=<?php echo $u['id']; ?>" onclick="return confirm('Eliminar este utilizador?')" class="delete">Eliminar</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '_footer.php'; ?>