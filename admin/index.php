<?php
session_start();
require_once '../important/conexao.php';

// Verificar se é admin
if(!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'admin') {
    header('Location: ../site/login.php');
    exit;
}

// Estatísticas
$stats = [];
$stats['noticias'] = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$stats['eventos'] = $pdo->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
$stats['cursos'] = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
$stats['utilizadores'] = $pdo->query("SELECT COUNT(*) FROM utilizadores")->fetchColumn();
$stats['comentarios'] = $pdo->query("SELECT COUNT(*) FROM comentarios WHERE aprovado = 0")->fetchColumn();

// Últimas atividades
$logs = $pdo->query("SELECT l.*, u.nome FROM logs_admin l JOIN utilizadores u ON l.usuario_id = u.id ORDER BY l.data_criacao DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - EduWeb</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .admin-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .admin-header h1 { font-size: 1.5rem; margin-bottom: 5px; }
        .admin-header p { opacity: 0.9; font-size: 0.9rem; }
        
        .admin-container { display: flex; min-height: calc(100vh - 80px); }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            transition: background 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid #2a5298;
        }
        .stat-card h3 { font-size: 0.9rem; color: #666; margin-bottom: 10px; }
        .stat-card .number { font-size: 2.5rem; font-weight: bold; color: #2a5298; }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card h2 { margin-bottom: 20px; color: #333; }
        
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f8f9fa; }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-criar { background: #d4edda; color: #155724; }
        .badge-editar { background: #fff3cd; color: #856404; }
        .badge-eliminar { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🎓 Painel de Administração - EduWeb</h1>
        <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</p>
    </div>

    <div class="admin-container">
        <div class="sidebar">
            <a href="../site/index.php" class="active">📊 Dashboard</a>
            <a href="../site/noticias.php">📰 Notícias</a>
            <a href="../site/eventos.php">📅 Eventos</a>
            <a href="../site/cursos.php">📚 Cursos</a>
            <a href="../site/utilizadores.php">👥 Utilizadores</a>
            <a href="../site/comentarios.php">💬 Comentários <?php if($stats['comentarios'] > 0): ?>(<?php echo $stats['comentarios']; ?>)<?php endif; ?></a>
            <a href="logs.php">📋 Logs</a>
            <hr style="border: none; border-top: 1px solid #34495e; margin: 15px 0;">
            <a href="../index.php">🏠 Ver Site</a>
            <a href="../logout.php">🚪 Sair</a>
        </div>

        <div class="main-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>NOTÍCIAS</h3>
                    <div class="number"><?php echo $stats['noticias']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>EVENTOS</h3>
                    <div class="number"><?php echo $stats['eventos']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>CURSOS</h3>
                    <div class="number"><?php echo $stats['cursos']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>UTILIZADORES</h3>
                    <div class="number"><?php echo $stats['utilizadores']; ?></div>
                </div>
            </div>

            <div class="card">
                <h2>📋 Últimas Atividades</h2>
                <?php if(empty($logs)): ?>
                    <p style="color: #999;">Sem atividades registadas.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Utilizador</th>
                                <th>Ação</th>
                                <th>Tipo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['nome']); ?></td>
                                <td><?php echo htmlspecialchars($log['acao']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $log['tipo']; ?>">
                                        <?php echo strtoupper($log['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($log['data_criacao'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>