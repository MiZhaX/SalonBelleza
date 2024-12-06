<?php
namespace Models;

class Servicio
{
    private int $id;
    private string $nombre;
    private float $precio;
    private int $duracionMinutos;
    private int $idEspecialidad;

    public function __construct(?int $id = 0, string $nombre = '', float $precio = 0.0, int $duracionMinutos = 0, int $idEspecialidad = 0)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->duracionMinutos = $duracionMinutos;
        $this->idEspecialidad = $idEspecialidad;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getDuracionMinutos(): int
    {
        return $this->duracionMinutos;
    }

    public function getIdEspecialidad(): int
    {
        return $this->idEspecialidad;
    }

    // Setters
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }

    public function setDuracionMinutos(int $duracionMinutos): void
    {
        $this->duracionMinutos = $duracionMinutos;
    }

    public function setIdEspecialidad(int $idEspecialidad): void
    {
        $this->idEspecialidad = $idEspecialidad;
    }
}
