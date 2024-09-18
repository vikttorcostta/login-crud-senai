<?php

/**
 * CURSO TÉCNICO EM DESENVOLVIMENTO DE SISTEMAS
 * @author paulo.v.melo@ba.estudante.senai.br
 * @author davi.caridade@ba.estudante.senai.br
 */

 require_once './src/classes/BancoDeDados.php';
 require_once './src/classes/UsuarioModel.php';

use UsuarioModel\UsuarioModel;



/**
 * CADASTRO
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'] ;
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];

    var_dump($nome, $telefone, $email, $cpf, $senha, $confirmarSenha);

    if (empty($nome) || empty($telefone) || empty($email) || empty($cpf) || empty($senha) || empty($confirmarSenha)) {
        echo "<script>alert('Todos os campos são obrigatórios.');</script>";
        //header('Location: /../../index.php');
        exit();
    }

    if ($senha !== $confirmarSenha) {
        echo "<script>alert('As senhas não coincidem.');</script>";
        exit();
    }

    $usuario = new UsuarioModel();
    if ($usuario->cadastrar($nome, $telefone, $email, $cpf, $senha)) {
        header('Location: ./public/system.php');
        exit();
    } else {
        echo "<script>alert('Erro ao cadastrar o usuário.');</script>";
    }
}
/**
 * LOGIN
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $email = $_GET['email'];
    $senha = $_GET['senha'];

    if (empty($email) || empty($senha)) {
        echo "<script>alert('E-mail e senha são obrigatórios.');</script>";
        exit();
    }

    $usuario = new UsuarioModel();

    if ($usuario->authLogin($email, $senha)) {
        header('Location: ./public/system.php');
        exit();
    } else {
        echo "<script>alert('E-mail ou senha inválidos.');</script>";
    }
}