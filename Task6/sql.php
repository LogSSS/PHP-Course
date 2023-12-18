<?php
class SQL
{
    private $host = "localhost";
    private $port = "5432";
    private $db = "postgres";
    private $user = "postgres";
    private $password = "1231";

    public function getConnect()
    {
        try {
            $conn = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->db;user=$this->user;password=$this->password");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
