<?php
/**
 * Pagamento.php
 * Modelo para gestão de pagamentos
 */
class Pagamento {
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    /**
     * Obter pedido para pagamento
     */
    public function obterPedidoParaPagamento($pedidoId) {
        try {
            $sql = "SELECT p.*, u.email as usuario_email
                    FROM pedidos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    WHERE p.id = :pedido_id
                    AND p.status_pagamento IN ('pendente', 'recusado')";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter pedido para pagamento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualizar status do pagamento
     */
    public function atualizarStatusPagamento($pedidoId, $status) {
        try {
            $sql = "UPDATE pedidos 
                    SET status_pagamento = :status,
                        data_atualizacao = CURRENT_TIMESTAMP
                    WHERE id = :pedido_id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao atualizar status pagamento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar transação de pagamento
     */
    public function registrarTransacao($dados) {
        try {
            // Primeiro, criar tabela de transações se não existir
            $this->criarTabelaTransacoes();
            
            $sql = "INSERT INTO transacoes_pagamento (
                        pedido_id, metodo_pagamento, valor, 
                        status, codigo_transacao, dados_transacao
                    ) VALUES (
                        :pedido_id, :metodo_pagamento, :valor,
                        :status, :codigo_transacao, :dados_transacao
                    )";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $dados['pedido_id']);
            $stmt->bindParam(':metodo_pagamento', $dados['metodo_pagamento']);
            $stmt->bindParam(':valor', $dados['valor']);
            $stmt->bindParam(':status', $dados['status']);
            $stmt->bindParam(':codigo_transacao', $dados['codigo_transacao']);
            $stmt->bindParam(':dados_transacao', json_encode($dados['dados_transacao'] ?? []));
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao registrar transação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter métodos de pagamento ativos
     */
    public function obterMetodosAtivos() {
        try {
            $sql = "SELECT * FROM formas_pagamento 
                    WHERE ativo = TRUE
                    ORDER BY nome";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter métodos de pagamento: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter transações de um pedido
     */
    public function obterTransacoesPedido($pedidoId) {
        try {
            $this->criarTabelaTransacoes();
            
            $sql = "SELECT * FROM transacoes_pagamento 
                    WHERE pedido_id = :pedido_id
                    ORDER BY data_criacao DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter transações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Criar tabela de transações se não existir
     */
    private function criarTabelaTransacoes() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS transacoes_pagamento (
                        id SERIAL PRIMARY KEY,
                        pedido_id INTEGER REFERENCES pedidos(id),
                        metodo_pagamento VARCHAR(50),
                        valor DECIMAL(10,2) NOT NULL,
                        status VARCHAR(20) DEFAULT 'pendente',
                        codigo_transacao VARCHAR(100),
                        dados_transacao JSONB,
                        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
            
            $this->conexao->exec($sql);
            
            // Criar índice
            $sql_idx = "CREATE INDEX IF NOT EXISTS idx_transacoes_pedido 
                        ON transacoes_pagamento(pedido_id)";
            $this->conexao->exec($sql_idx);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela transações: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validar dados de cartão de crédito
     */
    public function validarDadosCartao($dadosCartao) {
        // Validações básicas
        $camposObrigatorios = ['numero', 'nome', 'validade', 'cvv'];
        
        foreach ($camposObrigatorios as $campo) {
            if (!isset($dadosCartao[$campo]) || empty(trim($dadosCartao[$campo]))) {
                return false;
            }
        }
        
        // Validar número do cartão (formato básico)
        $numero = preg_replace('/\s+/', '', $dadosCartao['numero']);
        if (!preg_match('/^[0-9]{13,19}$/', $numero)) {
            return false;
        }
        
        // Validar validade (MM/AA)
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $dadosCartao['validade'])) {
            return false;
        }
        
        // Verificar se cartão não está vencido
        list($mes, $ano) = explode('/', $dadosCartao['validade']);
        $ano = '20' . $ano;
        
        $validade = DateTime::createFromFormat('Y-m', "$ano-$mes");
        $hoje = new DateTime();
        
        if ($validade < $hoje) {
            return false;
        }
        
        // Validar CVV
        if (!preg_match('/^[0-9]{3,4}$/', $dadosCartao['cvv'])) {
            return false;
        }
        
        return true;
    }
}
?>