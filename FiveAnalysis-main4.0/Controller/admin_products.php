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

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addProduct($conexao, $loja_afiliada_id);
            break;
        case 'edit':
            editProduct($conexao, $loja_afiliada_id);
            break;
        case 'delete':
            deleteProduct($conexao, $loja_afiliada_id);
            break;
        case 'list':
            listProducts($conexao, $loja_afiliada_id);
            break;
        default:
            header('Location: ../View/AdminDashboard.php?error=4');
            exit;
    }
    
} catch (PDOException $e) {
    error_log('Erro no admin_products.php: ' . $e->getMessage());
    header('Location: ../View/AdminDashboard.php?error=5');
    exit;
}

function addProduct($conexao, $loja_afiliada_id) {
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
        header('Location: ../View/AdminDashboard.php?error=6');
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
        header('Location: ../View/AdminDashboard.php?success=1');
    } else {
        header('Location: ../View/AdminDashboard.php?error=7');
    }
}

function editProduct($conexao, $loja_afiliada_id) {
    $produto_id = intval($_POST['produto_id']);
    
    // Verificar se o produto pertence à loja (se não for admin)
    if ($loja_afiliada_id !== null) {
        $sql_check = "SELECT id FROM produtos WHERE id = :produto_id AND loja_afiliada_id = :loja_afiliada_id";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bindParam(':produto_id', $produto_id);
        $stmt_check->bindParam(':loja_afiliada_id', $loja_afiliada_id);
        $stmt_check->execute();
        
        if (!$stmt_check->fetch()) {
            header('Location: ../View/AdminDashboard.php?error=8');
            exit;
        }
    }
    
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
        header('Location: ../View/AdminDashboard.php?error=6');
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
    
    // Atualizar produto no banco
    $sql = "UPDATE produtos SET 
            nome = :nome, 
            descricao = :descricao, 
            preco = :preco, 
            categoria_id = :categoria_id, 
            marca_id = :marca_id, 
            imagem_url = :imagem_url, 
            estoque = :estoque, 
            especificacoes = :especificacoes, 
            compatibilidade = :compatibilidade,
            data_atualizacao = CURRENT_TIMESTAMP
            WHERE id = :produto_id";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':marca_id', $marca_id);
    $stmt->bindParam(':imagem_url', $imagem_url);
    $stmt->bindParam(':estoque', $estoque);
    $stmt->bindParam(':especificacoes', $especificacoes);
    $stmt->bindParam(':compatibilidade', $compatibilidade);
    $stmt->bindParam(':produto_id', $produto_id);
    
    if ($stmt->execute()) {
        header('Location: ../View/AdminDashboard.php?success=2');
    } else {
        header('Location: ../View/AdminDashboard.php?error=9');
    }
}

function deleteProduct($conexao, $loja_afiliada_id) {
    $produto_id = intval($_POST['produto_id'] ?? $_GET['produto_id']);
    
    if ($produto_id <= 0) {
        header('Location: ../View/AdminDashboard.php?error=10');
        exit;
    }
    
    // Verificar se o produto pertence à loja (se não for admin)
    if ($loja_afiliada_id !== null) {
        $sql_check = "SELECT id FROM produtos WHERE id = :produto_id AND loja_afiliada_id = :loja_afiliada_id";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bindParam(':produto_id', $produto_id);
        $stmt_check->bindParam(':loja_afiliada_id', $loja_afiliada_id);
        $stmt_check->execute();
        
        if (!$stmt_check->fetch()) {
            header('Location: ../View/AdminDashboard.php?error=8');
            exit;
        }
    }
    
    // Verificar se o produto está sendo usado em montagens
    $sql_check_montagem = "SELECT COUNT(*) as count FROM montagem_componentes WHERE produto_id = :produto_id";
    $stmt_check_montagem = $conexao->prepare($sql_check_montagem);
    $stmt_check_montagem->bindParam(':produto_id', $produto_id);
    $stmt_check_montagem->execute();
    $result = $stmt_check_montagem->fetch();
    
    if ($result['count'] > 0) {
        // Se está sendo usado, apenas desativar
        $sql = "UPDATE produtos SET ativo = false, data_atualizacao = CURRENT_TIMESTAMP WHERE id = :produto_id";
        $message = 'Produto desativado com sucesso (está sendo usado em montagens)';
    } else {
        // Se não está sendo usado, pode deletar completamente
        $sql = "DELETE FROM produtos WHERE id = :produto_id";
        $message = 'Produto removido com sucesso';
    }
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':produto_id', $produto_id);
    
    if ($stmt->execute()) {
        header('Location: ../View/AdminDashboard.php?success=3&message=' . urlencode($message));
    } else {
        header('Location: ../View/AdminDashboard.php?error=11');
    }
}

function listProducts($conexao, $loja_afiliada_id) {
    // Construir query baseada no tipo de usuário
    if ($loja_afiliada_id !== null) {
        $sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome, la.nome_loja
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN marcas m ON p.marca_id = m.id
                LEFT JOIN lojas_afiliadas la ON p.loja_afiliada_id = la.id
                WHERE p.loja_afiliada_id = :loja_afiliada_id
                ORDER BY p.data_criacao DESC";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':loja_afiliada_id', $loja_afiliada_id);
    } else {
        $sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome, la.nome_loja
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN marcas m ON p.marca_id = m.id
                LEFT JOIN lojas_afiliadas la ON p.loja_afiliada_id = la.id
                ORDER BY p.data_criacao DESC";
        $stmt = $conexao->prepare($sql);
    }
    
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retornar JSON para AJAX
    header('Content-Type: application/json');
    echo json_encode($produtos);
}
?>

