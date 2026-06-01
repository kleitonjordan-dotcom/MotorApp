<?php
session_start();

// 1. Se não estiver logado, chuta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

// 2. Se estiver logado mas NÃO for mecânico, chuta para o painel de cliente
if ($_SESSION['usuario_tipo'] !== 'mecanico') {
    header("Location: ../pages/index.php");
    exit();
}

include("../php/conexao.php");

// ... (resto do seu código do painel.php que busca os agendamentos) ...

/*<?php*/
include("../php/conexao.php");

// Busca TODOS os agendamentos da oficina, trazendo os dados do cliente e do veículo juntos
$sql_todos = "SELECT agendamento.*, cliente.nome, cliente.telefone, veiculo.marca, veiculo.modelo, veiculo.placa 
              FROM agendamento 
              JOIN cliente ON agendamento.id_cliente = cliente.id
              JOIN veiculo ON agendamento.id_veiculo = veiculo.id
              ORDER BY agendamento.data_agendamento ASC, agendamento.hora_agendamento ASC";

$resultado_todos = $conn->query($sql_todos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorApp - Painel do Mecânico</title>
    
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/painel_admin.css">
</head>
<body>

    <div class="sidebar">
        <div class="top-sidebar">
            <h2 style="color: #ff6600;">MotorApp <br><span style="font-size: 14px; color: #fff;">MECÂNICO</span></h2>
            <div class="menu-items">
                <a href="painel.php" class="active">📅 Todos Agendamentos</a>
            </div>
        </div>
        <a href="../pages/login.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1>Painel de Controle - Oficina</h1>
        <p>Gerencie todos os serviços agendados pelos clientes aqui.</p>

        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>Cliente / Tel</th>
                    <th>Veículo (Placa)</th>
                    <th>Data / Hora</th>
                    <th>Descrição do Defeito</th>
                    <th>Status Atual</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_todos->num_rows > 0): ?>
                    <?php while($agenda = $resultado_todos->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo $agenda['nome']; ?></strong><br>
                                <span style="font-size: 12px; color: #777;"><?php echo $agenda['telefone']; ?></span>
                            </td>
                            <td>
                                <?php echo $agenda['marca'] . " " . $agenda['modelo']; ?><br>
                                <span style="font-size: 12px; background: #eee; padding: 2px 5px; border-radius: 3px;">
                                    <?php echo strtoupper($agenda['placa']); ?>
                                </span>
                            </td>
                            <td>
                                📅 <?php echo date('d/m/Y', strtotime($agenda['data_agendamento'])); ?><br>
                                🕒 <?php echo substr($agenda['hora_agendamento'], 0, 5); ?>h
                            </td>
                            <td style="max-width: 250px; color: #555; font-size: 14px;">
                                <?php echo $agenda['descricao']; ?>
                            </td>
                            <form action="atualizar_status.php" method="POST">
                                <td>
                                    <input type="hidden" name="id_agendamento" value="<?php echo $agenda['id']; ?>">
                                    <select name="status" class="select-status status-<?php echo str_replace(' ', '', $agenda['status']); ?>">
                                        <option value="Pendente" <?php if($agenda['status'] == 'Pendente') echo 'selected'; ?>>Pendente</option>
                                        <option value="Em Manutenção" <?php if($agenda['status'] == 'Em Manutenção') echo 'selected'; ?>>Em Manutenção</option>
                                        <option value="Concluído" <?php if($agenda['status'] == 'Concluído') echo 'selected'; ?>>Concluído</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" class="btn-atualizar">Salvar</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #777;">Nenhum agendamento registrado no sistema.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>