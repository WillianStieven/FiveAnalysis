<?php
    //validar Login
    session_start();
    include '../Model/conexao.php';
$email = $_POST['email'];
$senha = $_POST['senha'];
$sql = "SELECT * FROM usuarios WHERE email = :email";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row && password_verify($senha, $row['senha_hash'])){
    $_SESSION['email'] = $email;
    $_SESSION['senha_hash'] = $row['senha_hash'];
    header('Location: ../View/Paginainicial.php');
} else {
    unset($_SESSION['email']);
    unset($_SESSION['senha_hash']);
    header('Location: ../View/Login.php');
}
?>