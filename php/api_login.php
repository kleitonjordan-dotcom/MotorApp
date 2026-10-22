<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "motorapp";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erro na conexão com o banco de dados"]);
    exit();
}

// Recebe os parâmetros limpando espaços vazios
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senhaInput = isset($_POST['senha']) ? trim($_POST['senha']) : '';

if (empty($email) || empty($senhaInput)) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
    exit();
}

$conn->set_charset("utf8mb4");

// Busca o usuário pelo e-mail
$sql = "SELECT id, nome, senha, tipo FROM cliente WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Aceita os dois jeitos de senha que existem no banco:
//  - texto puro (contas criadas pelo app)
//  - criptografada com password_hash (contas criadas pelo site, ex.: mecânicos)
$senhaOk = false;
if ($cliente) {
    $senhaOk = ($cliente['senha'] === $senhaInput) || password_verify($senhaInput, $cliente['senha']);
}

// Tipo da conta no banco e tipo escolhido no seletor da tela de login
$tipoConta = ($cliente && $cliente['tipo']) ? $cliente['tipo'] : 'cliente';
$tipoEscolhido = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';

if ($senhaOk && in_array($tipoEscolhido, ['cliente', 'mecanico'], true) && $tipoEscolhido !== $tipoConta) {
    // Senha certa, mas o usuário marcou o tipo errado no seletor
    $certo = ($tipoConta === 'mecanico') ? 'Mecânico' : 'Cliente';
    echo json_encode([
        "success" => false,
        "message" => "Esta conta é de $certo. Selecione \"$certo\" na tela de login."
    ], JSON_UNESCAPED_UNICODE);
} else if ($senhaOk) {
    echo json_encode([
        "success" => true,
        "message" => "Login realizado com sucesso!",
        "nome" => $cliente['nome'],
        "id" => $cliente['id'],
        "tipo" => $cliente['tipo'] ?: 'cliente' // 'cliente' ou 'mecanico'
    ]);
} else {
    echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos."]);
}

$stmt->close();
$conn->close();
?>