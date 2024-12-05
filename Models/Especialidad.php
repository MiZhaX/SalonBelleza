<?php
namespace Models;

class Especialidad
{
    private $id;
    private $nombre;

    public function __construct($id = '', $nombre = '')
    {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    // Getter para obtener el ID
    public function getId(): int
    {
        return $this->id;
    }

    // Setter para establecer el ID
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Getter para obtener el nombre
    public function getNombre(): string
    {
        return $this->nombre;
    }

    // Setter para establecer el nombre
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}
?>