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
    private bool $confirmarSenha;
    private const ENTIDADE = 'usuario';
    private $fail;          

    public function __construct(string $nome = null, string $email = null, int $telefone = null, int $cpf = null, int $senha = null){
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->cpf = $cpf;
        $this->senha = $senha;
    }

    public function insertCadastro(): bool{
        // Implementar a lógica de inserção do usuário no banco de dados(Cadastro)

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

    public function isConfirmarSenha(): bool{
        // verifica se o segundo campo de senha corresponde o primeiro campo de senha preenchido
        if($this->senha === $this->confirmarSenha){
            return true;
        }else{
            return false;
        }
    }

    public function authCadastro(){
      // verifica se já existe um usuario cadastrado no banco com os dados inseridos no formulario de cadastro para efetuar a criação
    }

    public function authLogin(){
      // verifica se os dados inseridos no formulario de login correspondem com os dados cadastrados no banco para efetuar acesso
    }
  
    public function isCadastro(){
       // validação dos campos preenchidos no formulario de cadastro
    }

    public function isLogin(){
      // validação dos campos preenchidos no formulario de login
    }
 
    public function isCPF(){
        // validação do campo de cpf
        return true;
    }

}