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

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de conductor no válido.');
        }

        $conductor = $this->conductorModel->getById($id);
        if (!$conductor) {
            http_response_code(404);
            exit('Conductor no encontrado.');
        }

        require __DIR__ . '/../views/conductor/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $this->conductorModel->actualizar(
            $id,
            $_POST['nombre'] ?? '',
            $_POST['documento'] ?? '',
            $_POST['licencia'] ?? '',
            $_POST['telefono'] ?? ''
        );

        header('Location: ' . app_url('/?msg=' . ($ok ? 'editado' : 'error')));
        exit;
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->conductorModel->eliminar($id);

        header('Location: ' . app_url('/?msg=' . ($ok ? 'eliminado' : 'error')));
        exit;
    }
}
