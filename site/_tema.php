<?php
/**
 * _tema.php — Incluir no topo de qualquer página em /site/
 * Garante tema persistente via cookie + sessão.
 *
 * Uso: require_once '_tema.php';
 * Assume que session_start() já foi chamado.
 */

if (!isset($_SESSION['tema'])) {
    $_SESSION['tema'] = isset($_COOKIE['eduweb_tema']) ? $_COOKIE['eduweb_tema'] : 'claro';
}

if (isset($_GET['toggle_theme']) || isset($_POST['toggle_theme'])) {
    $_SESSION['tema'] = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
    setcookie('eduweb_tema', $_SESSION['tema'], time() + (365 * 24 * 3600), '/');

    // Se for chamada AJAX do splash, responde OK sem redirecionar
    if (!empty($_GET['ajax'])) {
        http_response_code(200);
        exit;
    }

    // Redireciona de volta sem o parâmetro
    $url   = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    unset($query['toggle_theme']);
    header('Location: ' . $url . (!empty($query) ? '?' . http_build_query($query) : ''));
    exit;
}

$temaClasse = $_SESSION['tema'] === 'claro' ? 'tema-claro' : 'tema-escuro';
$logoImg    = $_SESSION['tema'] === 'claro' ? 'escuro' : 'claro';
