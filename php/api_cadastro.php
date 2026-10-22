<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "motorapp";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erro na conexão com o banco de dados"]);
    exit();
}

// Recebe os dados enviados via POST pelo aplicativo Android
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$senhaInput = $_POST['senha'] ?? '';

if (empty($nome) || empty($email) || empty($telefone) || empty($senhaInput)) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
    exit();
}

// Verifica se o e-mail já existe no banco
$sqlCheck = "SELECT id FROM cliente WHERE email = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Este e-mail já está cadastrado."]);
    exit();
}

// Insere o novo cliente
$sql = "INSERT INTO cliente (nome, email, telefone, senha) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $telefone, $senhaInput);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao inserir no banco de dados."]);
}

$stmt->close();
$conn->close();
?>