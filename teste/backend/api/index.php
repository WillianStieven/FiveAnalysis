<?php
/**
 * API Principal - FiveAnalysis Backend
 * Router e endpoints principais
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/BuildController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Middleware de autenticação
function requireAuth() {
    $token = JWT::extractFromHeader();
    
    if (!$token) {
        jsonResponse(['success' => false, 'message' => 'Token de acesso necessário'], 401);
        return null;
    }
    
    $payload = JWT::getPayload($token);
    
    if (!$payload) {
        jsonResponse(['success' => false, 'message' => 'Token inválido'], 401);
        return null;
    }
    
    return $payload;
}

// Middleware de admin
function requireAdmin() {
    $payload = requireAuth();
    
    if (!$payload) {
        return null;
    }
    
    if ($payload['perfil'] !== 'Administrador') {
        jsonResponse(['success' => false, 'message' => 'Acesso negado'], 403);
        return null;
    }
    
    return $payload;
}

// Router simples
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/backend/api', '', $path);

// Remover barra inicial
$path = ltrim($path, '/');

// Dividir path em segmentos
$segments = explode('/', $path);

// Roteamento
try {
    switch ($segments[0]) {
        case 'auth':
            $authController = new AuthController();
            
            switch ($segments[1]) {
                case 'login':
                    if ($requestMethod === 'POST') {
                        $authController->login();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'register':
                    if ($requestMethod === 'POST') {
                        $authController->register();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'logout':
                    if ($requestMethod === 'POST') {
                        $authController->logout();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'refresh':
                    if ($requestMethod === 'POST') {
                        $authController->refresh();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'verify':
                    if ($requestMethod === 'GET') {
                        $authController->verify();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'change-password':
                    if ($requestMethod === 'POST') {
                        $authController->changePassword();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'forgot-password':
                    if ($requestMethod === 'POST') {
                        $authController->requestPasswordReset();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                default:
                    jsonResponse(['success' => false, 'message' => 'Endpoint não encontrado'], 404);
                    break;
            }
            break;
            
        case 'products':
            $productController = new ProductController();
            
            switch ($segments[1]) {
                case null:
                case '':
                    if ($requestMethod === 'GET') {
                        $productController->index();
                    } elseif ($requestMethod === 'POST') {
                        $productController->create();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'categories':
                    if ($requestMethod === 'GET') {
                        $productController->getCategories();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'brands':
                    if ($requestMethod === 'GET') {
                        $productController->getBrands();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'promotional':
                    if ($requestMethod === 'GET') {
                        $productController->getPromotional();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'top-rated':
                    if ($requestMethod === 'GET') {
                        $productController->getTopRated();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'best-selling':
                    if ($requestMethod === 'GET') {
                        $productController->getBestSelling();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                default:
                    // Verificar se é um ID numérico
                    if (is_numeric($segments[1])) {
                        $id = (int)$segments[1];
                        
                        if ($requestMethod === 'GET') {
                            $productController->show($id);
                        } elseif ($requestMethod === 'PUT') {
                            $productController->update($id);
                        } elseif ($requestMethod === 'DELETE') {
                            $productController->delete($id);
                        } else {
                            jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                        }
                    } else {
                        // Tentar como categoria
                        if ($requestMethod === 'GET') {
                            $productController->getByCategory($segments[1]);
                        } else {
                            jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                        }
                    }
                    break;
            }
            break;
            
        case 'builds':
            $buildController = new BuildController();
            
            switch ($segments[1]) {
                case null:
                case '':
                    if ($requestMethod === 'GET') {
                        $buildController->index();
                    } elseif ($requestMethod === 'POST') {
                        $buildController->create();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'public':
                    if ($requestMethod === 'GET') {
                        $buildController->getPublicBuilds();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                case 'popular':
                    if ($requestMethod === 'GET') {
                        $buildController->getPopularBuilds();
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                    }
                    break;
                    
                default:
                    if (is_numeric($segments[1])) {
                        $id = (int)$segments[1];
                        
                        switch ($segments[2]) {
                            case null:
                            case '':
                                if ($requestMethod === 'GET') {
                                    $buildController->show($id);
                                } elseif ($requestMethod === 'PUT') {
                                    $buildController->update($id);
                                } elseif ($requestMethod === 'DELETE') {
                                    $buildController->delete($id);
                                } else {
                                    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                                }
                                break;
                                
                            case 'components':
                                if ($requestMethod === 'POST') {
                                    $buildController->addComponent($id);
                                } elseif ($requestMethod === 'DELETE') {
                                    $buildController->removeComponent($id);
                                } else {
                                    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                                }
                                break;
                                
                            case 'compatibility':
                                if ($requestMethod === 'GET') {
                                    $buildController->checkCompatibility($id);
                                } else {
                                    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                                }
                                break;
                                
                            case 'duplicate':
                                if ($requestMethod === 'POST') {
                                    $buildController->duplicate($id);
                                } else {
                                    jsonResponse(['success' => false, 'message' => 'Método não permitido'], 405);
                                }
                                break;
                                
                            default:
                                jsonResponse(['success' => false, 'message' => 'Endpoint não encontrado'], 404);
                                break;
                        }
                    } else {
                        jsonResponse(['success' => false, 'message' => 'Endpoint não encontrado'], 404);
                    }
                    break;
            }
            break;
            
        case 'health':
            // Endpoint de saúde da API
            jsonResponse([
                'success' => true,
                'message' => 'API funcionando',
                'data' => [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'version' => '1.0.0',
                    'environment' => ENVIRONMENT
                ]
            ]);
            break;
            
        default:
            jsonResponse(['success' => false, 'message' => 'Endpoint não encontrado'], 404);
            break;
    }
    
} catch (Exception $e) {
    logMessage('ERROR', 'Erro na API: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
}
?>
