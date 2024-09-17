<?php

/**
 * CURSO TÉCNICO EM DESENVOLVIMENTO DE SISTEMAS
 * @author paulo.v.melo@ba.estudante.senai.br
 * @author davi.caridade@ba.estudante.senai.br
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

        $senhaHasheada = $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

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
    public function listar(): array
    {
        $pdo = "SELECT * FROM " . self::ENTIDADE;
        $estado = $this->connection->prepare($pdo);
        $estado->execute();
        return $estado->fetchAll();
    }

    /** BUSCAR POR ID DO USUÁRIO
     * @param $id
     * @return array|null
     */
    public function buscar($id): ?array
    {
        $pdo = "SELECT * FROM " . self::ENTIDADE ." WHERE id = :id";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':id', $id);
        $estado->execute();
        return $estado->fetch();
    }

    /** EDITAR USUÁRIO
     * @param string $nome
     * @param string $email
     * @param string $telefone
     * @param string $cpf
     * @param $id
     * @return bool
     */
    public function editar(string $nome, string $email, string $telefone, string $cpf, $id): bool
    {
        $pdo = "UPDATE " . self::ENTIDADE . " SET nome = :nome, email = :email, telefone = :telefone, cpf = :cpf, WHERE id = :id";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':nome', $this->nome);
        $estado->bindParam(':email', $this->email);
        $estado->bindParam(':telefone', $this->telefone);
        $estado->bindParam(':cpf', $this->cpf);
        return $estado->execute();
    }

    public function excluir($id): bool
    {
        $pdo = "DELETE FROM " . self::ENTIDADE ." WHERE id = :id";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':id', $id);
        return $estado->execute();
    }

    /** AUTENTICAÇÃO DE CADASTRO
     * @return bool
     */
    public function authCadastro(): bool
    {
        $pdo = "SELECT email FROM " . self::ENTIDADE . "WHERE email = :email";
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

        return true;
    }

    /** VALIDAÇÃO DOS CAMPOS DE LOGIN
     * @return bool
     */
    public function isLogin(): bool
    {
        return $this->isConfirmarSenha();
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
    public function isNome(): bool
    {
        return true;
    }

    /** VALIDAÇÃO E LIMPEZA DO CPF
     * @return bool
     */
    public function isCPF(string $cpf): bool
    {
        $this->cpf = $cpf;
        $this->cpf = filter_var($this->cpf, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->cpf = preg_replace('/[^0-9]/', '', $this->cpf);

        if (strlen($this->cpf) != 11) return false;

        for ($i = 9; $i < 11; $i++){
            for ($j = 0, $t = 0; $t < $i; $t++){
                $j += $cpf[$t] * (($i + 1) - $t);
            }
            $t = ((10 * $j) % 11) % 10;
            if ($cpf[$t] != $j) return false;
        }
        return true;
    }

    /** VALIDAÇÃO E LIMPEZA DE TELEFONE
     * @return bool
     */
    public function isTelefone(string $telefone): bool
    {
        $this->telefone = $telefone;
        $this->telefone = filter_var($this->telefone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->telefone = preg_replace('/[^0-9]/', '', $this->telefone);
        if (strlen($this->telefone) != 11) return false;
        return true;
    }

    /** VALIDAÇÃO E LIMPEZA DE E-MAIL
     * @return bool
     */
    public function isEmail(string $email): bool
    {
        return true;
    }

    /** VALIDAÇÃO E LIMPEZA DE SENHA
     * @return bool
     */
    public function isSenha(): bool
    {

        return true;
    }
}