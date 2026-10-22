<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 1. LÓGICA PARA CADASTRAR O VEÍCULO
if (isset($_POST['cadastrar_veiculo'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];

    // Inserimos no banco passando o id_cliente da sessão
    $sql_insert = "INSERT INTO veiculo (id_cliente, marca, modelo, placa) 
                   VALUES ('$id_usuario', '$marca', '$modelo', '$placa')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Veículo cadastrado com sucesso!'); window.location.href='meus_veiculos.php';</script>";
    } else {
        echo "Erro ao cadastrar veículo: " . $conn->error;
    }
}

// 2. LÓGICA PARA BUSCAR OS VEÍCULOS APENAS DESTE CLIENTE
$sql_busca = "SELECT * FROM veiculo WHERE id_cliente = '$id_usuario'";
$meus_carros = $conn->query($sql_busca);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorApp - Meus Veículos</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/meus_dados.css"> <link rel="stylesheet" href="../css/meus_veiculos.css">
</head>
<body>

    <div class="sidebar">
        <div class="top-sidebar">
            <h2>MotorApp</h2>
            <div class="menu-items">
                <a href="index.php">🏠 Início</a>
                <a href="meus_dados.php">👤 Meus Dados</a>
                <a href="meus_veiculos.php" class="active">🚗 Meus Veículos</a>
                <a href="agendamentos.php">📅 Agendamentos</a>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1 style="color: #333; margin-bottom: 20px;">Gerenciar Meus Veículos</h1>

        <div class="veiculos-container">
            <div class="box-cadastro">
                <h3 style="margin-bottom: 15px; color: #ff6600;">Cadastrar Novo Carro</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" name="marca" placeholder="Ex: Ford, Volkswagen" required>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" placeholder="Ex: Fiesta, Gol" required>
                    </div>
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" placeholder="Ex: ABC1D23" required>
                    </div>
                    <button type="submit" name="cadastrar_veiculo" class="btn-salvar">Cadastrar Veículo</button>
                </form>
            </div>

            <div class="lista-veiculos">
                <h3 style="margin-bottom: 15px; color: #333;">Meus Carros Cadastrados</h3>
                
                <?php if ($meus_carros->num_rows > 0): ?>
                    <?php while($carro = $meus_carros->fetch_assoc()): ?>
                        <div class="veiculo-card">
                            <h4><?php echo $carro['marca'] . " " . $carro['modelo']; ?></h4>
                            <p><strong>Placa:</strong> <?php echo strtoupper($carro['placa']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #777;">Você ainda não tem nenhum veículo cadastrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>