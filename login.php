<?php
session_start(); // Iniciar a sessão

// Incluir o arquivo de conexão com o banco de dados
require 'db_connect.php';

// Variável para armazenar mensagens de erro
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login']; // Pode ser e-mail ou nome de usuário
    $senha = $_POST['senha'];

    // Verifica se os campos foram preenchidos
    if (empty($login) || empty($senha)) {
        $msg = "Preencha todos os campos!";
    } else {
        // Tentar encontrar o usuário no banco de dados (verificar por e-mail ou nome de usuário)
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR nome_usuario = ?");
            $stmt->execute([$login, $login]); // O valor do login é usado para buscar por e-mail ou nome de usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar se o usuário foi encontrado e se a senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Definir a sessão do usuário
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo'] = $usuario['tipo'];

                // Redirecionar para o dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $msg = "Login ou senha incorretos!";
            }
        } catch (PDOException $e) {
            $msg = "Erro ao fazer login: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Barbearia</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h1 class="text-center mb-4">Login</h1>
            
                <!-- Exibir mensagem de erro se houver -->
                <?php if (!empty($msg)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário de login -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="login" class="form-label">E-mail ou Nome de Usuário</label>
                        <input type="text" name="login" id="login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
