<?php
/**
 * Model para Usuários
 * FiveAnalysis Backend
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar usuário por ID
     */
    public function getById($id) {
        $sql = "
            SELECT 
                u.*,
                pu.nome as perfil_nome
            FROM usuarios u
            LEFT JOIN perfis_usuarios pu ON u.perfil_id = pu.id
            WHERE u.id = :id
        ";
        
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Buscar usuário por email
     */
    public function getByEmail($email) {
        $sql = "
            SELECT 
                u.*,
                pu.nome as perfil_nome
            FROM usuarios u
            LEFT JOIN perfis_usuarios pu ON u.perfil_id = pu.id
            WHERE u.email = :email
        ";
        
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    /**
     * Buscar todos os usuários com paginação
     */
    public function getAll($page = 1, $limit = DEFAULT_PAGE_SIZE, $filters = []) {
        $offset = ($page - 1) * $limit;
        $whereClause = '';
        $params = [];
        
        if (!empty($filters['perfil'])) {
            $whereClause .= " AND u.perfil_id = :perfil";
            $params['perfil'] = $filters['perfil'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (u.nome ILIKE :search OR u.email ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.perfil_id,
                pu.nome as perfil_nome,
                u.created_at
            FROM usuarios u
            LEFT JOIN perfis_usuarios pu ON u.perfil_id = pu.id
            WHERE 1=1 {$whereClause}
            ORDER BY u.nome
            LIMIT :limit OFFSET :offset
        ";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Criar novo usuário
     */
    public function create($data) {
        $requiredFields = ['nome', 'email', 'senha'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo obrigatório: {$field}");
            }
        }
        
        // Verificar se email já existe
        if ($this->getByEmail($data['email'])) {
            throw new Exception("Email já cadastrado");
        }
        
        // Hash da senha
        $data['senha'] = hashPassword($data['senha']);
        
        // Definir perfil padrão se não especificado
        if (empty($data['perfil_id'])) {
            $data['perfil_id'] = 1; // Cliente
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('usuarios', $data);
    }
    
    /**
     * Atualizar usuário
     */
    public function update($id, $data) {
        // Se estiver atualizando a senha, fazer hash
        if (!empty($data['senha'])) {
            $data['senha'] = hashPassword($data['senha']);
        }
        
        // Se estiver atualizando o email, verificar se já existe
        if (!empty($data['email'])) {
            $existingUser = $this->getByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                throw new Exception("Email já cadastrado");
            }
        }
        
        return $this->db->update('usuarios', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Deletar usuário
     */
    public function delete($id) {
        return $this->db->delete('usuarios', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Verificar credenciais de login
     */
    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);
        
        if (!$user || !verifyPassword($password, $user['senha'])) {
            return false;
        }
        
        // Remover senha do retorno
        unset($user['senha']);
        
        return $user;
    }
    
    /**
     * Buscar montagens do usuário
     */
    public function getBuilds($userId, $page = 1, $limit = DEFAULT_PAGE_SIZE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                mp.*,
                COUNT(mi.id) as total_componentes
            FROM montagens_pc mp
            LEFT JOIN montagem_itens mi ON mp.id = mi.montagem_id
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
     * Buscar orçamentos do usuário
     */
    public function getBudgets($userId, $page = 1, $limit = DEFAULT_PAGE_SIZE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                o.*,
                COUNT(oi.id) as total_itens
            FROM orcamentos o
            LEFT JOIN orcamento_itens oi ON o.id = oi.orcamento_id
            WHERE o.usuario_id = :user_id
            GROUP BY o.id
            ORDER BY o.data_criacao DESC
            LIMIT :limit OFFSET :offset
        ";
        
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Buscar pedidos do usuário
     */
    public function getOrders($userId, $page = 1, $limit = DEFAULT_PAGE_SIZE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                p.*,
                COUNT(pl.id) as total_lojas
            FROM pedidos p
            LEFT JOIN pedido_loja pl ON p.id = pl.pedido_id
            WHERE p.usuario_id = :user_id
            GROUP BY p.id
            ORDER BY p.data_pedido DESC
            LIMIT :limit OFFSET :offset
        ";
        
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Buscar notificações do usuário
     */
    public function getNotifications($userId, $unreadOnly = false) {
        $whereClause = "WHERE usuario_id = :user_id";
        $params = ['user_id' => $userId];
        
        if ($unreadOnly) {
            $whereClause .= " AND lida = false";
        }
        
        $sql = "
            SELECT *
            FROM notificacoes
            {$whereClause}
            ORDER BY data_criacao DESC
        ";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Marcar notificação como lida
     */
    public function markNotificationAsRead($notificationId, $userId) {
        return $this->db->update(
            'notificacoes', 
            ['lida' => true], 
            'id = :id AND usuario_id = :user_id',
            ['id' => $notificationId, 'user_id' => $userId]
        );
    }
    
    /**
     * Criar notificação para usuário
     */
    public function createNotification($userId, $tipo, $mensagem) {
        $data = [
            'usuario_id' => $userId,
            'tipo' => $tipo,
            'mensagem' => $mensagem,
            'lida' => false,
            'data_criacao' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('notificacoes', $data);
    }
    
    /**
     * Buscar estatísticas do usuário
     */
    public function getStats($userId) {
        $sql = "
            SELECT 
                (SELECT COUNT(*) FROM montagens_pc WHERE usuario_id = :user_id) as total_montagens,
                (SELECT COUNT(*) FROM orcamentos WHERE usuario_id = :user_id) as total_orcamentos,
                (SELECT COUNT(*) FROM pedidos WHERE usuario_id = :user_id) as total_pedidos,
                (SELECT COUNT(*) FROM notificacoes WHERE usuario_id = :user_id AND lida = false) as notificacoes_nao_lidas,
                (SELECT COALESCE(SUM(total_pedido), 0) FROM pedidos WHERE usuario_id = :user_id AND status = 'concluido') as total_gasto
        ";
        
        return $this->db->fetchOne($sql, ['user_id' => $userId]);
    }
    
    /**
     * Contar total de usuários
     */
    public function count($filters = []) {
        $whereClause = '';
        $params = [];
        
        if (!empty($filters['perfil'])) {
            $whereClause .= " AND perfil_id = :perfil";
            $params['perfil'] = $filters['perfil'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (nome ILIKE :search OR email ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1 {$whereClause}";
        $result = $this->db->fetchOne($sql, $params);
        
        return $result['total'];
    }
    
    /**
     * Buscar perfis de usuário
     */
    public function getProfiles() {
        $sql = "SELECT * FROM perfis_usuarios ORDER BY nome";
        return $this->db->fetchAll($sql);
    }
}
?>
