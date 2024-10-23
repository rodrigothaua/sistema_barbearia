<?php
session_start(); // Iniciar a sessão

// Verificar se a sessão está ativa
if (isset($_SESSION['usuario_id'])) {
    // Destruir todas as variáveis de sessão
    $_SESSION = array();

    // Destruir a sessão
    session_destroy();
}

// Redirecionar para a página de login
header("Location: index.php");
exit();
?>
