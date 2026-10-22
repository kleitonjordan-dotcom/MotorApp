<?php
session_start();

// Só mecânico logado pode mudar status
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') !== 'mecanico') {
    header("Location: ../pages/login.php");
    exit();
}

include("../php/conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_agendamento = (int)($_POST['id_agendamento'] ?? 0);
    $novo_status = $_POST['status'] ?? '';
    $status_validos = ['Pendente', 'Confirmado', 'Em Manutenção', 'Concluído', 'Cancelado'];

    if ($id_agendamento <= 0 || !in_array($novo_status, $status_validos, true)) {
        die("Dados inválidos.");
    }

    // Consulta preparada (evita SQL Injection)
    $stmt = $conn->prepare("UPDATE agendamento SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $id_agendamento);

    if ($stmt->execute()) {
        $stmt->close();
        // Volta para a mesma lista que o mecânico estava vendo
        $volta = isset($_POST['filtro']) ? "?status=" . urlencode($_POST['filtro']) : "?status=Todos";
        if (($_POST['periodo'] ?? '') === 'hoje') $volta .= "&periodo=hoje";
        header("Location: painel.php" . $volta);
        exit();
    } else {
        echo "Erro ao atualizar status: " . $conn->error;
    }
}
?>
