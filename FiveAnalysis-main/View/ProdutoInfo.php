<?php
include '../Model/conexao.php';

// Buscar produto por ID
$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($produto_id > 0) {
    $sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN marcas m ON p.marca_id = m.id 
            WHERE p.id = :id AND p.ativo = true";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $produto_id);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        header('Location: Catalogo.php');
        exit;
    }
} else {
    header('Location: Catalogo.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiveAnalysis - <?= htmlspecialchars($produto['nome']) ?></title>
    
    <!-- Fonte Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS Externo -->
    <link rel="stylesheet" href="../Style/styles.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        /* Estilos específicos da página de produto */
        .main-content {
            display: flex;
            padding: 40px;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-image {
            flex: 1;
        }

        .product-image img {
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
        }

        .product-info {
            flex: 1;
        }

        .seller-info {
            margin-bottom: 20px;
        }

        .seller-info p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .seller-info .kabum {
            color: var(--primary-color);
            font-weight: bold;
        }

        .favorite-icon {
            width: 24px;
            height: 24px;
            margin-top: 20px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-primary);
        }

        .favorite-icon:hover {
            color: #ff6b6b;
        }

        .product-description {
            margin-top: 20px;
        }

        .product-description h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .product-description p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .similar-components {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .title-line {
            width: 100px;
            height: 2px;
            background-color: var(--primary-color);
            margin-right: 20px;
        }

        .section-title h2 {
            font-size: 20px;
            color: var(--text-primary);
        }

        .components-grid {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }

        .decorative-line {
            position: absolute;
            bottom: 200px;
            right: 50px;
            width: 200px;
            height: 2px;
            background: linear-gradient(45deg, var(--primary-color), transparent);
            transform: rotate(-15deg);
            opacity: 0.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
          <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" style="height: 40px;">
        </div>
        <nav>
            <ul>
                <li><a href="../View/PaginaInicial.php">Início</a></li>
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="Montagem.php" class="active">Montar computador</a></li>
            </ul>
        </nav>
        <div class="icons">
            <i class="bi bi-search"></i>
            <i class="bi bi-cart2"></i>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="product-image">
            <img src="<?= $produto['imagem_url'] ?: '../Assets/img/a1.jpg' ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
            <svg class="favorite-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
        
        <div class="product-info">
            <div class="seller-info">
                <p>Vendido e entregue por: <span class="kabum">FiveAnalysis</span></p>
                <p><strong><?= htmlspecialchars($produto['categoria_nome']) ?> - <?= htmlspecialchars($produto['marca_nome']) ?></strong></p>
            </div>
            
            <div class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
            <div class="pix-discount">À vista no PIX com 5% OFF</div>
            
            <div class="action-buttons">
                <button class="btn-primary" onclick="addToCart(<?= $produto['id'] ?>)">ADICIONAR AO CARRINHO</button>
                <span class="or-text">OU</span>
                <div class="cart-icon">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="product-description">
                <h3>Descrição do componente:</h3>
                <p><?= htmlspecialchars($produto['descricao']) ?></p>
                
                <?php if ($produto['especificacoes']): ?>
                    <?php $especs = json_decode($produto['especificacoes'], true); ?>
                    <?php if ($especs): ?>
                        <h4>Especificações:</h4>
                        <ul>
                            <?php foreach ($especs as $key => $value): ?>
                                <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="ratings">
                <div class="rating-item">
                    <div class="rating-label">ESTOQUE DISPONÍVEL</div>
                    <div class="rating-number"><?= $produto['estoque'] ?></div>
                </div>
                <div class="rating-item">
                    <div class="rating-label">CATEGORIA</div>
                    <div class="rating-number"><?= htmlspecialchars($produto['categoria_nome']) ?></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Decorative Line -->
    <div class="decorative-line"></div>

    <!-- Similar Components -->
    <section class="similar-components">
        <div class="section-title">
            <div class="title-line"></div>
            <h2>COMPONENTES SIMILARES</h2>
        </div>
        
        <div class="components-grid">
            <div class="component-card">
                <img src="https://via.placeholder.com/200x120/333/fff?text=ASUS+Prime" alt="ASUS Prime">
                <p>Placa Mãe Asus Prime</p>
            </div>
            <div class="component-card">
                <img src="https://via.placeholder.com/200x120/333/fff?text=ASRock+B450M-HDV" alt="ASRock B450M-HDV R4.0">
                <p>Placa Mãe ASRock B450M-HDV R4.0</p>
            </div>
            <div class="component-card">
                <img src="https://via.placeholder.com/200x120/333/fff?text=ASRock+Steel+Legend" alt="ASRock B450M Steel Legend">
                <p>Placa Mãe ASRock B450M Steel Legend</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="links">
                <h4>HiperLinks</h4>
                <ul>
                    <li><a href="#">Hardware</a></li>
                    <li><a href="#">Documentação</a></li>
                    <li><a href="sobre.html">Sobre</a></li>
                    <li><a href="#">Guia de Montagem</a></li>
                </ul>
            </div>
            <div class="contato">
                <h4>Contato</h4>
                <p>email: grigolodev@gmail.com</p>
            </div>
            <div class="social">
                <a href="#"><i class="bi bi-linkedin"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
            </div>
        </div>
        <p class="copyright">Todos os Direitos Reservados a FiveAnalysis.</p>
    </footer>

    <!-- JavaScript -->
    <script src="js/main.js"></script>
</body>
</html>