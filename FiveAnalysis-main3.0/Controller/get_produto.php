<?php
header('Content-Type: application/json');

include '../Model/conexao.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do produto não fornecido'
    ]);
    exit;
}

$produto_id = intval($_GET['id']);

if ($produto_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do produto inválido'
    ]);
    exit;
}

try {
    // Buscar produto com categoria e marca
    $sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN marcas m ON p.marca_id = m.id 
            WHERE p.id = :id AND p.ativo = true";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($produto) {
        echo json_encode([
            'success' => true,
            'produto' => [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => floatval($produto['preco']),
                'imagem_url' => $produto['imagem_url'] ?: '../Assets/img/a1.jpg',
                'categoria_nome' => $produto['categoria_nome'] ?: '',
                'marca_nome' => $produto['marca_nome'] ?: '',
                'descricao' => $produto['descricao']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Produto não encontrado'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar produto: ' . $e->getMessage()
    ]);
}
?>

