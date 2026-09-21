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

    public function show($id)
    {
        return $this->conductorModel->getById($id);
    }
}