<?php
// Configurações do Banco de Dados
$host    = '127.0.0.1';
$banco   = 'hotel_db';
$usuario = 'root';        // Usuário padrão do XAMPP/WAMP
$senha   = 'root';            // Senha padrão do XAMPP costuma ser vazia

// DSN (Data Source Name): endereço de onde o banco está rodando
$dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";

// Configurações adicionais para segurança e facilidade
$opcoes = [
    // Lança exceções caso ocorra algum erro de SQL
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Retorna os dados como array associativo por padrão
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
    // Descomente a linha abaixo apenas para testar no início:
    //echo "Conexão realizada com sucesso!";
} catch (PDOException $e) {
    // Caso ocorra erro, exibe uma mensagem amigável e interrompe o script
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>