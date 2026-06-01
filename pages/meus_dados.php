<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT * FROM cliente WHERE id = '$id_usuario'";
$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

if (isset($_POST['atualizar'])) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $sql_update = "UPDATE cliente SET nome='$nome', telefone='$telefone', email='$email' WHERE id='$id_usuario'";

    if ($conn->query($sql_update) === TRUE) {
        $_SESSION['usuario_nome'] = $nome;
        echo "<script>alert('Dados updated com sucesso!'); window.location.href='meus_dados.php';</script>";
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorApp - Meus Dados</title>
    
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/meus_dados.css">
</head>
<body>

    <div class="sidebar">
        <div class="top-sidebar">
            <h2>MotorApp</h2>
            <div class="menu-items">
                <a href="index.php">🏠 Início</a>
                <a href="meus_dados.php" class="active">👤 Meus Dados</a>
                <a href="meus_veiculos.php">🚗 Meus Veículos</a>
                <a href="agendamentos.php">📅 Agendamentos</a>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1 style="margin-bottom: 20px; color: #333;">Meus Dados Pessoais</h1>
        
        <div class="form-box">
            <form method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="<?php echo $usuario['telefone']; ?>" required>
                </div>
                <button type="submit" name="atualizar" class="btn-salvar">Salvar Alterações</button>
            </form>
        </div>
    </div>

</body>
</html>