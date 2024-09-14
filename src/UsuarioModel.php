<?php

//namespace UsuarioModel;

use Connect;
use PDO;

class UsuarioModel {
    // Atributos do usuário(Cadastro)
    public string $nome;
    public int $telefone;
    public string $email;
    public string $cpf;
    public int $senha;
    public bool $confirmarSenha;
    private const ENTIDADE = 'Usuario';
    public $fail;

    // Atributos do usuário(Login)
    public string $Email;
    public string $senhaLogin;

    public function __construct($nome = null, $telefone = null, $email = null, $cpf = null, $senha = null, $confirmarSenha = null)
    {
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->cpf = $cpf;
        $this->senha = $senha;
        $this->confirmarSenha = $confirmarSenha;
    }

    public function insertCadastro(): bool{
        // Implementar a lógica de inserção do usuário no banco de dados(Cadastro)

        if(!$this->VerificarSenha()){
            $this->fail = 'As senhas não são iguais';
            return false;
        }

        if($this->cadastroUnico()){
            $this->fail = 'Alguns dos campos acima já foi cadastrados';
            return false;
        }

        if($this->checando()){
            $this->fail = 'Necessario preencher todos os campos acima';
            return false;
        }

        // Hash da senha antes de armazenar no banco de dados
        $hashedPassword = password_hash($this->senha, PASSWORD_DEFAULT);

        $pdo = Connect::getConection();
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

    public static function listar(): array{
        // Implementar a lógica de listagem dos usuários no banco de dados
        $pdo = Connect::getConection();
        $res = $pdo->query("SELECT * FROM". self::ENTIDADE);
        return $res->fetchAll(PDO::FETCH_ASSOC);
        echo "Listando usuários!";
    }

    public function login(): bool{
        // Implementar a lógica de login do usuário no banco de dados
        

        $pdo = Connect::getConection();
        $res = $pdo->prepare("INSERT INTO ".self::ENTIDADE."(Email,senhaLogin) VALUES (:Email, :senhaLogin)");
        $res->bindParam(':Email', $this->Email);
        $res->bindParam(':confirmarSenha', $this->senhaLogin);
        return $res->execute();
        echo "Login realizado com sucesso!";
    }

    public function VerificarSenha(): bool{
        //Implementar a lógica de verificação de senha no banco de dados
        if($this->senha === $this->confirmarSenha){
            return true;
        }else{
            $this->fail = 'Senhas não coincidem';
            return false;
        }
    }

    public function VerificarCPF(){
        // Implementar a lógica de verificação do CPF no banco de dados
        return true;
    }

    public function cadastroUnico(): bool{
        // Implementar a lógica de verificação se o email e CPF, e outros campos, já estão cadastrados no banco de dados
        $pdo = Connect::getConection();
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

    public function checando(): bool{
        //Impede o usuario de deixar algum campo vazio na hota do cadastro
        if(empty($this->nome) > 45 || empty($this->telefone) > 45 || empty($this->email) > 45 || empty($this->cpf) > 45 || empty($this->senha) > 45){
            return true;
        }
        if(strlen(string: $this->nome) > 45 || strlen(string: $this->telefone) > 45 || strlen(string: $this->email) > 45 || strlen(string: $this->cpf) || strlen(string: $this->senha)){
            return true;
        }
        return false;
    }

    /*
    public function LoginUnico():  bool{
        // Implementar a lógica de verificação de campos já cadastrados
        $pdo =  Connect::getConection();
        $res = $pdo->prepare("SELECT * FROM ".self::ENTIDADE." WHERE Email = :Email or senhaLogin = :senhaLogin");
        $res->bindValue(':Email', $this->Email);
        $res->bindValue(':senhaLogin', $this->senhaLogin);
        $res->execute();
        if($res->rowCount() > 0){
            return true;
        }
        return false;
    }
    */

    public function checandoLogin(): bool{
        // Impede o usuario de deixar algum campo vazio na hora do login
        if(empty($this->Email) > 45 || empty($this->senhaLogin) > 45){
            return true;
        }
        if(strlen(string: $this->Email) > 45 || strlen(string: $this->senhaLogin) > 45){
            return true;
        }
        return false;
    }

}