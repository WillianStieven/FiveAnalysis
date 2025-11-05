<?php
session_start();

// Verificar se o usuário está logado e tem permissão
if (!isset($_SESSION['email']) || !isset($_SESSION['tipo_usuario'])) {
    header('Location: AdminLogin.php?error=1');
    exit;
}

// Verificar se é admin ou loja afiliada
if ($_SESSION['tipo_usuario'] !== 'admin' && $_SESSION['tipo_usuario'] !== 'loja_afiliada') {
    header('Location: AdminLogin.php?error=2');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto - FiveAnalysis</title>
    
    <!-- Fonte Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- CSS Externo -->
    <link rel="stylesheet" href="../Style/styles.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid #333333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ffffff;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #333333;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0c43b1;
            box-shadow: 0 0 0 2px rgba(12, 67, 177, 0.2);
        }
        
        .btn-cadastrar {
            background: #0c43b1;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-cadastrar:hover {
            background: #0a3a9e;
            transform: translateY(-2px);
        }
        
        .success-message {
            background: #10b981;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #ef4444;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
          <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" style="height: 40px;">
        </div>
        <nav>
            <ul>
                <li><a href="../index.php">Início</a></li>
                <li><a href="Catalogo.php">Catálogo</a></li>
                <li><a href="CadastroProduto.php" class="active">Cadastrar Produto</a></li>
                <li><a href="AdminDashboard.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1><i class="bi bi-plus-circle"></i> Cadastrar Novo Produto</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="bi bi-check-circle"></i> Produto cadastrado com sucesso!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="bi bi-exclamation-triangle"></i> Erro ao cadastrar produto. Tente novamente.
            </div>
        <?php endif; ?>
        
        <form action="../Controller/cadastrar_produto.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome do Produto *</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição *</label>
                <textarea id="descricao" name="descricao" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="preco">Preço (R$) *</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoria *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php
                    include '../Model/conexao.php';
                    $sql = "SELECT id, nome FROM categorias ORDER BY nome";
                    $stmt = $conexao->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="marca_id">Marca *</label>
                <select id="marca_id" name="marca_id" required>
                    <option value="">Selecione uma marca</option>
                    <?php
                    $sql = "SELECT id, nome FROM marcas ORDER BY nome";
                    $stmt = $conexao->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="imagem_url">URL da Imagem</label>
                <input type="url" id="imagem_url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg">
            </div>
            
            <div class="form-group">
                <label for="estoque">Quantidade em Estoque</label>
                <input type="number" id="estoque" name="estoque" min="0" value="0">
            </div>
            
            <div class="form-group">
                <label for="especificacoes">Especificações (JSON)</label>
                <textarea id="especificacoes" name="especificacoes" rows="4" placeholder='{"socket": "LGA1700", "nucleos": 6, "frequencia": "3.7GHz"}'></textarea>
            </div>
            
            <div class="form-group">
                <label for="compatibilidade">Compatibilidade (JSON)</label>
                <textarea id="compatibilidade" name="compatibilidade" rows="4" placeholder='{"socket": "LGA1700", "chipset": ["B660", "H670"]}'></textarea>
            </div>
            
            <button type="submit" class="btn-cadastrar">
                <i class="bi bi-plus-circle"></i> Cadastrar Produto
            </button>
        </form>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="Catalogo.php" style="color: #0c43b1; text-decoration: none;">
                <i class="bi bi-arrow-left"></i> Voltar ao Catálogo
            </a>
        </div>
    </div>
</body>
</html>

