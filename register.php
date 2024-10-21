<?php
// Processar o registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', 'root', 'planner');

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validação do nome de usuário (apenas letras e números)
    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        header("Location: register.php?message=Nome de usuário inválido!&status=error");
        exit();
    }

    // Verificar se o número máximo de usuários foi atingido
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $stmt->bind_result($user_count);
    $stmt->fetch();
    $stmt->close();

    if ($user_count >= 10) {
        header("Location: register.php?message=Limite de 10 usuários atingido!&status=error");
        exit();
    }

    // Verificar se as senhas coincidem
    if ($password !== $confirm_password) {
        header("Location: register.php?message=As senhas não coincidem!&status=error");
        exit();
    }

    // Verificar tamanho da senha
    if (strlen($password) < 8) {
        header("Location: register.php?message=A senha deve ter no mínimo 8 caracteres!&status=error");
        exit();
    }

    // Verificar se o usuário já existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: register.php?message=Nome de usuário já existe!&status=error");
        $stmt->close();
        exit();
    }

    // Registrar o usuário
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: index.php?message=Registrado com sucesso!&status=success");
    } else {
        header("Location: register.php?message=Erro no registro!&status=error");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .register-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-container button:hover {
            background-color: #45a049;
        }

        .error, .success {
            text-align: center;
            color: red;
        }

        .success {
            color: green;
        }

        .back-link {
            margin-top: 15px;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .register-container {
                padding: 15px;
                max-width: 100%;
            }

            .register-container input, .register-container button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registrar</h2>
        <?php if (isset($_GET['message'])): ?>
            <p class="<?= $_GET['status']; ?>"><?= $_GET['message']; ?></p>
        <?php endif; ?>
        <form id="register-form" method="POST" action="register.php">
            <input type="text" name="username" placeholder="Usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <input type="password" name="confirm_password" placeholder="Confirme a Senha" required>
            <button type="submit">Registrar</button>
        </form>
        <div class="back-link">
            <p><a href="index.php">Voltar ao Login</a></p>
        </div>
    </div>
</body>
</html>

