<?php

require_once __DIR__ . '/../models/Pasajero.php';

class PasajeroController
{
    private $pasajeroModel;

    public function __construct($conexion)
    {
        $this->pasajeroModel =
            new Pasajero($conexion);
    }

    public function index()
    {
        return $this->pasajeroModel->obtenerTodos();
    }

    public function show($id)
    {
        return $this->pasajeroModel->getById($id);
    }
}