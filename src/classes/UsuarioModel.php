<?php

namespace Senai\CrudPhp\classes;

use PDO;

class UsuarioModel
{
    private string $nome;
    private string $email;
    private string $telefone;
    private string $cpf;
    private string $senha;
    private string $confirmarSenha;
    private const ENTIDADE = 'usuario';
    private $fail;
    private $db;
    
    //private string $password;
    private string $EmailLogin;

    public function __construct(string $nome = null, string $email = null, string $telefone = null, int $cpf = null, string $senha = null,string $EmailLogin = null, string $confirmarSenha = null){
        
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->cpf = $cpf;
        $this->senha = $senha;
        //$this->password = $password;
        $this->EmailLogin = $EmailLogin;
        $this->confirmarSenha = $confirmarSenha;
    }

    public function insertCadastro(): bool{
        // Implementar a lógica de inserção do usuário no banco de dados(Cadastro)

        if($this->checando()){
            $this->fail = 'Necessario preencher todos os campos acima';
            return false;
        }

        if(!$this->VerificarSenha()){
            $this->fail = 'As senhas não são iguais';
            return false;
        }

        if($this->cadastroUnico()){
            $this->fail = 'Alguns dos campos acima já foi cadastrados';
            return false;
        }

       

        // Hash da senha antes de armazenar no banco de dados
        $hashedPassword = password_hash($this->senha, PASSWORD_DEFAULT);

        $pdo = BancoDeDados::__construct();
        $res = $pdo->prepare(query: "INSERT INTO". self::ENTIDADE. "(nome,telefone,email,cpf,senha,confirmarSenha) VALUES (:nome,:telefone,:email,:cpf,:senha)");
        $res->bindParam(':nome', $this->nome);
        $res->bindParam(':telefone', $this->telefone);
        $res->bindParam(':email', $this->email);
        $res->bindParam(':cpf', $this->cpf);
        $res->bindParam(':senha', $hashedPassword);
        //$res->bindParam(':confirmarSenha', $this->confirmarSenha);
        return $res->execute();
        echo "Usuário inserido com sucesso!";
    }

    public static function listar(): array {
         // Implementar a lógica de listagem dos usuários no banco de dados
         $pdo = BancoDeDados::__construct();
         $res = $pdo->query("SELECT * FROM". self::ENTIDADE);
         return $res->fetchAll(PDO::FETCH_ASSOC);
         echo "Listando usuários!";
    }

    public function login(){
         // Implementar a lógica de login do usuário no banco de dados
         // Checar se os campos de login foram preenchidos
    if ($this->checandoLogin()) {
        $this->fail = 'Preencha todos os campos.';
        return false;
    }

    // Buscar o usuário no banco de dados com base no email
    $pdo = BancoDeDados::__construct();
    $res = $pdo->prepare("SELECT * FROM " . self::ENTIDADE . " WHERE email = :email");
    $res->bindParam(':email', $this->EmailLogin);
    $res->execute();

    // Se o usuário existir, verificar a senha
    if ($res->rowCount() === 1) {
        $usuario = $res->fetch(PDO::FETCH_ASSOC);
        
        // Verificar a senha usando password_verify
        if (password_verify($this->senha, $usuario['senha'])) {
            // Sucesso no login, aqui você pode iniciar uma sessão, se necessário
            echo "Login realizado com sucesso!";
            return true;
        } else {
            $this->fail = 'Senha incorreta.';
            return false;
        }
    } else {
        $this->fail = 'Usuário não encontrado.';
        return false;
    }
    }

    public function VerificarSenha(): bool{
        //Implementar a lógica de verificação de senha no banco de dados
        if($this->senha === $this->confirmarSenha){
            return true;
        }else{
            $this->fail = "Senhas não conferem!";
            return false;
        }
    }

    public function VerificarCPF(){
        //Implementar a lógica de verificação de senha no banco
        return true;
    }

    public function cadastrounico(){
        // Implementar a lógica de verificação se o email e CPF, e outros campos, já estão cadastrados no banco de dados
        $pdo = BancoDeDados::__construct();
        $res = $pdo->prepare("SELECT * FROM ".self::ENTIDADE."WHERE nome = :nome OR telefone = :telefone OR email = :email, cpf = :cpf OR senha = :senha");
        $res->bindValue(':nome', $this->nome);
        $res->bindValue(':telefone', $this->telefone);
        $res->bindValue(':email', $this->email);
        $res->bindValue(':cpf', $this->cpf);
        $res->bindValue(':senha', $this->senha);
        $res->execute();
        if($res->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function checando(){
        //Impede o usuario de deixar algum campo vazio na hota do cadastro
        if(empty($this->nome) || empty($this->telefone) || empty($this->email)  || empty($this->cpf)  || empty($this->senha) ){
            return true;
        }
        return false;
    }

    public function LoginUnico(){
        $pdo =  BancoDeDados::__construct();
        $res = $pdo->prepare("SELECT * FROM ".self::ENTIDADE." WHERE Email = :Email or senhaLogin = :senhaLogin");
        $res->bindValue(':Email', $this->email);
        $res->bindValue(':senhaLogin', $this->senha);
        $res->execute();
        if($res->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function checandoLogin(){
        // Impede o usuario de deixar algum campo vazio na hora do login
        if(empty($this->Email)  || empty($this->senhaLogin) ){
            return true;
        }
        return false;
    }

}