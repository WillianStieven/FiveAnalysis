<?php
/**
 * PagamentoController.php
 * Controlador para processamento de pagamentos
 */
session_start();
require_once '../Model/conexao.php';
require_once '../Model/Pagamento.php';

class PagamentoController {
    private $pagamentoModel;
    
    public function __construct($conexao) {
        $this->pagamentoModel = new Pagamento($conexao);
    }
    
    /**
     * Processar pagamento de um pedido
     */
    public function processarPagamento($pedidoId, $metodoPagamento, $dadosPagamento) {
        try {
            // Validar pedido
            $pedido = $this->pagamentoModel->obterPedidoParaPagamento($pedidoId);
            if (!$pedido) {
                return ['success' => false, 'message' => 'Pedido não encontrado'];
            }
            
            // Verificar se pedido já está pago
            if ($pedido['status_pagamento'] === 'aprovado') {
                return ['success' => false, 'message' => 'Pedido já foi pago'];
            }
            
            // Processar conforme método de pagamento
            switch ($metodoPagamento) {
                case 'cartao_credito':
                    $resultado = $this->processarCartaoCredito($dadosPagamento, $pedido);
                    break;
                    
                case 'boleto':
                    $resultado = $this->processarBoleto($pedido);
                    break;
                    
                case 'pix':
                    $resultado = $this->processarPix($pedido);
                    break;
                    
                default:
                    return ['success' => false, 'message' => 'Método de pagamento inválido'];
            }
            
            if ($resultado['success']) {
                // Atualizar status do pagamento
                $this->pagamentoModel->atualizarStatusPagamento($pedidoId, 'aprovado');
                
                // Registrar transação
                $this->pagamentoModel->registrarTransacao([
                    'pedido_id' => $pedidoId,
                    'metodo_pagamento' => $metodoPagamento,
                    'valor' => $pedido['total'],
                    'status' => 'aprovado',
                    'codigo_transacao' => $resultado['codigo_transacao'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Pagamento processado com sucesso',
                    'dados' => $resultado['dados'] ?? []
                ];
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao processar pagamento: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obter métodos de pagamento disponíveis
     */
    public function obterMetodosDisponiveis() {
        return $this->pagamentoModel->obterMetodosAtivos();
    }
    
    /**
     * Gerar código de pagamento PIX
     */
    public function gerarPix($pedidoId) {
        $pedido = $this->pagamentoModel->obterPedidoParaPagamento($pedidoId);
        
        if (!$pedido) {
            return ['success' => false, 'message' => 'Pedido não encontrado'];
        }
        
        // Gerar payload PIX (simulação)
        $pixData = [
            'codigo' => '00020126580014BR.GOV.BCB.PIX0136123e4567-e12b-12d1-a456-4266141740005204000053039865802BR5913FIVEANALYSIS6008CHAPECO62070503***6304' . substr(md5($pedidoId), 0, 4),
            'qrcode' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=', // QR code fake
            'valor' => $pedido['total'],
            'chave' => 'fiveanalysis@pix.com',
            'descricao' => 'Pedido #' . $pedido['numero_pedido']
        ];
        
        return ['success' => true, 'pix' => $pixData];
    }
    
    /**
     * Gerar boleto
     */
    public function gerarBoleto($pedidoId) {
        $pedido = $this->pagamentoModel->obterPedidoParaPagamento($pedidoId);
        
        if (!$pedido) {
            return ['success' => false, 'message' => 'Pedido não encontrado'];
        }
        
        // Gerar dados do boleto (simulação)
        $boletoData = [
            'codigo_barras' => '34191.09008 61713.727386 01000.000000 9 12340000015000',
            'linha_digitavel' => '34191.09008 61713.727386 01000.000000 9 12340000015000',
            'valor' => $pedido['total'],
            'vencimento' => date('Y-m-d', strtotime('+3 days')),
            'beneficiario' => 'FiveAnalysis Tech Solutions',
            'cnpj' => '09.321.222/0001-00',
            'instrucoes' => 'Pagável em qualquer banco ou lotérica'
        ];
        
        return ['success' => true, 'boleto' => $boletoData];
    }
    
    // ========== MÉTODOS PRIVADOS ==========
    
    private function processarCartaoCredito($dadosCartao, $pedido) {
        // Simulação de processamento de cartão
        // EM PRODUÇÃO: Integrar com gateway de pagamento (MercadoPago, PagSeguro, etc.)
        
        // Validar dados do cartão
        if (!$this->validarCartaoCredito($dadosCartao)) {
            return ['success' => false, 'message' => 'Dados do cartão inválidos'];
        }
        
        // Simular transação bem-sucedida
        return [
            'success' => true,
            'codigo_transacao' => 'TRX-' . time() . '-' . $pedido['id'],
            'dados' => [
                'autorizacao' => substr(md5(time()), 0, 6),
                'parcelas' => $dadosCartao['parcelas'] ?? 1
            ]
        ];
    }
    
    private function processarBoleto($pedido) {
        // Boleto sempre gera pendência
        return [
            'success' => true,
            'codigo_transacao' => 'BOL-' . time() . '-' . $pedido['id'],
            'dados' => $this->gerarBoleto($pedido['id'])
        ];
    }
    
    private function processarPix($pedido) {
        // PIX sempre gera pendência
        return [
            'success' => true,
            'codigo_transacao' => 'PIX-' . time() . '-' . $pedido['id'],
            'dados' => $this->gerarPix($pedido['id'])
        ];
    }
    
    private function validarCartaoCredito($dados) {
        // Validações básicas (simulação)
        $camposObrigatorios = ['numero', 'nome', 'validade', 'cvv'];
        
        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return false;
            }
        }
        
        // Validar número do cartão (simulação Luhn)
        if (strlen($dados['numero']) < 13 || strlen($dados['numero']) > 19) {
            return false;
        }
        
        // Validar validade (MM/YY)
        if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $dados['validade'])) {
            return false;
        }
        
        // Validar CVV
        if (!preg_match('/^[0-9]{3,4}$/', $dados['cvv'])) {
            return false;
        }
        
        return true;
    }
}

// Handler para requisições
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../Model/conexao.php';
    $controller = new PagamentoController($conexao);
    
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
        exit;
    }
    
    switch ($_POST['action']) {
        case 'processar':
            if (isset($_POST['pedido_id'], $_POST['metodo_pagamento'])) {
                $response = $controller->processarPagamento(
                    $_POST['pedido_id'],
                    $_POST['metodo_pagamento'],
                    $_POST['dados_pagamento'] ?? []
                );
                echo json_encode($response);
            }
            break;
            
        case 'metodos_disponiveis':
            $metodos = $controller->obterMetodosDisponiveis();
            echo json_encode(['success' => true, 'metodos' => $metodos]);
            break;
            
        case 'gerar_pix':
            if (isset($_POST['pedido_id'])) {
                $response = $controller->gerarPix($_POST['pedido_id']);
                echo json_encode($response);
            }
            break;
            
        case 'gerar_boleto':
            if (isset($_POST['pedido_id'])) {
                $response = $controller->gerarBoleto($_POST['pedido_id']);
                echo json_encode($response);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
}
?>