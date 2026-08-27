<?php

class Database
{
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $conexion;

    public function __construct()
    {
        $env = parse_ini_file(__DIR__ . "/../.env");

        $this->host = $env['DB_HOST'];
        $this->port = $env['DB_PORT'];
        $this->dbname = $env['DB_NAME'];
        $this->username = $env['DB_USER'];
        $this->password = $env['DB_PASSWORD'];

        $this->conectar();
    }

    private function conectar()
    {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->conexion;
    }
}