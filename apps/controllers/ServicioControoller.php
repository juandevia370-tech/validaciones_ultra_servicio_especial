<?php

require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Conductor.php';
require_once __DIR__ . '/../models/Pasajero.php';
require_once __DIR__ . '/../models/Vehiculo.php';
require_once __DIR__ . '/../models/Destino.php';

class ServicioController
{
    private $servicioModel;
    private $clienteModel;
    private $conductorModel;
    private $pasajeroModel;
    private $vehiculoModel;
    private $destinoModel;

    public function __construct($conexion)
    {
        $this->servicioModel =
            new Servicio($conexion);
        $this->clienteModel = new Cliente($conexion);
        $this->conductorModel = new Conductor($conexion);
        $this->pasajeroModel = new Pasajero($conexion);
        $this->vehiculoModel = new Vehiculo($conexion);
        $this->destinoModel = new Destino($conexion);
    }

    public function index()
    {
        return $this->servicioModel->obtenerTodos();
    }

    public function crear()
    {
        $catalogos = $this->cargarCatalogos();
        $clientes = $catalogos['clientes'];
        $pasajeros = $catalogos['pasajeros'];
        $conductores = $catalogos['conductores'];
        $vehiculos = $catalogos['vehiculos'];
        $destinos = $catalogos['destinos'];

        require __DIR__ . '/../views/servicio/crear.php';
    }

    private function datosFormulario()
    {
        $campos = [
            'cliente_id', 'pasajero_id', 'conductor_id', 'vehiculo_id', 'destino_id',
            'telefono_contacto', 'ciudad_origen', 'direccion_origen', 'ciudad_destino',
            'direccion_destino', 'fecha_servicio', 'hora_servicio', 'ticket', 'flete',
            'estado', 'observaciones',
        ];
        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '';
        }

        return $datos;
    }

    public function guardar()
    {
        try {
            return $this->servicioModel->guardar($this->datosFormulario());
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return false;
        }
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

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de servicio no válido.');
        }

        $servicio = $this->servicioModel->getById($id);
        if (!$servicio) {
            http_response_code(404);
            exit('Servicio no encontrado.');
        }

        $catalogos = $this->cargarCatalogos();
        $clientes = $catalogos['clientes'];
        $pasajeros = $catalogos['pasajeros'];
        $conductores = $catalogos['conductores'];
        $vehiculos = $catalogos['vehiculos'];
        $destinos = $catalogos['destinos'];

        require __DIR__ . '/../views/servicio/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = false;
        if ($id && $id > 0) {
            try {
                $ok = $this->servicioModel->actualizar($id, $this->datosFormulario());
            } catch (InvalidArgumentException $e) {
                $_SESSION['flash_error'] = $e->getMessage();
            }
        }

        header('Location: ' . app_url('/?tabla=servicios&msg=' . ($ok ? 'editado' : 'error')));
        exit;
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->servicioModel->eliminar($id);

        header('Location: ' . app_url('/?msg=' . ($ok ? 'eliminado' : 'error')));
        exit;
    }

    private function cargarCatalogos()
    {
        return [
            'clientes' => $this->clienteModel->obtenerTodos(),
            'pasajeros' => $this->pasajeroModel->obtenerTodos(),
            'conductores' => $this->conductorModel->obtenerTodos(),
            'vehiculos' => $this->vehiculoModel->obtenerTodos(),
            'destinos' => $this->destinoModel->obtenerTodos(),
        ];
    }
}