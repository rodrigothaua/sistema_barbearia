<?php
session_start();
require 'db_connect.php'; // Inclua a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: resumo_agendamento.php"); // Redireciona para login se não estiver logado
    exit();
}

// Obtém as informações do usuário logado
$stmt = $pdo->prepare("SELECT nome, email, telefone FROM clientes WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca os agendamentos do usuário, incluindo o nome do cliente
$stmt = $pdo->prepare("
    SELECT agendamentos.*, clientes.nome 
    FROM agendamentos 
    JOIN clientes ON agendamentos.usuario_id = clientes.id 
    WHERE agendamentos.usuario_id = ?
");
$stmt->execute([$_SESSION['usuario_id']]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processa o envio do formulário de agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $servico = $_POST['servico'];

    // Verifica se já existe um agendamento para a mesma data e hora
    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE usuario_id = ? AND data = ? AND hora = ?");
    $stmt->execute([$_SESSION['usuario_id'], $data, $hora]);

    if ($stmt->rowCount() > 0) {
        $mensagem = "Já existe um agendamento para esse horário.";
        $tipoMensagem = "danger"; // Erro
    } else {
        // Insere o novo agendamento no banco de dados
        $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, data, hora, servico) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['usuario_id'], $data, $hora, $servico])) {
            // Redireciona para a página de resumo
            $agendamentoId = $pdo->lastInsertId(); // Obtém o ID do agendamento inserido
            header("Location: resumo_agendamento.php?id=$agendamentoId");
            exit();
        } else {
            $mensagem = "Erro ao realizar o agendamento. Tente novamente.";
            $tipoMensagem = "danger"; // Erro
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Seus Agendamentos</h1>

        <?php if (count($agendamentos) === 0): ?>
            <div class="alert alert-warning text-center" role="alert">
                Sem agendamentos. Você pode agendar um novo serviço!
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agendarModal">
                    Agendar Serviço
                </button>
            </div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Serviço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agendamento['nome']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['data']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['hora']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['servico']); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm">Excluir</button>
                                <button class="btn btn-warning btn-sm">Alterar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agendarModal">
            Agendar Serviço
        </button>
        <a class="btn btn-danger" href="logout.php">Sair</a>
    </div>

    <!-- Modal para Agendamento -->
    <div class="modal fade" id="agendarModal" tabindex="-1" aria-labelledby="agendarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agendarModalLabel">Agendar Serviço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgendamento" method="POST" action="agendamento.php">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="<?php echo htmlspecialchars($usuario['telefone']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" name="data" id="data" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora" class="form-label">Hora</label>
                            <select name="hora" id="hora" class="form-select" required>
                                <option value="">Selecione um horário</option>
                                <?php
                                // Gera horários disponíveis das 08:00 até 19:00
                                for ($h = 8; $h <= 19; $h++) {
                                    $horaFormatada = sprintf("%02d:00", $h);
                                    echo "<option value='$horaFormatada'>$horaFormatada</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="servico" class="form-label">Serviço</label>
                            <input type="text" name="servico" id="servico" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agendar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
