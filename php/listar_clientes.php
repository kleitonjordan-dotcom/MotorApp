<?php
// Lista de clientes (nome e telefone) em JSON.
// Este código ficava antes dentro do conexao.php; foi movido para cá
// para não quebrar as páginas do site que incluem o conexao.php.
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . "/conexao.php");

$sql = "SELECT nome, telefone FROM cliente";
$result = $conn->query($sql);

$clientes = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
}

echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
