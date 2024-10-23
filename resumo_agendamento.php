<?php
session_start();
require 'db_connect.php'; // Inclua a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); // Redireciona para login se não estiver logado
    exit();
}

// Verifica se o ID do agendamento foi passado
if (!isset($_GET['id'])) {
    header("Location: agendamento.php"); // Redireciona para agendamentos se o ID não estiver presente
    exit();
}

$agendamentoId = $_GET['id'];

// Obtém os detalhes do agendamento
$stmt = $pdo->prepare("
    SELECT agendamentos.*, clientes.nome 
    FROM agendamentos 
    JOIN clientes ON agendamentos.usuario_id = clientes.id 
    WHERE agendamentos.id = ?
");
$stmt->execute([$agendamentoId]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o agendamento existe
if (!$agendamento) {
    header("Location: agendamento.php"); // Redireciona se o agendamento não for encontrado
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo do Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Resumo do Agendamento</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detalhes do Agendamento</h5>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($agendamento['nome']); ?></p>
                <p><strong>Data:</strong> <?php echo htmlspecialchars($agendamento['data']); ?></p>
                <p><strong>Hora:</strong> <?php echo htmlspecialchars($agendamento['hora']); ?></p>
                <p><strong>Serviço:</strong> <?php echo htmlspecialchars($agendamento['servico']); ?></p>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="agendamento.php" class="btn btn-primary btn-sm">Voltar para Agendamentos</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
