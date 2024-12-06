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

    public function obtenerCitasPorCliente(int $idCliente): array
    {
        $query = $this->conexion->prepare("SELECT * FROM citas WHERE id_cliente = :id_cliente ORDER BY fecha, hora");
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
                $fila['fecha'],
                $fila['hora'],
                $fila['duracion_minutos'],
                $fila['estado']
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
                $row['fecha'],
                $row['hora'],
                $row['duracion_minutos'],
                $row['estado']
            );
        }

        return $citas;
    }

    public function crearCita(Cita $cita): bool
    {
        $query = $this->conexion->prepare("
            INSERT INTO citas (id_cliente, id_empleado, id_servicio, fecha, hora, duracion_minutos, estado) 
            VALUES (:id_cliente, :id_empleado, :id_servicio, :fecha, :hora, :duracion_minutos, :estado)
        ");
        $query->bindValue(':id_cliente', $cita->getIdCliente(), PDO::PARAM_INT);
        $query->bindValue(':id_empleado', $cita->getIdEmpleado(), PDO::PARAM_INT);
        $query->bindValue(':id_servicio', $cita->getIdServicio(), PDO::PARAM_INT);
        $query->bindValue(':fecha', $cita->getFecha(), PDO::PARAM_STR);
        $query->bindValue(':hora', $cita->getHora(), PDO::PARAM_STR);
        $query->bindValue(':duracion_minutos', $cita->getDuracionMinutos(), PDO::PARAM_INT);
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

    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $duracionMinutos)
{
    // Calcular la hora final para la nueva cita
    $horaFinal = date('H:i:s', strtotime("$hora +$duracionMinutos minutes"));

    // Imprimir valores para depuración
    var_dump("idEmpleado: ", $idEmpleado);
    var_dump("fecha: ", $fecha);
    var_dump("hora: ", $hora);
    var_dump("duracionMinutos: ", $duracionMinutos);
    var_dump("horaFinal: ", $horaFinal); // Hora final calculada

    // Consulta para verificar la disponibilidad del empleado
    $query = "
        SELECT COUNT(*) 
        FROM citas c
        INNER JOIN servicios s ON c.id_servicio = s.id
        WHERE c.id_empleado = :id_empleado
        AND c.fecha_cita = :fecha
        AND (
            -- Verificar si la nueva cita se superpone con una cita existente
            (c.hora_cita < :hora_final AND DATE_ADD(c.hora_cita, INTERVAL s.duracion_minutos MINUTE) > :hora)
            OR
            (c.hora_cita < :hora AND DATE_ADD(:hora, INTERVAL :duracion_minutos MINUTE) > c.hora_cita)
        )
    ";

    // Preparar la consulta
    $stmt = $this->conexion->prepare($query);
    $stmt->bindParam(':id_empleado', $idEmpleado, PDO::PARAM_INT);
    $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
    $stmt->bindParam(':hora_final', $horaFinal, PDO::PARAM_STR); // Hora final de la cita
    $stmt->bindParam(':duracion_minutos', $duracionMinutos, PDO::PARAM_INT); // Duración del servicio

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->fetchColumn(); 

    // Imprimir el resultado de la consulta para depuración
    var_dump("Result from DB: ", $result);

    // Si el resultado es 0, no hay citas en ese horario
    return $result == 0; 
}

}
