<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

require_once '../Model/conexao.php';
require_once '../Controller/PedidoController.php';

$controller = new PedidoController($conexao);
$pedidos = $controller->listarPedidosUsuario($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - FiveAnalysis</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Style/Global.css">
    
    <style>
        .pedidos-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .pedidos-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .pedidos-header h1 {
            color: var(--text-primary);
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .pedidos-subtitle {
            color: var(--text-muted);
            font-size: 16px;
        }
        
        .filtros-pedidos {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .filtro-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filtro-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
        }
        
        .filtro-group select,
        .filtro-group input {
            width: 100%;
            padding: 10px 12px;
            background: var(--background-dark);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .btn-filtrar {
            align-self: flex-end;
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-filtrar:hover {
            background: var(--primary-hover);
        }
        
        .pedidos-lista {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .pedido-card {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .pedido-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }
        
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .pedido-info {
            flex: 1;
        }
        
        .pedido-numero {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }
        
        .pedido-data {
            font-size: 14px;
            color: var(--text-muted);
        }
        
        .pedido-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pendente {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .status-processando {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        .status-enviado {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .status-entregue {
            background: rgba(5, 150, 105, 0.1);
            color: #059669;
            border: 1px solid rgba(5, 150, 105, 0.2);
        }
        
        .status-cancelado {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .pedido-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detalhe-item {
            padding: 10px;
            background: var(--background-dark);
            border-radius: var(--border-radius);
        }
        
        .detalhe-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .detalhe-valor {
            font-size: 16px;
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .pedido-itens {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .pedido-itens h4 {
            color: var(--text-primary);
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .itens-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .item-resumo {
            background: var(--background-dark);
            padding: 10px;
            border-radius: var(--border-radius);
            text-align: center;
        }
        
        .item-nome {
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .item-quantidade {
            font-size: 12px;
            color: var(--text-muted);
        }
        
        .pedido-acoes {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-acao {
            padding: 8px 16px;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-ver {
            background: var(--primary-color);
            color: var(--text-primary);
        }
        
        .btn-ver:hover {
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
        
        .btn-rastrear {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .btn-rastrear:hover {
            background: rgba(16, 185, 129, 0.2);
        }
        
        .sem-pedidos {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .sem-pedidos i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .sem-pedidos h3 {
            font-size: 24px;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .pedido-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .pedido-status {
                align-self: flex-start;
            }
            
            .pedido-detalhes {
                grid-template-columns: 1fr;
            }
            
            .itens-lista {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .pedido-acoes {
                flex-direction: column;
            }
            
            .btn-acao {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    
    <div class="pedidos-container">
        <!-- Header -->
        <div class="pedidos-header">
            <h1><i class="bi bi-receipt"></i> Meus Pedidos</h1>
            <p class="pedidos-subtitle">Acompanhe todos os seus pedidos realizados</p>
        </div>
        
        <!-- Filtros -->
        <div class="filtros-pedidos">
            <div class="filtro-group">
                <label>Status</label>
                <select id="filtroStatus">
                    <option value="">Todos os status</option>
                    <option value="pendente">Pendente</option>
                    <option value="processando">Processando</option>
                    <option value="enviado">Enviado</option>
                    <option value="entregue">Entregue</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            
            <div class="filtro-group">
                <label>Data Inicial</label>
                <input type="date" id="filtroDataInicio">
            </div>
            
            <div class="filtro-group">
                <label>Data Final</label>
                <input type="date" id="filtroDataFim">
            </div>
            
            <button class="btn-filtrar" id="btnFiltrar">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
        </div>
        
        <!-- Lista de Pedidos -->
        <div class="pedidos-lista" id="pedidosLista">
            <?php if (empty($pedidos)): ?>
                <div class="sem-pedidos">
                    <i class="bi bi-inbox"></i>
                    <h3>Nenhum pedido encontrado</h3>
                    <p>Você ainda não realizou nenhum pedido</p>
                    <a href="Catalogo.php" class="btn" style="margin-top: 20px; display: inline-block;">
                        <i class="bi bi-arrow-left"></i> Ver Produtos
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): 
                    $statusClass = 'status-' . $pedido['status'];
                    $dataFormatada = date('d/m/Y H:i', strtotime($pedido['data_criacao']));
                ?>
                    <div class="pedido-card" data-pedido-id="<?= $pedido['id'] ?>">
                        <div class="pedido-header">
                            <div class="pedido-info">
                                <div class="pedido-numero"><?= htmlspecialchars($pedido['numero_pedido'] ?? 'N/A') ?></div>
                                <div class="pedido-data">Realizado em: <?= $dataFormatada ?></div>
                            </div>
                            <div class="pedido-status <?= $statusClass ?>">
                                <?= ucfirst($pedido['status']) ?>
                            </div>
                        </div>
                        
                        <div class="pedido-detalhes">
                            <div class="detalhe-item">
                                <div class="detalhe-label">Total</div>
                                <div class="detalhe-valor">R$ <?= number_format($pedido['total'], 2, ',', '.') ?></div>
                            </div>
                            
                            <div class="detalhe-item">
                                <div class="detalhe-label">Itens</div>
                                <div class="detalhe-valor"><?= $pedido['total_itens'] ?? 0 ?></div>
                            </div>
                            
                            <div class="detalhe-item">
                                <div class="detalhe-label">Forma de Pagamento</div>
                                <div class="detalhe-valor">
                                    <?= match($pedido['metodo_pagamento'] ?? '') {
                                        'cartao_credito' => 'Cartão de Crédito',
                                        'boleto' => 'Boleto',
                                        'pix' => 'PIX',
                                        default => 'Não informado'
                                    } ?>
                                </div>
                            </div>
                            
                            <?php if ($pedido['status'] === 'enviado'): ?>
                            <div class="detalhe-item">
                                <div class="detalhe-label">Código de Rastreio</div>
                                <div class="detalhe-valor" style="font-family: monospace;">
                                    <?= substr(md5($pedido['id']), 0, 12) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pedido-itens">
                            <h4>Produtos do Pedido</h4>
                            <div class="itens-lista">
                                <!-- Itens serão carregados via JavaScript -->
                                <div class="item-resumo">
                                    <div class="item-nome">Carregando...</div>
                                    <div class="item-quantidade">0 itens</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pedido-acoes">
                            <button class="btn-acao btn-ver" onclick="verDetalhesPedido(<?= $pedido['id'] ?>)">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </button>
                            
                            <?php if (in_array($pedido['status'], ['pendente', 'processando'])): ?>
                            <button class="btn-acao btn-cancelar" onclick="cancelarPedido(<?= $pedido['id'] ?>)">
                                <i class="bi bi-x-circle"></i> Cancelar Pedido
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($pedido['status'] === 'enviado'): ?>
                            <button class="btn-acao btn-rastrear" onclick="rastrearPedido('<?= substr(md5($pedido['id']), 0, 12) ?>')">
                                <i class="bi bi-truck"></i> Rastrear Envio
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="../Assets/js/pedidos.js"></script>
</body>
</html>