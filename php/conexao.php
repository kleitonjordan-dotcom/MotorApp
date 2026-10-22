<?php
// Arquivo de conexão usado pelas páginas do SITE (pages/ e admin/).
// Ele só deve conectar ao banco: não pode imprimir nada nem mudar o
// Content-Type, senão as páginas HTML que fazem include("conexao.php")
// quebram. A lista de clientes em JSON que existia aqui foi movida
// para php/listar_clientes.php.

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "motorapp";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
