<?php
session_start();
include '../Model/conexao.php';

// Verificar se o usuário está logado e tem permissão
if (!isset($_SESSION['email']) || !isset($_SESSION['tipo_usuario'])) {
    header('Location: ../View/AdminLogin.php?error=1');
    exit;
}

// Verificar se é admin ou loja afiliada
if ($_SESSION['tipo_usuario'] !== 'admin' && $_SESSION['tipo_usuario'] !== 'loja_afiliada') {
    header('Location: ../View/AdminLogin.php?error=2');
    exit;
}

// Obter informações da loja afiliada se aplicável
$loja_afiliada_id = null;
if ($_SESSION['tipo_usuario'] === 'loja_afiliada') {
    $sql = "SELECT la.id FROM lojas_afiliadas la 
            JOIN usuarios u ON la.usuario_id = u.id 
            WHERE u.email = :email AND la.status = 'ativo'";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$loja) {
        header('Location: ../View/AdminLogin.php?error=3');
        exit;
    }
    
    $loja_afiliada_id = $loja['id'];
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/CadastroProduto.php?error=1');
    exit;
}

try {
    // Validar dados obrigatórios
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $categoria_id = intval($_POST['categoria_id']);
    $marca_id = intval($_POST['marca_id']);
    $estoque = intval($_POST['estoque'] ?? 0);
    $imagem_url = trim($_POST['imagem_url'] ?? '');
    
    // Validar campos obrigatórios
    if (empty($nome) || empty($descricao) || $preco <= 0 || $categoria_id <= 0 || $marca_id <= 0) {
        header('Location: ../View/CadastroProduto.php?error=2');
        exit;
    }
    
    // Processar especificações JSON
    $especificacoes = null;
    if (!empty($_POST['especificacoes'])) {
        $espec_data = json_decode($_POST['especificacoes'], true);
        if ($espec_data !== null) {
            $especificacoes = json_encode($espec_data);
        }
    }
    
    // Processar compatibilidade JSON
    $compatibilidade = null;
    if (!empty($_POST['compatibilidade'])) {
        $comp_data = json_decode($_POST['compatibilidade'], true);
        if ($comp_data !== null) {
            $compatibilidade = json_encode($comp_data);
        }
    }
    
    // Inserir produto no banco
    $sql = "INSERT INTO produtos (nome, descricao, preco, categoria_id, marca_id, loja_afiliada_id, imagem_url, estoque, especificacoes, compatibilidade, ativo) 
            VALUES (:nome, :descricao, :preco, :categoria_id, :marca_id, :loja_afiliada_id, :imagem_url, :estoque, :especificacoes, :compatibilidade, true)";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':marca_id', $marca_id);
    $stmt->bindParam(':loja_afiliada_id', $loja_afiliada_id);
    $stmt->bindParam(':imagem_url', $imagem_url);
    $stmt->bindParam(':estoque', $estoque);
    $stmt->bindParam(':especificacoes', $especificacoes);
    $stmt->bindParam(':compatibilidade', $compatibilidade);
    
    if ($stmt->execute()) {
        header('Location: ../View/CadastroProduto.php?success=1');
    } else {
        header('Location: ../View/CadastroProduto.php?error=3');
    }
    
} catch (PDOException $e) {
    error_log('Erro ao cadastrar produto: ' . $e->getMessage());
    header('Location: ../View/CadastroProduto.php?error=4');
}
?>




