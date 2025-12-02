<?php
/**
 * Pedido.php
 * Modelo para manipulação de pedidos
 */
class Pedido {
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    /**
     * Criar novo pedido
     */
    public function criar($dados) {
        try {
            $sql = "INSERT INTO pedidos (
                        usuario_id, subtotal, desconto, frete, total,
                        metodo_pagamento, endereco_entrega, observacoes
                    ) VALUES (
                        :usuario_id, :subtotal, :desconto, :frete, :total,
                        :metodo_pagamento, :endereco, :observacoes
                    ) RETURNING id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $dados['usuario_id']);
            $stmt->bindParam(':subtotal', $dados['subtotal']);
            $stmt->bindParam(':desconto', $dados['desconto']);
            $stmt->bindParam(':frete', $dados['frete']);
            $stmt->bindParam(':total', $dados['total']);
            $stmt->bindParam(':metodo_pagamento', $dados['metodo_pagamento']);
            $stmt->bindParam(':endereco', $dados['endereco_entrega']);
            $stmt->bindParam(':observacoes', $dados['observacoes']);
            
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'] ?? false;
            
        } catch (PDOException $e) {
            error_log("Erro ao criar pedido: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Adicionar item ao pedido
     */
    public function adicionarItem($dados) {
        try {
            $sql = "INSERT INTO pedido_itens (
                        pedido_id, produto_id, nome_produto,
                        preco_unitario, quantidade, subtotal
                    ) VALUES (
                        :pedido_id, :produto_id, :nome_produto,
                        :preco_unitario, :quantidade, :subtotal
                    )";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $dados['pedido_id']);
            $stmt->bindParam(':produto_id', $dados['produto_id']);
            $stmt->bindParam(':nome_produto', $dados['nome_produto']);
            $stmt->bindParam(':preco_unitario', $dados['preco_unitario']);
            $stmt->bindParam(':quantidade', $dados['quantidade']);
            $stmt->bindParam(':subtotal', $dados['subtotal']);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao adicionar item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Listar pedidos por usuário
     */
    public function listarPorUsuario($usuarioId, $limit = 10) {
        try {
            $sql = "SELECT p.*, 
                           COUNT(pi.id) as total_itens,
                           SUM(pi.quantidade) as total_produtos
                    FROM pedidos p
                    LEFT JOIN pedido_itens pi ON p.id = pi.pedido_id
                    WHERE p.usuario_id = :usuario_id
                    GROUP BY p.id
                    ORDER BY p.data_criacao DESC
                    LIMIT :limit";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuarioId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao listar pedidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter pedido por ID
     */
    public function obterPorId($pedidoId) {
        try {
            $sql = "SELECT p.*, 
                           u.nome as usuario_nome,
                           u.email as usuario_email
                    FROM pedidos p
                    LEFT JOIN usuarios u ON p.usuario_id = u.id
                    WHERE p.id = :pedido_id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter pedido: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter itens do pedido
     */
    public function obterItens($pedidoId) {
        try {
            $sql = "SELECT pi.*, 
                           p.imagem_url,
                           p.descricao,
                           la.nome_loja
                    FROM pedido_itens pi
                    LEFT JOIN produtos p ON pi.produto_id = p.id
                    LEFT JOIN lojas_afiliadas la ON pi.loja_afiliada_id = la.id
                    WHERE pi.pedido_id = :pedido_id
                    ORDER BY pi.data_adicao";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter itens: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualizar status do pedido
     */
    public function atualizarStatus($pedidoId, $status) {
        try {
            $sql = "UPDATE pedidos 
                    SET status = :status, 
                        data_atualizacao = CURRENT_TIMESTAMP
                    WHERE id = :pedido_id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao atualizar status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar histórico de status
     */
    public function registrarStatus($pedidoId, $status, $observacao = '') {
        try {
            $sql = "INSERT INTO pedido_status (pedido_id, status, observacao)
                    VALUES (:pedido_id, :status, :observacao)";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':observacao', $observacao);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erro ao registrar status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter histórico de status
     */
    public function obterHistoricoStatus($pedidoId) {
        try {
            $sql = "SELECT * FROM pedido_status 
                    WHERE pedido_id = :pedido_id
                    ORDER BY data_status DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter histórico: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar se pedido pertence ao usuário
     */
    public function verificarPropriedade($pedidoId, $usuarioId) {
        try {
            $sql = "SELECT id FROM pedidos 
                    WHERE id = :pedido_id AND usuario_id = :usuario_id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->bindParam(':usuario_id', $usuarioId);
            $stmt->execute();
            
            return $stmt->fetch() !== false;
            
        } catch (PDOException $e) {
            error_log("Erro ao verificar propriedade: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter número do pedido
     */
    public function obterNumeroPedido($pedidoId) {
        try {
            $sql = "SELECT numero_pedido FROM pedidos WHERE id = :pedido_id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedidoId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['numero_pedido'] ?? null;
            
        } catch (PDOException $e) {
            error_log("Erro ao obter número pedido: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Getter para conexão
     */
    public function getConexao() {
        return $this->conexao;
    }
}
?>