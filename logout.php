<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (isset($_SESSION['user_id'])) {
    // Registra o evento de logout (opcional, para fins de auditoria)
    // Aqui você poderia registrar em um log ou banco de dados, se necessário

    // Destroi a sessão de forma segura
    $_SESSION = []; // Limpa todas as variáveis da sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params(); // Obtém os parâmetros do cookie da sessão
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], $params["domain"], 
            $params["secure"], $params["httponly"]
        ); // Remove o cookie da sessão
    }
    session_destroy(); // Destroi a sessão

    // Redireciona para a página de login com uma mensagem
    header("Location: index.php?message=Logout realizado com sucesso!&status=success");
    exit();
} else {
    // Caso não esteja logado, redireciona para a página de login
    header("Location: index.php");
    exit();
}
?>

