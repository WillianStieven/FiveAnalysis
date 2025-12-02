<?php
/**
 * Venda.php
 * Modelo para gestão de vendas (visão administrativa)
 */
class Venda {
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    /**
     * Listar vendas com filtros
     */
    public function listarComFiltros($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            // Construir WHERE dinamicamente
            if (isset($filtros['status']) && $filtros['status']) {
                $where[] = "p.status = :status";
                $params[':status'] = $filtros['status'];
            }
            
            if (isset($filtros['data_inicio']) && $filtros['data_inicio']) {
                $where[] = "p.data_criacao >= :data_inicio";
                $params[':data_inicio'] = $filtros['data_inicio'];
            }
            
            if (isset($filtros['data_fim']) && $filtros['data_fim']) {
                $where[] = "p.data_criacao <= :data_fim";
                $params[':data_fim'] = $filtros['data_fim'] . ' 23:59:59';
            }
            
            if (isset($filtros['loja_id']) && $filtros['loja_id']) {
                $where[] = "pi.loja_afiliada_id = :loja_id";
                $params[':loja_id'] = $filtros['loja_id'];
            }
            
            // Se usuário for loja, filtrar apenas suas vendas
            if (isset($_SESSION['loja_id']) && $_SESSION['tipo_usuario'] === 'loja') {
                $where[] = "pi.loja_afiliada_id = :minha_loja_id";
                $params[':minha_loja_id'] = $_SESSION['loja_id'];
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $sql = "SELECT DISTINCT p.*, 
                           u.nome as usuario_nome,
                           u.email as usuario_email,
                           COUNT(pi.id) as total_itens,
                           SUM(pi.quantidade) as total_produtos
                    FROM pedidos p
                    LEFT JOIN pedido_itens pi ON p.id = pi.pedido_id
                    LEFT JOIN usuarios u ON p.usuario_id = u.id
                    $whereClause
                    GROUP BY p.id, u.id
                    ORDER BY p.data_criacao DESC
                    LIMIT 100";
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao listar vendas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter vendas por período
     */
    public function obterVendasPorPeriodo($periodo = '30dias') {
        try {
            $interval = '';
            
            switch ($periodo) {
                case '7dias':
                    $interval = '7 DAY';
                    break;
                case '30dias':
                    $interval = '30 DAY';
                    break;
                case '90dias':
                    $interval = '90 DAY';
                    break;
                case 'ano':
                    $interval = '1 YEAR';
                    break;
                default:
                    $interval = '30 DAY';
            }
            
            $sql = "SELECT 
                        DATE(p.data_criacao) as data,
                        COUNT(*) as total_vendas,
                        SUM(p.total) as total_faturado,
                        AVG(p.total) as ticket_medio
                    FROM pedidos p
                    WHERE p.data_criacao >= CURRENT_DATE - INTERVAL '$interval'
                    AND p.status NOT IN ('cancelado')
                    GROUP BY DATE(p.data_criacao)
                    ORDER BY data DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter vendas por período: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter produtos mais vendidos
     */
    public function obterProdutosMaisVendidos($limit = 10) {
        try {
            $sql = "SELECT 
                        pi.produto_id,
                        pi.nome_produto,
                        SUM(pi.quantidade) as total_vendido,
                        SUM(pi.subtotal) as total_faturado,
                        p.imagem_url,
                        c.nome as categoria
                    FROM pedido_itens pi
                    JOIN produtos p ON pi.produto_id = p.id
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    GROUP BY pi.produto_id, pi.nome_produto, p.imagem_url, c.nome
                    ORDER BY total_vendido DESC
                    LIMIT :limit";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter produtos mais vendidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter total de vendas
     */
    public function obterTotalVendas($periodo = null) {
        try {
            $where = "WHERE status NOT IN ('cancelado')";
            $params = [];
            
            if ($periodo) {
                $where .= " AND data_criacao >= CURRENT_DATE - INTERVAL :periodo";
                $params[':periodo'] = $periodo;
            }
            
            $sql = "SELECT 
                        COUNT(*) as total_pedidos,
                        SUM(total) as total_faturado,
                        AVG(total) as ticket_medio,
                        MIN(total) as menor_venda,
                        MAX(total) as maior_venda
                    FROM pedidos
                    $where";
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter total de vendas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter ticket médio
     */
    public function obterTicketMedio() {
        try {
            $sql = "SELECT 
                        AVG(total) as ticket_medio
                    FROM pedidos
                    WHERE status NOT IN ('cancelado')
                    AND data_criacao >= CURRENT_DATE - INTERVAL '30 DAY'";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['ticket_medio'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Erro ao obter ticket médio: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Atualizar status de venda
     */
    public function atualizarStatus($vendaId, $novoStatus, $observacoes = '') {
        try {
            // Iniciar transação
            $this->conexao->beginTransaction();
            
            // Atualizar status do pedido
            $sql = "UPDATE pedidos 
                    SET status = :status, 
                        data_atualizacao = CURRENT_TIMESTAMP
                    WHERE id = :venda_id";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':venda_id', $vendaId);
            $stmt->bindParam(':status', $novoStatus);
            $stmt->execute();
            
            // Registrar no histórico
            $sql2 = "INSERT INTO pedido_status (pedido_id, status, observacao)
                     VALUES (:pedido_id, :status, :observacao)";
            
            $stmt2 = $this->conexao->prepare($sql2);
            $stmt2->bindParam(':pedido_id', $vendaId);
            $stmt2->bindParam(':status', $novoStatus);
            $stmt2->bindParam(':observacao', $observacoes);
            $stmt2->execute();
            
            // Commit
            $this->conexao->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conexao->rollBack();
            error_log("Erro ao atualizar status venda: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gerar relatório por período
     */
    public function gerarRelatorioPeriodo($dataInicio, $dataFim) {
        try {
            $sql = "SELECT 
                        p.*,
                        u.nome as usuario_nome,
                        u.email as usuario_email,
                        (SELECT COUNT(*) FROM pedido_itens WHERE pedido_id = p.id) as total_itens,
                        (SELECT SUM(quantidade) FROM pedido_itens WHERE pedido_id = p.id) as total_produtos
                    FROM pedidos p
                    LEFT JOIN usuarios u ON p.usuario_id = u.id
                    WHERE p.data_criacao BETWEEN :data_inicio AND :data_fim
                    AND p.status NOT IN ('cancelado')
                    ORDER BY p.data_criacao DESC";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim . ' 23:59:59');
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao gerar relatório: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter vendas por loja
     */
    public function obterVendasPorLoja($lojaId = null, $periodo = '30dias') {
        try {
            $interval = '';
            
            switch ($periodo) {
                case '7dias':
                    $interval = '7 DAY';
                    break;
                case '30dias':
                    $interval = '30 DAY';
                    break;
                case '90dias':
                    $interval = '90 DAY';
                    break;
                default:
                    $interval = '30 DAY';
            }
            
            $where = "WHERE p.data_criacao >= CURRENT_DATE - INTERVAL '$interval'
                     AND p.status NOT IN ('cancelado')";
            
            $params = [];
            
            if ($lojaId) {
                $where .= " AND pi.loja_afiliada_id = :loja_id";
                $params[':loja_id'] = $lojaId;
            }
            
            $sql = "SELECT 
                        la.id as loja_id,
                        la.nome_loja,
                        COUNT(DISTINCT p.id) as total_pedidos,
                        SUM(pi.subtotal) as total_faturado,
                        COUNT(pi.id) as total_itens_vendidos,
                        AVG(pi.subtotal) as ticket_medio_loja
                    FROM pedido_itens pi
                    JOIN pedidos p ON pi.pedido_id = p.id
                    JOIN lojas_afiliadas la ON pi.loja_afiliada_id = la.id
                    $where
                    GROUP BY la.id, la.nome_loja
                    ORDER BY total_faturado DESC";
            
            $stmt = $this->conexao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao obter vendas por loja: " . $e->getMessage());
            return [];
        }
    }
}
?>