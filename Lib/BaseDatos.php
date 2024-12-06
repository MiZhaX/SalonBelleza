<?php
    namespace Lib;
    use PDO;
    use PDOException;

    class BaseDatos{
        private PDO $conexion;
        private mixed $resultado;

        public function __construct(
            private $tipo_de_base = 'mysql',
            private string $servidor = SERVERNAME,  
            private string $usuario = USERNAME,
            private string $pass = PASSWORD,
            private string $base_datos = DATABASE) {
                $this->conexion = $this->conectar();
            }

            private function conectar(): PDO {
                try{
                    $opciones = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                        PDO::MYSQL_ATTR_FOUND_ROWS => true
                    );

                    $conexion = new PDO("mysql:host={$this->servidor};dbname={$this->base_datos}", $this->usuario, $this->pass, $opciones);
                    return $conexion;
                } catch(PDOException $e){
                    echo "Ha surgido un error y no se puede conectar a la base de datos. Detalle: ".$e->getMessage();
                    exit;
                }
            }

            public function prepare($pre){
                return $this->conexion->prepare($pre);
            }          
    }
?>