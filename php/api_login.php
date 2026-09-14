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

// Recebe os parâmetros limpando espaços vazios
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senhaInput = isset($_POST['senha']) ? trim($_POST['senha']) : '';

if (empty($email) || empty($senhaInput)) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
    exit();
}

// Busca o cliente pelo e-mail e senha
$sql = "SELECT id, nome FROM cliente WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $senhaInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cliente = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "Login realizado com sucesso!",
        "nome" => $cliente['nome'],
        "id" => $cliente['id']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
}

$stmt->close();
$conn->close();
?>