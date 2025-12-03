<?php
    // Cria o Cadastro
    session_start();
    include 'conexao.php';
    $email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$sql = "INSERT INTO usuarios (email, senha_hash) VALUES (:email, :senha_hash)";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':senha_hash', $senha);
if($stmt->execute()){
    $_SESSION['email'] = $email;
    $_SESSION['senha_hash'] = $senha;
    header('Location: inicio.php');
} else {
    unset($_SESSION['email']);
    unset($_SESSION['senha_hash']);
    header('Location: cadastro.php');
}
?>