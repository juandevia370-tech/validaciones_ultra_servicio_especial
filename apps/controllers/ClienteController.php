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

    public function show($id)
    {
        return $this->clienteModel->getById($id);
    }
}