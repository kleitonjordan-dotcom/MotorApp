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
$cliente_id = $_POST['cliente_id'] ?? $_GET['cliente_id'] ?? '';

if (empty($cliente_id)) {
    echo json_encode(["success" => false, "message" => "ID do cliente não informado."]);
    exit();
}

// --- AÇÃO 1: LISTAR VEÍCULOS DO CLIENTE ---
if ($acao === 'listar') {
    $sql = "SELECT id, marca, modelo, placa, ano FROM veiculo WHERE cliente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $veiculos = [];
    while ($row = $result->fetch_assoc()) {
        $veiculos[] = $row;
    }

    echo json_encode(["success" => true, "veiculos" => $veiculos]);
    $stmt->close();
} 
// --- AÇÃO 2: CADASTRAR NOVO VEÍCULO ---
else if ($acao === 'cadastrar') {
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $ano = $_POST['ano'] ?? '';

    if (empty($marca) || empty($modelo) || empty($placa)) {
        echo json_encode(["success" => false, "message" => "Preencha marca, modelo e placa!"]);
        exit();
    }

    $sql = "INSERT INTO veiculo (cliente_id, marca, modelo, placa, ano) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $cliente_id, $marca, $modelo, $placa, $ano);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Veículo cadastrado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao cadastrar veículo: " . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Ação inválida."]);
}

$conn->close();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao == 'excluir') {
    $veiculo_id = $_POST['veiculo_id'] ?? '';

    if (empty($veiculo_id)) {
        echo json_encode(["success" => false, "message" => "ID do veículo inválido."]);
        exit();
    }

    $sql = "DELETE FROM veiculos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $veiculo_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Veículo excluído com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir o veículo."]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

?>