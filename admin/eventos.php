<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'eventos';
$page_title  = 'Eventos';
$mensagem    = '';

// Export CSV
if (isset($_GET['exportar'])) {
    $rows = $pdo->query(
        "SELECT e.id, e.titulo, e.local, e.data_evento, e.categoria, u.nome as responsavel, e.visualizacoes, e.publicado
         FROM eventos e JOIN utilizadores u ON e.responsavel_id = u.id ORDER BY e.data_evento DESC"
    )->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="eventos_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($f, ['ID', 'Título', 'Local', 'Data', 'Categoria', 'Responsável', 'Visualizações', 'Publicado']);
    foreach ($rows as $r) {
        fputcsv($f, [$r['id'], $r['titulo'], $r['local'], date('d/m/Y H:i', strtotime($r['data_evento'])),
            $r['categoria'] ?? '-', $r['responsavel'], $r['visualizacoes'], $r['publicado'] ? 'Sim' : 'Não']);
    }
    fclose($f); exit;
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $titulo    = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $local     = $_POST['local'];
    $data_ev   = $_POST['data_evento'];
    $categoria = $_POST['categoria'];
    $capacidade= $_POST['capacidade'] ?: null;

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext    = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'eventos/' . uniqid() . '.' . $ext;
        $dir    = '../uploads/eventos/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../uploads/' . $imagem);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO eventos (titulo, descricao, local, data_evento, categoria, imagem_destaque, responsavel_id, capacidade, publicado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
    );
    if ($stmt->execute([$titulo, $descricao, $local, $data_ev, $categoria, $imagem, $_SESSION['user_id'], $capacidade])) {
        $id = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO logs_admin (usuario_id, acao, tipo, tabela, item_id, descricao) VALUES (?, 'Criar evento', 'criar', 'eventos', ?, ?)")
            ->execute([$_SESSION['user_id'], $id, $titulo]);
        $mensagem = ['type' => 'success', 'text' => '✅ Evento criado!'];
    }
}

// Delete
if (isset($_GET['eliminar'])) {
    $id  = (int)$_GET['eliminar'];
    $row = $pdo->prepare("SELECT titulo FROM eventos WHERE id = ?");
    $row->execute([$id]);
    $titulo = $row->fetchColumn();
    $pdo->prepare("DELETE FROM eventos WHERE id = ?")->execute([$id]);
    $pdo->prepare("INSERT INTO logs_admin (usuario_id, acao, tipo, tabela, item_id, descricao) VALUES (?, 'Eliminar evento', 'eliminar', 'eventos', ?, ?)")
        ->execute([$_SESSION['user_id'], $id, $titulo]);
    $mensagem = ['type' => 'success', 'text' => '🗑️ Evento eliminado!'];
}

$eventos = $pdo->query(
    "SELECT e.*, u.nome as responsavel FROM eventos e 
     JOIN utilizadores u ON e.responsavel_id = u.id ORDER BY e.data_evento DESC"
)->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>📅 Eventos</h2>
        <p><?php echo count($eventos); ?> evento(s) registado(s)</p>
    </div>
    <a href="?exportar=1" class="btn btn-success">⬇️ Exportar CSV</a>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?php echo $mensagem['type']; ?>"><?php echo $mensagem['text']; ?></div>
<?php endif; ?>

<div class="card">
    <h2>➕ Adicionar Evento</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Título *</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria">
                    <option value="">Sem categoria</option>
                    <option value="Académico">Académico</option>
                    <option value="Desporto">Desporto</option>
                    <option value="Cultural">Cultural</option>
                    <option value="Palestras">Palestras</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Local *</label>
                <input type="text" name="local" required>
            </div>
            <div class="form-group">
                <label>Capacidade</label>
                <input type="number" name="capacidade" min="1" placeholder="Deixar vazio = sem limite">
            </div>
        </div>
        <div class="form-group">
            <label>Descrição *</label>
            <textarea name="descricao" required></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Imagem</label>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <div class="form-group">
                <label>Data e Hora *</label>
                <input type="datetime-local" name="data_evento" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
            </div>
        </div>
        <button type="submit" name="adicionar" class="btn btn-primary">Adicionar Evento</button>
    </form>
</div>

<div class="card">
    <h2>📋 Eventos Existentes</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Título</th><th>Local</th><th>Data</th><th>Responsável</th><th>👁</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($eventos as $e): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?php echo $e['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($e['titulo']); ?></strong></td>
                    <td><?php echo htmlspecialchars($e['local']); ?></td>
                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo date('d/m/Y H:i', strtotime($e['data_evento'])); ?></td>
                    <td><?php echo htmlspecialchars($e['responsavel']); ?></td>
                    <td style="color:var(--text-muted);"><?php echo $e['visualizacoes']; ?></td>
                    <td>
                        <div class="action-links">
                            <a href="../site/evento.php?id=<?php echo $e['id']; ?>" target="_blank" class="view">Ver</a>
                            <a href="?eliminar=<?php echo $e['id']; ?>" onclick="return confirm('Eliminar este evento?')" class="delete">Eliminar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '_footer.php'; ?>
