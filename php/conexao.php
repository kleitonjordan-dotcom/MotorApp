<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "motorapp";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão: " . $conn->connect_error]));
}

// Tabela no singular: 'cliente'
$sql = "SELECT nome, telefone FROM cliente";
$result = $conn->query($sql);

$clientes = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
}

echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
$conn->close();
?>