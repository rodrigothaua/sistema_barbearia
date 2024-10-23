<?php
require 'db_connect.php'; // Inclua a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
    
    // Verificar se o e-mail já está cadastrado
    $stmtEmail = $pdo->prepare("SELECT * FROM clientes WHERE email = ?");
    $stmtEmail->execute([$email]);
    if ($stmtEmail->rowCount() > 0) {
        $mensagem = "E-mail já cadastrado.";
    } else {
        $stmt->execute([$nome, $email, $telefone, $senha]);
        echo "Usuário cadastrado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Cadastro de Usuário</h1>

        <?php if (isset($mensagem)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <div class="mt-3">
            <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
