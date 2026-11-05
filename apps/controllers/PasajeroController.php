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

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de pasajero no válido.');
        }

        $pasajero = $this->pasajeroModel->getById($id);
        if (!$pasajero) {
            http_response_code(404);
            exit('Pasajero no encontrado.');
        }

        $clientes = $this->clienteModel->obtenerTodos();
        require __DIR__ . '/../views/pasajero/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $this->pasajeroModel->actualizar(
            $id,
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['cliente_id'] ?? ''
        );

        header('Location: ' . app_url('/?msg=' . ($ok ? 'editado' : 'error')));
        exit;
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->pasajeroModel->eliminar($id);

        header('Location: ' . app_url('/?msg=' . ($ok ? 'eliminado' : 'error')));
        exit;
    }
}