<?php

require_once __DIR__ . '/../models/Servicio.php';

class ServicioController
{
    private $servicioModel;

    public function __construct($conexion)
    {
        $this->servicioModel =
            new Servicio($conexion);
    }

    public function index()
    {
        return $this->servicioModel->obtenerTodos();
    }

    public function show($id)
    {
        return $this->servicioModel->getById($id);
    }
}