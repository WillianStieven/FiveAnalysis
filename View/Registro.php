<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FiveAnalisis</title>

   <!--Link CSS-->
    <link rel="stylesheet" href="../Style/Login.css">
    <!--Link Favicon-->
    <link rel="shortcut icon" href="../imagens/favicon.png" type="image/x-icon">
    <!--Link Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Link Font Awesome-->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    
</head>
<body>
    <!--Container Cadastro-->
    <div class="container-login">
        <div class="content-login">
            <form action="../Controller/validacaocadastro.php" method="POST">
                <img class="img-login" src="../Assets/img/Logotipo.png" alt="Logo FiveAnality">
                <span>Preencha abaixo suas informações para Cadastro.</span>
                <input type="email" name="email" placeholder="Digite seu e-mail" required>
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                <input type="submit" value="Entrar" class="btn-login">
                <a href="login.php">Já possui uma conta? Faça Login.</a>
            </form>
        </div>
    </div>
</body>
</html>