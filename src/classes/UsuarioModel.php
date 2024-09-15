<?php

namespace Senai\CrudPhp\classes;

use PDO;

class UsuarioModel
{
    private string $nome;
    private string $email;
    private int $telefone;
    private int $cpf;
    private int $senha;
    private bool $confirmarSenha;
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

    }

    public function checando(){

    }

    public function LoginUnico(){

    }

    public function checandoLogin(){

    }

}