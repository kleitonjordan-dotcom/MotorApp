<?php
session_start();

// 1. Se não estiver logado, manda para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

// 2. Se estiver logado mas NÃO for mecânico, manda para o painel de cliente
if (($_SESSION['usuario_tipo'] ?? '') !== 'mecanico') {
    header("Location: ../pages/index.php");
    exit();
}

include("../php/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$status_validos = ['Pendente', 'Confirmado', 'Em Manutenção', 'Concluído', 'Cancelado'];
$filtro = $_GET['status'] ?? 'Todos';
if ($filtro !== 'Todos' && !in_array($filtro, $status_validos, true)) {
    $filtro = 'Todos';
}
// Filtro extra "Hoje" (igual ao card "Serviços agendados hoje" do app)
$apenas_hoje = (($_GET['periodo'] ?? '') === 'hoje');

// Contadores por status (para os cards de resumo e o menu lateral)
$contadores = array_fill_keys($status_validos, 0);
$res = $conn->query("SELECT status, COUNT(*) AS total FROM agendamento GROUP BY status");
while ($row = $res->fetch_assoc()) {
    if (isset($contadores[$row['status']])) $contadores[$row['status']] = (int)$row['total'];
}

$hoje = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM agendamento WHERE data_agendamento = ? AND status <> 'Cancelado'");
$stmt->bind_param("s", $hoje);
$stmt->execute();
$total_hoje = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Busca os agendamentos com dados do cliente e do veículo
// (LEFT JOIN para não sumir agendamento se o veículo for excluído)
$sql = "SELECT a.*, c.nome, c.telefone, v.marca, v.modelo, v.placa
        FROM agendamento a
        LEFT JOIN cliente c ON a.id_cliente = c.id
        LEFT JOIN veiculo v ON a.id_veiculo = v.id
        WHERE 1 = 1";
$tipos = "";
$params = [];
if ($filtro !== 'Todos') {
    $sql .= " AND a.status = ?";
    $tipos .= "s";
    $params[] = $filtro;
}
if ($apenas_hoje) {
    $sql .= " AND a.data_agendamento = ? AND a.status <> 'Cancelado'";
    $tipos .= "s";
    $params[] = $hoje;
}
$sql .= " ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC";

$stmt = $conn->prepare($sql);
if ($tipos !== "") $stmt->bind_param($tipos, ...$params);
$stmt->execute();
$resultado_todos = $stmt->get_result();
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
                <a href="painel.php?periodo=hoje" class="<?php echo $apenas_hoje ? 'active' : ''; ?>">📆 Hoje (<?php echo $total_hoje; ?>)</a>
                <a href="painel.php" class="<?php echo ($filtro === 'Todos' && !$apenas_hoje) ? 'active' : ''; ?>">📅 Todos Agendamentos</a>
                <?php foreach ($status_validos as $s): ?>
                    <a href="?status=<?php echo urlencode($s); ?>" class="<?php echo ($filtro === $s && !$apenas_hoje) ? 'active' : ''; ?>">
                        <?php echo e($s); ?> (<?php echo $contadores[$s]; ?>)
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="../pages/logout.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1>Painel de Controle - Oficina</h1>
        <p>Olá, <?php echo e($_SESSION['usuario_nome'] ?? 'Mecânico'); ?>. Gerencie todos os serviços agendados pelos clientes aqui.</p>

        <div class="admin-resumo">
            <!-- Cada card é clicável e abre a lista já filtrada (igual ao app) -->
            <a href="?status=Pendente"><strong><?php echo $contadores['Pendente']; ?></strong><span>Pendentes</span></a>
            <a href="?status=Confirmado"><strong><?php echo $contadores['Confirmado']; ?></strong><span>Confirmados</span></a>
            <a href="?status=<?php echo urlencode('Em Manutenção'); ?>"><strong><?php echo $contadores['Em Manutenção']; ?></strong><span>Em manutenção</span></a>
            <a href="?periodo=hoje"><strong><?php echo $total_hoje; ?></strong><span>Agendados hoje</span></a>
        </div>

        <h2 style="margin-top: 10px;">
            <?php
            if ($apenas_hoje) echo 'Serviços de hoje (' . date('d/m/Y') . ')';
            else echo $filtro === 'Todos' ? 'Todos os agendamentos' : 'Agendamentos: ' . e($filtro);
            ?>
        </h2>

        <div class="tabela-wrapper">
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>Cliente / Tel</th>
                    <th>Veículo (Placa)</th>
                    <th>Data / Hora</th>
                    <th>Pacote / Serviço</th>
                    <th>Status Atual</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_todos->num_rows > 0): ?>
                    <?php while ($agenda = $resultado_todos->fetch_assoc()): ?>
                        <?php $classe = str_replace(' ', '', $agenda['status']); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($agenda['nome'] ?? 'Cliente removido'); ?></strong><br>
                                <span style="font-size: 12px; color: #777;"><?php echo e($agenda['telefone'] ?? ''); ?></span>
                            </td>
                            <td>
                                <?php echo e(trim(($agenda['marca'] ?? '') . " " . ($agenda['modelo'] ?? 'Veículo removido'))); ?><br>
                                <span style="font-size: 12px; background: #eee; padding: 2px 5px; border-radius: 3px;">
                                    <?php echo e(strtoupper($agenda['placa'] ?? '')); ?>
                                </span>
                            </td>
                            <td>
                                📅 <?php echo date('d/m/Y', strtotime($agenda['data_agendamento'])); ?><br>
                                🕒 <?php echo e(substr($agenda['hora_agendamento'], 0, 5)); ?>h
                            </td>
                            <td style="max-width: 250px; color: #555; font-size: 14px;">
                                <?php echo e($agenda['descricao']); ?>
                            </td>
                            <td colspan="2">
                                <form action="atualizar_status.php" method="POST" class="form-status">
                                    <input type="hidden" name="id_agendamento" value="<?php echo (int)$agenda['id']; ?>">
                                    <input type="hidden" name="filtro" value="<?php echo e($filtro); ?>">
                                    <input type="hidden" name="periodo" value="<?php echo $apenas_hoje ? 'hoje' : ''; ?>">
                                    <select name="status" class="select-status status-<?php echo e($classe); ?>">
                                        <?php foreach ($status_validos as $s): ?>
                                            <option value="<?php echo e($s); ?>" <?php echo $agenda['status'] === $s ? 'selected' : ''; ?>><?php echo e($s); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn-atualizar">Salvar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #777;">Nenhum agendamento encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

</body>
</html>
