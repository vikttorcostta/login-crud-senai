<?php

namespace Senai\CrudPhp\classes;
require_once 'vendor/autoload.php';


use PDOException;
use PDO;

class BancoDeDados
{
    private string $database = DBNAME;
    private string $hostname = HOSTNAME;
    private string $username = USERNAME;
    private string $password = PASSWORD;
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct(){
        try {
            $this->pdo = new PDO("mysql:host=$this->hostname;dbname=$this->database", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Banco conectado";
        }catch (PDOException $e){
            echo "Erro ao conectar banco de dados" . $e->getMessage();
        }
    }
    public static function getInstance(): self{
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO{
        return $this->pdo;
    }

    private function __clone(){

    }
}