<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 1. LÓGICA PARA SALVAR O AGENDAMENTO
if (isset($_POST['agendar'])) {
    $id_veiculo = $_POST['id_veiculo'];
    $data = $_POST['data_agendamento'];
    $hora = $_POST['hora_agendamento'];
    $descricao = $_POST['descricao'];

    $sql_insert = "INSERT INTO agendamento (id_cliente, id_veiculo, data_agendamento, hora_agendamento, descricao) 
                   VALUES ('$id_usuario', '$id_veiculo', '$data', '$hora', '$descricao')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Agendamento solicitado com sucesso!'); window.location.href='agendamentos.php';</script>";
    } else {
        echo "Erro ao agendar: " . $conn->error;
    }
}

// 2. BUSCA OS VEÍCULOS DO CLIENTE PARA MOSTRAR NO FORMULÁRIO
$sql_veiculos = "SELECT * FROM veiculo WHERE id_cliente = '$id_usuario'";
$meus_veiculos = $conn->query($sql_veiculos);

// 3. BUSCA OS AGENDAMENTOS JÁ FEITOS POR ESSE CLIENTE (UNINDO COM A TABELA VEICULO PARA VER O NOME DO CARRO)
$sql_busca_agenda = "SELECT agendamento.*, veiculo.marca, veiculo.modelo 
                     FROM agendamento 
                     JOIN veiculo ON agendamento.id_veiculo = veiculo.id 
                     WHERE agendamento.id_cliente = '$id_usuario'
                     ORDER BY agendamento.data_agendamento ASC";
$meus_agendamentos = $conn->query($sql_busca_agenda);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorApp - Agendamentos</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/meus_dados.css">
    <link rel="stylesheet" href="../css/agendamentos.css">
</head>
<body>

    <div class="sidebar">
        <div class="top-sidebar">
            <h2>MotorApp</h2>
            <div class="menu-items">
                <a href="index.php">🏠 Início</a>
                <a href="meus_dados.php">👤 Meus Dados</a>
                <a href="meus_veiculos.php">🚗 Meus Veículos</a>
                <a href="agendamentos.php" class="active">📅 Agendamentos</a>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1 style="color: #333; margin-bottom: 20px;">Agendar Serviço</h1>

        <div class="agendamentos-container">
            <div class="box-agendar">
                <h3 style="margin-bottom: 15px; color: #ff6600;">Novo Agendamento</h3>
                <form method="POST">
                    
                    <div class="form-group">
                        <label>Escolha o Veículo</label>
                        <select name="id_veiculo" class="select-veiculo" required>
                            <option value="">-- Selecione o carro --</option>
                            <?php if($meus_veiculos->num_rows > 0): ?>
                                <?php while($carro = $meus_veiculos->fetch_assoc()): ?>
                                    <option value="<?php echo $carro['id']; ?>">
                                        <?php echo $carro['marca'] . " " . $carro['modelo'] . " (" . $carro['placa'] . ")"; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="" disabled>Cadastre um veículo primeiro!</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Data desejada</label>
                        <input type="date" name="data_agendamento" required>
                    </div>

                    <div class="form-group">
                        <label>Horário</label>
                        <input type="time" name="hora_agendamento" required>
                    </div>

                    <div class="form-group">
                        <label>O que o veículo tem? (Defeito/Serviço)</label>
                        <textarea name="descricao" class="textarea-descricao" placeholder="Ex: Revisão de 50.000km, barulho na roda dianteira, troca de pastilhas..." required></textarea>
                    </div>

                    <button type="submit" name="agendar" class="btn-salvar">Confirmar Agendamento</button>
                </form>
            </div>

            <div class="lista-agendamentos">
                <h3 style="margin-bottom: 15px; color: #333;">Meus Agendamentos</h3>
                /*trocar aqui*/
               <?php if ($meus_agendamentos->num_rows > 0): ?>
                    <?php while($agenda = $meus_agendamentos->fetch_assoc()): ?>
                        <div class="agendamento-card">
                            <h4>🚗 <?php echo $agenda['marca'] . " " . $agenda['modelo']; ?></h4>
                            <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($agenda['data_agendamento'])); ?></p>
                            <p><strong>Horário:</strong> <?php echo substr($agenda['hora_agendamento'], 0, 5); ?>h</p>
                            <p><strong>Problema relatado:</strong> <?php echo $agenda['descricao']; ?></p>
                            
                            <?php 
                                // Essa linha limpa espaços e acentos para converter ex: "Em Manutenção" em "EmManutencao" para o CSS funcionar
                                $status_classe = str_replace(array(' ', 'ç', 'ã'), array('', 'c', 'a'), $agenda['status']); 
                            ?>
                            <span class="status-badge badge-<?php echo $status_classe; ?>">
                                📊 Status: <?php echo $agenda['status']; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #777;">Você não possui nenhum agendamento marcado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>