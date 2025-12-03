<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FiveAnalysis</title>

    <!--Link CSS-->
    <link rel="stylesheet" href="../Style/Login.css">
    <!--Link Favicon-->
    <link rel="shortcut icon" href="../Assets/img/logotipo.png" type="image/x-icon">
    <!--Link Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Link Font Awesome-->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    
    <style>
        .login-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .login-option {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #333333;
            border-radius: 16px;
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .login-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0c43b1, #0a3a9e);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .login-option:hover::before {
            transform: scaleX(1);
        }
        
        .login-option:hover {
            background: rgba(12, 67, 177, 0.1);
            border-color: #0c43b1;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(12, 67, 177, 0.2);
        }
        
        .login-option h3 {
            color: #ffffff;
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-option p {
            color: #cccccc;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .login-option a {
            color: #0c43b1;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid #0c43b1;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .login-option a:hover {
            background: #0c43b1;
            color: #ffffff;
            transform: scale(1.05);
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
            background: #333333;
        }
        
        .divider span {
            background: #000000;
            padding: 0 15px;
            color: #cccccc;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .login-options {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .login-option {
                padding: 20px 15px;
            }
            
            .login-option h3 {
                font-size: 16px;
            }
            
            .login-option a {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <!--Container Login-->
    <div class="container-login">
        <div class="content-login">
            <form action="../Controller/validacaologin.php" method="POST">
                <img class="img-login" src="../Assets/img/logotipo.png" alt="Logo FiveAnalysis">
                <span>Faça Login abaixo e acesse sua conta.</span>
                <input type="email" name="email" placeholder="Digite seu e-mail" required>
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                <input type="submit" value="Entrar" class="btn-login">
                
                <div class="divider">
                    <span>ou</span>
                </div>
                
                <div class="login-options">
                    <div class="login-option">
                        <h3><i class="fas fa-user-plus"></i> Novo Usuário</h3>
                        <p>Primeira vez aqui? Crie sua conta e comece a montar seu computador dos sonhos.</p>
                        <a href="Registro.php">Criar Conta</a>
                    </div>
                    
                    <div class="login-option">
                        <h3><i class="fas fa-user-shield"></i>Loja Filiada</h3>
                        <p>Lojas filiadas e administradores podem acessar o painel administrativo aqui.</p>
                        <a href="AdminLogin.php">Acessar loja Filiada</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>