<?php
/**
 * Controller para Produtos
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    /**
     * Listar produtos
     */
    public function index() {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE), MAX_PAGE_SIZE);
            
            $filters = [
                'categoria' => $_GET['categoria'] ?? null,
                'marca' => $_GET['marca'] ?? null,
                'preco_min' => $_GET['preco_min'] ?? null,
                'preco_max' => $_GET['preco_max'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            
            // Remover filtros vazios
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });
            
            $products = $this->productModel->getAll($page, $limit, $filters);
            $total = $this->productModel->count($filters);
            
            $response = [
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            jsonResponse($response);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao listar produtos: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar produto por ID
     */
    public function show($id) {
        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                jsonResponse(['success' => false, 'message' => 'Produto não encontrado'], 404);
                return;
            }
            
            // Buscar produtos afiliados (melhores preços)
            $affiliateProducts = $this->productModel->getAffiliateProducts($id);
            
            // Buscar produtos similares
            $similarProducts = $this->productModel->getSimilarProducts($id);
            
            // Buscar produtos compatíveis
            $compatibleProducts = $this->productModel->getCompatibleProducts($id);
            
            $response = [
                'success' => true,
                'data' => [
                    'product' => $product,
                    'affiliate_products' => $affiliateProducts,
                    'similar_products' => $similarProducts,
                    'compatible_products' => $compatibleProducts
                ]
            ];
            
            jsonResponse($response);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar produto: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar produtos por categoria
     */
    public function getByCategory($category) {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE), MAX_PAGE_SIZE);
            
            $products = $this->productModel->getByCategory($category, $page, $limit);
            $total = $this->productModel->count(['categoria' => $category]);
            
            $response = [
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            jsonResponse($response);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar produtos por categoria: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar produtos em promoção
     */
    public function getPromotional() {
        try {
            $limit = min((int)($_GET['limit'] ?? 10), 50);
            
            $products = $this->productModel->getPromotionalProducts($limit);
            
            jsonResponse([
                'success' => true,
                'data' => $products
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar produtos promocionais: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar produtos mais avaliados
     */
    public function getTopRated() {
        try {
            $limit = min((int)($_GET['limit'] ?? 10), 50);
            
            $products = $this->productModel->getTopRatedProducts($limit);
            
            jsonResponse([
                'success' => true,
                'data' => $products
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar produtos mais avaliados: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar produtos mais vendidos
     */
    public function getBestSelling() {
        try {
            $limit = min((int)($_GET['limit'] ?? 10), 50);
            
            $products = $this->productModel->getBestSellingProducts($limit);
            
            jsonResponse([
                'success' => true,
                'data' => $products
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar produtos mais vendidos: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar categorias
     */
    public function getCategories() {
        try {
            $categories = $this->productModel->getCategories();
            
            jsonResponse([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar categorias: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar marcas
     */
    public function getBrands() {
        try {
            $brands = $this->productModel->getBrands();
            
            jsonResponse([
                'success' => true,
                'data' => $brands
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar marcas: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Criar novo produto (admin)
     */
    public function create() {
        try {
            // Verificar se é admin (implementar middleware de autenticação)
            // $this->requireAdmin();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                jsonResponse(['success' => false, 'message' => 'Dados inválidos'], 400);
                return;
            }
            
            $productId = $this->productModel->create($input);
            
            jsonResponse([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'data' => ['id' => $productId]
            ], 201);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao criar produto: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Atualizar produto (admin)
     */
    public function update($id) {
        try {
            // Verificar se é admin
            // $this->requireAdmin();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                jsonResponse(['success' => false, 'message' => 'Dados inválidos'], 400);
                return;
            }
            
            $rowsAffected = $this->productModel->update($id, $input);
            
            if ($rowsAffected === 0) {
                jsonResponse(['success' => false, 'message' => 'Produto não encontrado'], 404);
                return;
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'Produto atualizado com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao atualizar produto: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Deletar produto (admin)
     */
    public function delete($id) {
        try {
            // Verificar se é admin
            // $this->requireAdmin();
            
            $rowsAffected = $this->productModel->delete($id);
            
            if ($rowsAffected === 0) {
                jsonResponse(['success' => false, 'message' => 'Produto não encontrado'], 404);
                return;
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'Produto deletado com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao deletar produto: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
}
?>
