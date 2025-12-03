<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finalizar Pagamento - FiveAnalysis</title>

  <!-- Fonte Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- CSS Externo -->
  <link rel="stylesheet" href="../Style/styles.css">
  <link rel="stylesheet" href="../Style/Global.css">
  <link rel="stylesheet" href="../Style/pagamento.css">
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

  <main class="payment-container">
    <!-- Cabeçalho -->
    <div class="payment-header">
      <a href="Carrinho.php" class="back-btn">
        <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
      </a>
      <h1><i class="bi bi-credit-card"></i> Finalizar Pagamento</h1>
      <p class="payment-subtitle">Escolha a forma de pagamento e confirme seu pedido</p>
    </div>

    <div class="payment-content">
      <!-- Formulário de Pagamento -->
      <div class="payment-form-section">
        <!-- Seleção de Método de Pagamento -->
        <div class="payment-method-card">
          <h2><i class="bi bi-wallet2"></i> Método de Pagamento</h2>
          
          <div class="payment-methods">
            <div class="payment-option" onclick="selectPaymentMethod('pix')">
              <input type="radio" name="payment" id="pix" value="pix" checked>
              <label for="pix">
                <div class="payment-icon">
                  <i class="bi bi-qr-code"></i>
                </div>
                <div class="payment-info">
                  <strong>PIX</strong>
                  <span>Aprovação imediata</span>
                  <span class="discount-badge">5% de desconto</span>
                </div>
              </label>
            </div>

            <div class="payment-option" onclick="selectPaymentMethod('credit')">
              <input type="radio" name="payment" id="credit" value="credit">
              <label for="credit">
                <div class="payment-icon">
                  <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="payment-info">
                  <strong>Cartão de Crédito</strong>
                  <span>Em até 12x sem juros</span>
                </div>
              </label>
            </div>

            <div class="payment-option" onclick="selectPaymentMethod('boleto')">
              <input type="radio" name="payment" id="boleto" value="boleto">
              <label for="boleto">
                <div class="payment-icon">
                  <i class="bi bi-upc-scan"></i>
                </div>
                <div class="payment-info">
                  <strong>Boleto Bancário</strong>
                  <span>Vencimento em 3 dias úteis</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Formulário de Cartão de Crédito -->
        <div id="creditCardForm" class="payment-details-card" style="display: none;">
          <h3><i class="bi bi-credit-card"></i> Dados do Cartão</h3>
          
          <div class="form-group">
            <label for="cardNumber">Número do Cartão</label>
            <div class="input-with-icon">
              <i class="bi bi-credit-card"></i>
              <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19">
            </div>
          </div>

          <div class="form-group">
            <label for="cardName">Nome no Cartão</label>
            <div class="input-with-icon">
              <i class="bi bi-person"></i>
              <input type="text" id="cardName" placeholder="Nome como está no cartão">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="cardExpiry">Validade</label>
              <div class="input-with-icon">
                <i class="bi bi-calendar"></i>
                <input type="text" id="cardExpiry" placeholder="MM/AA" maxlength="5">
              </div>
            </div>

            <div class="form-group">
              <label for="cardCVV">CVV</label>
              <div class="input-with-icon">
                <i class="bi bi-shield-lock"></i>
                <input type="text" id="cardCVV" placeholder="000" maxlength="3">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="installments">Parcelas</label>
            <div class="input-with-icon">
              <i class="bi bi-calendar3"></i>
              <select id="installments">
                <option value="1">1x sem juros</option>
                <option value="2">2x sem juros</option>
                <option value="3">3x sem juros</option>
                <option value="4">4x sem juros</option>
                <option value="5">5x sem juros</option>
                <option value="6">6x sem juros</option>
                <option value="7">7x sem juros</option>
                <option value="8">8x sem juros</option>
                <option value="9">9x sem juros</option>
                <option value="10">10x sem juros</option>
                <option value="11">11x sem juros</option>
                <option value="12">12x sem juros</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Dados de Entrega -->
        <div class="payment-details-card">
          <h3><i class="bi bi-truck"></i> Endereço de Entrega</h3>
          
          <div class="form-group">
            <label for="cep">CEP</label>
            <div class="input-with-icon">
              <i class="bi bi-mailbox"></i>
              <input type="text" id="cep" placeholder="00000-000" maxlength="9">
            </div>
          </div>

          <div class="form-group">
            <label for="endereco">Endereço</label>
            <input type="text" id="endereco" placeholder="Rua, Avenida...">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="numero">Número</label>
              <input type="text" id="numero" placeholder="123">
            </div>

            <div class="form-group">
              <label for="complemento">Complemento</label>
              <input type="text" id="complemento" placeholder="Apto, Bloco...">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="bairro">Bairro</label>
              <input type="text" id="bairro" placeholder="Bairro">
            </div>

            <div class="form-group">
              <label for="cidade">Cidade</label>
              <input type="text" id="cidade" placeholder="Cidade">
            </div>

            <div class="form-group">
              <label for="estado">Estado</label>
              <select id="estado">
                <option value="">UF</option>
                <option value="AC">AC</option>
                <option value="AL">AL</option>
                <option value="AP">AP</option>
                <option value="AM">AM</option>
                <option value="BA">BA</option>
                <option value="CE">CE</option>
                <option value="DF">DF</option>
                <option value="ES">ES</option>
                <option value="GO">GO</option>
                <option value="MA">MA</option>
                <option value="MT">MT</option>
                <option value="MS">MS</option>
                <option value="MG">MG</option>
                <option value="PA">PA</option>
                <option value="PB">PB</option>
                <option value="PR">PR</option>
                <option value="PE">PE</option>
                <option value="PI">PI</option>
                <option value="RJ">RJ</option>
                <option value="RN">RN</option>
                <option value="RS">RS</option>
                <option value="RO">RO</option>
                <option value="RR">RR</option>
                <option value="SC">SC</option>
                <option value="SP">SP</option>
                <option value="SE">SE</option>
                <option value="TO">TO</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumo do Pedido -->
      <aside class="order-summary">
        <div class="summary-card">
          <h2><i class="bi bi-receipt"></i> Resumo do Pedido</h2>
          
          <div class="order-items" id="orderItems">
            <!-- Itens serão inseridos via JavaScript -->
          </div>

          <div class="summary-divider"></div>

          <div class="summary-values">
            <div class="summary-row">
              <span>Subtotal</span>
              <span id="summarySubtotal">R$ 0,00</span>
            </div>
            <div class="summary-row">
              <span>Frete</span>
              <span id="summaryShipping" class="free-shipping">Grátis</span>
            </div>
            <div class="summary-row discount-row" id="discountRow" style="display: none;">
              <span>Desconto PIX (5%)</span>
              <span class="discount" id="summaryDiscount">- R$ 0,00</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total-row">
              <span>Total</span>
              <span class="total-price" id="summaryTotal">R$ 0,00</span>
            </div>
          </div>

          <button class="btn btn-primary btn-block btn-finish" onclick="processarPagamento()">
            <i class="bi bi-check-circle"></i> Confirmar Pagamento
          </button>

          <div class="security-info">
            <i class="bi bi-shield-check"></i>
            <span>Pagamento 100% seguro</span>
          </div>
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
  <script src="../Assets/js/pagamento.js"></script>
</body>
</html>