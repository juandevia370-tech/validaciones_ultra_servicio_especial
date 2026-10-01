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

    public function crear()
    {
        require __DIR__ . '/../views/servicio/crear.php';
    }

    public function guardar()
    {
        return $this->servicioModel->guardar(
            $_POST['cliente_id'] ?? '',
            $_POST['pasajero_id'] ?? '',
            $_POST['conductor_id'] ?? '',
            $_POST['vehiculo_id'] ?? '',
            $_POST['destino_id'] ?? '',
            $_POST['fecha_servicio'] ?? '',
            $_POST['hora_servicio'] ?? '',
            $_POST['estado'] ?? ''
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
        return $this->servicioModel->getById($id);
    }



    
}