<?php
$conn = new mysqli('localhost', 'root', 'root');

if ($conn->connect_error) {
    die("Falha de conexão: " . $conn->connect_error);
}
echo "Conexão bem-sucedida!";
$conn->close();
?>

