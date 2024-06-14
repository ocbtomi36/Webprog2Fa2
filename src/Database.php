<?php 

class Database {
 
    public function __construct(private string $db_host, private string $db_name, private string $db_user, private string $db_pass) {
        
    }

    public function getConnection(): PDO  {
        $dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8";
        return new PDO($dsn, $this->db_user, $this->db_pass,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}