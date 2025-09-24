<?php
/**
 * Controller para Autenticação
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Login do usuário
     */
    public function login() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['email']) || empty($input['password'])) {
                jsonResponse(['success' => false, 'message' => 'Email e senha são obrigatórios'], 400);
                return;
            }
            
            $email = sanitizeInput($input['email']);
            $password = $input['password'];
            
            if (!validateEmail($email)) {
                jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
                return;
            }
            
            // Autenticar usuário
            $user = $this->userModel->authenticate($email, $password);
            
            if (!$user) {
                jsonResponse(['success' => false, 'message' => 'Credenciais inválidas'], 401);
                return;
            }
            
            // Gerar tokens
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'perfil' => $user['perfil_nome']
            ];
            
            $accessToken = JWT::encode($payload);
            $refreshToken = JWT::generateRefreshToken();
            
            // Salvar refresh token no banco (implementar tabela refresh_tokens)
            // $this->saveRefreshToken($user['id'], $refreshToken);
            
            // Log de login
            logMessage('INFO', 'Usuário logado', ['user_id' => $user['id'], 'email' => $email]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'data' => [
                    'user' => $user,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_in' => JWT_EXPIRATION
                ]
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro no login: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Registro de usuário
     */
    public function register() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                jsonResponse(['success' => false, 'message' => 'Dados inválidos'], 400);
                return;
            }
            
            $requiredFields = ['nome', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($input[$field])) {
                    jsonResponse(['success' => false, 'message' => "Campo obrigatório: {$field}"], 400);
                    return;
                }
            }
            
            $nome = sanitizeInput($input['nome']);
            $email = sanitizeInput($input['email']);
            $password = $input['password'];
            
            if (!validateEmail($email)) {
                jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
                return;
            }
            
            if (strlen($password) < 6) {
                jsonResponse(['success' => false, 'message' => 'Senha deve ter pelo menos 6 caracteres'], 400);
                return;
            }
            
            // Criar usuário
            $userData = [
                'nome' => $nome,
                'email' => $email,
                'password' => $password,
                'perfil_id' => 1 // Cliente
            ];
            
            $userId = $this->userModel->create($userData);
            
            // Buscar dados do usuário criado
            $user = $this->userModel->getById($userId);
            unset($user['senha']); // Remover senha do retorno
            
            // Gerar tokens
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'perfil' => $user['perfil_nome']
            ];
            
            $accessToken = JWT::encode($payload);
            $refreshToken = JWT::generateRefreshToken();
            
            // Log de registro
            logMessage('INFO', 'Novo usuário registrado', ['user_id' => $userId, 'email' => $email]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Usuário registrado com sucesso',
                'data' => [
                    'user' => $user,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_in' => JWT_EXPIRATION
                ]
            ], 201);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro no registro: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Logout do usuário
     */
    public function logout() {
        try {
            $token = JWT::extractFromHeader();
            
            if ($token) {
                $payload = JWT::getPayload($token);
                if ($payload) {
                    // Invalidar refresh token (implementar)
                    // $this->invalidateRefreshToken($payload['user_id']);
                    
                    logMessage('INFO', 'Usuário deslogado', ['user_id' => $payload['user_id']]);
                }
            }
            
            jsonResponse([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro no logout: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Renovar token de acesso
     */
    public function refresh() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['refresh_token'])) {
                jsonResponse(['success' => false, 'message' => 'Refresh token é obrigatório'], 400);
                return;
            }
            
            // Verificar refresh token (implementar validação no banco)
            // $userId = $this->validateRefreshToken($input['refresh_token']);
            
            // Por enquanto, usar token atual para obter user_id
            $token = JWT::extractFromHeader();
            if (!$token) {
                jsonResponse(['success' => false, 'message' => 'Token não fornecido'], 401);
                return;
            }
            
            $payload = JWT::getPayload($token);
            if (!$payload) {
                jsonResponse(['success' => false, 'message' => 'Token inválido'], 401);
                return;
            }
            
            // Gerar novo access token
            $newPayload = [
                'user_id' => $payload['user_id'],
                'email' => $payload['email'],
                'perfil' => $payload['perfil']
            ];
            
            $newAccessToken = JWT::encode($newPayload);
            
            jsonResponse([
                'success' => true,
                'message' => 'Token renovado com sucesso',
                'data' => [
                    'access_token' => $newAccessToken,
                    'expires_in' => JWT_EXPIRATION
                ]
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao renovar token: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Verificar token
     */
    public function verify() {
        try {
            $token = JWT::extractFromHeader();
            
            if (!$token) {
                jsonResponse(['success' => false, 'message' => 'Token não fornecido'], 401);
                return;
            }
            
            $payload = JWT::getPayload($token);
            
            if (!$payload) {
                jsonResponse(['success' => false, 'message' => 'Token inválido'], 401);
                return;
            }
            
            // Buscar dados atualizados do usuário
            $user = $this->userModel->getById($payload['user_id']);
            unset($user['senha']); // Remover senha do retorno
            
            jsonResponse([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token_valid' => true
                ]
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao verificar token: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Token inválido'], 401);
        }
    }
    
    /**
     * Alterar senha
     */
    public function changePassword() {
        try {
            $token = JWT::extractFromHeader();
            $payload = JWT::getPayload($token);
            
            if (!$payload) {
                jsonResponse(['success' => false, 'message' => 'Token inválido'], 401);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['current_password']) || empty($input['new_password'])) {
                jsonResponse(['success' => false, 'message' => 'Senha atual e nova senha são obrigatórias'], 400);
                return;
            }
            
            $currentPassword = $input['current_password'];
            $newPassword = $input['new_password'];
            
            if (strlen($newPassword) < 6) {
                jsonResponse(['success' => false, 'message' => 'Nova senha deve ter pelo menos 6 caracteres'], 400);
                return;
            }
            
            // Buscar usuário
            $user = $this->userModel->getById($payload['user_id']);
            if (!$user) {
                jsonResponse(['success' => false, 'message' => 'Usuário não encontrado'], 404);
                return;
            }
            
            // Verificar senha atual
            if (!verifyPassword($currentPassword, $user['senha'])) {
                jsonResponse(['success' => false, 'message' => 'Senha atual incorreta'], 400);
                return;
            }
            
            // Atualizar senha
            $this->userModel->update($user['id'], ['senha' => $newPassword]);
            
            logMessage('INFO', 'Senha alterada', ['user_id' => $user['id']]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Senha alterada com sucesso'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao alterar senha: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
    
    /**
     * Solicitar reset de senha
     */
    public function requestPasswordReset() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['email'])) {
                jsonResponse(['success' => false, 'message' => 'Email é obrigatório'], 400);
                return;
            }
            
            $email = sanitizeInput($input['email']);
            
            if (!validateEmail($email)) {
                jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
                return;
            }
            
            // Verificar se usuário existe
            $user = $this->userModel->getByEmail($email);
            
            if ($user) {
                // Gerar token de reset
                $resetToken = generateToken(32);
                
                // Salvar token no banco (implementar tabela password_resets)
                // $this->savePasswordResetToken($user['id'], $resetToken);
                
                // Enviar email (implementar)
                // $this->sendPasswordResetEmail($user['email'], $resetToken);
                
                logMessage('INFO', 'Solicitação de reset de senha', ['user_id' => $user['id'], 'email' => $email]);
            }
            
            // Sempre retornar sucesso por segurança
            jsonResponse([
                'success' => true,
                'message' => 'Se o email existir, você receberá instruções para redefinir sua senha'
            ]);
            
        } catch (Exception $e) {
            logMessage('ERROR', 'Erro ao solicitar reset de senha: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }
}
?>
