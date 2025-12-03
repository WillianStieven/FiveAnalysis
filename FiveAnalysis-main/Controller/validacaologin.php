<?php
    //validar Login
    session_start();
    include '../Model/conexao.php';
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $sql = "SELECT u.* FROM usuarios u  WHERE u.email = :email";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($row && password_verify($senha, $row['senha_hash'])){
        $_SESSION['email'] = $email;
        $_SESSION['senha_hash'] = $row['senha_hash'];
        $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
        $_SESSION['nome'] = $row['nome'];
        $_SESSION['user_id'] = $row['id'];
        
        // Se for loja afiliada, verificar se está ativa
        if ($row['tipo_usuario'] === 'loja_afiliada') {
            if ($row['loja_status'] !== 'ativo') {
                unset($_SESSION['email']);
                unset($_SESSION['senha_hash']);
                unset($_SESSION['tipo_usuario']);
                unset($_SESSION['nome']);
                unset($_SESSION['user_id']);
                header('Location: ../View/AdminLogin.php?error=4');
                exit;
            }
            $_SESSION['loja_id'] = $row['loja_id'];
            $_SESSION['nome_loja'] = $row['nome_loja'];
        }
        
        // Redirecionar baseado no tipo de usuário
        if ($row['tipo_usuario'] === 'admin' || $row['tipo_usuario'] === 'loja_afiliada') {
            header('Location: ../View/AdminDashboard.php');
        } else {
            header('Location: ../View/PaginaInicial.php');
        }
    } else {
        unset($_SESSION['email']);
        unset($_SESSION['senha_hash']);
        unset($_SESSION['tipo_usuario']);
        unset($_SESSION['nome']);
        unset($_SESSION['user_id']);
        header('Location: ../View/Login.php');
    }
?>