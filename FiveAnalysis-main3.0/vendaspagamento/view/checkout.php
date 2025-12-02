<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

// Obter carrinho da sessão
$carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
$totalCarrinho = 0;

foreach ($carrinho as $item) {
    $totalCarrinho += $item['preco'] * $item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FiveAnalysis</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Style/Global.css">
    <link rel="stylesheet" href="../Style/carrinho.css">
    
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .checkout-header h1 {
            color: var(--text-primary);
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .checkout-steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
            position: relative;
        }
        
        .checkout-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--background-dark);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--text-muted);
        }
        
        .step.active .step-number {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--text-primary);
        }
        
        .step-label {
            color: var(--text-muted);
            font-size: 14px;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .checkout-form {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 30px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            color: var(--text-primary);
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background: var(--background-dark);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            font-size: 16px;
            transition: var(--transition);
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--background-dark);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background: rgba(12, 67, 177, 0.1);
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-icon {
            font-size: 24px;
            color: var(--text-muted);
        }
        
        .payment-method.selected .payment-icon {
            color: var(--primary-color);
        }
        
        .payment-info {
            flex: 1;
        }
        
        .payment-info h4 {
            color: var(--text-primary);
            margin: 0 0 5px 0;
        }
        
        .payment-info p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }
        
        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        
        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0a3a9e 100%);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(12, 67, 177, 0.4);
        }
        
        .btn-checkout:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .cartao-detalhes {
            display: none;
            background: var(--background-dark);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-top: 15px;
            border: 1px solid var(--border-color);
        }
        
        .cartao-detalhes.active {
            display: block;
        }
        
        @media (max-width: 1024px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }
            
            .checkout-steps {
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .checkout-steps {
                flex-direction: column;
                gap: 15px;
            }
            
            .checkout-steps::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    
    <div class="checkout-container">
        <!-- Header -->
        <div class="checkout-header">
            <h1><i class="bi bi-credit-card"></i> Finalizar Compra</h1>
            <p style="color: var(--text-muted);">Preencha os dados para concluir sua compra</p>
        </div>
        
        <!-- Passos do Checkout -->
        <div class="checkout-steps">
            <div class="step">
                <div class="step-number">1</div>
                <span class="step-label">Carrinho</span>
            </div>
            <div class="step active">
                <div class="step-number">2</div>
                <span class="step-label">Checkout</span>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <span class="step-label">Confirmação</span>
            </div>
        </div>
        
        <!-- Alertas -->
        <div class="alert alert-success" id="successAlert" style="display: none;"></div>
        <div class="alert alert-error" id="errorAlert" style="display: none;"></div>
        
        <div class="checkout-content">
            <!-- Formulário -->
            <div>
                <form id="checkoutForm">
                    <!-- Endereço de Entrega -->
                    <div class="form-section">
                        <h2><i class="bi bi-geo-alt"></i> Endereço de Entrega</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>CEP *</label>
                                <input type="text" id="cep" name="cep" required maxlength="9">
                            </div>
                            <div class="form-group">
                                <label>Estado *</label>
                                <select id="estado" name="estado" required>
                                    <option value="">Selecione...</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Endereço *</label>
                            <input type="text" id="endereco" name="endereco" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Número *</label>
                                <input type="text" id="numero" name="numero" required>
                            </div>
                            <div class="form-group">
                                <label>Complemento</label>
                                <input type="text" id="complemento" name="complemento">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Bairro *</label>
                                <input type="text" id="bairro" name="bairro" required>
                            </div>
                            <div class="form-group">
                                <label>Cidade *</label>
                                <input type="text" id="cidade" name="cidade" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações de Contato -->
                    <div class="form-section">
                        <h2><i class="bi bi-person"></i> Informações de Contato</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome Completo *</label>
                                <input type="text" id="nome_completo" name="nome_completo" required>
                            </div>
                            <div class="form-group">
                                <label>Telefone *</label>
                                <input type="tel" id="telefone" name="telefone" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" required>
                        </div>
                    </div>
                    
                    <!-- Método de Pagamento -->
                    <div class="form-section">
                        <h2><i class="bi bi-credit-card"></i> Método de Pagamento</h2>
                        
                        <div class="payment-methods">
                            <label class="payment-method selected" data-method="cartao_credito">
                                <input type="radio" name="metodo_pagamento" value="cartao_credito" checked>
                                <div class="payment-icon">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Cartão de Crédito</h4>
                                    <p>Pague em até 12x sem juros</p>
                                </div>
                            </label>
                            
                            <label class="payment-method" data-method="boleto">
                                <input type="radio" name="metodo_pagamento" value="boleto">
                                <div class="payment-icon">
                                    <i class="bi bi-upc"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>Boleto Bancário</h4>
                                    <p>Pague em qualquer banco ou lotérica</p>
                                </div>
                            </label>
                            
                            <label class="payment-method" data-method="pix">
                                <input type="radio" name="metodo_pagamento" value="pix">
                                <div class="payment-icon">
                                    <i class="bi bi-qr-code"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>PIX</h4>
                                    <p>Pagamento instantâneo via QR Code</p>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Detalhes do Cartão -->
                        <div class="cartao-detalhes active" id="cartaoDetalhes">
                            <h3 style="margin-bottom: 15px; color: var(--text-primary);">Dados do Cartão</h3>
                            
                            <div class="form-group">
                                <label>Número do Cartão *</label>
                                <input type="text" id="numero_cartao" name="numero_cartao" placeholder="1234 5678 9012 3456">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nome no Cartão *</label>
                                    <input type="text" id="nome_cartao" name="nome_cartao" placeholder="Como escrito no cartão">
                                </div>
                                <div class="form-group">
                                    <label>Validade *</label>
                                    <input type="text" id="validade_cartao" name="validade_cartao" placeholder="MM/AA">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>CVV *</label>
                                    <input type="text" id="cvv_cartao" name="cvv_cartao" placeholder="123" maxlength="4">
                                </div>
                                <div class="form-group">
                                    <label>Parcelas</label>
                                    <select id="parcelas" name="parcelas">
                                        <option value="1">1x de R$ <?= number_format($totalCarrinho, 2, ',', '.') ?></option>
                                        <option value="2">2x de R$ <?= number_format($totalCarrinho / 2, 2, ',', '.') ?></option>
                                        <option value="3">3x de R$ <?= number_format($totalCarrinho / 3, 2, ',', '.') ?></option>
                                        <option value="4">4x de R$ <?= number_format($totalCarrinho / 4, 2, ',', '.') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="form-section">
                        <h2><i class="bi bi-chat"></i> Observações</h2>
                        <div class="form-group">
                            <textarea id="observacoes" name="observacoes" rows="3" placeholder="Alguma observação sobre o pedido? (opcional)"></textarea>
                        </div>
                    </div>
                    
                    <!-- Termos -->
                    <div class="form-section">
                        <div class="form-group">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                                <input type="checkbox" id="termos" required style="margin-top: 3px;">
                                <span style="color: var(--text-muted);">
                                    Li e aceito os <a href="#" style="color: var(--primary-color);">termos e condições</a> de compra e a 
                                    <a href="#" style="color: var(--primary-color);">política de privacidade</a> da FiveAnalysis.
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-checkout" id="btnFinalizar">
                        <i class="bi bi-check-circle"></i> Finalizar Pedido
                    </button>
                </form>
            </div>
            
            <!-- Resumo do Pedido -->
            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Resumo do Pedido</h3>
                    
                    <div class="summary-details">
                        <?php if (empty($carrinho)): ?>
                            <div class="summary-row">
                                <span>Carrinho vazio</span>
                            </div>
                        <?php else: ?>
                            <?php 
                            $subtotal = 0;
                            foreach ($carrinho as $item): 
                                $itemSubtotal = $item['preco'] * $item['quantidade'];
                                $subtotal += $itemSubtotal;
                            ?>
                                <div class="summary-row">
                                    <span><?= htmlspecialchars($item['nome']) ?> x<?= $item['quantidade'] ?></span>
                                    <span>R$ <?= number_format($itemSubtotal, 2, ',', '.') ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                            </div>
                            
                            <?php
                            // Calcular frete
                            $frete = ($subtotal >= 1000) ? 0 : 35.00;
                            $desconto = ($subtotal >= 500) ? $subtotal * 0.05 : 0;
                            $total = $subtotal + $frete - $desconto;
                            ?>
                            
                            <div class="summary-row">
                                <span>Frete</span>
                                <span><?= $frete == 0 ? 'Grátis' : 'R$ ' . number_format($frete, 2, ',', '.') ?></span>
                            </div>
                            
                            <?php if ($desconto > 0): ?>
                            <div class="summary-row discount">
                                <span>Desconto</span>
                                <span>- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="totalCheckout">R$ <?= number_format($total, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="contact-card">
                    <h4><i class="bi bi-shield-check"></i> Compra Segura</h4>
                    <p style="margin-bottom: 10px; color: var(--text-muted); font-size: 14px;">
                        <i class="bi bi-check-circle" style="color: #10b981;"></i> Dados protegidos por SSL
                    </p>
                    <p style="margin-bottom: 10px; color: var(--text-muted); font-size: 14px;">
                        <i class="bi bi-check-circle" style="color: #10b981;"></i> Política de devolução em 7 dias
                    </p>
                    <p style="color: var(--text-muted); font-size: 14px;">
                        <i class="bi bi-check-circle" style="color: #10b981;"></i> Suporte 24/7
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="../Assets/js/checkout.js"></script>
</body>
</html>