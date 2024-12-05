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

            // public function consulta(string $consultaSQL): void{
            //     $this->resultado = $this->conexion->query($consultaSQL);
            // }

            // public function extraer_registro(): mixed {
            //     return ($fila = $this->resultado->fetch(PDO::FETCH_ASSOC))? $fila:false;
            // }

            // public function extraer_todos(): array{
            //     return $this->resultado->fetchAll(PDO::FETCH_ASSOC);
            // }       
            
            // public function consultaPreparada(string $sql, array $params): bool {
            //     try {
            //         $stmt = $this->conexion->prepare($sql);
            //         return $stmt->execute($params);
            //     } catch (PDOException $e) {
            //         echo "Error al ejecutar la consulta: " . $e->getMessage();
            //         return false;
            //     }
            // }            
    }
?>