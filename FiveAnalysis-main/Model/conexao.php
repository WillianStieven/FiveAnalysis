<?php

// Configurações do banco de dados usando variáveis de ambiente
$host     = $_ENV['DB_HOST'] ?? 'postgres';
$port     = $_ENV['DB_PORT'] ?? '5432';
$usuario  = $_ENV['DB_USER'] ?? 'admin';
$senha    = $_ENV['DB_PASSWORD'] ?? 'admin123';
$banco    = $_ENV['DB_NAME'] ?? 'FiveAnalysis';

try {
    // Conectar ao banco PostgreSQL
    $conexao = new PDO("pgsql:host=$host;port=$port;dbname=$banco", $usuario, $senha);
    
    // Configurar modo de erro
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar charset
    $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Log de sucesso apenas em desenvolvimento
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
        error_log('✅ Conexão com o banco de dados realizada com sucesso!');
    }
    
} catch (PDOException $e) {
    // Log do erro
    error_log('Erro de conexão com banco de dados: ' . $e->getMessage());
    
    // Em produção, mostrar erro genérico
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
        die('Erro interno do servidor. Tente novamente mais tarde.');
    } else {
        die('Erro de conexão: ' . $e->getMessage());
    }
}

?>
