<?php
// Script para verificar e corrigir o utilizador admin
require_once '../important/conexao.php';

echo "<h2>Verificação de Utilizadores Admin</h2>";

// Listar todos os utilizadores
echo "<h3>Utilizadores existentes:</h3>";
$stmt = $pdo->query("SELECT id, nome, email, tipo FROM utilizadores");
$users = $stmt->fetchAll();

if(empty($users)) {
    echo "<p style='color: red;'>⚠️ Nenhum utilizador encontrado na base de dados!</p>";
    echo "<p>A tabela 'utilizadores' pode estar vazia ou não existir.</p>";
} else {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr>";
    foreach($users as $u) {
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['nome']}</td>";
        echo "<td>{$u['email']}</td>";
        echo "<td>{$u['tipo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";

// Criar/atualizar utilizador admin com credenciais corretas
echo "<h3>Criar/Atualizar Admin:</h3>";

$email_admin = 'admin@eduweb.pt';
$password_admin = 'admin123';
$password_hash = password_hash($password_admin, PASSWORD_DEFAULT);

// Verificar se já existe
$stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
$stmt->execute([$email_admin]);
$existe = $stmt->fetch();

if($existe) {
    // Atualizar
    $stmt = $pdo->prepare("UPDATE utilizadores SET password = ?, tipo = 'admin', ativo = 1 WHERE email = ?");
    $stmt->execute([$password_hash, $email_admin]);
    echo "<p style='color: green;'>✅ Utilizador admin@eduweb.pt ATUALIZADO com sucesso!</p>";
} else {
    // Criar novo
    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, tipo, ativo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Administrador', $email_admin, $password_hash, 'admin', 1]);
    echo "<p style='color: green;'>✅ Utilizador admin@eduweb.pt CRIADO com sucesso!</p>";
}

echo "<div style='background: #e7f3ff; padding: 15px; border: 1px solid #b3d9ff; border-radius: 5px; margin-top: 20px;'>";
echo "<h4>Credenciais de Login:</h4>";
echo "<p><strong>Email:</strong> admin@eduweb.pt</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;'>⚠️ Altere a password após o primeiro login!</p>";
echo "</div>";

echo "<hr>";
echo "<h3>Teste de Verificação da Password:</h3>";

// Buscar o admin criado/atualizado
$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
$stmt->execute([$email_admin]);
$admin = $stmt->fetch();

if($admin) {
    if(password_verify('admin123', $admin['password'])) {
        echo "<p style='color: green;'>✅ Password verificada com sucesso! O login deve funcionar.</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro: A password não foi verificada corretamente.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Utilizador não encontrado após criação/atualização.</p>";
}
?>