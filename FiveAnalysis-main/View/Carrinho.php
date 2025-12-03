<?php

  session_start();
  $usuariologado = isset($_SESSION['email']) ? $_SESSION['email'] : null;

?>

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
    <div class="logo">
      <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" style="height: 40px;">
    </div>
    <nav>
      <ul>
        <li><a href="PaginaInicial.php">Início</a></li>
        <li><a href="Sobre.php">Sobre</a></li>
        <li><a href="Catalogo.php">Catálogo</a></li>
        <li><a href="Montagem.php">Montar computador</a></li>
      </ul>
    </nav>
    <div class="icons">
      <i class="bi bi-search"></i>
      <a href="Carrinho.php" class="cart-link">
        <i class="bi bi-cart2"></i>
        <span class="cart-badge" id="cartBadge">0</span>
      </a>
      <i class="bi bi-person" id="userIcon" onclick="toggleUserMenu()"></i>
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
        <div id="cartItemsList" class="cart-items-list">
          <!-- Itens serão inseridos aqui via JavaScript -->
          <div class="empty-cart" id="emptyCart">
            <i class="bi bi-cart-x"></i>
            <h2>Seu carrinho está vazio</h2>
            <p>Adicione produtos ao carrinho para começar suas compras</p>
            <a href="PaginaInicial.php" class="btn btn-primary">Continuar Comprando</a>
          </div>
        </div>
      </div>

      <!-- Resumo do Orçamento -->
      <aside class="cart-summary" id="cartSummary" style="display: none;">
        <div class="summary-header">
          <h2>Resumo do Orçamento</h2>
        </div>
        
        <div class="summary-content">
          <div class="summary-row">
            <span>Subtotal</span>
            <span id="subtotal">R$ 0,00</span>
          </div>
          <div class="summary-row">
            <span>Desconto</span>
            <span class="discount" id="discount">- R$ 0,00</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row total-row">
            <span>Total</span>
            <span class="total-price" id="totalPrice">R$ 0,00</span>
          </div>
        </div>

        <div class="summary-footer">
          <button class="btn btn-primary btn-block" id="finalizeOrder" onclick="finalizarPedido()">
            <i class="bi bi-check-circle"></i> Finalizar Pedido
          </button>
          <a href="PaginaInicial.php" class="btn btn-secondary btn-block">
            <i class="bi bi-arrow-left"></i> Continuar Comprando
          </a>
        </div>

        <div class="payment-info">
          <p><i class="bi bi-info-circle"></i> Pagamento à vista no PIX com 5% de desconto</p>
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
  <script>
    // Carregar carrinho ao iniciar a página
    document.addEventListener('DOMContentLoaded', function() {
      atualizarCarrinho();
      atualizarBadgeCarrinho();
    });

    

    
  </script>
</body>
</html>

