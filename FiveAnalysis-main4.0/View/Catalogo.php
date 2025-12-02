<?php
include '../Model/conexao.php';

// Buscar produtos do banco
$sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        LEFT JOIN marcas m ON p.marca_id = m.id 
        WHERE p.ativo = true 
        ORDER BY p.data_criacao DESC";

$stmt = $conexao->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias para filtro
$sql_categorias = "SELECT id, nome FROM categorias ORDER BY nome";
$stmt_categorias = $conexao->prepare($sql_categorias);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos - FiveAnalysis</title>
    
    <!-- Fonte Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- CSS Externo -->
    <link rel="stylesheet" href="../Style/styles.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        .catalog-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .filters {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #333333;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #ffffff;
            font-weight: 600;
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #333333;
            border-radius: 8px;
            background: #1a1a1a;
            color: #ffffff;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #333333;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #0c43b1;
            box-shadow: 0 10px 30px rgba(12, 67, 177, 0.2);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 15px;
            background: white;
        }
        
        .product-title {
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }
        
        .product-category {
            font-size: 12px;
            color: #0c43b1;
            margin-bottom: 5px;
        }
        
        .product-brand {
            font-size: 12px;
            color: #cccccc;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 15px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-primary {
            background: #0c43b1;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }
        
        .btn-primary:hover {
            background: #0a3a9e;
        }
        
        .btn-secondary {
            background: transparent;
            color: #0c43b1;
            border: 1px solid #0c43b1;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #0c43b1;
            color: #ffffff;
        }
        
        .add-product-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #0c43b1;
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(12, 67, 177, 0.3);
            transition: all 0.3s ease;
        }
        
        .add-product-btn:hover {
            background: #0a3a9e;
            transform: scale(1.1);
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
                <li><a href="../View/PaginaInicial.php">Início</a></li>
                <li><a href="Catalogo.php" class="active">Catálogo</a></li>
                <li><a href="Montagem.php">Montar computador</a></li>
                <li><a href="AdminDashboard.php">Admin</a></li>
            </ul>
        </nav>
        <div class="icons">
            <i class="bi bi-search"></i>
            <a href="Carrinho.php" class="cart-link" style="color: var(--text-primary); text-decoration: none; position: relative;">
                <i class="bi bi-cart2"></i>
                <span class="cart-badge" id="cartBadge" style="display: none; position: absolute; top: -8px; right: -8px; background: var(--primary-color); color: white; border-radius: 50%; width: 20px; height: 20px; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">0</span>
            </a>
        </div>
    </header>

    <div class="catalog-container">
        <h1><i class="bi bi-grid"></i> Catálogo de Produtos</h1>
        
        <div class="filters">
            <h3><i class="bi bi-funnel"></i> Filtros</h3>
            <div class="filter-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" onchange="filterProducts()">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="products-grid" id="productsGrid">
            <?php foreach ($produtos as $produto): ?>
                <div class="product-card" data-category="<?= $produto['categoria_id'] ?>">
                    <img src="<?= $produto['imagem_url'] ?: '../Assets/img/a1.jpg' ?>" 
                         alt="<?= htmlspecialchars($produto['nome']) ?>" 
                         class="product-image">
                    
                    <div class="product-category"><?= htmlspecialchars($produto['categoria_nome']) ?></div>
                    <div class="product-brand"><?= htmlspecialchars($produto['marca_nome']) ?></div>
                    <div class="product-title"><?= htmlspecialchars($produto['nome']) ?></div>
                    <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                    
                    <div class="product-actions">
                        <button class="btn-primary" onclick="viewProduct(<?= $produto['id'] ?>)">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <button class="btn-secondary" onclick="addToCart(<?= $produto['id'] ?>)">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($produtos)): ?>
            <div style="text-align: center; padding: 50px; color: #cccccc;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 20px;"></i>
                <h3>Nenhum produto encontrado</h3>
                <p>Adicione produtos ao catálogo para começar.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="CadastroProduto.php" class="add-product-btn" title="Adicionar Produto">
        <i class="bi bi-plus"></i>
    </a>

    <script src="../Assets/js/main.js"></script>
    <script src="../Assets/js/carrinho.js"></script>
    <script>
        // Atualizar badge do carrinho ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof atualizarBadgeCarrinho === 'function') {
                atualizarBadgeCarrinho();
            }
        });
    </script>
    <script>
        function filterProducts() {
            const categoriaId = document.getElementById('categoria').value;
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                if (!categoriaId || card.dataset.category === categoriaId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function viewProduct(id) {
            window.location.href = `ProdutoInfo.php?id=${id}`;
        }
        
        function addToCart(id) {
            // Usar a função do carrinho.js se disponível
            if (typeof window.addToCart === 'function') {
                // Buscar dados do produto na página
                const produtoCard = document.querySelector(`[onclick*="addToCart(${id})"]`)?.closest('.product-card');
                if (produtoCard) {
                    const nome = produtoCard.querySelector('.product-title')?.textContent || 'Produto';
                    const precoText = produtoCard.querySelector('.product-price')?.textContent || 'R$ 0,00';
                    const preco = parseFloat(precoText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                    const imagem = produtoCard.querySelector('.product-image')?.src || '../Assets/img/a1.jpg';
                    const categoria = produtoCard.querySelector('.product-category')?.textContent || '';
                    const marca = produtoCard.querySelector('.product-brand')?.textContent || '';
                    
                    window.addToCart(id, nome, preco, imagem, categoria, marca);
                } else {
                    window.addToCart(id);
                }
            } else {
                alert('Carregando sistema de carrinho...');
            }
        }
    </script>
</body>
</html>