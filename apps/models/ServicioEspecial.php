<?php

declare(strict_types=1);

class ServicioEspecial
{
    private string $nombre;
    private string $descripcion;
    private float $precio;
    private int $duracionMinutos;

    public function __construct(
        string $nombre,
        string $descripcion,
        float $precio,
        int $duracionMinutos
    ) {
        $this->setNombre($nombre);
        $this->setDescripcion($descripcion);
        $this->setPrecio($precio);
        $this->setDuracionMinutos($duracionMinutos);
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        if (trim($nombre) === '') {
            throw new InvalidArgumentException('El nombre no puede estar vacío.');
        }

        $this->nombre = trim($nombre);
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        if (trim($descripcion) === '') {
            throw new InvalidArgumentException('La descripción no puede estar vacía.');
        }

        $this->descripcion = trim($descripcion);
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): void
    {
        if ($precio <= 0) {
            throw new InvalidArgumentException('El precio debe ser mayor que cero.');
        }

        $this->precio = $precio;
    }

    public function getDuracionMinutos(): int
    {
        return $this->duracionMinutos;
    }

    public function setDuracionMinutos(int $duracionMinutos): void
    {
        if ($duracionMinutos <= 0) {
            throw new InvalidArgumentException('La duración debe ser mayor que cero.');
        }

        $this->duracionMinutos = $duracionMinutos;
    }
}
