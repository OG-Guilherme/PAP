<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'noticias';
$page_title  = 'Notícias';
$mensagem    = '';

// Adicionar notícia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $titulo    = $_POST['titulo'];
    $conteudo  = $_POST['conteudo'];
    $resumo    = $_POST['resumo'];
    $categoria = $_POST['categoria'];
    $data_pub  = $_POST['data_publicacao'];
    $slug      = preg_replace('/[^a-z0-9]+/', '-', strtolower($titulo));

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext    = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'noticias/' . uniqid() . '.' . $ext;
        $dir    = '../site/uploads/noticias/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], '../site/uploads/' . $imagem);
    }

    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, slug, resumo, conteudo, imagem_destaque, autor_id, categoria, data_publicacao, publicado) VALUES (?,?,?,?,?,?,?,?,1)");
    if ($stmt->execute([$titulo, $slug, $resumo, $conteudo, $imagem, $_SESSION['user_id'], $categoria, $data_pub])) {
        logAdminAction($pdo, $_SESSION['user_id'], 'Criar notícia', 'criar', 'noticias', $pdo->lastInsertId(), $titulo);
        $mensagem = ['type' => 'success', 'text' => 'Notícia criada com sucesso!'];
    }
}

// Eliminar notícia
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $stmt = $pdo->prepare("SELECT titulo FROM noticias WHERE id = ?"); $stmt->execute([$id]);
    $n = $stmt->fetch();
    $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$id]);
    logAdminAction($pdo, $_SESSION['user_id'], 'Eliminar notícia', 'eliminar', 'noticias', $id, $n['titulo'] ?? '');
    $mensagem = ['type' => 'success', 'text' => 'Notícia eliminada!'];
}

$noticias = $pdo->query("SELECT n.*, u.nome as autor FROM noticias n JOIN utilizadores u ON n.autor_id = u.id ORDER BY n.data_publicacao DESC")->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>📰 Gestão de Notícias</h2>
        <p><?= count($noticias) ?> notícia(s) registada(s)</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?= $mensagem['type'] ?>"><?= htmlspecialchars($mensagem['text']) ?></div>
<?php endif; ?>

<!-- Formulário Adicionar -->
<div class="card" style="margin-bottom:24px;">
    <h3 class="card-title">+ Adicionar Notícia</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" required>
        </div>
        <div class="form-group">
            <label>Resumo</label>
            <input type="text" name="resumo" maxlength="200" placeholder="Breve resumo (máx 200 caracteres)">
        </div>
        <div class="form-group">
            <label>Categoria</label>
            <select name="categoria">
                <option value="">Sem categoria</option>
                <option>Académico</option>
                <option>Desporto</option>
                <option>Cultura</option>
                <option>Avisos</option>
            </select>
        </div>
        <div class="form-group">
            <label>Conteúdo</label>
            <textarea name="conteudo" required style="min-height:180px;"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Imagem de Destaque</label>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <div class="form-group">
                <label>Data de Publicação</label>
                <input type="datetime-local" name="data_publicacao" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
        </div>
        <button type="submit" name="adicionar" class="btn btn-primary">Adicionar Notícia</button>
    </form>
</div>

<!-- Tabela Existentes -->
<div class="card">
    <h3 class="card-title">📋 Notícias Existentes (<?= count($noticias) ?>)</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoria</th>
                    <th>Data</th>
                    <th>Views</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias as $n): ?>
                <tr>
                    <td><?= $n['id'] ?></td>
                    <td><strong><?= htmlspecialchars($n['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($n['autor']) ?></td>
                    <td><?= $n['categoria'] ?: '—' ?></td>
                    <td><?= date('d/m/Y', strtotime($n['data_publicacao'])) ?></td>
                    <td><?= $n['visualizacoes'] ?></td>
                    <td class="actions">
                        <a href="../site/noticia.php?id=<?= $n['id'] ?>" target="_blank" class="btn-sm btn-view">Ver</a>
                        <a href="?eliminar=<?= $n['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Eliminar esta notícia?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '_footer.php'; ?>