<?php

$host     = 'localhost';
$usuario  = 'postgres';
$senha    = 'unochapeco';
$banco    = 'FiveAnalysis';

try {

    $conexao = new PDO("pgsql:host=$host;dbname=$banco", $usuario, $senha);

    
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '✅ Conexão com o banco de dados realizada com sucesso!';
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}


?>
