<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'cursos';
$page_title  = 'Cursos';
$mensagem    = '';

// Export
if (isset($_GET['exportar'])) {
    $rows = $pdo->query("SELECT id, nome, sigla, tipo, duracao_anos, ativo FROM cursos ORDER BY nome")->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="cursos_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($f, ['ID', 'Nome', 'Sigla', 'Tipo', 'Duração (anos)', 'Ativo']);
    foreach ($rows as $r) {
        fputcsv($f, [$r['id'], $r['nome'], $r['sigla'], $r['tipo'], $r['duracao_anos'], $r['ativo'] ? 'Sim' : 'Não']);
    }
    fclose($f); exit;
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome     = $_POST['nome'];
    $sigla    = $_POST['sigla'];
    $tipo     = $_POST['tipo'];
    $duracao  = (int)$_POST['duracao_anos'];
    $descricao= $_POST['descricao'];

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext    = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'cursos/' . uniqid() . '.' . $ext;
        $dir    = '../uploads/cursos/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../uploads/' . $imagem);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO cursos (nome, sigla, tipo, duracao_anos, descricao, imagem, ativo) VALUES (?,?,?,?,?,?,1)"
    );
    if ($stmt->execute([$nome, $sigla, $tipo, $duracao, $descricao, $imagem])) {
        $mensagem = ['type' => 'success', 'text' => '✅ Curso adicionado!'];
    }
}

// Delete
if (isset($_GET['eliminar'])) {
    $pdo->prepare("DELETE FROM cursos WHERE id = ?")->execute([(int)$_GET['eliminar']]);
    $mensagem = ['type' => 'success', 'text' => '🗑️ Curso eliminado!'];
}

$cursos = $pdo->query("SELECT * FROM cursos ORDER BY ordem, nome")->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>📚 Cursos</h2>
        <p><?php echo count($cursos); ?> curso(s) registado(s)</p>
    </div>
    <a href="?exportar=1" class="btn btn-success">⬇️ Exportar CSV</a>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?php echo $mensagem['type']; ?>"><?php echo $mensagem['text']; ?></div>
<?php endif; ?>

<div class="card">
    <h2>➕ Adicionar Curso</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Sigla *</label>
                <input type="text" name="sigla" required maxlength="10">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo">
                    <option value="Regular">Regular</option>
                    <option value="Profissional">Profissional</option>
                    <option value="CEF">CEF</option>
                </select>
            </div>
            <div class="form-group">
                <label>Duração (anos)</label>
                <input type="number" name="duracao_anos" value="3" min="1" max="5">
            </div>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao"></textarea>
        </div>
        <div class="form-group">
            <label>Imagem</label>
            <input type="file" name="imagem" accept="image/*">
        </div>
        <button type="submit" name="adicionar" class="btn btn-primary">Adicionar Curso</button>
    </form>
</div>

<div class="card">
    <h2>📋 Cursos Existentes</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nome</th><th>Sigla</th><th>Tipo</th><th>Duração</th><th>Estado</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $c): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?php echo $c['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['nome']); ?></strong></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($c['sigla']); ?></span></td>
                    <td><?php echo htmlspecialchars($c['tipo']); ?></td>
                    <td><?php echo $c['duracao_anos']; ?> anos</td>
                    <td><span class="badge <?php echo $c['ativo'] ? 'badge-criar' : 'badge-eliminar'; ?>"><?php echo $c['ativo'] ? 'Ativo' : 'Inativo'; ?></span></td>
                    <td>
                        <div class="action-links">
                            <a href="../curso.php?id=<?php echo $c['id']; ?>" target="_blank" class="view">Ver</a>
                            <a href="?eliminar=<?php echo $c['id']; ?>" onclick="return confirm('Eliminar este curso?')" class="delete">Eliminar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '_footer.php'; ?>
