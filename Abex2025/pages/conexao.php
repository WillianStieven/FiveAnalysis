<?php


$host = 'localhost';
$usuario = 'postgres';
$senha = '123';
$banco = 'FiveAnalysis';

try {
    $conexao = new PDO("pgsql:host=$host;dbname=$banco", $usuario, $senha);
    echo 'Show de Bola';
} catch (PDOException $e) {
    die('Falha na conexão: ' . $e->getMessage());
}

?>