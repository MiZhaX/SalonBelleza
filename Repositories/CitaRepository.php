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

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM citas";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $citasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $citas = [];

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

    public function obtenerCitasPorCliente(int $idCliente): array
    {
        $query = $this->conexion->prepare("SELECT * FROM citas WHERE id_cliente = :id_cliente ORDER BY fecha_cita, hora_cita");
        $query->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $query->execute();
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

        $citas = [];
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

    public function obtenerCitasPorEmpleado(int $idEmpleado): array
    {
        $sql = "SELECT * FROM citas WHERE id_empleado = :id_empleado";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_empleado', $idEmpleado, PDO::PARAM_INT);
        $stmt->execute();

        $citas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $citas[] = new Cita(
                $row['id'],
                $row['id_cliente'],
                $row['id_empleado'],
                $row['id_servicio'],
                $row['fecha_cita'],
                $row['hora_cita'],
                $row['estado'],
                $row['detalles']
            );
        }

        return $citas;
    }

    public function obtenerPorId(string $id): ?Cita
    {
        $sql = "SELECT * FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

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

    public function obtenerUltimoIdInsertado(): int
    {
        return $this->conexion->lastInsertId();
    }

    public function crearCita(Cita $cita): bool
    {
        $query = $this->conexion->prepare("
            INSERT INTO citas (id_cliente, id_empleado, id_servicio, fecha_cita, hora_cita, estado) 
            VALUES (:id_cliente, :id_empleado, :id_servicio, :fecha, :hora, :estado)
        ");
        $query->bindValue(':id_cliente', $cita->getIdCliente(), PDO::PARAM_INT);
        $query->bindValue(':id_empleado', $cita->getIdEmpleado(), PDO::PARAM_INT);
        $query->bindValue(':id_servicio', $cita->getIdServicio(), PDO::PARAM_INT);
        $query->bindValue(':fecha', $cita->getFecha(), PDO::PARAM_STR);
        $query->bindValue(':hora', $cita->getHora(), PDO::PARAM_STR);
        $query->bindValue(':estado', $cita->getEstado(), PDO::PARAM_STR);

        return $query->execute();
    }

    public function actualizarEstadoCita(int $idCita, string $nuevoEstado): bool
    {
        $sql = "UPDATE citas SET estado = :estado WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);

        // Vincular parámetros
        $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idCita, PDO::PARAM_INT);

        // Ejecutar la consulta y devolver si la actualización fue exitosa
        return $stmt->execute();
    }

    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $idServicio): bool
    {
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

        // Preparar la consulta
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_empleado', $idEmpleado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':id_servicio', $idServicio, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetchColumn();

        // Si el resultado es 0, no hay conflictos en el horario
        return $result == 0;
    }

    public function verificarDisponibilidadCliente(int $idCliente, string $fecha, string $hora, int $idServicio): bool
    {
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

        // Preparar la consulta
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':id_servicio', $idServicio, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetchColumn();

        // Si el resultado es 0, no hay conflictos en el horario
        return $result == 0;
    }

    public function finalizarCita(int $idCita, string $detalles): bool
    {
        // Obtener la cita
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

    public function eliminarCita(int $idCita): bool
    {
        $query = "DELETE FROM citas WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $idCita, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
