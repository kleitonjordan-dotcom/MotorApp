<?php
// Inicia a sessão para lembrar do usuário logado
session_start();

// Inclui o arquivo de conexão
include("../php/conexao.php");

if (isset($_POST['entrar'])) {
    
    // Captura os dados digitados e limpa contra erros básicos
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    // Busca o cliente pelo e-mail digitado
    $sql_busca = "SELECT * FROM cliente WHERE email = '$email'";
    $resultado = $conn->query($sql_busca);

    // Se encontrou o e-mail no banco de dados...
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verifica se a senha digitada bate com a senha criptografada do banco
        if (password_verify($senha, $usuario['senha'])) {
            
            // Salva os dados do cliente na Sessão do navegador
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo']; // <-- Linha nova: Guarda se é cliente ou mecanico

            // Redireciona o usuário dependendo do nível de acesso (tipo)
            if ($_SESSION['usuario_tipo'] == 'mecanico') {
                // Se for mecânico, vai para o painel dentro da pasta admin
                header("Location: ../admin/painel.php");
            } else {
                // Se for cliente comum, vai para a página inicial normal
                header("Location: index.php");
            }
            exit();

            /*fim aqui */
        } else {
            // Senha incorreta
            echo "<script>alert('Erro: Senha incorreta!');</script>";
        }
    } else {
        // E-mail não encontrado
        echo "<script>alert('Erro: Este e-mail não está cadastrado!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MotorApp - Login</title>

    <link rel="stylesheet" href="../css/pageLogin.css">
</head>

<body>

    <div class="container">

        <h1>MotorApp</h1>

        <form method="POST">

            <input type="email" name="email" placeholder="Digite seu email" required>

            <input type="password" name="senha" placeholder="Digite sua senha" required>

            <button type="submit" name="entrar">
                Entrar
            </button>

        </form>

        <a href="cadastro.php">
            Criar conta
        </a>

    </div>

    <script src="../js/script.js"></script>

</body>

</html>