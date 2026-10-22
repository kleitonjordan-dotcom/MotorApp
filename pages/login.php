<?php
// Inicia a sessão para lembrar do usuário logado
session_start();

// Inclui o arquivo de conexão
include("../php/conexao.php");

// Tipo escolhido no seletor (Cliente / Mecânico). Padrão: cliente
$tipoEscolhido = (($_POST['tipo'] ?? 'cliente') === 'mecanico') ? 'mecanico' : 'cliente';
$erro = '';

if (isset($_POST['entrar'])) {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Busca o usuário pelo e-mail (consulta preparada: evita SQL Injection)
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM cliente WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Aceita senha criptografada (contas do site) e senha em texto (contas criadas pelo app),
    // igual ao php/api_login.php usado pelo aplicativo
    $senhaOk = $usuario && (password_verify($senha, $usuario['senha']) || $usuario['senha'] === $senha);

    if (!$usuario) {
        $erro = 'Este e-mail não está cadastrado!';
    } else if (!$senhaOk) {
        $erro = 'Senha incorreta!';
    } else {
        $tipoConta = $usuario['tipo'] ?: 'cliente';

        if ($tipoConta !== $tipoEscolhido) {
            // Senha certa, mas marcou o tipo errado no seletor
            $certo = ($tipoConta === 'mecanico') ? 'Mecânico' : 'Cliente';
            $erro = "Esta conta é de $certo. Selecione \"$certo\" para entrar.";
        } else {
            // Salva os dados do usuário na Sessão do navegador
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $tipoConta; // 'cliente' ou 'mecanico'

            // Mecânico vai para o painel da oficina; cliente vai para a página inicial
            if ($tipoConta === 'mecanico') {
                header("Location: ../admin/painel.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
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

        <?php if ($erro !== ''): ?>
            <p class="erro-login"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="POST">

            <!-- SELETOR: entrar como Cliente ou como Mecânico -->
            <span class="rotulo-tipo">Entrar como:</span>
            <div class="seletor-tipo">
                <input type="radio" name="tipo" id="tipoCliente" value="cliente" <?php echo $tipoEscolhido === 'cliente' ? 'checked' : ''; ?>>
                <label for="tipoCliente">👤 Cliente</label>

                <input type="radio" name="tipo" id="tipoMecanico" value="mecanico" <?php echo $tipoEscolhido === 'mecanico' ? 'checked' : ''; ?>>
                <label for="tipoMecanico">🔧 Mecânico</label>
            </div>

            <input type="email" name="email" placeholder="Digite seu email"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <input type="password" name="senha" placeholder="Digite sua senha" required>

            <button type="submit" name="entrar">
                Entrar
            </button>

        </form>

        <!-- Só cliente cria conta pelo site (a conta do mecânico é criada pela oficina) -->
        <a href="cadastro.php" id="linkCadastro">
            Criar conta
        </a>

    </div>

    <script>
        // Esconde o "Criar conta" quando a opção Mecânico está marcada
        function atualizarCadastro() {
            var mecanico = document.getElementById('tipoMecanico').checked;
            document.getElementById('linkCadastro').style.display = mecanico ? 'none' : 'block';
        }
        document.getElementById('tipoCliente').addEventListener('change', atualizarCadastro);
        document.getElementById('tipoMecanico').addEventListener('change', atualizarCadastro);
        atualizarCadastro();
    </script>

    <script src="../js/script.js"></script>

</body>

</html>
