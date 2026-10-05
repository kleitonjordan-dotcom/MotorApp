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

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// ==========================================
// 1. BUSCAR DADOS DO CLIENTE
// ==========================================
if ($acao == 'buscar') {
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "ID do cliente não informado."]);
        exit();
    }

    $sql = "SELECT id, nome, email, cpf, telefone FROM cliente WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "cliente" => $row]);
    } else {
        echo json_encode(["success" => false, "message" => "Cliente não encontrado."]);
    }
    $stmt->close();
}

// ==========================================
// 2. ATUALIZAR DADOS DO CLIENTE (Com CPF)
// ==========================================
else if ($acao == 'atualizar') {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    if (empty($id) || empty($nome) || empty($email)) {
        echo json_encode(["success" => false, "message" => "Preencha os campos obrigatórios."]);
        exit();
    }

    $sql = "UPDATE cliente SET nome = ?, email = ?, cpf = ?, telefone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $email, $cpf, $telefone, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Dados atualizados com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar dados."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Ação inválida."]);
}

$conn->close();
?>