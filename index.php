<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Diário - Login</title>
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

        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .error, .success {
            text-align: center;
            color: red;
        }

        .success {
            color: green;
        }

        .register-link {
            margin-top: 15px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
                max-width: 100%;
            }

            .login-container input, .login-container button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Planner Diário</h1>

        <div class="login-container">
            <h2>Login</h2>
            <?php if (isset($_GET['message'])): ?>
                <p class="<?= htmlspecialchars($_GET['status']); ?>"><?= htmlspecialchars($_GET['message']); ?></p>
            <?php endif; ?>
            <form id="login-form" method="POST" action="login.php">
                <input type="text" name="username" placeholder="Usuário" required aria-label="Usuário">
                <input type="password" name="password" placeholder="Senha" required aria-label="Senha">
                <button type="submit">Entrar</button>
            </form>
            <div class="register-link">
                <p>Não tem uma conta? <a href="register.php">Registrar-se</a></p>
            </div>
        </div>
    </div>
</body>
</html>

