<?php

require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../models/Conductor.php';

class VehiculoController
{
    private $vehiculoModel;
    private $conductorModel;

    public function __construct($conexion)
    {
        $this->vehiculoModel = new Vehiculo($conexion);
        $this->conductorModel = new Conductor($conexion);
    }

    public function crear()
    {
        $conductores = $this->conductorModel->obtenerTodos();
        require __DIR__ . '/../views/vehiculo/crear.php';
    }

    public function guardar()
    {
        return $this->vehiculoModel->guardar(
            $_POST['placa'] ?? '',
            $_POST['marca'] ?? '',
            $_POST['modelo'] ?? '',
            $_POST['capacidad'] ?? '',
            $_POST['conductor_id'] ?? ''
        );
    }

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de vehículo no válido.');
        }

        $vehiculo = $this->vehiculoModel->getById($id);
        if (!$vehiculo) {
            http_response_code(404);
            exit('Vehículo no encontrado.');
        }

        $conductores = $this->conductorModel->obtenerTodos();
        require __DIR__ . '/../views/vehiculo/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->vehiculoModel->actualizar(
            $id,
            $_POST['placa'] ?? '',
            $_POST['marca'] ?? '',
            $_POST['modelo'] ?? '',
            $_POST['capacidad'] ?? '',
            $_POST['conductor_id'] ?? ''
        );

        header('Location: ' . app_url('/?msg=' . ($ok ? 'editado' : 'error')));
        exit;
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->vehiculoModel->eliminar($id);

        header('Location: ' . app_url('/?msg=' . ($ok ? 'eliminado' : 'error')));
        exit;
    }
}
