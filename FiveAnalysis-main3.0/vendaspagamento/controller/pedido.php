<?php
/**
 * PedidoController.php
 * Controlador para gerenciamento de pedidos
 */
session_start();
require_once '../Model/conexao.php';
require_once '../Model/Pedido.php';

class PedidoController {
    private $pedidoModel;
    
    public function __construct($conexao) {
        $this->pedidoModel = new Pedido($conexao);
    }
    
    /**
     * Criar novo pedido a partir do carrinho
     */
    public function criarPedido($dadosPedido) {
        try {
            // Validar dados
            if (!$this->validarDadosPedido($dadosPedido)) {
                return ['success' => false, 'message' => 'Dados inválidos para o pedido'];
            }
            
            // Obter itens do carrinho da sessão
            $itensCarrinho = $this->obterItensCarrinho();
            if (empty($itensCarrinho)) {
                return ['success' => false, 'message' => 'Carrinho vazio'];
            }
            
            // Verificar estoque
            if (!$this->verificarEstoque($itensCarrinho)) {
                return ['success' => false, 'message' => 'Estoque insuficiente para alguns produtos'];
            }
            
            // Calcular totais
            $totais = $this->calcularTotais($itensCarrinho, $dadosPedido);
            
            // Preparar dados do pedido
            $pedidoData = [
                'usuario_id' => $_SESSION['usuario_id'] ?? null,
                'subtotal' => $totais['subtotal'],
                'desconto' => $totais['desconto'],
                'frete' => $totais['frete'],
                'total' => $totais['total'],
                'metodo_pagamento' => $dadosPedido['metodo_pagamento'],
                'endereco_entrega' => json_encode($dadosPedido['endereco']),
                'observacoes' => $dadosPedido['observacoes'] ?? ''
            ];
            
            // Criar pedido no banco
            $pedidoId = $this->pedidoModel->criar($pedidoData);
            
            if (!$pedidoId) {
                return ['success' => false, 'message' => 'Erro ao criar pedido'];
            }
            
            // Adicionar itens ao pedido
            $this->adicionarItensPedido($pedidoId, $itensCarrinho);
            
            // Atualizar estoque
            $this->atualizarEstoque($itensCarrinho);
            
            // Limpar carrinho
            $this->limparCarrinho();
            
            // Registrar histórico
            $this->pedidoModel->registrarStatus($pedidoId, 'pendente', 'Pedido criado com sucesso');
            
            return [
                'success' => true,
                'pedido_id' => $pedidoId,
                'numero_pedido' => $this->pedidoModel->obterNumeroPedido($pedidoId),
                'total' => $totais['total'],
                'message' => 'Pedido realizado com sucesso!'
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    /**
     * Listar pedidos do usuário
     */
    public function listarPedidosUsuario($usuarioId, $limit = 10) {
        return $this->pedidoModel->listarPorUsuario($usuarioId, $limit);
    }
    
    /**
     * Obter detalhes do pedido
     */
    public function obterDetalhesPedido($pedidoId, $usuarioId) {
        // Verificar se pedido pertence ao usuário
        if (!$this->pedidoModel->verificarPropriedade($pedidoId, $usuarioId)) {
            return ['success' => false, 'message' => 'Pedido não encontrado'];
        }
        
        $pedido = $this->pedidoModel->obterPorId($pedidoId);
        $itens = $this->pedidoModel->obterItens($pedidoId);
        $historico = $this->pedidoModel->obterHistoricoStatus($pedidoId);
        
        return [
            'success' => true,
            'pedido' => $pedido,
            'itens' => $itens,
            'historico' => $historico
        ];
    }
    
    /**
     * Cancelar pedido
     */
    public function cancelarPedido($pedidoId, $usuarioId, $motivo = '') {
        if (!$this->pedidoModel->verificarPropriedade($pedidoId, $usuarioId)) {
            return ['success' => false, 'message' => 'Pedido não encontrado'];
        }
        
        $pedido = $this->pedidoModel->obterPorId($pedidoId);
        
        // Verificar se pode cancelar
        if (!in_array($pedido['status'], ['pendente', 'processando'])) {
            return ['success' => false, 'message' => 'Pedido não pode ser cancelado neste status'];
        }
        
        // Atualizar status
        $result = $this->pedidoModel->atualizarStatus($pedidoId, 'cancelado');
        
        if ($result) {
            // Registrar histórico
            $this->pedidoModel->registrarStatus($pedidoId, 'cancelado', 
                'Pedido cancelado pelo usuário. Motivo: ' . $motivo);
            
            // Devolver itens ao estoque
            $this->devolverEstoque($pedidoId);
            
            return ['success' => true, 'message' => 'Pedido cancelado com sucesso'];
        }
        
        return ['success' => false, 'message' => 'Erro ao cancelar pedido'];
    }
    
    // ========== MÉTODOS PRIVADOS ==========
    
    private function validarDadosPedido($dados) {
        $camposObrigatorios = [
            'metodo_pagamento',
            'endereco.cep', 'endereco.rua', 'endereco.numero',
            'endereco.bairro', 'endereco.cidade', 'endereco.estado'
        ];
        
        foreach ($camposObrigatorios as $campo) {
            if (strpos($campo, '.') !== false) {
                list($parent, $child) = explode('.', $campo);
                if (!isset($dados[$parent][$child]) || empty($dados[$parent][$child])) {
                    return false;
                }
            } elseif (!isset($dados[$campo]) || empty($dados[$campo])) {
                return false;
            }
        }
        
        return true;
    }
    
    private function obterItensCarrinho() {
        // Adaptar conforme sua implementação do carrinho
        if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
            return $_SESSION['carrinho'];
        }
        return [];
    }
    
    private function calcularTotais($itens, $dadosPedido) {
        $subtotal = 0;
        
        foreach ($itens as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }
        
        // Calcular frete (exemplo simples)
        $frete = $this->calcularFrete($dadosPedido['endereco']['cep'], $subtotal);
        
        // Aplicar desconto (se houver)
        $desconto = $this->calcularDesconto($subtotal);
        
        $total = $subtotal + $frete - $desconto;
        
        return [
            'subtotal' => $subtotal,
            'frete' => $frete,
            'desconto' => $desconto,
            'total' => $total
        ];
    }
    
    private function calcularFrete($cep, $subtotal) {
        // Frete grátis para compras acima de R$ 1000
        if ($subtotal >= 1000) {
            return 0;
        }
        
        // Simulação simples de cálculo de frete
        return 35.00;
    }
    
    private function calcularDesconto($subtotal) {
        // Exemplo: 5% de desconto para compras acima de R$ 500
        if ($subtotal >= 500) {
            return $subtotal * 0.05;
        }
        return 0;
    }
    
    private function adicionarItensPedido($pedidoId, $itens) {
        foreach ($itens as $item) {
            $this->pedidoModel->adicionarItem([
                'pedido_id' => $pedidoId,
                'produto_id' => $item['id'],
                'nome_produto' => $item['nome'],
                'preco_unitario' => $item['preco'],
                'quantidade' => $item['quantidade'],
                'subtotal' => $item['preco'] * $item['quantidade']
            ]);
        }
    }
    
    private function verificarEstoque($itens) {
        foreach ($itens as $item) {
            $sql = "SELECT estoque FROM produtos WHERE id = :produto_id";
            $stmt = $this->pedidoModel->getConexao()->prepare($sql);
            $stmt->bindParam(':produto_id', $item['id']);
            $stmt->execute();
            
            $estoque = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$estoque || $estoque['estoque'] < $item['quantidade']) {
                return false;
            }
        }
        return true;
    }
    
    private function atualizarEstoque($itens) {
        foreach ($itens as $item) {
            $sql = "UPDATE produtos 
                    SET estoque = estoque - :quantidade,
                        vendidos = vendidos + :quantidade
                    WHERE id = :produto_id";
            
            $stmt = $this->pedidoModel->getConexao()->prepare($sql);
            $stmt->bindParam(':produto_id', $item['id']);
            $stmt->bindParam(':quantidade', $item['quantidade']);
            $stmt->execute();
        }
    }
    
    private function devolverEstoque($pedidoId) {
        $itens = $this->pedidoModel->obterItens($pedidoId);
        
        foreach ($itens as $item) {
            $sql = "UPDATE produtos 
                    SET estoque = estoque + :quantidade,
                        vendidos = vendidos - :quantidade
                    WHERE id = :produto_id";
            
            $stmt = $this->pedidoModel->getConexao()->prepare($sql);
            $stmt->bindParam(':produto_id', $item['produto_id']);
            $stmt->bindParam(':quantidade', $item['quantidade']);
            $stmt->execute();
        }
    }
    
    private function limparCarrinho() {
        unset($_SESSION['carrinho']);
    }
}

// Handler para requisições
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../Model/conexao.php';
    $controller = new PedidoController($conexao);
    
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
        exit;
    }
    
    switch ($_POST['action']) {
        case 'criar':
            $response = $controller->criarPedido($_POST);
            echo json_encode($response);
            break;
            
        case 'listar':
            $pedidos = $controller->listarPedidosUsuario($_SESSION['usuario_id']);
            echo json_encode(['success' => true, 'pedidos' => $pedidos]);
            break;
            
        case 'detalhes':
            if (isset($_POST['pedido_id'])) {
                $response = $controller->obterDetalhesPedido($_POST['pedido_id'], $_SESSION['usuario_id']);
                echo json_encode($response);
            }
            break;
            
        case 'cancelar':
            if (isset($_POST['pedido_id'])) {
                $response = $controller->cancelarPedido(
                    $_POST['pedido_id'], 
                    $_SESSION['usuario_id'],
                    $_POST['motivo'] ?? ''
                );
                echo json_encode($response);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
}
?>