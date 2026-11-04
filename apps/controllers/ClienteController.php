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
    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de cliente no válido.');
        }

        $cliente = $this->clienteModel->getById($id);

        if (!$cliente) {
            http_response_code(404);
            exit('Cliente no encontrado.');
        }

        require __DIR__ . '/../views/cliente/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $ok = $id && $this->clienteModel->actualizar(
            $id,
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['correo'] ?? '',
            $_POST['telefono'] ?? ''
        );

        header('Location: /?msg=' . ($ok ? 'editado' : 'error'));
        exit;
    }
    }