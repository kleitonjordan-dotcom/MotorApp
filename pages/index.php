<?php
session_start();
include("../php/conexao.php");

// Segurança: Se não estiver logado, volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 1. CONSULTA PARA CONTAR QUANTOS VEÍCULOS O CLIENTE TEM
$sql_veiculos = "SELECT COUNT(*) as total_veiculos FROM veiculo WHERE id_cliente = '$id_usuario'";
$resultado_veiculos = $conn->query($sql_veiculos);
$dados_veiculos = $resultado_veiculos->fetch_assoc();
$total_veiculos = $dados_veiculos['total_veiculos'];

// 2. CONSULTA PARA BUSCAR O PRÓXIMO AGENDAMENTO ATIVO (A partir de hoje)
$data_hoje = date('Y-m-d');
$sql_proximo_agenda = "SELECT agendamento.*, veiculo.marca, veiculo.modelo 
                       FROM agendamento 
                       JOIN veiculo ON agendamento.id_veiculo = veiculo.id 
                       WHERE agendamento.id_cliente = '$id_usuario' AND agendamento.data_agendamento >= '$data_hoje'
                       ORDER BY agendamento.data_agendamento ASC, agendamento.hora_agendamento ASC 
                       LIMIT 1";

$resultado_agenda = $conn->query($sql_proximo_agenda);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorApp - Área do Cliente</title>
    
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

    <div class="sidebar">
        <div class="top-sidebar">
            <h2>MotorApp</h2>
            <div class="menu-items">
                <a href="index.php" class="active">🏠 Início</a>
                <a href="meus_dados.php">👤 Meus Dados</a>
                <a href="meus_veiculos.php">🚗 Meus Veículos</a>
                <a href="agendamentos.php">📅 Agendamentos</a>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="header-dash">
            <h1>Olá, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
            <p>Seja bem-vindo à área do cliente da nossa oficina.</p>
        </div>

        <div class="dashboard-cards">
            
            <div class="card">
                <h3>Meus Veículos</h3>
                <p><?php echo $total_veiculos; ?></p>
            </div>
            
            <div class="card">
               <h3>Próximo Agendamento</h3>
            <p style="font-size: 16px; font-weight: normal; margin-top: 5px;">
                <?php 
                if ($resultado_agenda->num_rows > 0) {
                    $agenda = $resultado_agenda->fetch_assoc();
                    $data_formatada = date('d/m/Y', strtotime($agenda['data_agendamento']));
                    $hora_formatada = substr($agenda['hora_agendamento'], 0, 5);
                    
                    // Prepara a classe limpando espaços e acentos para o CSS inline funcionar
                    $status_classe = str_replace(array(' ', 'ç', 'ã'), array('', 'c', 'a'), $agenda['status']);
                    
                    echo "<strong>" . $agenda['marca'] . " " . $agenda['modelo'] . "</strong><br>";
                    echo "📅 " . $data_formatada . " às " . $hora_formatada . "h<br>";
                    
                    // Injeta o crachá do status com estilos diretos na tag (CSS Inline)
                    echo "<span class='status-badge badge-" . $status_classe . "' style='display:inline-block; margin-top:8px; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:bold;'>Status: " . $agenda['status'] . "</span>";
                } else {
                    echo "Nenhum agendamento ativo";
                }
                ?>
            </p>
            </div>

        </div>
    </div>

</body>
</html>