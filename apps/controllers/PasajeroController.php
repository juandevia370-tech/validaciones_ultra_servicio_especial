<?php

require_once __DIR__ . '/../models/Pasajero.php';
require_once __DIR__ . '/../models/Cliente.php';

class PasajeroController
{
    private $pasajeroModel;
    private $clienteModel;

    public function __construct($conexion)
    {
        $this->pasajeroModel =
            new Pasajero($conexion);
        $this->clienteModel =
            new Cliente($conexion);
    }

    public function index()
    {
        return $this->pasajeroModel->obtenerTodos();
    }

    public function crear()
    {
        $clientes = $this->clienteModel->obtenerTodos();
        require __DIR__ . '/../views/pasajero/crear.php';
    }

    public function guardar()
    {
        return $this->pasajeroModel->guardar(
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['cliente_id'] ?? ''
        );
    }

    public function resultado($registros)
    {
        if (!is_array($registros) || $registros === []) {
            $registros = $this->index();
        }

        require __DIR__ . '/../views/resultado.php';
    }

    public function show($id)
    {
        return $this->pasajeroModel->getById($id);
    }
}