<?php

namespace Repositories;

use Models\Cita;
use Lib\BaseDatos;
use PDO;

class CitaRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Obtener todas las citas
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM citas";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $citasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $citas = [];

        // Crear una nueva cita y almacenarla por cada fila
        foreach ($citasData as $citaData) {
            $cita = new Cita(
                id: $citaData['id'],
                idCliente: $citaData['id_cliente'],
                idEmpleado: $citaData['id_empleado'],
                idServicio: $citaData['id_servicio'],
                fecha: $citaData['fecha_cita'],
                hora: $citaData['hora_cita'],
                estado: $citaData['estado'],
                detalles: $citaData['detalles']
            );
            $citas[] = $cita;
        }

        return $citas;
    }

    // Obtener todas las citas de un cliente
    public function obtenerCitasPorCliente(int $idCliente): array
    {
        $stmt = $this->conexion->prepare("SELECT * FROM citas WHERE id_cliente = :id_cliente ORDER BY fecha_cita, hora_cita");
        $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $citas = [];

        // Crear y almacenar una cita por cada fila
        foreach ($resultados as $fila) {
            $citas[] = new Cita(
                $fila['id'],
                $fila['id_cliente'],
                $fila['id_empleado'],
                $fila['id_servicio'],
                $fila['fecha_cita'],
                $fila['hora_cita'],
                $fila['estado'],
                $fila['detalles']
            );
        }

        return $citas;
    }

    // Obtener todas las citas de un empleado
    public function obtenerCitasPorEmpleado(int $idEmpleado): array
    {
        $stmt = $this->conexion->prepare("SELECT * FROM citas WHERE id_empleado = :id_empleado ORDER BY fecha_cita, hora_cita");
        $stmt->bindParam(':id_empleado', $idEmpleado, PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $citas = [];

        // Crear y almacenar una cita por cada fila
        foreach ($resultados as $fila) {
            $citas[] = new Cita(
                $fila['id'],
                $fila['id_cliente'],
                $fila['id_empleado'],
                $fila['id_servicio'],
                $fila['fecha_cita'],
                $fila['hora_cita'],
                $fila['estado'],
                $fila['detalles']
            );
        }

        return $citas;
    }

    // Obtener la cita por su Id
    public function obtenerPorId(string $id): ?Cita
    {
        $sql = "SELECT * FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Crear y alamcenar la cita con los datos obtenidos
        if ($resultado) {
            $cita = new Cita();
            $cita->setId($resultado['id']);
            $cita->setIdCliente($resultado['id_cliente']);
            $cita->setIdEmpleado($resultado['id_empleado']);
            $cita->setIdServicio($resultado['id_servicio']);
            $cita->setFecha($resultado['fecha_cita']);
            $cita->setHora($resultado['hora_cita']);
            $cita->setEstado($resultado['estado']);
            $cita->setDetalles($resultado['detalles']);
            return $cita;
        }

        return null;
    }

    // Obtener el último Id de Cita insertado
    public function obtenerUltimoIdInsertado(): int
    {
        return $this->conexion->lastInsertId();
    }

    // Crear una cita
    public function crearCita(Cita $cita): bool
    {
        $query = $this->conexion->prepare("
            INSERT INTO citas (id_cliente, id_empleado, id_servicio, fecha_cita, hora_cita, estado) 
            VALUES (:id_cliente, :id_empleado, :id_servicio, :fecha, :hora, :estado)
        ");

        // Enlazar los datos del Cliente con los parámetros
        $query->bindValue(':id_cliente', $cita->getIdCliente(), PDO::PARAM_INT);
        $query->bindValue(':id_empleado', $cita->getIdEmpleado(), PDO::PARAM_INT);
        $query->bindValue(':id_servicio', $cita->getIdServicio(), PDO::PARAM_INT);
        $query->bindValue(':fecha', $cita->getFecha(), PDO::PARAM_STR);
        $query->bindValue(':hora', $cita->getHora(), PDO::PARAM_STR);
        $query->bindValue(':estado', $cita->getEstado(), PDO::PARAM_STR);

        return $query->execute();
    }

    // Actualizar el estado de una cita
    public function actualizarEstadoCita(int $idCita, string $nuevoEstado): bool
    {
        $sql = "UPDATE citas SET estado = :estado WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);

        // Vincular parámetros con lel nuevo estado
        $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idCita, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Verificar si el empleado tiene alguna cita establecida en la fecha y hora dadas como parámetros
    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $idServicio): bool
    {
        // Consulta SQL 
        $query = "
                SELECT COUNT(*) 
                FROM citas c
                INNER JOIN servicios s ON c.id_servicio = s.id
                WHERE c.id_empleado = :id_empleado
                AND c.fecha_cita = :fecha
                AND c.estado != 'cancelada'
                AND (
                    -- Caso 1: La nueva cita comienza dentro de una cita existente
                    (:hora BETWEEN c.hora_cita AND DATE_ADD(c.hora_cita, INTERVAL s.duracion_minutos MINUTE))
                    OR
                    -- Caso 2: La nueva cita termina dentro de una cita existente
                    (DATE_ADD(:hora, INTERVAL (SELECT duracion_minutos FROM servicios WHERE id = :id_servicio) MINUTE)
                    BETWEEN c.hora_cita AND DATE_ADD(c.hora_cita, INTERVAL s.duracion_minutos MINUTE))
                    OR
                    -- Caso 3: La nueva cita engloba completamente una cita existente
                    (c.hora_cita BETWEEN :hora AND DATE_ADD(:hora, INTERVAL (SELECT duracion_minutos FROM servicios WHERE id = :id_servicio) MINUTE))
                )";

        $stmt = $this->conexion->prepare($query);

        // Vincular parámetros con los datos
        $stmt->bindParam(':id_empleado', $idEmpleado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':id_servicio', $idServicio, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        // Si el resultado es 0, no hay ninguna cita que intervenga en el horario
        return $result == 0;
    }

    // Verificar si el cliente tiene alguna cita establecida en la fecha y hora dadas como parámetros
    public function verificarDisponibilidadCliente(int $idCliente, string $fecha, string $hora, int $idServicio): bool
    {
        // Consulta SQL
        $query = "
                SELECT COUNT(*) 
                FROM citas c
                INNER JOIN servicios s ON c.id_servicio = s.id
                WHERE c.id_cliente = :id_cliente
                AND c.fecha_cita = :fecha
                AND c.estado != 'cancelada'
                AND (
                    -- Caso 1: La nueva cita comienza dentro de una cita existente
                    (:hora BETWEEN c.hora_cita AND DATE_ADD(c.hora_cita, INTERVAL s.duracion_minutos MINUTE))
                    OR
                    -- Caso 2: La nueva cita termina dentro de una cita existente
                    (DATE_ADD(:hora, INTERVAL (SELECT duracion_minutos FROM servicios WHERE id = :id_servicio) MINUTE)
                    BETWEEN c.hora_cita AND DATE_ADD(c.hora_cita, INTERVAL s.duracion_minutos MINUTE))
                    OR
                    -- Caso 3: La nueva cita engloba completamente una cita existente
                    (c.hora_cita BETWEEN :hora AND DATE_ADD(:hora, INTERVAL (SELECT duracion_minutos FROM servicios WHERE id = :id_servicio) MINUTE))
                )";

        $stmt = $this->conexion->prepare($query);

        // Vincular parámetros con los datos
        $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':id_servicio', $idServicio, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        // Si el resultado es 0, no hay ninguna cita que intervenga en el horario
        return $result == 0;
    }

    // Completar una cita
    public function finalizarCita(int $idCita, string $detalles): bool
    {
        // Obtener la cita por el Id
        $cita = $this->obtenerPorId($idCita);

        if ($cita) {
            // Actualizar el estado a "completada" y los detalles
            $query = "UPDATE citas SET estado = 'completada', detalles = :detalles WHERE id = :id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindValue(':detalles', $detalles, PDO::PARAM_STR);
            $stmt->bindValue(':id', $idCita, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    // Eliminar una cita
    public function eliminarCita(int $idCita): bool
    {
        $query = "DELETE FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $idCita, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
