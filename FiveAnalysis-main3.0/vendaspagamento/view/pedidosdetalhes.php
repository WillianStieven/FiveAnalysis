<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$pedidoId = $_GET['id'] ?? null;

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
$itens = $resultado['itens'];
$historico = $resultado['historico'];

// Decodificar endereço
$endereco = json_decode($pedido['endereco_entrega'] ?? '{}', true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - FiveAnalysis</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        .detalhes-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .detalhes-header {
            margin-bottom: 30px;
        }
        
        .detalhes-header h1 {
            color: var(--text-primary);
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .voltar-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 20px;
            transition: var(--transition);
        }
        
        .voltar-link:hover {
            color: var(--primary-hover);
            transform: translateX(-5px);
        }
        
        .detalhes-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .info-section {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .info-section h2 {
            color: var(--text-primary);
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
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
        
        .itens-lista {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .item-card {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: var(--background-dark);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }
        
        .item-imagem {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-nome {
            font-size: 16px;
            color: var(--text-primary);
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .item-detalhes {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            font-size: 14px;
            color: var(--text-muted);
        }
        
        .item-subtotal {
            font-size: 16px;
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 5px;
        }
        
        .historico-timeline {
            position: relative;
            padding-left: 20px;
        }
        
        .historico-timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-color);
        }
        
        .historico-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .historico-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .historico-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid var(--background-card);
        }
        
        .historico-data {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }
        
        .historico-status {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .historico-obs {
            font-size: 13px;
            color: var(--text-muted);
        }
        
        .resumo-pedido {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            position: sticky;
            top: 100px;
        }
        
        .resumo-linha {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .resumo-linha:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .resumo-label {
            color: var(--text-muted);
        }
        
        .resumo-valor {
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .resumo-total {
            font-size: 24px;
            color: var(--primary-color);
            font-weight: 700;
        }
        
        .acoes-pedido {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-acao {
            width: 100%;
            padding: 12px;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .btn-imprimir {
            background: var(--background-dark);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-imprimir:hover {
            background: var(--border-color);
        }
        
        .btn-contato {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-contato:hover {
            background: var(--primary-hover);
        }
        
        .btn-cancelar {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .btn-cancelar:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        
        @media (max-width: 1024px) {
            .detalhes-content {
                grid-template-columns: 1fr;
            }
            
            .resumo-pedido {
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .item-card {
                flex-direction: column;
                text-align: center;
            }
            
            .item-imagem {
                width: 100%;
                max-width: 200px;
                height: 150px;
                margin: 0 auto;
            }
            
            .item-detalhes {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    
    <div class="detalhes-container">
        <!-- Header -->
        <div class="detalhes-header">
            <a href="Pedidos.php" class="voltar-link">
                <i class="bi bi-arrow-left"></i> Voltar para Meus Pedidos
            </a>
            <h1><i class="bi bi-receipt"></i> Pedido <?= htmlspecialchars($pedido['numero_pedido'] ?? 'N/A') ?></h1>
            <p style="color: var(--text-muted);">Detalhes completos do seu pedido</p>
        </div>
        
        <div class="detalhes-content">
            <!-- Informações Principais -->
            <div>
                <!-- Status e Informações -->
                <div class="info-section">
                    <h2><i class="bi bi-info-circle"></i> Informações do Pedido</h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Número do Pedido</div>
                            <div class="info-valor"><?= htmlspecialchars($pedido['numero_pedido'] ?? 'N/A') ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Data do Pedido</div>
                            <div class="info-valor">
                                <?= date('d/m/Y H:i', strtotime($pedido['data_criacao'])) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-valor" style="
                                color: <?= match($pedido['status']) {
                                    'pendente' => '#f59e0b',
                                    'processando' => '#3b82f6',
                                    'enviado' => '#10b981',
                                    'entregue' => '#059669',
                                    'cancelado' => '#ef4444',
                                    default => 'var(--text-primary)'
                                }; ?>;
                                font-weight: 600;
                            ">
                                <?= ucfirst($pedido['status']) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Forma de Pagamento</div>
                            <div class="info-valor">
                                <?= match($pedido['metodo_pagamento'] ?? '') {
                                    'cartao_credito' => 'Cartão de Crédito',
                                    'boleto' => 'Boleto Bancário',
                                    'pix' => 'PIX',
                                    default => 'Não informado'
                                } ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Status do Pagamento</div>
                            <div class="info-valor" style="
                                color: <?= match($pedido['status_pagamento'] ?? 'pendente') {
                                    'aprovado' => '#10b981',
                                    'pendente' => '#f59e0b',
                                    'recusado' => '#ef4444',
                                    'reembolsado' => '#6b7280',
                                    default => 'var(--text-primary)'
                                }; ?>;
                                font-weight: 600;
                            ">
                                <?= ucfirst($pedido['status_pagamento'] ?? 'pendente') ?>
                            </div>
                        </div>
                        
                        <?php if ($pedido['status'] === 'enviado'): ?>
                        <div class="info-item">
                            <div class="info-label">Código de Rastreio</div>
                            <div class="info-valor" style="font-family: monospace;">
                                <?= substr(md5($pedido['id']), 0, 12) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Itens do Pedido -->
                <div class="info-section">
                    <h2><i class="bi bi-box"></i> Produtos do Pedido</h2>
                    
                    <div class="itens-lista">
                        <?php if (empty($itens)): ?>
                            <p style="color: var(--text-muted); text-align: center; padding: 20px;">
                                Nenhum item encontrado para este pedido
                            </p>
                        <?php else: ?>
                            <?php foreach ($itens as $item): ?>
                                <div class="item-card">
                                    <img src="<?= htmlspecialchars($item['imagem_url'] ?? '../Assets/img/a1.jpg') ?>" 
                                         alt="<?= htmlspecialchars($item['nome_produto']) ?>" 
                                         class="item-imagem">
                                    
                                    <div class="item-info">
                                        <div class="item-nome"><?= htmlspecialchars($item['nome_produto']) ?></div>
                                        
                                        <div class="item-detalhes">
                                            <span>Preço unitário: R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                                            <span>Quantidade: <?= $item['quantidade'] ?></span>
                                            <?php if (!empty($item['nome_loja'])): ?>
                                            <span>Loja: <?= htmlspecialchars($item['nome_loja']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="item-subtotal">
                                            Subtotal: R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Endereço de Entrega -->
                <?php if (!empty($endereco)): ?>
                <div class="info-section">
                    <h2><i class="bi bi-geo-alt"></i> Endereço de Entrega</h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Endereço</div>
                            <div class="info-valor">
                                <?= htmlspecialchars($endereco['rua'] ?? '') ?>, 
                                <?= htmlspecialchars($endereco['numero'] ?? '') ?>
                                <?= !empty($endereco['complemento']) ? ' - ' . htmlspecialchars($endereco['complemento']) : '' ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Bairro</div>
                            <div class="info-valor"><?= htmlspecialchars($endereco['bairro'] ?? '') ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Cidade/Estado</div>
                            <div class="info-valor">
                                <?= htmlspecialchars($endereco['cidade'] ?? '') ?> / 
                                <?= htmlspecialchars($endereco['estado'] ?? '') ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">CEP</div>
                            <div class="info-valor"><?= htmlspecialchars($endereco['cep'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Histórico do Pedido -->
                <?php if (!empty($historico)): ?>
                <div class="info-section">
                    <h2><i class="bi bi-clock-history"></i> Histórico do Pedido</h2>
                    
                    <div class="historico-timeline">
                        <?php foreach ($historico as $evento): ?>
                            <div class="historico-item">
                                <div class="historico-data">
                                    <?= date('d/m/Y H:i', strtotime($evento['data_status'])) ?>
                                </div>
                                <div class="historico-status">
                                    <?= ucfirst($evento['status']) ?>
                                </div>
                                <?php if (!empty($evento['observacao'])): ?>
                                <div class="historico-obs">
                                    <?= htmlspecialchars($evento['observacao']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Resumo e Ações -->
            <div>
                <!-- Resumo do Pedido -->
                <div class="resumo-pedido">
                    <h2 style="color: var(--text-primary); font-size: 20px; margin-bottom: 20px;">
                        Resumo do Pedido
                    </h2>
                    
                    <div class="resumo-linha">
                        <span class="resumo-label">Subtotal</span>
                        <span class="resumo-valor">R$ <?= number_format($pedido['subtotal'], 2, ',', '.') ?></span>
                    </div>
                    
                    <div class="resumo-linha">
                        <span class="resumo-label">Frete</span>
                        <span class="resumo-valor">
                            <?= $pedido['frete'] == 0 ? 'Grátis' : 'R$ ' . number_format($pedido['frete'], 2, ',', '.') ?>
                        </span>
                    </div>
                    
                    <?php if ($pedido['desconto'] > 0): ?>
                    <div class="resumo-linha">
                        <span class="resumo-label">Desconto</span>
                        <span class="resumo-valor" style="color: #10b981;">
                            - R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="resumo-linha">
                        <span class="resumo-label" style="font-size: 18px;">Total</span>
                                                <span class="resumo-valor resumo-total">
                            R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($pedido['observacoes'])): ?>
                    <div class="resumo-linha">
                        <span class="resumo-label">Observações</span>
                        <span class="resumo-valor"><?= htmlspecialchars($pedido['observacoes']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Ações do Pedido -->
                <div class="acoes-pedido">
                    <button class="btn-acao btn-imprimir" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir Pedido
                    </button>
                    
                    <button class="btn-acao btn-contato" onclick="window.location.href='mailto:suporte@fiveanalysis.com?subject=Pedido%20<?= $pedido['numero_pedido'] ?>'">
                        <i class="bi bi-envelope"></i> Falar com Suporte
                    </button>
                    
                    <?php if (in_array($pedido['status'], ['pendente', 'processando'])): ?>
                    <button class="btn-acao btn-cancelar" onclick="cancelarPedido(<?= $pedido['id'] ?>)">
                        <i class="bi bi-x-circle"></i> Cancelar Pedido
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script>
        function cancelarPedido(pedidoId) {
            if (confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
                const motivo = prompt('Por favor, informe o motivo do cancelamento:');
                
                if (motivo === null) return;
                
                fetch('../Controller/PedidoController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=cancelar&pedido_id=${pedidoId}&motivo=${encodeURIComponent(motivo)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido cancelado com sucesso!');
                        window.location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao cancelar pedido');
                });
            }
        }
    </script>
</body>
</html>