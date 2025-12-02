<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - FiveAnalysis</title>
    
    <!--Link CSS-->
    <link rel="stylesheet" href="../Style/Login.css">
    <!--Link Favicon-->
    <link rel="shortcut icon" href="../Assets/img/logotipo.png" type="image/x-icon">
    <!--Link Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Link Font Awesome-->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    
    <style>
        :root{
            --Input-Background: #131313;
            --Botão-Enviar: #0c43b1;
            --Botão-Hover: #0a3a9e;
            --Card-Background: rgba(17, 17, 17, 0.9);
            --Border-Color: #333333;
            --Text-Primary: #ffffff;
            --Text-Muted: #cccccc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: black;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.1;
            z-index: -1;
        }
        
        .admin-login-container {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }
        
        .admin-login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--Botão-Enviar), var(--Botão-Hover));
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .admin-header .admin-logo {
            height: 50px;
            margin: 0 auto 20px;
            display: block;
            transition: all 0.3s ease;
        }
        
        .admin-header .admin-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 40px rgba(12, 67, 177, 0.4);
        }
        
        .admin-header h1 {
            color: var(--Text-Primary);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .admin-header p {
            color: var(--Text-Muted);
            font-size: 14px;
        }
        
        .admin-badge {
            background: var(--Botão-Enviar);
            color: var(--Text-Primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--Text-Primary);
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: var(--Input-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 6px;
            color: var(--Text-Primary);
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input::placeholder {
            color: var(--Text-Muted);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--Botão-Enviar);
            box-shadow: 0 0 0 2px rgba(12, 67, 177, 0.2);
        }
        
        .admin-login-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--Botão-Enviar) 0%, var(--Botão-Hover) 100%);
            color: var(--Text-Primary);
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .admin-login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .admin-login-btn:hover::before {
            left: 100%;
        }
        
        .admin-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(12, 67, 177, 0.4);
        }
        
        .admin-login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--Border-Color);
        }
        
        .divider span {
            background: var(--Card-Background);
            padding: 0 15px;
            color: var(--Text-Muted);
            font-size: 14px;
        }
        
        .back-to-user {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-user a {
            color: var(--Text-Muted);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .back-to-user a:hover {
            color: var(--Botão-Enviar);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
            color: var(--Text-Muted);
        }
        
        .loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .admin-features {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--Border-Color);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .admin-features h3 {
            color: var(--Text-Primary);
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .admin-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-features li {
            color: var(--Text-Muted);
            font-size: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .admin-features li i {
            color: var(--Botão-Enviar);
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-header">
            <img src="../Assets/img/logotipo.png" alt="FiveAnalysis Logo" class="admin-logo">
            <div class="admin-badge">ÁREA ADMINISTRATIVA</div>
            <h1>Login Administrativo</h1>
            <p>Acesso restrito para lojas filiadas</p>
        </div>
        
        <div class="error-message" id="errorMessage">
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                switch ($error) {
                    case '1':
                        echo 'Você precisa fazer login para acessar esta área.';
                        break;
                    case '2':
                        echo 'Acesso negado. Esta área é restrita para administradores e lojas afiliadas.';
                        break;
                    case '3':
                        echo 'Sua loja não está ativa no sistema. Entre em contato com o suporte.';
                        break;
                    case '4':
                        echo 'Sua conta de loja afiliada está inativa ou suspensa.';
                        break;
                    default:
                        echo 'Erro de autenticação. Tente novamente.';
                }
            }
            ?>
        </div>
        <div class="success-message" id="successMessage"></div>
        
        <form id="adminLoginForm" action="../Controller/validacaologin.php" method="POST">
            <div class="form-group">
                <label for="email">Email Administrativo</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail administrativo" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="admin-login-btn" id="adminLoginBtn">
                <i class="fas fa-sign-in-alt"></i> Entrar como Administrador
            </button>
            
            <div class="loading" id="loading">
                <i class="fas fa-spinner fa-spin"></i> Verificando credenciais...
            </div>
        </form>
        
        <div class="admin-features">
            <h3>Recursos Administrativos</h3>
            <ul>
                <li><i class="fas fa-plus-circle"></i> Adicionar produtos</li>
                <li><i class="fas fa-edit"></i> Editar produtos</li>
                <li><i class="fas fa-trash"></i> Remover produtos</li>
                <li><i class="fas fa-chart-line"></i> Relatórios de vendas</li>
                <li><i class="fas fa-users"></i> Gerenciar clientes</li>
            </ul>
        </div>
        
        <div class="divider">
            <span>ou</span>
        </div>
        
        <div class="back-to-user">
            <a href="Login.php">
                <i class="fas fa-arrow-left"></i>
                Voltar ao login de usuário
            </a>
        </div>
    </div>
    
    <script src="../Assets/js/main.js"></script>
    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const adminLoginBtn = document.getElementById('adminLoginBtn');
            const loading = document.getElementById('loading');
            
            // Limpar mensagens anteriores
            showError('');
            showSuccess('');
            
            // Validar campos
            if (!email || !password) {
                showError('Por favor, preencha todos os campos.');
                return;
            }
            
            // Mostrar loading
            adminLoginBtn.disabled = true;
            loading.style.display = 'block';
            
            // O formulário será submetido normalmente para o PHP
            // Não precisamos prevenir o comportamento padrão
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = message ? 'block' : 'none';
            if (message) {
                setTimeout(() => {
                    errorDiv.style.display = 'none';
                }, 5000);
            }
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = message ? 'block' : 'none';
            if (message) {
                setTimeout(() => {
                    successDiv.style.display = 'none';
                }, 5000);
            }
        }
    </script>
</body>
</html>
