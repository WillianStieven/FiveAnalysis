<?php
/**
 * Script para criar uma loja afiliada de exemplo
 * Execute este arquivo uma vez para criar uma loja afiliada de teste
 * 
 * IMPORTANTE: Remova ou proteja este arquivo em produção!
 */

session_start();
include '../Model/conexao.php';

// Apenas permitir em ambiente de desenvolvimento
// Remova ou ajuste esta verificação em produção
if ($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1') {
    die('Acesso negado. Este script só pode ser executado em ambiente de desenvolvimento.');
}

try {
    // Verificar se a tabela existe, se não, criar
    $sql = "CREATE TABLE IF NOT EXISTS lojas_afiliadas (
        id SERIAL PRIMARY KEY,
        nome_loja VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        senha_hash VARCHAR(255) NOT NULL,
        cnpj VARCHAR(18),
        telefone VARCHAR(20),
        endereco TEXT,
        ativo BOOLEAN DEFAULT true,
        data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ultimo_acesso TIMESTAMP
    )";
    $conexao->exec($sql);
    
    // Criar índice
    try {
        $conexao->exec("CREATE INDEX IF NOT EXISTS idx_lojas_email ON lojas_afiliadas(email)");
    } catch (PDOException $e) {
        // Índice pode já existir
    }
    
    // Dados da loja de exemplo
    $nome_loja = 'Loja Afiliada Exemplo';
    $email = 'loja@fiveanalysis.com';
    $senha = 'admin123'; // Senha em texto plano
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $cnpj = '12.345.678/0001-90';
    $telefone = '(49) 99999-9999';
    $endereco = 'Endereço de exemplo, 123';
    
    // Verificar se já existe
    $check = $conexao->prepare("SELECT id FROM lojas_afiliadas WHERE email = :email");
    $check->bindParam(':email', $email);
    $check->execute();
    
    if ($check->fetch()) {
        echo "Loja afiliada com email $email já existe!<br>";
        echo "Email: $email<br>";
        echo "Senha: $senha<br>";
    } else {
        // Inserir loja afiliada
        $sql = "INSERT INTO lojas_afiliadas (nome_loja, email, senha_hash, cnpj, telefone, endereco, ativo) 
                VALUES (:nome_loja, :email, :senha_hash, :cnpj, :telefone, :endereco, true)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nome_loja', $nome_loja);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha_hash', $senha_hash);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        
        if ($stmt->execute()) {
            echo "✅ Loja afiliada criada com sucesso!<br><br>";
            echo "<strong>Credenciais de acesso:</strong><br>";
            echo "Email: <strong>$email</strong><br>";
            echo "Senha: <strong>$senha</strong><br><br>";
            echo "<a href='../View/AdminLogin.php'>Ir para o login administrativo</a>";
        } else {
            echo "❌ Erro ao criar loja afiliada.";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

