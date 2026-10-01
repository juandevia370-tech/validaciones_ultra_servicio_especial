<?php

require_once __DIR__ . '/../models/Conductor.php';

class ConductorController
{
    private $conductorModel;

    public function __construct($conexion)
    {
        $this->conductorModel =
            new Conductor($conexion);
    }

    public function index()
    {
        return $this->conductorModel->obtenerTodos();
    }

    public function crear()
    {
        require __DIR__ . '/../views/conductor/crear.php';
    }

    public function guardar()
    {
        return $this->conductorModel->guardar(
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['licencia'] ?? '',
            $_POST['telefono'] ?? ''
        );
    }

    public function resultado($registros)
    {
        if (!is_array($registros) || $registros === []) {
            $registros = $this->index();
        }

        require __DIR__ . '/../views/resultado.php';
    }
}
