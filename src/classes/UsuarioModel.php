<?php

/**
 * CURSO TÉCNICO EM DESENVOLVIMENTO DE SISTEMAS
 * @author paulo.v.melo@ba.estudante.senai.br
 * @author davi.caridade@ba.estudante.senai.br
 */

namespace UsuarioModel;

require_once 'BancoDeDados.php';

use BancoDeDados\BancoDeDados;
use PDO;



class UsuarioModel
{
    private string $nome;
    private string $email;
    private string $telefone;
    private string $cpf;
    private string $senha;
    private const ENTIDADE = 'usuario';
    private PDO $connection;

    public function __construct(){
       $this->connection = BancoDeDados::getInstance()->getConnection();
    }

    public function cadastrar(string $nome, string $telefone, string $email, string $cpf, string $senha): bool{

        $senha = password_hash($senha, PASSWORD_DEFAULT);

        $pdo = "INSERT INTO " .self::ENTIDADE ." (nome, telefone, email, cpf, senha) " . " VALUES(:nome, :telefone, :email, :cpf, :senha)";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':nome', $nome);
        $estado->bindParam(':telefone', $telefone);
        $estado->bindParam(':email', $email);
        $estado->bindParam(':cpf', $cpf);
        $estado->bindParam(':senha', $senha);
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
        $pdo = "SELECT * FROM " . self::ENTIDADE ." WHERE usuario_id = :id";
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
    public function editar(string $nome, string $telefone, string $email, string $cpf, $id): bool
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->cpf = $cpf;

        $pdo = "UPDATE " . self::ENTIDADE . " SET nome = :nome, telefone = :telefone, email = :email, cpf = :cpf WHERE id = :id";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':nome', $this->nome);
        $estado->bindParam(':telefone', $this->telefone);
        $estado->bindParam(':email', $this->email);
        $estado->bindParam(':cpf', $cpf);
        $estado->bindParam(':id', $id);
        return $estado->execute();
    }


    public function excluir($id): bool
    {
        $pdo = "DELETE FROM " . self::ENTIDADE ." WHERE usuario_id = :id";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':id', $id);
        return $estado->execute();
    }

    /** AUTENTICAÇÃO DE CADASTRO
     * @return bool
     */
    public function authCadastro(string $email): bool
    {
        $this->email = $email;

        $pdo = "SELECT email FROM " . self::ENTIDADE . " WHERE email = :email";
        $estado = $this->connection->prepare($pdo);
        $estado->bindParam(':email', $this->email);
        $estado->execute();
        if($estado->rowCount() > 0)return true;
        return false;
    }

    /** AUTENTICAÇÃO DE USUÁRIO
     * @param string $email
     * @param string $senha
     * @return bool
     */
    public function authLogin(string $email, string $senha): bool
    {
        $this->email = $email;
        $this->senha = $senha;

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
        if (empty($nome) || empty($email) || empty($telefone) || empty($cpf) || empty($senha) || empty($confirmarSenha)) return false;
        return true;
    }

    /** VALIDAÇÃO DA CONFIRMAÇÃO DE SENHA
     * @param string $confirmarSenha
     * @param string $senha
     * @return bool
     */
    public function isConfirmarSenha(string $confirmarSenha, string $senha): bool
    {
        $this->senha = $senha;
        if ($confirmarSenha != $this->senha) return true;
        return false;
    }

    /** VALIDAÇÃO E LIMPEZA DO CPF
     * @param string $cpf
     * @return bool
     */
    public function isCPF(string $cpf): bool
    {
        $this->cpf = $cpf;
        $this->cpf = filter_var($this->cpf, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->cpf = preg_replace('/[^0-9]/', '', $this->cpf);

        if (str_contains($this->cpf, ' ')) return false;
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
     * @param string $telefone
     * @return bool
     */
    public function isTelefone(string $telefone): bool
    {
        $this->telefone = $telefone;
        $this->telefone = filter_var($this->telefone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->telefone = preg_replace('/[^0-9]/', '', $this->telefone);

        if (str_contains($this->telefone, ' ')) return false;
        if (strlen($this->telefone) != 11) return false;
        return true;
    }

    /** VALIDAÇÃO E LIMPEZA DE E-MAIL
     * @param string $email
     * @return bool
     */
    public function isEmail(string $email): bool
    {
        $this->email = $email;
        $this->email = filter_var($this->email, FILTER_VALIDATE_EMAIL);

        if (str_contains($this->email, ' ')) return false;
        if (!$this->email) return false;
        return true;
    }

    /** VALIDAÇÃO DE SENHA
     */
    public function isSenha(string $senha, int $tamanhoMinimo = 8, int $tamanhoMaximo = 16): bool
    {
        $this->senha = $senha;
        if (str_contains($this->senha, ' ')) return false;
        if (strlen($this->senha) < $tamanhoMinimo) return false;
        if (strlen($this->senha) > $tamanhoMaximo) return false;
        return true;
    }
}