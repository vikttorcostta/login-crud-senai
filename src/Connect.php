<?php

class Connect{
    private const HOST = 'localhost';
    private const DBNAME = 'crudLoginPHP';
    private const USER = 'root';
    private const PASS = '';
    
    private static $instance;
    private static $fail;

    private function __construct()
    {
        
    }

    public static function getConection(): ?PDO{
        
        if (empty(self::$instance)) {            
            try {
                self::$instance = new PDO("mysql:host=" . self::HOST .
                    ";dbname=" . self::DBNAME,
                    self::USER,
                    self::PASS);                
            } catch (\PDOException $exception) {
                self::$fail = $exception;
            }
        }
        return self::$instance;            
    }
}


?>