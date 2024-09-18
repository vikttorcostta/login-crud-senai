<?php

/**
 * CURSO TÉCNICO EM DESENVOLVIMENTO DE SISTEMAS
 * @author paulo.v.melo@ba.estudante.senai.br
 * @author davi.caridade@ba.estudante.senai.br
 */


namespace BancoDeDados;

require_once './src/configuracoes/configuracoes.php';
require_once './src/classes/UsuarioModel.php';


use PDOException;
use PDO;


class BancoDeDados
{

    private string $database = DBNAME;
    private string $servername = HOSTNAME;
    private string $username = USERNAME;
    private string $password = PASSWORD;
    private static ?self $instance = null;
    private PDO $connection;


    private function __construct()
    {

        try {
            $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Banco de dados conectado com sucesso!";
            var_dump($this->connection);
        } catch (PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

    public static function getInstance(): BancoDeDados
    {

        if (self::$instance === null) {

            self::$instance = new BancoDeDados();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {

        return $this->connection;

    }

    private function __clone()
    {

    }
}
