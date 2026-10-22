<?php
// =====================================================================
// API DA ÁREA DO MECÂNICO (dono da oficina)
// ---------------------------------------------------------------------
// Arquivo SEPARADO de propósito: assim o api_agendamentos.php que o app
// do cliente usa continua funcionando exatamente como antes.
//
// Ações disponíveis (todas exigem mecanico_id de um usuário tipo 'mecanico'):
//   GET  ?acao=resumo&mecanico_id=X
//        -> contadores por status + quantidade de serviços de hoje
//   GET  ?acao=listar&mecanico_id=X&status=Pendente|Confirmado|Em Manutenção|Concluído|Cancelado|Todos[&periodo=hoje]
//        -> lista de agendamentos com dados do cliente e do veículo
//   POST acao=atualizar, mecanico_id=X, id=ID_AGENDAMENTO, status=NOVO_STATUS
//        -> muda o status de um agendamento
// =====================================================================
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "motorapp";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erro na conexão com o banco de dados"]);
    exit();
}
$conn->set_charset("utf8mb4");

function responder($dados) {
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$mecanico_id = (int)($_POST['mecanico_id'] ?? $_GET['mecanico_id'] ?? 0);

// ---------------------------------------------------------------------
// Segurança básica: só usuário do tipo 'mecanico' pode usar esta API
// ---------------------------------------------------------------------
$chk = $conn->prepare("SELECT id FROM cliente WHERE id = ? AND tipo = 'mecanico'");
$chk->bind_param("i", $mecanico_id);
$chk->execute();
$ehMecanico = $chk->get_result()->num_rows > 0;
$chk->close();

if (!$ehMecanico) {
    responder(["success" => false, "message" => "Acesso negado: usuário não é mecânico."]);
    $conn->close();
    exit();
}

$statusValidos = ['Pendente', 'Confirmado', 'Em Manutenção', 'Concluído', 'Cancelado'];

// =====================================================================
// 1. RESUMO (contadores do menu do mecânico)
// =====================================================================
if ($acao === 'resumo') {
    $resumo = array_fill_keys($statusValidos, 0);

    $r = $conn->query("SELECT status, COUNT(*) AS total FROM agendamento GROUP BY status");
    while ($row = $r->fetch_assoc()) {
        if (array_key_exists($row['status'], $resumo)) {
            $resumo[$row['status']] = (int)$row['total'];
        }
    }

    $hoje = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM agendamento WHERE data_agendamento = ? AND status <> 'Cancelado'");
    $stmt->bind_param("s", $hoje);
    $stmt->execute();
    $resumo['hoje'] = (int)$stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    responder(["success" => true, "resumo" => $resumo]);
}

// =====================================================================
// 2. LISTAR AGENDAMENTOS (com filtro opcional de status e de "hoje")
// =====================================================================
else if ($acao === 'listar') {
    $status = $_GET['status'] ?? $_POST['status'] ?? 'Todos';
    $periodo = $_GET['periodo'] ?? $_POST['periodo'] ?? '';

    // LEFT JOIN: se o cliente excluir o veículo, o agendamento continua aparecendo
    $sql = "SELECT a.id,
                   a.descricao AS pacote,
                   a.data_agendamento,
                   a.hora_agendamento,
                   a.status,
                   COALESCE(c.nome, 'Cliente removido') AS cliente_nome,
                   COALESCE(c.telefone, '') AS telefone,
                   COALESCE(v.marca, '') AS marca,
                   COALESCE(v.modelo, 'Veículo removido') AS modelo,
                   COALESCE(v.placa, '') AS placa
            FROM agendamento a
            LEFT JOIN cliente c ON a.id_cliente = c.id
            LEFT JOIN veiculo v ON a.id_veiculo = v.id
            WHERE 1 = 1";

    $tipos = "";
    $params = [];

    if ($status !== 'Todos' && in_array($status, $statusValidos, true)) {
        $sql .= " AND a.status = ?";
        $tipos .= "s";
        $params[] = $status;
    }

    if ($periodo === 'hoje') {
        $sql .= " AND a.data_agendamento = ? AND a.status <> 'Cancelado'";
        $tipos .= "s";
        $params[] = date('Y-m-d');
    }

    $sql .= " ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC";

    $stmt = $conn->prepare($sql);
    if ($tipos !== "") {
        $stmt->bind_param($tipos, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $agendamentos = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $agendamentos[] = $row;
    }
    $stmt->close();

    responder(["success" => true, "agendamentos" => $agendamentos]);
}

// =====================================================================
// 3. ATUALIZAR STATUS (Confirmar, Iniciar, Concluir, Cancelar...)
// =====================================================================
else if ($acao === 'atualizar') {
    $id = (int)($_POST['id'] ?? 0);
    $novoStatus = $_POST['status'] ?? '';

    if ($id <= 0 || !in_array($novoStatus, $statusValidos, true)) {
        responder(["success" => false, "message" => "Dados inválidos."]);
        $conn->close();
        exit();
    }

    // Confere se o agendamento existe (o cliente pode ter cancelado/excluído)
    $existe = $conn->prepare("SELECT id FROM agendamento WHERE id = ?");
    $existe->bind_param("i", $id);
    $existe->execute();
    $encontrou = $existe->get_result()->num_rows > 0;
    $existe->close();

    if (!$encontrou) {
        responder(["success" => false, "message" => "Agendamento não encontrado (o cliente pode ter cancelado)."]);
        $conn->close();
        exit();
    }

    $stmt = $conn->prepare("UPDATE agendamento SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novoStatus, $id);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        responder(["success" => true, "message" => "Status atualizado para: $novoStatus"]);
    } else {
        responder(["success" => false, "message" => "Erro ao atualizar o status."]);
    }
}

else {
    responder(["success" => false, "message" => "Ação inválida."]);
}

$conn->close();
?>
