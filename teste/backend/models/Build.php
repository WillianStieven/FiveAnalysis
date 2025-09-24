<?php
/**
 * Model para Montagens de PC
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../config/database.php';

class Build {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Criar nova montagem
     */
    public function create($userId, $nomeMontagem, $componentes = []) {
        $this->db->beginTransaction();
        
        try {
            // Criar a montagem
            $montagemData = [
                'usuario_id' => $userId,
                'nome_montagem' => $nomeMontagem,
                'data_criacao' => date('Y-m-d H:i:s')
            ];
            
            $montagemId = $this->db->insert('montagens_pc', $montagemData);
            
            // Adicionar componentes
            if (!empty($componentes)) {
                foreach ($componentes as $componente) {
                    $itemData = [
                        'montagem_id' => $montagemId,
                        'produto_id' => $componente['produto_id']
                    ];
                    $this->db->insert('montagem_itens', $itemData);
                }
            }
            
            $this->db->commit();
            return $montagemId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Buscar montagem por ID
     */
    public function getById($id, $userId = null) {
        $whereClause = "WHERE mp.id = :id";
        $params = ['id' => $id];
        
        if ($userId) {
            $whereClause .= " AND mp.usuario_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $sql = "
            SELECT 
                mp.*,
                u.nome as usuario_nome,
                u.email as usuario_email
            FROM montagens_pc mp
            LEFT JOIN usuarios u ON mp.usuario_id = u.id
            {$whereClause}
        ";
        
        return $this->db->fetchOne($sql, $params);
    }
    
    /**
     * Buscar componentes da montagem
     */
    public function getComponents($montagemId) {
        $sql = "
            SELECT 
                p.*,
                mi.id as item_id,
                COALESCE(AVG(ap.nota), 0) as nota_media,
                COUNT(ap.id) as total_avaliacoes
            FROM montagem_itens mi
            JOIN produtos p ON mi.produto_id = p.id
            LEFT JOIN avaliacoes_produtos ap ON p.id = ap.produto_id
            WHERE mi.montagem_id = :montagem_id
            GROUP BY p.id, mi.id
            ORDER BY p.categoria, p.nome
        ";
        
        return $this->db->fetchAll($sql, ['montagem_id' => $montagemId]);
    }
    
    /**
     * Buscar montagens do usuário
     */
    public function getUserBuilds($userId, $page = 1, $limit = DEFAULT_PAGE_SIZE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                mp.*,
                COUNT(mi.id) as total_componentes,
                COALESCE(SUM(pa.preco), 0) as preco_total
            FROM montagens_pc mp
            LEFT JOIN montagem_itens mi ON mp.id = mi.montagem_id
            LEFT JOIN produtos p ON mi.produto_id = p.id
            LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
            WHERE mp.usuario_id = :user_id
            GROUP BY mp.id
            ORDER BY mp.data_criacao DESC
            LIMIT :limit OFFSET :offset
        ";
        
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Adicionar componente à montagem
     */
    public function addComponent($montagemId, $produtoId) {
        // Verificar se a montagem existe e pertence ao usuário
        $montagem = $this->getById($montagemId);
        if (!$montagem) {
            throw new Exception("Montagem não encontrada");
        }
        
        // Verificar se o componente já existe na montagem
        $sql = "SELECT id FROM montagem_itens WHERE montagem_id = :montagem_id AND produto_id = :produto_id";
        $existing = $this->db->fetchOne($sql, [
            'montagem_id' => $montagemId,
            'produto_id' => $produtoId
        ]);
        
        if ($existing) {
            throw new Exception("Componente já adicionado à montagem");
        }
        
        // Adicionar componente
        $data = [
            'montagem_id' => $montagemId,
            'produto_id' => $produtoId
        ];
        
        return $this->db->insert('montagem_itens', $data);
    }
    
    /**
     * Remover componente da montagem
     */
    public function removeComponent($montagemId, $produtoId) {
        return $this->db->delete(
            'montagem_itens',
            'montagem_id = :montagem_id AND produto_id = :produto_id',
            [
                'montagem_id' => $montagemId,
                'produto_id' => $produtoId
            ]
        );
    }
    
    /**
     * Atualizar montagem
     */
    public function update($id, $userId, $data) {
        // Verificar se a montagem pertence ao usuário
        $montagem = $this->getById($id, $userId);
        if (!$montagem) {
            throw new Exception("Montagem não encontrada");
        }
        
        return $this->db->update('montagens_pc', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Deletar montagem
     */
    public function delete($id, $userId) {
        $this->db->beginTransaction();
        
        try {
            // Verificar se a montagem pertence ao usuário
            $montagem = $this->getById($id, $userId);
            if (!$montagem) {
                throw new Exception("Montagem não encontrada");
            }
            
            // Deletar itens da montagem
            $this->db->delete('montagem_itens', 'montagem_id = :montagem_id', ['montagem_id' => $id]);
            
            // Deletar a montagem
            $this->db->delete('montagens_pc', 'id = :id', ['id' => $id]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Verificar compatibilidade da montagem
     */
    public function checkCompatibility($montagemId) {
        $componentes = $this->getComponents($montagemId);
        $incompatibilidades = [];
        
        foreach ($componentes as $i => $componente1) {
            foreach ($componentes as $j => $componente2) {
                if ($i >= $j) continue; // Evitar duplicatas
                
                $sql = "
                    SELECT status 
                    FROM compatibilidade_peca 
                    WHERE (peca1_id = :peca1 AND peca2_id = :peca2) 
                    OR (peca1_id = :peca2 AND peca2_id = :peca1)
                ";
                
                $result = $this->db->fetchOne($sql, [
                    'peca1' => $componente1['id'],
                    'peca2' => $componente2['id']
                ]);
                
                if ($result && $result['status'] !== 'compativel') {
                    $incompatibilidades[] = [
                        'componente1' => $componente1['nome'],
                        'componente2' => $componente2['nome'],
                        'status' => $result['status']
                    ];
                }
            }
        }
        
        return $incompatibilidades;
    }
    
    /**
     * Calcular preço total da montagem
     */
    public function calculateTotalPrice($montagemId) {
        $sql = "
            SELECT 
                COALESCE(MIN(pa.preco), 0) as preco_minimo,
                COALESCE(MAX(pa.preco), 0) as preco_maximo,
                COALESCE(AVG(pa.preco), 0) as preco_medio
            FROM montagem_itens mi
            JOIN produtos p ON mi.produto_id = p.id
            LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
            WHERE mi.montagem_id = :montagem_id
        ";
        
        return $this->db->fetchOne($sql, ['montagem_id' => $montagemId]);
    }
    
    /**
     * Buscar montagens públicas (para inspiração)
     */
    public function getPublicBuilds($page = 1, $limit = DEFAULT_PAGE_SIZE, $filters = []) {
        $offset = ($page - 1) * $limit;
        $whereClause = "WHERE mp.publico = true";
        $params = [];
        
        if (!empty($filters['categoria'])) {
            $whereClause .= " AND EXISTS (
                SELECT 1 FROM montagem_itens mi 
                JOIN produtos p ON mi.produto_id = p.id 
                WHERE mi.montagem_id = mp.id AND p.categoria = :categoria
            )";
            $params['categoria'] = $filters['categoria'];
        }
        
        if (!empty($filters['preco_max'])) {
            $whereClause .= " AND (
                SELECT COALESCE(MIN(pa.preco), 0) 
                FROM montagem_itens mi 
                JOIN produtos p ON mi.produto_id = p.id 
                LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
                WHERE mi.montagem_id = mp.id
            ) <= :preco_max";
            $params['preco_max'] = $filters['preco_max'];
        }
        
        $sql = "
            SELECT 
                mp.*,
                u.nome as usuario_nome,
                COUNT(mi.id) as total_componentes,
                COALESCE(SUM(pa.preco), 0) as preco_total
            FROM montagens_pc mp
            LEFT JOIN usuarios u ON mp.usuario_id = u.id
            LEFT JOIN montagem_itens mi ON mp.id = mi.montagem_id
            LEFT JOIN produtos p ON mi.produto_id = p.id
            LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
            {$whereClause}
            GROUP BY mp.id, u.nome
            ORDER BY mp.data_criacao DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Duplicar montagem
     */
    public function duplicate($montagemId, $userId, $novoNome = null) {
        $montagem = $this->getById($montagemId);
        if (!$montagem) {
            throw new Exception("Montagem não encontrada");
        }
        
        $componentes = $this->getComponents($montagemId);
        
        $nomeMontagem = $novoNome ?: $montagem['nome_montagem'] . ' (Cópia)';
        
        return $this->create($userId, $nomeMontagem, $componentes);
    }
    
    /**
     * Contar montagens do usuário
     */
    public function countUserBuilds($userId) {
        $sql = "SELECT COUNT(*) as total FROM montagens_pc WHERE usuario_id = :user_id";
        $result = $this->db->fetchOne($sql, ['user_id' => $userId]);
        return $result['total'];
    }
    
    /**
     * Buscar montagens mais populares
     */
    public function getPopularBuilds($limit = 10) {
        $sql = "
            SELECT 
                mp.*,
                u.nome as usuario_nome,
                COUNT(mi.id) as total_componentes,
                COALESCE(SUM(pa.preco), 0) as preco_total
            FROM montagens_pc mp
            LEFT JOIN usuarios u ON mp.usuario_id = u.id
            LEFT JOIN montagem_itens mi ON mp.id = mi.montagem_id
            LEFT JOIN produtos p ON mi.produto_id = p.id
            LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
            WHERE mp.publico = true
            GROUP BY mp.id, u.nome
            ORDER BY mp.visualizacoes DESC, mp.data_criacao DESC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
}
?>
