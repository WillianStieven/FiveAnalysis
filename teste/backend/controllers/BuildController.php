<?php
/**
 * Controller para Montagens de PC
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../models/Build.php';
require_once __DIR__ . '/../models/Product.php';

class BuildController {
    private $buildModel;
    private $productModel;
    
    public function __construct() {
        $this->buildModel = new Build();
        $this->productModel = new Product();
    }
    
    /**
     * Listar montagens do usuário
     */
    public function index() {
        try {
            // Verificar autenticação
            $userId = $this->getCurrentUserId();
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE), MAX_PAGE_SIZE);
            
            $builds = $this->buildModel->getUserBuilds($userId, $page, $limit);
            $total = $this->buildModel->countUserBuilds($userId);
            
            $response = [
                'success' => true,
                'data' => $builds,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            jsonResponse($response);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao listar montagens: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar montagem por ID
     */
    public function show($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $build = $this->buildModel->getById($id, $userId);
            
            if (!$build) {
                jsonResponse(['success' => false, 'message' => 'Montagem não encontrada'], 404);
                return;
            }
            
            // Buscar componentes da montagem
            $components = $this->buildModel->getComponents($id);
            
            // Verificar compatibilidade
            $compatibility = $this->buildModel->checkCompatibility($id);
            
            // Calcular preço total
            $pricing = $this->buildModel->calculateTotalPrice($id);
            
            $response = [
                'success' => true,
                'data' => [
                    'build' => $build,
                    'components' => $components,
                    'compatibility' => $compatibility,
                    'pricing' => $pricing
                ]
            ];
            
            jsonResponse($response);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar montagem: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Criar nova montagem
     */
    public function create() {
        try {
            $userId = $this->getCurrentUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['nome_montagem'])) {
                jsonResponse(['success' => false, 'message' => 'Nome da montagem é obrigatório'], 400);
                return;
            }
            
            $componentes = $input['componentes'] ?? [];
            
            // Validar componentes
            foreach ($componentes as $componente) {
                if (empty($componente['produto_id'])) {
                    jsonResponse(['success' => false, 'message' => 'ID do produto é obrigatório'], 400);
                    return;
                }
                
                // Verificar se o produto existe
                $product = $this->productModel->getById($componente['produto_id']);
                if (!$product) {
                    jsonResponse(['success' => false, 'message' => 'Produto não encontrado: ' . $componente['produto_id']], 400);
                    return;
                }
            }
            
            $buildId = $this->buildModel->create($userId, $input['nome_montagem'], $componentes);
            
            jsonResponse([
                'success' => true,
                'message' => 'Montagem criada com sucesso',
                'data' => ['id' => $buildId]
            ], 201);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao criar montagem: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Adicionar componente à montagem
     */
    public function addComponent($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['produto_id'])) {
                jsonResponse(['success' => false, 'message' => 'ID do produto é obrigatório'], 400);
                return;
            }
            
            // Verificar se o produto existe
            $product = $this->productModel->getById($input['produto_id']);
            if (!$product) {
                jsonResponse(['success' => false, 'message' => 'Produto não encontrado'], 400);
                return;
            }
            
            $itemId = $this->buildModel->addComponent($id, $input['produto_id']);
            
            jsonResponse([
                'success' => true,
                'message' => 'Componente adicionado com sucesso',
                'data' => ['id' => $itemId]
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao adicionar componente: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Remover componente da montagem
     */
    public function removeComponent($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['produto_id'])) {
                jsonResponse(['success' => false, 'message' => 'ID do produto é obrigatório'], 400);
                return;
            }
            
            $rowsAffected = $this->buildModel->removeComponent($id, $input['produto_id']);
            
            if ($rowsAffected === 0) {
                jsonResponse(['success' => false, 'message' => 'Componente não encontrado na montagem'], 404);
                return;
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'Componente removido com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao remover componente: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Atualizar montagem
     */
    public function update($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                jsonResponse(['success' => false, 'message' => 'Dados inválidos'], 400);
                return;
            }
            
            $rowsAffected = $this->buildModel->update($id, $userId, $input);
            
            if ($rowsAffected === 0) {
                jsonResponse(['success' => false, 'message' => 'Montagem não encontrada'], 404);
                return;
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'Montagem atualizada com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao atualizar montagem: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Deletar montagem
     */
    public function delete($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $this->buildModel->delete($id, $userId);
            
            jsonResponse([
                'success' => true,
                'message' => 'Montagem deletada com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao deletar montagem: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Duplicar montagem
     */
    public function duplicate($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            $novoNome = $input['nome'] ?? null;
            
            $newBuildId = $this->buildModel->duplicate($id, $userId, $novoNome);
            
            jsonResponse([
                'success' => true,
                'message' => 'Montagem duplicada com sucesso',
                'data' => ['id' => $newBuildId]
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao duplicar montagem: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Verificar compatibilidade da montagem
     */
    public function checkCompatibility($id) {
        try {
            $userId = $this->getCurrentUserId();
            
            // Verificar se a montagem pertence ao usuário
            $build = $this->buildModel->getById($id, $userId);
            if (!$build) {
                jsonResponse(['success' => false, 'message' => 'Montagem não encontrada'], 404);
                return;
            }
            
            $compatibility = $this->buildModel->checkCompatibility($id);
            
            jsonResponse([
                'success' => true,
                'data' => $compatibility
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao verificar compatibilidade: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar montagens públicas
     */
    public function getPublicBuilds() {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE), MAX_PAGE_SIZE);
            
            $filters = [
                'categoria' => $_GET['categoria'] ?? null,
                'preco_max' => $_GET['preco_max'] ?? null
            ];
            
            // Remover filtros vazios
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });
            
            $builds = $this->buildModel->getPublicBuilds($page, $limit, $filters);
            
            jsonResponse([
                'success' => true,
                'data' => $builds
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar montagens públicas: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Buscar montagens populares
     */
    public function getPopularBuilds() {
        try {
            $limit = min((int)($_GET['limit'] ?? 10), 50);
            
            $builds = $this->buildModel->getPopularBuilds($limit);
            
            jsonResponse([
                'success' => true,
                'data' => $builds
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao buscar montagens populares: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Obter ID do usuário atual (placeholder)
     */
    private function getCurrentUserId() {
        // Implementar autenticação JWT
        // Por enquanto, retornar 1 para testes
        return 1;
    }
}
?>
