<?php
include("../php/conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_agendamento = $_POST['id_agendamento'];
    $novo_status = $_POST['status'];

    $sql_update = "UPDATE agendamento SET status = '$novo_status' WHERE id = '$id_agendamento'";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: painel.php");
        exit();
    } else {
        echo "Erro ao atualizar status: " . $conn->error;
    }
}
?>