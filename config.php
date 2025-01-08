<?php
class Database
{
    private $host = "localhost";
    private $db_name = "todoapp";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
