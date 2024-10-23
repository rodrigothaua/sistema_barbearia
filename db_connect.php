<?php
// Definindo as credenciais de conexão
$host = 'localhost'; // Normalmente 'localhost' ou IP do servidor
$db = 'barber'; // Nome do banco de dados
$user = 'root'; // Usuário do MySQL
$pass = ''; // Senha do MySQL

// Tentativa de conexão com o banco de dados
try {
    // Usando PDO (PHP Data Objects) para conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    // Definindo o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    // Em caso de erro, exibirá a mensagem
    echo "Falha na conexão: " . $e->getMessage();
}
?>
