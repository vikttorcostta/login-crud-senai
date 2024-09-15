<?php

namespace Senai\CrudPhp\classes;

use PDO;

class UsuarioModel
{
    private string $nome;
    private string $email;
    private string $telefone;
    private int $cpf;
    private string $senha;
    private string $confirmarSenha;
    private const ENTIDADE = 'usuario';
    private $fail;
    
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

        if(!$this->checando()){
            $this->fail = 'Necessario preencher todos os campos acima';
            return false;
        }

        if(!$this->VerificarSenha()){
            //$this->fail;
            return false;
        }

        if(!$this->cadastrounico()){
            $this->fail = 'Alguns Campos acima não foram cadastrados';
            return false;
        }

       

        //Hash da senha antes de armazenar no banco de dados
        $hashedPassword = password_hash($this->senha, PASSWORD_DEFAULT);

        $pdo = BancoDeDados::__construct();
        $res = $pdo->prepare("INSERT INTO " . self::ENTIDADE . " (nome, telefone, email, cpf, senha) VALUES (:nome, :telefone, :email, :cpf, :senha)");
        $res->bindParam(':nome', $this->nome);
        $res->bindParam(':telefone', $this->telefone);
        $res->bindParam(':email', $this->email);
        $res->bindParam(':cpf', $this->cpf);
        $res->bindParam(':senha', $hashedPassword);
        return $res->execute();
    }

    public static function listar(): array {
         // Implementar a lógica de listagem dos usuários no banco dedados
        $pdo = BancoDeDados::__construct();
        $res = $pdo->query("SELECT * FROM". self::ENTIDADE);
        return $res->fetchALL(PDO::FETCH_ASSOC);
    }

    public function login(){
         // Implementar a lógica de login do usuário no banco de dados

         if($this->checandoLogin()){
             $this->fail = "Preencha todos os campos";
             return false;
         }

         $pdo = BancoDeDados::__construct();
         $res = $pdo->prepare("SELECT * FROM ".self::ENTIDADE."WHERE email = :email");
         $res->bindParam(':email', $this->EmailLogin);
         //$res->bindParam(':confirmarSenha', $this->password);
         return $res->execute();

         if($res->rowCount() === 1){
            $usuario = $res->fetch(PDO::FETCH_ASSOC);

            //Verificar a senha com password_Verifty()
            if(password_verify($this->senha, $usuario['senha'])){
                echo "Login realizado com sucesso!";
                return true;
            }else{
                $this->fail = "Senha incorreta!";
                return false;
            }
         }else{
             $this->fail = "Email não encontrado!";
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

    public function VerificarCPF(): bool{
        //Implementar a lógica de verificação de senha no banco
        
        return true;
    }

    public function cadastrounico(): bool{
         // Implementar a lógica de verificação se o email e CPF, e outros campos, já estão cadastrados no banco de dados
         
         $pdo = BancoDeDados::__construct();
         $res = $pdo->prepare("SELECT * FROM". self::ENTIDADE . " WHERE nome = :nome OR telefone = :telefone OR email :email OR cpf :cpf OR senha :senha");
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
        if (empty($this->nome) || empty($this->telefone) || empty($this->email) || empty($this->cpf) || empty($this->senha)) {
            return false;
        }
        return false;
    }

    public function LoginUnico(){
         // Implementar a lógica de verificação de campos já cadastrados
        $pdo = BancoDeDados::__construct();
        $res = $pdo->prepare("SELECT * FROM". self::ENTIDADE . " WHERE EmailLogin = :EmailLogin OR password = :password");
        $res->bindValue(':EmailLogin', $this->EmailLogin);
        $res->bindValue(':password', $this->senha);
        $res->execute();
        if($res->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function checandoLogin(){
        // Impede o usuario de deixar algum campo vazio na hora do login
        if(empty($this->EmailLogin) || empty($this->senha)){
            return true;
        }
        if(strlen(string: $this->EmailLogin) || strlen(string: $this->senha)){
            return true;
        }
        return false;
    }

}