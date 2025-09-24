<?php
/**
 * Model para Produtos
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../config/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar todos os produtos com paginação
     */
    public function getAll($page = 1, $limit = DEFAULT_PAGE_SIZE, $filters = []) {
        $offset = ($page - 1) * $limit;
        $whereClause = '';
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['categoria'])) {
            $whereClause .= " AND p.categoria = :categoria";
            $params['categoria'] = $filters['categoria'];
        }
        
        if (!empty($filters['marca'])) {
            $whereClause .= " AND p.marca = :marca";
            $params['marca'] = $filters['marca'];
        }
        
        if (!empty($filters['preco_min'])) {
            $whereClause .= " AND p.preco >= :preco_min";
            $params['preco_min'] = $filters['preco_min'];
        }
        
        if (!empty($filters['preco_max'])) {
            $whereClause .= " AND p.preco <= :preco_max";
            $params['preco_max'] = $filters['preco_max'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (p.nome ILIKE :search OR p.modelo ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql = "
            SELECT 
                p.*,
                COALESCE(AVG(ap.nota), 0) as nota_media,
                COUNT(ap.id) as total_avaliacoes,
                MIN(pa.preco) as preco_minimo,
                MAX(pa.preco) as preco_maximo
            FROM produtos p
            LEFT JOIN avaliacoes_produtos ap ON p.id = ap.produto_id
            LEFT JOIN produtos_afiliados pa ON p.id = pa.produto_id
            WHERE 1=1 {$whereClause}
            GROUP BY p.id
            ORDER BY p.nome
            LIMIT :limit OFFSET :offset
        ";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Buscar produto por ID
     */
    public function getById($id) {
        $sql = "
            SELECT 
                p.*,
                COALESCE(AVG(ap.nota), 0) as nota_media,
                COUNT(ap.id) as total_avaliacoes
            FROM produtos p
            LEFT JOIN avaliacoes_produtos ap ON p.id = ap.produto_id
            WHERE p.id = :id
            GROUP BY p.id
        ";
        
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Buscar produtos por categoria
     */
    public function getByCategory($category, $page = 1, $limit = DEFAULT_PAGE_SIZE) {
        return $this->getAll($page, $limit, ['categoria' => $category]);
    }
    
    /**
     * Buscar produtos afiliados (melhores preços)
     */
    public function getAffiliateProducts($productId) {
        $sql = "
            SELECT 
                pa.*,
                la.nome as loja_nome,
                la.site_url,
                la.logo_url
            FROM produtos_afiliados pa
            JOIN lojas_afiliadas la ON pa.loja_id = la.id
            WHERE pa.produto_id = :product_id
            ORDER BY pa.preco ASC
        ";
        
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }
    
    /**
     * Buscar produtos compatíveis
     */
    public function getCompatibleProducts($productId) {
        $sql = "
            SELECT 
                p.*,
                cp.status as compatibilidade_status
            FROM compatibilidade_peca cp
            JOIN produtos p ON (
                CASE 
                    WHEN cp.peca1_id = :product_id THEN cp.peca2_id = p.id
                    WHEN cp.peca2_id = :product_id THEN cp.peca1_id = p.id
                END
            )
            WHERE (cp.peca1_id = :product_id OR cp.peca2_id = :product_id)
            AND cp.status = 'compativel'
        ";
        
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }
    
    /**
     * Buscar produtos similares
     */
    public function getSimilarProducts($productId, $limit = 5) {
        $product = $this->getById($productId);
        if (!$product) {
            return [];
        }
        
        $sql = "
            SELECT 
                p.*,
                COALESCE(AVG(ap.nota), 0) as nota_media
            FROM produtos p
            LEFT JOIN avaliacoes_produtos ap ON p.id = ap.produto_id
            WHERE p.categoria = :categoria 
            AND p.id != :product_id
            GROUP BY p.id
            ORDER BY nota_media DESC, p.preco ASC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($sql, [
            'categoria' => $product['categoria'],
            'product_id' => $productId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Buscar produtos em promoção
     */
    public function getPromotionalProducts($limit = 10) {
        $sql = "
            SELECT 
                p.*,
                pa.preco as preco_atual,
                hp.preco_anterior,
                ROUND(((hp.preco_anterior - pa.preco) / hp.preco_anterior * 100), 2) as desconto_percentual
            FROM produtos p
            JOIN produtos_afiliados pa ON p.id = pa.produto_id
            JOIN historico_preco hp ON pa.id = hp.produto_afiliado_id
            WHERE pa.preco < hp.preco_anterior
            AND hp.data_atualizacao >= CURRENT_DATE - INTERVAL '7 days'
            ORDER BY desconto_percentual DESC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Buscar produtos mais avaliados
     */
    public function getTopRatedProducts($limit = 10) {
        $sql = "
            SELECT 
                p.*,
                COALESCE(AVG(ap.nota), 0) as nota_media,
                COUNT(ap.id) as total_avaliacoes
            FROM produtos p
            LEFT JOIN avaliacoes_produtos ap ON p.id = ap.produto_id
            GROUP BY p.id
            HAVING COUNT(ap.id) > 0
            ORDER BY nota_media DESC, total_avaliacoes DESC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Buscar produtos mais vendidos
     */
    public function getBestSellingProducts($limit = 10) {
        $sql = "
            SELECT 
                p.*,
                SUM(pli.quantidade) as total_vendido
            FROM produtos p
            JOIN produtos_afiliados pa ON p.id = pa.produto_id
            JOIN pedido_loja_itens pli ON pa.id = pli.produto_afiliado_id
            JOIN pedido_loja pl ON pli.pedido_loja_id = pl.id
            JOIN pedidos ped ON pl.pedido_id = ped.id
            WHERE ped.status = 'concluido'
            AND ped.data_pedido >= CURRENT_DATE - INTERVAL '30 days'
            GROUP BY p.id
            ORDER BY total_vendido DESC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Contar total de produtos
     */
    public function count($filters = []) {
        $whereClause = '';
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['categoria'])) {
            $whereClause .= " AND categoria = :categoria";
            $params['categoria'] = $filters['categoria'];
        }
        
        if (!empty($filters['marca'])) {
            $whereClause .= " AND marca = :marca";
            $params['marca'] = $filters['marca'];
        }
        
        if (!empty($filters['preco_min'])) {
            $whereClause .= " AND preco >= :preco_min";
            $params['preco_min'] = $filters['preco_min'];
        }
        
        if (!empty($filters['preco_max'])) {
            $whereClause .= " AND preco <= :preco_max";
            $params['preco_max'] = $filters['preco_max'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (nome ILIKE :search OR modelo ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE 1=1 {$whereClause}";
        $result = $this->db->fetchOne($sql, $params);
        
        return $result['total'];
    }
    
    /**
     * Buscar categorias disponíveis
     */
    public function getCategories() {
        $sql = "
            SELECT 
                categoria,
                COUNT(*) as total_produtos
            FROM produtos 
            GROUP BY categoria 
            ORDER BY categoria
        ";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Buscar marcas disponíveis
     */
    public function getBrands() {
        $sql = "
            SELECT 
                marca,
                COUNT(*) as total_produtos
            FROM produtos 
            WHERE marca IS NOT NULL
            GROUP BY marca 
            ORDER BY marca
        ";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Criar novo produto
     */
    public function create($data) {
        $requiredFields = ['nome', 'categoria', 'marca', 'modelo'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo obrigatório: {$field}");
            }
        }
        
        return $this->db->insert('produtos', $data);
    }
    
    /**
     * Atualizar produto
     */
    public function update($id, $data) {
        return $this->db->update('produtos', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Deletar produto
     */
    public function delete($id) {
        return $this->db->delete('produtos', 'id = :id', ['id' => $id]);
    }
}
?>
