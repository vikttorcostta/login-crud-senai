<?php

/**
 * CURSO TÉCNICO EM DESENVOLVIMENTO DE SISTEMAS
 * @author paulo.v.melo@ba.estudante.senai.br
 * @author davi.caridade@ba.estudante.senai.br1
 */

namespace Senai\CrudPhp\classes;
use PDO;

class UsuarioModel
{
    private string $nome;
    private string $email;
    private string $telefone;
    private string $cpf;
    private string $senha;
    private const string ENTIDADE = 'usuario';
    private PDO $connection;

    public function __construct(){

       $this->connection = BancoDeDados::getInstance()->getConnection();

    }

    public function cadastrar(string $nome, string $email, string $telefone, string $cpf, string $senha): bool{

        $senhaHasheada = password_hash($senha, PASSWORD_DEFAULT);

        $pdo = "INSERT INTO " .self::ENTIDADE ."(nome, email, telefone, cpf, senha)" . "VALUES(:nome, :email, :telefone, :cpf, :senha)";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':nome', $this->nome);
        $estado->bindParam(':email', $this->email);
        $estado->bindParam(':telefone', $this->telefone);
        $estado->bindParam(':cpf', $this->cpf);
        $estado->bindParam(':senha', $senhaHasheada);
        return $estado->execute();
    }

    /** LISTAGEM DE USUÁRIOS
     * @return array
     */
    public function listar(): array {
        $pdo = "SELECT * FROM " . self::ENTIDADE;
        $estado = $this->connection->prepare($pdo);
        $estado->execute();
        return $estado->fetchAll();
    }

    /** AUTENTICAÇÃO DE CADASTRO
     * @return bool
     */
    public function authCadastro(): bool
    {
        $pdo = "SELECT email FROM " . self::ENTIDADE . " WHERE email = :email";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':email', $this->email);
        $estado->execute();
        if($estado->rowCount() > 0)return true;
        return false;
    }

    /** AUTENTICAÇÃO DE USUÁRIO
     * @return bool
     */
    public function authLogin(): bool
    {
        $pdo = "SELECT email, senha FROM " . self::ENTIDADE . " WHERE email = :email";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':email', $this->email);
        $estado->execute();

        if ($estado->rowCount() > 0) {
            $usuario = $estado->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->senha, $usuario['senha'])) return true;
        }
        return false;
    }

    /** VALIDAÇÃO DOS CAMPOS DE CADASTRO
     * @param string $nome
     * @param string $email
     * @param string $telefone
     * @param string $cpf
     * @param string $senha
     * @param string $confirmarSenha
     * @return bool
     */
    public function isCadastro(string $nome, string $email, string $telefone, string $cpf, string $senha, string $confirmarSenha): bool
    {
        return $this->isNome($nome) && $this->isEmail($email) && $this->isTelefone($telefone) && $this->isCPF($cpf) && $this->isSenha($senha) && $senha === $confirmarSenha;
    }

    /** VALIDAÇÃO DOS CAMPOS DE LOGIN
     * @return bool
     */
    public function isLogin(): bool
    {
        return $this->authLogin();
    }

    /** VALIDAÇÃO DA CONFIRMAÇÃO DE SENHA
     * @param string $confirmarSenha
     * @return bool
     */
    public function isConfirmarSenha(string $confirmarSenha): bool
    {
        return $this->senha === $confirmarSenha;
    }

    /** VALIDAÇÃO E LIMPEZA DE NOME
     * @return bool
     */
    public function isNome(string $nome): bool
    {
        return !empty($nome) && strlen($nome) > 5;
    }

    /** VALIDAÇÃO E LIMPEZA DO CPF
     * @return bool
     */
    public function isCPF(string $cpf): bool
    {
        return preg_match("/^[0-9]{11}$/", $cpf);
    }

    /** VALIDAÇÃO E LIMPEZA DE TELEFONE
     * @return bool
     */
    public function isTelefone(string $telefone): bool
    {
        return preg_match("/^\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}$/", $telefone);
    }

    /** VALIDAÇÃO E LIMPEZA DE E-MAIL
     * @return bool
     */
    public function isEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /** VALIDAÇÃO E LIMPEZA DE SENHA
     * @return bool
     */
    public function isSenha(string $senha): bool
    {
        return strlen($senha) >= 6;
    }
}