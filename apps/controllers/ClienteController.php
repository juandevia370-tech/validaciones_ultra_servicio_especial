<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private $clienteModel;

    public function __construct($conexion)
    {
        $this->clienteModel =
            new Cliente($conexion);
    }

    public function index()
    {
        $clientes =
            $this->clienteModel->obtenerTodos();

        return $clientes;
    }

    public function crear()
    {
        require __DIR__ . '/../views/cliente/crear.php';
    }

    public function guardar()
    {
        return $this->clienteModel->guardar(
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['correo'] ?? '',
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

    public function show($id)
    {
        return $this->clienteModel->getById($id);
    }
}