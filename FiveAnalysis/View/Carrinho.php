<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Carrinho de Compras - FiveAnalysis</title>

  <!-- Fonte Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- CSS Externo -->
  <link rel="stylesheet" href="../Style/styles.css">
  <link rel="stylesheet" href="../Style/Global.css">
  <link rel="stylesheet" href="../Style/carrinho.css">
</head>
<body>
  <header>
    <div class="logo">Five<span>Analysis.</span></div>
    <nav>
      <ul>
        <li><a href="PaginaInicial.php">Início</a></li>
        <li><a href="Sobre.php">Sobre</a></li>
        <li><a href="Montagem.php">Montar computador</a></li>
      </ul>
    </nav>
    <div class="icons">
      <i class="bi bi-search"></i>
      <a href="Carrinho.php" class="cart-link">
        <i class="bi bi-cart2"></i>
        <span class="cart-badge" id="cartBadge">0</span>
      </a>
      <i class="bi bi-person" id="userIcon"></i>
    </div>
  </header>

  <main class="cart-container">
    <div class="cart-header">
      <h1><i class="bi bi-cart2"></i> Carrinho de Compras</h1>
      <p class="cart-subtitle">Revise seus produtos e finalize sua compra</p>
    </div>

    <div class="cart-content">
      <!-- Lista de Produtos -->
      <div class="cart-items-section">
        <div class="section-header">
          <h2>Produtos no Carrinho</h2>
          <span class="items-count" id="itemsCount">0 itens</span>
        </div>
        
        <div class="cart-items" id="cartItems">
          <!-- Itens serão inseridos aqui via JavaScript -->
          <div class="empty-cart" id="emptyCart">
            <i class="bi bi-cart-x"></i>
            <h3>Seu carrinho está vazio</h3>
            <p>Adicione produtos ao carrinho para começar</p>
            <a href="Montagem.php" class="btn btn-primary">Montar Computador</a>
          </div>
        </div>
      </div>

      <!-- Resumo do Orçamento -->
      <aside class="cart-summary">
        <div class="summary-card">
          <h3>Resumo do Orçamento</h3>
          
          <div class="summary-details">
            <div class="summary-row">
              <span>Subtotal</span>
              <span id="subtotal">R$ 0,00</span>
            </div>
            <div class="summary-row">
              <span>Frete</span>
              <span id="frete">R$ 0,00</span>
            </div>
            <div class="summary-row discount">
              <span>Desconto</span>
              <span id="desconto">- R$ 0,00</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total">
              <span>Total</span>
              <span id="total">R$ 0,00</span>
            </div>
          </div>

          <div class="summary-actions">
            <button class="btn btn-primary btn-lg" id="finalizeOrder" disabled>
              <i class="bi bi-check-circle"></i> Finalizar Orçamento
            </button>
            <a href="Montagem.php" class="btn btn-secondary">
              <i class="bi bi-plus-circle"></i> Continuar Comprando
            </a>
          </div>

          <div class="payment-info">
            <p><i class="bi bi-shield-check"></i> Compra 100% segura</p>
            <p><i class="bi bi-truck"></i> Frete grátis para compras acima de R$ 1.000,00</p>
          </div>
        </div>

        <!-- Informações de Contato -->
        <div class="contact-card">
          <h4><i class="bi bi-headset"></i> Precisa de Ajuda?</h4>
          <p>Entre em contato conosco</p>
          <a href="mailto:FiveAnalysis@gmail.com" class="contact-link">
            <i class="bi bi-envelope"></i> FiveAnalysis@gmail.com
          </a>
          <a href="tel:+5549998989898" class="contact-link">
            <i class="bi bi-telephone"></i> (49) 99898-9898
          </a>
        </div>
      </aside>
    </div>
  </main>

  <footer>
    <div class="footer-content">
      <div class="links">
        <h4>HiperLinks</h4>
        <ul>
          <li><a href="#">Hardware</a></li>
          <li><a href="#">Documentação</a></li>
          <li><a href="Sobre.php">Sobre</a></li>
          <li><a href="#">Guia de Montagem</a></li>
        </ul>
      </div>
      <div class="contato">
        <h4>Contato</h4>
        <ul>
          <li><a href="#">Email: FiveAnalysis@gmail.com</a></li>
          <li><a href="#">Telefone: (49) 99898-9898</a></li>
          <li><a href="#">Efapi, 7199 - Aventureiro, Chapecó - SC - 89219-731</a></li>
        </ul>
      </div>
      <div class="social">
        <a href="#"><i class="bi bi-linkedin"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-facebook"></i></a>
      </div>
    </div>
    <p class="copyright">Todos os Direitos Reservados a FiveAnalysis. CNPJ: 09.321.222/0001-00</p>
  </footer>

  <!-- JavaScript -->
  <script src="../Assets/js/main.js"></script>
  <script src="../Assets/js/carrinho.js"></script>
</body>
</html>

