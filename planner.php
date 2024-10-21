<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', 'root', 'planner');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Adicionar nota
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Verificação se o título está vazio ou tem tamanho inadequado
    if (strlen($title) < 3) {
        echo "<p style='color: red;'>O título da nota deve ter pelo menos 3 caracteres!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");
        $created_at = $date . ' ' . $time;
        $stmt->bind_param("isss", $user_id, $title, $content, $created_at);

        if ($stmt->execute()) {
            header("Location: planner.php?message=Nota adicionada com sucesso!&status=success");
        } else {
            echo "<p style='color: red;'>Erro ao adicionar nota: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Atualizar status da nota (marcar como concluída)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note_id'])) {
    $note_id = $_POST['note_id'];
    $completed = isset($_POST['completed']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE notes SET completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $completed, $note_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header("Location: planner.php?message=Status da nota atualizado!&status=success");
    } else {
        echo "<p style='color: red;'>Erro ao atualizar status: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Buscar notas
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT id, title, content, created_at, completed FROM notes WHERE user_id = $user_id");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Diário</title>
    <link rel="stylesheet" href="style.css">
   <style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
        margin-bottom: 20px;
    }
    .note-list ul {
        list-style-type: none;
        padding: 0;
    }
    .note-list li {
        padding: 15px;
        margin-bottom: 10px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .note-list li.completed {
        opacity: 0.6;
        text-decoration: line-through;
    }
    .note-list li strong {
        font-size: 18px;
        display: block;
    }
    .note-list li em {
        font-size: 12px;
        color: #666;
    }
    .message {
        text-align: center;
        margin-bottom: 15px;
    }
    .message.success {
        color: green;
    }
    .message.error {
        color: red;
    }
    .back-link {
        text-align: center;
        margin-top: 10px;
    }
    /* Novas estilizações para os campos do formulário */
    input[type="text"],
    textarea,
    input[type="date"],
    input[type="time"] {
        width: calc(100% - 20px); /* Ajusta a largura para não encostar nas bordas */
        padding: 10px; /* Adiciona preenchimento interno */
        margin-bottom: 10px; /* Espaçamento entre os campos */
        border: 1px solid #ccc; /* Borda padrão */
        border-radius: 5px; /* Bordas arredondadas */
    }
    button {
        padding: 10px 15px; /* Espaçamento interno do botão */
        margin-top: 10px; /* Margem superior para afastar do campo de horário */
        background-color: #4CAF50; /* Cor de fundo do botão */
        color: white; /* Cor do texto do botão */
        border: none; /* Sem borda */
        border-radius: 5px; /* Bordas arredondadas */
        cursor: pointer; /* Muda o cursor para indicar clicável */
    }
    button:hover {
        background-color: #45a049; /* Cor do botão ao passar o mouse */
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Planner Diário</h1>

        <!-- Exibir mensagens de sucesso ou erro -->
        <?php if (isset($_GET['message'])): ?>
            <p class="message <?= $_GET['status']; ?>"><?= $_GET['message']; ?></p>
        <?php endif; ?>

        <!-- Formulário para adicionar nota -->
        <form id="planner-form" method="POST" action="planner.php">
            <h2>Adicionar Nota</h2>
            <input type="text" name="title" placeholder="Título da Nota" required>
            <textarea name="content" placeholder="Conteúdo da Nota" rows="4" required></textarea>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <button type="submit">Adicionar Nota</button>
        </form>

        <!-- Lista de notas -->
        <div class="note-list">
            <h2>Suas Notas</h2>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="<?= $row['completed'] ? 'completed' : ''; ?>">
                        <form method="POST" action="planner.php">
                            <input type="hidden" name="note_id" value="<?= $row['id']; ?>">
                            <input type="checkbox" name="completed" value="1" <?= $row['completed'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                            <strong><?= htmlspecialchars($row['title']); ?></strong><br>
                            <?= nl2br(htmlspecialchars($row['content'])); ?><br>
                            <em>Criado em: <?= $row['created_at']; ?></em>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

         <div class="back-link">
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

    <!-- Rodapé -->
    <footer>
        <p>Direitos &copy; Luan Carlos e Jhulya Luiza</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>

