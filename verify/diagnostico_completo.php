<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico Completo do Sistema de Login</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { background: #e7f3ff; color: #004085; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    table th { background: #f8f9fa; padding: 10px; text-align: left; }
    table td { padding: 10px; border-bottom: 1px solid #ddd; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    .test-login { background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px solid #ffc107; }
</style>";

// Teste 1: Conexão à base de dados
echo "<div class='card'>";
echo "<h2>1️⃣ Teste de Conexão à Base de Dados</h2>";
try {
    require_once '../important/conexao.php';
    echo "<div class='success'>✅ Conexão estabelecida com sucesso!</div>";
    echo "<p>Host: localhost | Database: pap</p>";
} catch(Exception $e) {
    echo "<div class='error'>❌ ERRO na conexão: " . $e->getMessage() . "</div>";
    echo "<p><strong>SOLUÇÃO:</strong> Verifique se o MySQL está a correr e se as credenciais em conexao.php estão corretas.</p>";
    die();
}
echo "</div>";

// Teste 2: Verificar se a tabela existe
echo "<div class='card'>";
echo "<h2>2️⃣ Verificar Tabela 'utilizadores'</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'utilizadores'");
    if($stmt->rowCount() > 0) {
        echo "<div class='success'>✅ Tabela 'utilizadores' existe!</div>";
    } else {
        echo "<div class='error'>❌ Tabela 'utilizadores' NÃO EXISTE!</div>";
        echo "<p><strong>SOLUÇÃO:</strong> Importe o ficheiro pap__1_.sql no phpMyAdmin</p>";
        die();
    }
} catch(Exception $e) {
    echo "<div class='error'>❌ Erro ao verificar tabela: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Teste 3: Estrutura da tabela
echo "<div class='card'>";
echo "<h2>3️⃣ Estrutura da Tabela</h2>";
$colunas = $pdo->query("DESCRIBE utilizadores")->fetchAll();
echo "<table>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
foreach($colunas as $col) {
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Teste 4: Listar TODOS os utilizadores
echo "<div class='card'>";
echo "<h2>4️⃣ Utilizadores Existentes na Base de Dados</h2>";
$stmt = $pdo->query("SELECT id, nome, email, tipo, ativo FROM utilizadores ORDER BY tipo, nome");
$users = $stmt->fetchAll();

if(empty($users)) {
    echo "<div class='error'>❌ Nenhum utilizador encontrado! A tabela está vazia.</div>";
    echo "<p><strong>SOLUÇÃO:</strong> Importe novamente o ficheiro pap__1_.sql</p>";
} else {
    echo "<div class='success'>✅ Encontrados " . count($users) . " utilizadores</div>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Ativo</th></tr>";
    foreach($users as $u) {
        $classe = $u['ativo'] == 1 ? '' : 'style="opacity: 0.5; background: #f8d7da;"';
        echo "<tr $classe>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['nome']}</td>";
        echo "<td><strong>{$u['email']}</strong></td>";
        echo "<td><span style='background: " . ($u['tipo'] == 'admin' ? '#28a745' : '#6c757d') . "; color: white; padding: 3px 10px; border-radius: 3px;'>{$u['tipo']}</span></td>";
        echo "<td>" . ($u['ativo'] ? '✅' : '❌') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</div>";

// Teste 5: Verificar password hash de um utilizador
echo "<div class='card'>";
echo "<h2>5️⃣ Teste de Password Hash</h2>";
$stmt = $pdo->query("SELECT id, nome, email, password FROM utilizadores WHERE tipo = 'admin' LIMIT 1");
$admin = $stmt->fetch();

if($admin) {
    echo "<p><strong>Utilizador de teste:</strong> {$admin['nome']} ({$admin['email']})</p>";
    echo "<p><strong>Password Hash na BD:</strong><br><code style='word-break: break-all;'>{$admin['password']}</code></p>";
    
    // Teste com admin123
    $teste_password = 'admin123';
    if(password_verify($teste_password, $admin['password'])) {
        echo "<div class='success'>✅ A password 'admin123' FUNCIONA para este utilizador!</div>";
    } else {
        echo "<div class='error'>❌ A password 'admin123' NÃO funciona!</div>";
        echo "<p>Isto significa que o hash na base de dados está diferente.</p>";
    }
} else {
    echo "<div class='error'>❌ Nenhum admin encontrado!</div>";
}
echo "</div>";

// Teste 6: Simular o processo de login COMPLETO
echo "<div class='card'>";
echo "<h2>6️⃣ Simulação COMPLETA do Processo de Login</h2>";

// Pegar o primeiro admin ativo
$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE tipo = 'admin' AND ativo = 1 LIMIT 1");
$stmt->execute();
$test_user = $stmt->fetch();

if($test_user) {
    $test_email = $test_user['email'];
    $test_password = 'admin123';
    
    echo "<div class='info'>";
    echo "<h3>📝 Tentativa de Login:</h3>";
    echo "<p><strong>Email:</strong> {$test_email}</p>";
    echo "<p><strong>Password:</strong> {$test_password}</p>";
    echo "</div>";
    
    echo "<h3>Passo a Passo:</h3>";
    echo "<ol>";
    
    // Passo 1: Buscar utilizador
    echo "<li><strong>Buscar utilizador por email...</strong><br>";
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND ativo = 1");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();
    
    if($user) {
        echo "<div class='success'>✅ Utilizador encontrado: {$user['nome']}</div>";
    } else {
        echo "<div class='error'>❌ Utilizador NÃO encontrado ou inativo!</div>";
        echo "</li></ol>";
        echo "<div class='error'><strong>PROBLEMA IDENTIFICADO:</strong> O campo 'ativo' pode estar a 0 ou o email não existe!</div>";
    }
    echo "</li>";
    
    if($user) {
        // Passo 2: Verificar password
        echo "<li><strong>Verificar password...</strong><br>";
        
        if(password_verify($test_password, $user['password'])) {
            echo "<div class='success'>✅ Password correta!</div>";
            echo "<div class='success' style='margin-top: 20px;'>";
            echo "<h3>🎉 LOGIN DEVERIA FUNCIONAR!</h3>";
            echo "<p>Se o login não está a funcionar no site, o problema está no código do login.php</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>❌ Password incorreta!</div>";
            echo "<div class='error' style='margin-top: 20px;'>";
            echo "<h3>🔴 PROBLEMA IDENTIFICADO!</h3>";
            echo "<p>O hash da password na base de dados não corresponde a 'admin123'</p>";
            echo "<p><strong>SOLUÇÃO:</strong> Execute o script de correção abaixo.</p>";
            echo "</div>";
        }
        echo "</li>";
    }
    echo "</ol>";
}
echo "</div>";

// Teste 7: Verificar o código do login.php
echo "<div class='card'>";
echo "<h2>7️⃣ Análise do Código login.php</h2>";
if(file_exists('../site/login.php')) {
    $login_content = file_get_contents('login.php');
    
    echo "<h3>Verificações:</h3>";
    echo "<ul>";
    
    // Verificar require conexao
    if(strpos($login_content, "require_once 'conexao.php'") !== false) {
        echo "<li>✅ Inclui conexao.php</li>";
    } else {
        echo "<li>❌ NÃO inclui conexao.php corretamente</li>";
    }
    
    // Verificar SQL
    if(strpos($login_content, 'SELECT * FROM utilizadores WHERE email = ?') !== false) {
        echo "<li>✅ Query SQL correta</li>";
    } else {
        echo "<li>⚠️ Query SQL pode estar diferente</li>";
    }
    
    // Verificar password_verify
    if(strpos($login_content, 'password_verify') !== false) {
        echo "<li>✅ Usa password_verify()</li>";
    } else {
        echo "<li>❌ NÃO usa password_verify() - ERRO CRÍTICO!</li>";
    }
    
    // Verificar campo ativo
    if(strpos($login_content, 'ativo = 1') !== false || strpos($login_content, "ativo = 1") !== false) {
        echo "<li>✅ Verifica campo 'ativo'</li>";
    } else {
        echo "<li>⚠️ Pode não estar a verificar se o utilizador está ativo</li>";
    }
    
    echo "</ul>";
} else {
    echo "<div class='error'>❌ Ficheiro login.php não encontrado!</div>";
}
echo "</div>";

// SOLUÇÃO AUTOMÁTICA
echo "<div class='card test-login'>";
echo "<h2>🔧 CORREÇÃO AUTOMÁTICA</h2>";
echo "<p>Clique no botão abaixo para corrigir TODOS os utilizadores com a password 'admin123':</p>";

if(isset($_POST['corrigir_passwords'])) {
    $nova_password = 'admin123';
    $hash = password_hash($nova_password, PASSWORD_DEFAULT);
    
    // Atualizar TODOS os utilizadores
    $stmt = $pdo->prepare("UPDATE utilizadores SET password = ?, ativo = 1");
    $stmt->execute([$hash]);
    
    echo "<div class='success'>";
    echo "<h3>✅ PASSWORDS CORRIGIDAS!</h3>";
    echo "<p>Todos os utilizadores agora têm a password: <strong>admin123</strong></p>";
    echo "<p>Todos os utilizadores foram ativados (ativo = 1)</p>";
    echo "</div>";
    
    // Teste final
    echo "<h3>Teste Final:</h3>";
    $stmt = $pdo->query("SELECT id, nome, email, tipo FROM utilizadores LIMIT 3");
    $usuarios_teste = $stmt->fetchAll();
    
    echo "<p>Tente fazer login com QUALQUER um destes:</p>";
    echo "<table>";
    echo "<tr><th>Nome</th><th>Email</th><th>Password</th><th>Tipo</th></tr>";
    foreach($usuarios_teste as $u) {
        echo "<tr>";
        echo "<td>{$u['nome']}</td>";
        echo "<td><strong>{$u['email']}</strong></td>";
        echo "<td><code>admin123</code></td>";
        echo "<td>{$u['tipo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div class='info' style='margin-top: 20px;'>";
    echo "<h3>🚀 Próximos Passos:</h3>";
    echo "<ol>";
    echo "<li>Vá para a página de login: <a href='login.php'>login.php</a></li>";
    echo "<li>Use qualquer email da tabela acima</li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "<li>Se ainda não funcionar, o problema está no código do login.php</li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<form method='POST'>";
    echo "<button type='submit' name='corrigir_passwords' style='background: #ffc107; color: black; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;'>";
    echo "🔧 CORRIGIR PASSWORDS AGORA";
    echo "</button>";
    echo "</form>";
    echo "<p style='margin-top: 10px; color: #856404;'><small>⚠️ Isto vai definir a password de TODOS os utilizadores para 'admin123'</small></p>";
}
echo "</div>";

echo "<div class='card'>";
echo "<h2>📋 Resumo Final</h2>";
echo "<p>Este script verificou:</p>";
echo "<ol>";
echo "<li>✅ Conexão à base de dados</li>";
echo "<li>✅ Existência da tabela utilizadores</li>";
echo "<li>✅ Estrutura da tabela</li>";
echo "<li>✅ Utilizadores existentes</li>";
echo "<li>✅ Hash da password</li>";
echo "<li>✅ Simulação do processo de login</li>";
echo "<li>✅ Código do login.php</li>";
echo "</ol>";
echo "</div>";
?>