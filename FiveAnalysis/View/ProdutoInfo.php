<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiveAnalysis - Placa Mãe ASRock B450M-HDV R4.0</title>
    
    <!-- Fonte Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS Externo -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/components/button.css">
    <link rel="stylesheet" href="css/components/card.css">
    
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
        <div class="logo">Five<span>Analysis.</span></div>
        <nav>
            <ul>
                <li><a href="index.html">Início</a></li>
                <li><a href="sobre.html">Sobre</a></li>
                <li><a href="Shop_page.html" class="active">Montar computador</a></li>
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
            <img src="https://via.placeholder.com/400x300/333/fff?text=ASRock+B450M-HDV+R4.0" alt="Placa Mãe ASRock B450M-HDV R4.0">
            <svg class="favorite-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
        
        <div class="product-info">
            <div class="seller-info">
                <p>Vendido e entregue por: <span class="kabum">KaBuM</span></p>
            </div>
            
            <div class="price">R$ 599,99</div>
            <div class="pix-discount">À vista no PIX com 10% OFF</div>
            
            <div class="action-buttons">
                <button class="btn-primary">VER NA LOJA</button>
                <span class="or-text">OU</span>
                <div class="cart-icon">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="product-description">
                <h3>Descrição do componente:</h3>
                <p>Placa Mãe ASRock B450M-HDV R4.0, AMD AM4, Micro ATX, DDR4</p>
            </div>
            
            <div class="ratings">
                <div class="rating-item">
                    <div class="rating-label">NOTA GERAL DE COMPONENTE</div>
                    <div class="rating-number">7</div>
                </div>
                <div class="rating-item">
                    <div class="rating-label">NOTA GERAL DE DESEMPENHO</div>
                    <div class="rating-number">8</div>
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