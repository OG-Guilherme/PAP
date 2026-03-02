<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Correção de Problemas - Sistema EduWeb</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { background: #e7f3ff; color: #004085; padding: 15px; border-radius: 5px; margin: 10px 0; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>";

// Verificar estrutura de pastas
echo "<div class='card'>";
echo "<h2>1️⃣ Verificação da Estrutura de Pastas</h2>";

$diretorios = [
    '.' => 'Raiz do projeto',
    './admin' => 'Pasta admin',
    './uploads' => 'Pasta uploads',
    './uploads/noticias' => 'Pasta uploads/noticias',
    './uploads/eventos' => 'Pasta uploads/eventos'
];

$pasta_ok = true;
foreach($diretorios as $dir => $desc) {
    if(is_dir($dir)) {
        echo "<div class='success'>✅ {$desc}: <code>{$dir}</code></div>";
    } else {
        echo "<div class='error'>❌ {$desc} NÃO EXISTE: <code>{$dir}</code></div>";
        if($dir !== '.') {
            if(@mkdir($dir, 0777, true)) {
                echo "<div class='success'>✅ Pasta criada automaticamente!</div>";
            } else {
                $pasta_ok = false;
            }
        }
    }
}
echo "</div>";

// Verificar arquivos essenciais
echo "<div class='card'>";
echo "<h2>2️⃣ Verificação de Arquivos Essenciais</h2>";

$arquivos = [
    '../important/conexao.php' => 'Arquivo de conexão',
    '../important/config.php' => 'Arquivo de configuração',
    '../site/login.php' => 'Página de login',
    '../site/index.php' => 'Página inicial',
    'admin/index.php' => 'Painel admin'
];

$arquivos_faltando = [];
foreach($arquivos as $arquivo => $desc) {
    if(file_exists($arquivo)) {
        echo "<div class='success'>✅ {$desc}: <code>{$arquivo}</code></div>";
    } else {
        echo "<div class='error'>❌ {$desc} NÃO ENCONTRADO: <code>{$arquivo}</code></div>";
        $arquivos_faltando[] = $arquivo;
    }
}

// Análise específica: conexao.php vs config.php
if(file_exists('conexao.php') && file_exists('config.php')) {
    echo "<div class='warning'>";
    echo "<h3>⚠️ AVISO: Existem dois arquivos de configuração!</h3>";
    echo "<p>Você tem tanto <code>conexao.php</code> quanto <code>config.php</code></p>";
    echo "<p>Recomendo usar apenas um deles. Veja abaixo qual está sendo usado em cada arquivo.</p>";
    echo "</div>";
} elseif(file_exists('conexao.php')) {
    echo "<div class='info'>ℹ️ Usando <code>../important/conexao.php</code> (sem ../important/config.php)</div>";
} elseif(file_exists('config.php')) {
    echo "<div class='info'>ℹ️ Usando <code>../important/config.php</code> (sem ../important/conexao.php)</div>";
} else {
    echo "<div class='error'>❌ NEM ../important/conexao.php NEM config.php existem!</div>";
}

echo "</div>";

// Verificar includes nos arquivos PHP
echo "<div class='card'>";
echo "<h2>3️⃣ Verificação de Includes/Requires</h2>";

function verificar_includes($arquivo) {
    if(!file_exists($arquivo)) return null;
    
    $conteudo = file_get_contents($arquivo);
    $includes = [];
    
    // Procurar por require/include
    if(preg_match_all("/(require_once|require|include_once|include)\s+['\"]([^'\"]+)['\"]/", $conteudo, $matches)) {
        for($i = 0; $i < count($matches[0]); $i++) {
            $includes[] = [
                'tipo' => $matches[1][$i],
                'arquivo' => $matches[2][$i],
                'linha' => $matches[0][$i]
            ];
        }
    }
    
    return $includes;
}

$arquivos_para_verificar = [
    '../site/index.php',
    '../site/login.php',
    'admin/index.php',
    'admin/noticias.php'
];

foreach($arquivos_para_verificar as $arquivo) {
    echo "<h3>📄 {$arquivo}</h3>";
    
    $includes = verificar_includes($arquivo);
    
    if($includes === null) {
        echo "<div class='error'>❌ Arquivo não encontrado</div>";
        continue;
    }
    
    if(empty($includes)) {
        echo "<div class='warning'>⚠️ Nenhum include/require encontrado</div>";
        continue;
    }
    
    echo "<ul>";
    foreach($includes as $inc) {
        $caminho_completo = dirname($arquivo) . '/' . $inc['arquivo'];
        $caminho_completo = str_replace('/./', '/', $caminho_completo);
        
        if(file_exists($caminho_completo)) {
            echo "<li>✅ <code>{$inc['tipo']} '{$inc['arquivo']}'</code> - OK</li>";
        } else {
            echo "<li>❌ <code>{$inc['tipo']} '{$inc['arquivo']}'</code> - <span style='color: red;'>NÃO EXISTE!</span>";
            
            // Sugestão de correção
            if(strpos($inc['arquivo'], '../important/config.php') !== false && file_exists('conexao.php')) {
                echo "<br><small>💡 Sugestão: Trocar para <code>require_once 'conexao.php';</code></small>";
            } elseif(strpos($inc['arquivo'], '../important/config.php') !== false && file_exists('conexao.php')) {
                echo "<br><small>💡 Sugestão: Trocar para <code>require_once '../important/conexao.php';</code></small>";
            }
            echo "</li>";
        }
    }
    echo "</ul>";
}

echo "</div>";

// Solução Automática para o erro específico
echo "<div class='card'>";
echo "<h2>4️⃣ Solução para o Erro do Admin</h2>";

echo "<div class='error'>";
echo "<h3>Erro Reportado:</h3>";
echo "<pre>Warning: require_once(config.php): Failed to open stream: No such file or directory in C:\\xampp\\htdocs\\PAP\\admin\\index.php on line 3</pre>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📝 Diagnóstico:</h3>";
echo "<p>O arquivo <code>admin/index.php</code> está tentando incluir <code>config.php</code> mas deveria incluir <code>conexao.php</code></p>";
echo "</div>";

if(file_exists('admin/index.php')) {
    $admin_content = file_get_contents('admin/index.php');
    
    // Verificar o que está na linha 3
    $linhas = explode("\n", $admin_content);
    if(isset($linhas[2])) {
        echo "<div class='warning'>";
        echo "<h4>Linha 3 atual:</h4>";
        echo "<pre>" . htmlspecialchars(trim($linhas[2])) . "</pre>";
        echo "</div>";
    }
    
    // Verificar se precisa corrigir
    if(strpos($admin_content, "require_once '../important/config.php'") !== false || 
       strpos($admin_content, 'require_once "../important/config.php"') !== false ||
       strpos($admin_content, "require '../important/config.php'") !== false) {
        
        echo "<div class='warning'>";
        echo "<h3>🔧 CORREÇÃO NECESSÁRIA</h3>";
        echo "<p>Troque a linha 3 de:</p>";
        echo "<pre>require_once '../important/config.php';</pre>";
        echo "<p>Para:</p>";
        echo "<pre>require_once '../important/conexao.php';</pre>";
        echo "</div>";
        
        if(isset($_POST['corrigir_admin'])) {
            // Fazer backup
            copy('admin/index.php', 'admin/index.php.backup');
            
            // Corrigir
            $admin_content = str_replace("require_once '../important/config.php'", "require_once '../important/conexao.php'", $admin_content);
            $admin_content = str_replace('require_once "../important/config.php"', "require_once '../important/conexao.php'", $admin_content);
            $admin_content = str_replace("require '../important/config.php'", "require_once '../important/conexao.php'", $admin_content);
            
            file_put_contents('admin/index.php', $admin_content);
            
            echo "<div class='success'>";
            echo "<h3>✅ CORRIGIDO!</h3>";
            echo "<p>Backup criado em: <code>admin/index.php.backup</code></p>";
            echo "<p>Arquivo corrigido! Tente acessar agora: <a href='admin/index.php'>admin/index.php</a></p>";
            echo "</div>";
        } else {
            echo "<form method='POST'>";
            echo "<button type='submit' name='corrigir_admin' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;'>";
            echo "🔧 CORRIGIR AUTOMATICAMENTE";
            echo "</button>";
            echo "</form>";
        }
    } else {
        echo "<div class='success'>✅ O arquivo já está correto!</div>";
    }
} else {
    echo "<div class='error'>❌ Arquivo admin/index.php não encontrado</div>";
}

echo "</div>";

// Resumo Final
echo "<div class='card'>";
echo "<h2>📋 Resumo e Próximos Passos</h2>";
echo "<ol>";
echo "<li>Certifique-se de que <code>conexao.php</code> existe na raiz do projeto</li>";
echo "<li>Use o botão 'CORRIGIR AUTOMATICAMENTE' acima para corrigir o admin/index.php</li>";
echo "<li>Se ainda tiver problemas, verifique o arquivo <code>admin_index.php</code> que criei como substituto</li>";
echo "<li>Teste o login com o <code>diagnostico_completo.php</code> primeiro</li>";
echo "</ol>";

if(!empty($arquivos_faltando)) {
    echo "<div class='warning'>";
    echo "<h3>⚠️ Arquivos Faltando:</h3>";
    echo "<ul>";
    foreach($arquivos_faltando as $arq) {
        echo "<li><code>{$arq}</code></li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "</div>";
?>