<?php

$host     = 'localhost';
$usuario  = 'postgres';
$senha    = 'unochapeco';
$banco    = 'FiveAnalysis';

try {

    $conexao = new PDO("pgsql:host=$host;dbname=$banco", $usuario, $senha);

    
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Remover echo de debug em produção
    // echo '✅ Conexão com o banco de dados realizada com sucesso!';
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}


?>
