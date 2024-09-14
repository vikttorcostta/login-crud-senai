<?php

include 'Connect.php';
include 'UsuarioModel.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

$pessoa = new UsuarioModel(
    $_POST['nome'],
    $_POST['email'],
    $_POST['telefone'],
    $_POST['cpf'],
    $_POST['senha'],
    $_POST['Email'],
    //$_POST['senhaLogin'],
    //$_POST['confirmarSenha']
);
    
    if($pessoa->insertCadastro()){
        header('Location: index.html?mensagem=success');
    }else{
        header('Location: index.html?mensagem=erros'.$pessoa->$fail);
    }
}
?>