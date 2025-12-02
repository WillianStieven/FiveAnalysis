<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$pedidoId = $_GET['pedido'] ?? null;
$tipo = $_GET['tipo'] ?? 'sucesso'; // 'sucesso' ou 'pendente'

if (!$pedidoId) {
    header('Location: Pedidos.php');
    exit;
}

require_once '../Model/conexao.php';
require_once '../Controller/PedidoController.php';

$controller = new PedidoController($conexao);
$resultado = $controller->obterDetalhesPedido($pedidoId, $_SESSION['usuario_id']);

if (!$resultado['success']) {
    header('Location: Pedidos.php');
    exit;
}

$pedido = $resultado['pedido'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pedido - FiveAnalysis</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        .confirmacao-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .confirmacao-card {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 40px;
            margin: 20px 0;
        }
        
        .success-icon {
            font-size: 80px;
            color: #10b981;
            margin-bottom: 20px;
        }
        
        .pending-icon {
            font-size: 80px;
            color: #f59e0b;
            margin-bottom: 20px;
        }
        
        .confirmacao-title {
            font-size: 32px;
            color: var(--text-primary);
            margin-bottom: 15px;
        }
        
        .confirmacao-message {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .pedido-info {
            background: var(--background-dark);
            border-radius: var(--border-radius);
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-valor {
            font-size: 16px;
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .numero-pedido {
            font-size: 24px;
            color: var(--primary-color);
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 1px;
        }
        
        .instrucoes-pagamento {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: var(--border-radius);
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .instrucoes-pagamento h3 {
            color: #f59e0b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .pix-qrcode {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .pix-qrcode img {
            width: 200px;
            height: 200px;
            margin-bottom: 15px;
        }
        
        .codigo-pix {
            font-family: monospace;
            background: var(--background-dark);
            padding: 10px;
            border-radius: var(--border-radius);
            margin: 15px 0;
            word-break: break-all;
            font-size: 14px;
        }
        
        .boleto-info {
            text-align: center;
            margin: 20px 0;
        }
        
        .codigo-barras {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 3px;
            background: white;
            color: black;
            padding: 15px;
            border-radius: var(--border-radius);
            margin: 15px 0;
            border: 1px solid var(--border-color);
        }
        
        .linha-digitavel {
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 1px;
            margin: 10px 0;
        }
        
        .acoes-confirmacao {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-acao {
            padding: 15px 25px;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primario {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-primario:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-secundario {
            background: var(--background-dark);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secundario:hover {
            background: var(--border-color);
        }
        
        .timer {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 10px;
        }
        
        .timer span {
            font-weight: 600;
            color: #ef4444;
        }
        
        @media (max-width: 768px) {
            .confirmacao-card {
                padding: 25px;
            }
            
            .confirmacao-title {
                font-size: 24px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .acoes-confirmacao {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    
    <div class="confirmacao-container">
        <?php if ($tipo === 'sucesso'): ?>
            <!-- Confirmação de Sucesso -->
            <div class="success-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            
            <h1 class="confirmacao-title">Pedido Confirmado!</h1>
            <p class="confirmacao-message">
                Seu pedido foi recebido com sucesso e está sendo processado. 
                Você receberá uma confirmação por email em breve.
            </p>
            
        <?php else: ?>
            <!-- Pagamento Pendente -->
            <div class="pending-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            
            <h1 class="confirmacao-title">Pagamento Pendente</h1>
            <p class="confirmacao-message">
                Seu pedido foi criado com sucesso! Para concluir a compra, 
                <?php if ($pedido['metodo_pagamento'] === 'pix'): ?>
                    finalize o pagamento PIX abaixo.
                <?php elseif ($pedido['metodo_pagamento'] === 'boleto'): ?>
                    pague o boleto bancário abaixo.
                <?php else: ?>
                    finalize o pagamento.
                <?php endif; ?>
            </p>
            
            <!-- Instruções de Pagamento -->
            <?php if ($pedido['metodo_pagamento'] === 'pix'): ?>
            <div class="instrucoes-pagamento">
                <h3><i class="bi bi-qr-code"></i> Pagamento via PIX</h3>
                <p>Siga as instruções abaixo para finalizar o pagamento:</p>
                
                <div class="pix-qrcode">
                    <div style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; border-radius: 8px;">
                        <div style="text-align: center;">
                            <div style="width: 160px; height: 160px; background: #000; margin: 0 auto; border-radius: 4px;"></div>
                            <small style="color: #666; margin-top: 5px; display: block;">QR Code simulado</small>
                        </div>
                    </div>
                    <p><strong>Valor:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
                    <p><strong>Chave PIX:</strong> fiveanalysis@pix.com</p>
                </div>
                
                <div class="codigo-pix" id="codigoPix">
                    00020126580014BR.GOV.BCB.PIX0136123e4567-e12b-12d1-a456-4266141740005204000053039865802BR5913FIVEANALYSIS6008CHAPECO62070503***6304<?= substr(md5($pedidoId), 0, 4) ?>
                </div>
                
                <button class="btn-secundario" onclick="copiarCodigoPix()" style="margin-top: 10px;">
                    <i class="bi bi-clipboard"></i> Copiar Código PIX
                </button>
                
                <div class="timer">
                    <p>⏰ Tempo restante: <span id="tempoRestante">30:00</span></p>
                </div>
            </div>
            
            <?php elseif ($pedido['metodo_pagamento'] === 'boleto'): ?>
            <div class="instrucoes-pagamento">
                <h3><i class="bi bi-upc"></i> Pagamento via Boleto</h3>
                <p>Pague o boleto abaixo em qualquer banco ou lotérica:</p>
                
                <div class="boleto-info">
                    <div class="codigo-barras" id="codigoBarras">
                        34191.09008 61713.727386 01000.000000 9 12340000015000
                    </div>
                    
                    <div class="linha-digitavel">
                        34191.09008 61713.727386 01000.000000 9 12340000015000
                    </div>
                    
                    <p><strong>Valor:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
                    <p><strong>Vencimento:</strong> <?= date('d/m/Y', strtotime('+3 days')) ?></p>
                    
                    <button class="btn-secundario" onclick="copiarCodigoBarras()" style="margin-top: 10px;">
                        <i class="bi bi-clipboard"></i> Copiar Código de Barras
                    </button>
                    
                    <button class="btn-secundario" onclick="imprimirBoleto()" style="margin-top: 10px;">
                        <i class="bi bi-printer"></i> Imprimir Boleto
                    </button>
                </div>
                
                <div class="timer">
                    <p>⏰ Vencimento: <span><?= date('d/m/Y', strtotime('+3 days')) ?></span></p>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Informações do Pedido -->
        <div class="pedido-info">
            <h2 style="color: var(--text-primary); font-size: 20px; margin-bottom: 20px; text-align: center;">
                Detalhes do Pedido
            </h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Número do Pedido</div>
                    <div class="info-valor numero-pedido">
                        <?= htmlspecialchars($pedido['numero_pedido'] ?? 'N/A') ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Data</div>
                    <div class="info-valor">
                        <?= date('d/m/Y H:i', strtotime($pedido['data_criacao'])) ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-valor">
                        <?= ucfirst($pedido['status']) ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Total</div>
                    <div class="info-valor" style="color: var(--primary-color); font-size: 18px;">
                        R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ações -->
        <div class="acoes-confirmacao">
            <a href="Pedidos.php" class="btn-acao btn-primario">
                <i class="bi bi-receipt"></i> Ver Meus Pedidos
            </a>
            
            <a href="PaginaInicial.php" class="btn-acao btn-secundario">
                <i class="bi bi-house"></i> Voltar para Início
            </a>
            
            <?php if ($tipo === 'pendente'): ?>
            <button class="btn-acao btn-primario" onclick="verificarPagamento()" style="margin-top: 20px;">
                <i class="bi bi-check-circle"></i> Já efetuei o pagamento
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script>
        <?php if ($tipo === 'pendente'): ?>
        // Timer para pagamento PIX
        let tempoRestante = 30 * 60; // 30 minutos em segundos
        
        function atualizarTimer() {
            const minutos = Math.floor(tempoRestante / 60);
            const segundos = tempoRestante % 60;
            
            document.getElementById('tempoRestante').textContent = 
                `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
            
            if (tempoRestante <= 0) {
                alert('Tempo para pagamento expirado! Seu pedido será cancelado.');
                window.location.href = 'Pedidos.php';
                return;
            }
            
            tempoRestante--;
        }
        
        // Iniciar timer
        setInterval(atualizarTimer, 1000);
        atualizarTimer();
        
        // Verificar pagamento periodicamente
        function verificarPagamento() {
            fetch('../Controller/PagamentoController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=verificar&pedido_id=<?= $pedidoId ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.pago) {
                    alert('Pagamento confirmado! Seu pedido será processado.');
                    window.location.href = 'Confirmacao.php?pedido=<?= $pedidoId ?>&tipo=sucesso';
                } else {
                    alert('Pagamento ainda não confirmado. Tente novamente em alguns minutos.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao verificar pagamento');
            });
        }
        
        // Verificar automaticamente a cada 30 segundos
        setInterval(verificarPagamento, 30000);
        
        // Copiar código PIX
        function copiarCodigoPix() {
            const codigo = document.getElementById('codigoPix').textContent;
            navigator.clipboard.writeText(codigo)
                .then(() => {
                    alert('Código PIX copiado para a área de transferência!');
                })
                .catch(err => {
                    console.error('Erro ao copiar:', err);
                    alert('Erro ao copiar código');
                });
        }
        
        // Copiar código de barras
        function copiarCodigoBarras() {
            const codigo = document.getElementById('codigoBarras').textContent;
            navigator.clipboard.writeText(codigo.replace(/\s/g, ''))
                .then(() => {
                    alert('Código de barras copiado para a área de transferência!');
                })
                .catch(err => {
                    console.error('Erro ao copiar:', err);
                    alert('Erro ao copiar código');
                });
        }
        
        // Imprimir boleto
        function imprimirBoleto() {
            const conteudo = `
                <html>
                <head>
                    <title>Boleto - Pedido <?= $pedido['numero_pedido'] ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .boleto { border: 2px solid #000; padding: 20px; max-width: 600px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .dados { margin: 15px 0; }
                        .codigo-barras { font-family: monospace; font-size: 20px; letter-spacing: 3px; text-align: center; margin: 20px 0; }
                        .instrucoes { margin-top: 20px; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="boleto">
                        <div class="header">
                            <h2>Boleto Bancário</h2>
                            <p>FiveAnalysis Tech Solutions</p>
                            <p>CNPJ: 09.321.222/0001-00</p>
                        </div>
                        
                        <div class="dados">
                            <p><strong>Beneficiário:</strong> FiveAnalysis Tech Solutions</p>
                            <p><strong>Valor:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
                            <p><strong>Vencimento:</strong> <?= date('d/m/Y', strtotime('+3 days')) ?></p>
                            <p><strong>Número do Pedido:</strong> <?= $pedido['numero_pedido'] ?></p>
                        </div>
                        
                        <div class="codigo-barras">
                            34191.09008 61713.727386 01000.000000 9 12340000015000
                        </div>
                        
                        <div class="instrucoes">
                            <p><strong>Instruções:</strong></p>
                            <p>1. Pague em qualquer banco ou lotérica até a data de vencimento</p>
                            <p>2. Após o pagamento, seu pedido será processado em até 2 dias úteis</p>
                            <p>3. Guarde o comprovante de pagamento</p>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            const janela = window.open('', '_blank');
            janela.document.write(conteudo);
            janela.document.close();
            janela.print();
        }
        <?php endif; ?>
    </script>
</body>
</html>