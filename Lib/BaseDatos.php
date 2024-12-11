<?php

namespace Lib;

use PDO;
use PDOException;

class BaseDatos
{
    private PDO $conexion;
    private string $tipo_de_base;
    private string $servidor;
    private string $usuario;
    private string $pass;
    private string $base_datos;
    
    public function __construct() 
    {
        $this->tipo_de_base = 'mysql';
        $this->servidor = $_ENV['SERVERNAME'];
        $this->usuario = $_ENV['USERNAME'];
        $this->pass = $_ENV['PASSWORD'];
        $this->base_datos = $_ENV['DATABASE'];
        $this->conexion = $this->conectar();
    }

    private function conectar(): PDO
    {
        try {
            $opciones = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::MYSQL_ATTR_FOUND_ROWS => true
            );

            $conexion = new PDO("mysql:host={$this->servidor};dbname={$this->base_datos}", $this->usuario, $this->pass, $opciones);
            return $conexion;
        } catch (PDOException $e) {
            echo "Ha surgido un error y no se puede conectar a la base de datos. Detalle: " . $e->getMessage();
            exit;
        }
    }

    public function prepare($pre)
    {
        return $this->conexion->prepare($pre);
    }

    public function lastInsertId(): int
    {
        return (int) $this->conexion->lastInsertId();
    }
}
