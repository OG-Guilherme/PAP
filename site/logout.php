<?php
session_start();
session_destroy();
setcookie('eduweb_tema', '', time() - 3600, '/'); // NÃO apaga o tema — mantém a preferência
// Re-guarda só o tema se existia
header('Location: index.php');
exit;
