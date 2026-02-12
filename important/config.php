<?php
// config.php - Configuração central do EduWeb

// Configurações do Site
define('SITE_NAME', 'EduWeb');
define('SITE_URL', 'http://localhost/eduweb');
define('SITE_EMAIL', 'eduweb@gmail.com');
define('SITE_TELEFONE', '+351 21 XXX XXXX');
define('SITE_MORADA', 'Rua da Escola, Amadora, Portugal');

// Configurações de Base de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'pap');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de Upload
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_FILE_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Configurações de Paginação
define('ITEMS_PER_PAGE', 12);
define('ITEMS_PER_PAGE_ADMIN', 20);

// Modo Debug
define('DEBUG_MODE', true);

if(DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Lisbon');

// Configurações de Sessão (só se a sessão ainda não iniciou)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_lifetime', 0);
    session_start();
}

// Conexão à Base de Dados
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    if(DEBUG_MODE) {
        die("Erro de conexão: " . $e->getMessage());
    } else {
        die("Erro ao conectar à base de dados.");
    }
}

// Funções Auxiliares
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function formatDate($date) {
    if(!$date) return '';
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    if(!$datetime) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
}

function requireLogin() {
    if(!isLoggedIn()) {
        redirect('../site/login.php');
    }
}

function requireAdmin() {
    if(!isAdmin()) {
        redirect('../admin/index.php');
    }
}

function logAdminAction($pdo, $usuario_id, $acao, $tipo, $tabela = null, $item_id = null, $descricao = null, $dados_antigos = null, $dados_novos = null) {
    $stmt = $pdo->prepare("INSERT INTO logs_admin (usuario_id, acao, tipo, tabela, item_id, descricao, dados_antigos, dados_novos, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $usuario_id,
        $acao,
        $tipo,
        $tabela,
        $item_id,
        $descricao,
        $dados_antigos ? json_encode($dados_antigos) : null,
        $dados_novos ? json_encode($dados_novos) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

function uploadImage($file, $directory = 'geral') {
    $upload_path = UPLOAD_DIR . $directory . '/';
    
    if(!is_dir($upload_path)) {
        mkdir($upload_path, 0777, true);
    }
    
    if($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erro no upload'];
    }
    
    if($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Ficheiro muito grande'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if(!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Tipo de ficheiro não permitido'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $full_path = $upload_path . $filename;
    
    if(move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => true, 'filename' => $directory . '/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Erro ao mover ficheiro'];
}

function getThemeClass() {
    return isset($_SESSION['tema']) && $_SESSION['tema'] === 'escuro' ? 'tema-escuro' : 'tema-claro';
}
?>