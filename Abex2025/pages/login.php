<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FiveAnalisis</title>

    <!--Link CSS-->
    <link rel="stylesheet" href="../css/login.css">
    <!--Link Favicon-->
    <link rel="shortcut icon" href="../imagens/favicon.png" type="image/x-icon">
    <!--Link Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Link Font Awesome-->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    
</head>
<body>
    <!--Container Login-->
    <div class="container-login">
        <div class="content-login">
            <form action="../pages/validacaologin.php" method="POST">
                <img class="img-login" src="../img/logotipo.png" alt="Logo FiveAnality">
              <span>Faça Login abaixo e acesse sua conta.</span>
                <input type="email" name="email" placeholder="Digite seu e-mail" required>
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                <input type="submit" value="Entrar" class="btn-login">
                <a href="cadastro.php">Ainda não possui uma conta? Cadastre-se.</a>
            </form>
        </div>
    </div>
</body>
</html>