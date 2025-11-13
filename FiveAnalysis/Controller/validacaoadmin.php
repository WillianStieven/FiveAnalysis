<?php
    // Validação de Login Administrativo / Loja Afiliada
    session_start();
    include '../Model/conexao.php';
    
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Verificar se os campos foram preenchidos
    if (empty($email) || empty($senha)) {
        $_SESSION['admin_error'] = 'Por favor, preencha todos os campos.';
        header('Location: ../View/AdminLogin.php');
        exit;
    }
    
    try {
        $loja = null;
        
        // Primeiro, tentar verificar se existe na tabela de lojas afiliadas
        try {
            $sql = "SELECT * FROM lojas_afiliadas WHERE email = :email AND ativo = true";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $loja = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Tabela pode não existir ainda, continuar para próxima verificação
            error_log('Tabela lojas_afiliadas não encontrada ou erro: ' . $e->getMessage());
        }
        
        // Se não encontrou na tabela de lojas, verificar na tabela de usuários com permissão admin
        if (!$loja) {
            try {
                // Verificar se a coluna tipo_usuario existe
                $sql = "SELECT * FROM usuarios WHERE email = :email";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Se encontrou usuário, verificar se é admin
                if ($usuario) {
                    // Verificar se tem coluna tipo_usuario e se é admin
                    if (isset($usuario['tipo_usuario']) && $usuario['tipo_usuario'] === 'admin') {
                        $loja = $usuario;
                    } elseif (!isset($usuario['tipo_usuario'])) {
                        // Se a coluna não existe, verificar se o email contém 'admin' como fallback temporário
                        if (stripos($email, 'admin') !== false) {
                            $loja = $usuario;
                        }
                    }
                }
            } catch (PDOException $e) {
                error_log('Erro ao verificar usuário: ' . $e->getMessage());
            }
        }
        
        // Verificar credenciais
        if ($loja && isset($loja['senha_hash']) && password_verify($senha, $loja['senha_hash'])) {
            // Login bem-sucedido
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_id'] = $loja['id'] ?? $loja['id_usuario'] ?? null;
            $_SESSION['admin_nome'] = $loja['nome'] ?? $loja['nome_loja'] ?? 'Administrador';
            $_SESSION['admin_tipo'] = isset($loja['tipo_usuario']) ? 'admin' : 'loja_afiliada';
            
            // Limpar erros anteriores
            unset($_SESSION['admin_error']);
            
            header('Location: ../View/AdminDashboard.php');
            exit;
        } else {
            // Credenciais inválidas
            $_SESSION['admin_error'] = 'Email ou senha incorretos. Verifique suas credenciais.';
            header('Location: ../View/AdminLogin.php');
            exit;
        }
    } catch (PDOException $e) {
        // Erro de conexão com o banco
        $_SESSION['admin_error'] = 'Erro de conexão com o banco de dados: ' . $e->getMessage();
        error_log('Erro de login admin: ' . $e->getMessage());
        header('Location: ../View/AdminLogin.php');
        exit;
    } catch (Exception $e) {
        // Outros erros
        $_SESSION['admin_error'] = 'Erro inesperado. Tente novamente mais tarde.';
        error_log('Erro geral de login admin: ' . $e->getMessage());
        header('Location: ../View/AdminLogin.php');
        exit;
    }
?>

