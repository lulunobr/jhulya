<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', 'root', 'planner');

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário e faz a limpeza
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepara a consulta para buscar o usuário
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Verifica se o usuário existe
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verifica se a senha está correta
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id; // Armazena o ID do usuário na sessão
                header("Location: planner.php"); // Redireciona para a página do planner
                exit();
            } else {
                // Mensagem de erro para senha incorreta
                header("Location: index.php?message=Senha incorreta!&status=error");
                exit();
            }
        } else {
            // Mensagem de erro para usuário não encontrado
            header("Location: index.php?message=Usuário não encontrado!&status=error");
            exit();
        }

        $stmt->close(); // Fecha a declaração
    } else {
        die("Erro na preparação da query: " . $conn->error);
    }
}

$conn->close(); // Fecha a conexão com o banco de dados
?>

