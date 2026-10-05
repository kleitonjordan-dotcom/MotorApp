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
// 1. CADASTRAR AGENDAMENTO (Super flexível para a data e hora)
// ==========================================
if ($acao == 'cadastrar') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $veiculo_id = $_POST['veiculo_id'] ?? '';
    $pacote = $_POST['pacote'] ?? '';
    
    // Aceita múltiplos nomes possíveis que o app possa estar a enviar
    $data = $_POST['data'] ?? $_POST['data_agendamento'] ?? $_POST['dataAgendamento'] ?? $_POST['txtData'] ?? '';
    $hora = $_POST['hora'] ?? $_POST['hora_agendamento'] ?? $_POST['horaAgendamento'] ?? $_POST['txtHora'] ?? '';

    if (empty($cliente_id) || empty($veiculo_id) || empty($pacote)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os dados do agendamento."]);
        exit();
    }

    // Se a data ou hora vierem vazias, define a data/hora atual do servidor como segurança
    if (empty($data)) { $data = date('Y-m-d'); }
    if (empty($hora)) { $hora = date('H:i:s'); }

    $sql = "INSERT INTO agendamento (id_cliente, id_veiculo, descricao, data_agendamento, hora_agendamento, status) VALUES (?, ?, ?, ?, ?, 'Pendente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $cliente_id, $veiculo_id, $pacote, $data, $hora);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Agendamento realizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao salvar agendamento."]);
    }
    $stmt->close();
}

// ==========================================
// 2. LISTAR AGENDAMENTOS
// ==========================================
else if ($acao == 'listar') {
    $cliente_id = $_GET['cliente_id'] ?? '';

    if (empty($cliente_id)) {
        echo json_encode(["success" => false, "message" => "Cliente não informado."]);
        exit();
    }

    $sql = "SELECT a.id, a.descricao AS pacote, v.modelo AS veiculo_nome, a.data_agendamento AS data, a.hora_agendamento AS hora 
            FROM agendamento a 
            LEFT JOIN veiculo v ON a.id_veiculo = v.id 
            WHERE a.id_cliente = ? 
            ORDER BY a.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $agendamentos = array();
    while ($row = $result->fetch_assoc()) {
        if (empty($row['veiculo_nome'])) {
            $row['veiculo_nome'] = "Veículo não especificado";
        }
        if (empty($row['data']) || $row['data'] == '0000-00-00') $row['data'] = "A definir";
        if (empty($row['hora']) || $row['hora'] == '00:00:00') $row['hora'] = "A definir";

        $agendamentos[] = $row;
    }

    echo json_encode(array(
        "success" => true,
        "agendamentos" => $agendamentos
    ));
    
    $stmt->close();
}

// ==========================================
// 3. CANCELAR AGENDAMENTO
// ==========================================
else if ($acao == 'cancelar') {
    $id = $_POST['id'] ?? $_GET['id'] ?? '';

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "ID do agendamento não informado."]);
        exit();
    }

    $sql = "DELETE FROM agendamento WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Agendamento cancelado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao cancelar agendamento."]);
    }
    $stmt->close();
}

else {
    echo json_encode(["success" => false, "message" => "Ação inválida."]);
}

$conn->close();
?>