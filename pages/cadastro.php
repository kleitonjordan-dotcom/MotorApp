<?php

include("../php/conexao.php");

if(isset($_POST['cadastrar'])){

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    
    // 1. VERIFICAÇÃO DE E-MAIL DUPLICADO
    // Preparamos uma consulta para ver se o email digitado já existe na tabela cliente
    $sql_busca_email = "SELECT id FROM cliente WHERE email = '$email'";
    $resultado_busca = $conn->query($sql_busca_email);

    if($resultado_busca->num_rows > 0) {
        // Se o número de linhas for maior que 0, significa que o email já existe!
        echo "<script>alert('Erro: Este e-mail já está cadastrado no sistema!');</script>";
    } else {
        // 2. SE NÃO EXISTIR, FAZ O CADASTRO NORMALMENTE
        
        // Criptografa a senha de forma segura
        $senha_criptografada = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO cliente(nome, email, telefone, senha)
                VALUES('$nome', '$email', '$telefone', '$senha_criptografada')";

        if($conn->query($sql) === TRUE){
            echo "<script>alert('Cadastro realizado com sucesso!');</script>";
        } else {
            echo "Erro ao cadastrar: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro - MotorApp</title>

    <link rel="stylesheet" href="../css/cadastro.css">
</head>

<body>

    <div class="container">

        <h1>Criar Conta</h1>

        <form method="POST">

            <input type="text"
                   name="nome"
                   placeholder="Digite seu nome"
                   required>

            <input type="email"
                   name="email"
                   placeholder="Digite seu email"
                   required>

            <input type="text"
                   name="telefone"
                   placeholder="Digite seu telefone"
                   required>

            <input type="password"
                   name="senha"
                   placeholder="Digite sua senha"
                   required>

            <button type="submit" name="cadastrar">
                Cadastrar
            </button>

        </form>

        <a href="login.php">
            Já tenho conta
        </a>

    </div>

</body>

</html>