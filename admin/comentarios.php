<?php
session_start();
require_once '../important/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php'); exit;
}

$active_page = 'comentarios';
$page_title  = 'Comentários';
$mensagem    = '';

// Approve
if (isset($_GET['aprovar'])) {
    $pdo->prepare("UPDATE comentarios SET aprovado = 1 WHERE id = ?")->execute([(int)$_GET['aprovar']]);
    $mensagem = ['type' => 'success', 'text' => '✅ Comentário aprovado!'];
}

// Delete
if (isset($_GET['eliminar'])) {
    $pdo->prepare("DELETE FROM comentarios WHERE id = ?")->execute([(int)$_GET['eliminar']]);
    $mensagem = ['type' => 'success', 'text' => '🗑️ Comentário eliminado!'];
}

$comentarios = $pdo->query(
    "SELECT c.*, u.nome FROM comentarios c 
     JOIN utilizadores u ON c.usuario_id = u.id 
     ORDER BY c.aprovado ASC, c.data_criacao DESC"
)->fetchAll();

require '_header.php';
?>

<div class="page-header">
    <div>
        <h2>💬 Comentários</h2>
        <p><?php echo count($comentarios); ?> comentário(s) no total</p>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alert alert-<?php echo $mensagem['type']; ?>"><?php echo $mensagem['text']; ?></div>
<?php endif; ?>

<div class="card">
    <h2>📋 Todos os Comentários</h2>
    <?php if (empty($comentarios)): ?>
        <p style="color:var(--text-muted); text-align:center; padding:30px 0;">Sem comentários.</p>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Autor</th><th>Conteúdo</th><th>Tipo</th><th>Data</th><th>Estado</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($comentarios as $c): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($c['nome']); ?></strong></td>
                    <td style="max-width:300px; font-size:0.85rem;"><?php echo htmlspecialchars(substr($c['conteudo'], 0, 100)); ?>...</td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($c['tipo']); ?></span></td>
                    <td style="color:var(--text-muted); font-size:0.82rem;"><?php echo date('d/m/Y H:i', strtotime($c['data_criacao'])); ?></td>
                    <td>
                        <span class="badge <?php echo $c['aprovado'] ? 'badge-criar' : 'badge-editar'; ?>">
                            <?php echo $c['aprovado'] ? 'Aprovado' : 'Pendente'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-links">
                            <?php if (!$c['aprovado']): ?>
                                <a href="?aprovar=<?php echo $c['id']; ?>" class="view">Aprovar</a>
                            <?php endif; ?>
                            <a href="?eliminar=<?php echo $c['id']; ?>" onclick="return confirm('Eliminar comentário?')" class="delete">Eliminar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require '_footer.php'; ?>
