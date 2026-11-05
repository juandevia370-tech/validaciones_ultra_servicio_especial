<?php

require_once __DIR__ . '/../models/Destino.php';

class DestinoController
{
    private $destinoModel;

    public function __construct($conexion)
    {
        $this->destinoModel = new Destino($conexion);
    }

    public function crear()
    {
        require __DIR__ . '/../views/destino/crear.php';
    }

    public function guardar()
    {
        return $this->destinoModel->guardar(
            $_POST['ciudad_origen'] ?? '',
            $_POST['ciudad_destino'] ?? '',
            $_POST['direccion_destino'] ?? '',
            $_POST['tarifa'] ?? ''
        );
    }

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de destino no válido.');
        }

        $destino = $this->destinoModel->getById($id);
        if (!$destino) {
            http_response_code(404);
            exit('Destino no encontrado.');
        }

        require __DIR__ . '/../views/destino/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->destinoModel->actualizar(
            $id,
            $_POST['ciudad_origen'] ?? '',
            $_POST['ciudad_destino'] ?? '',
            $_POST['direccion_destino'] ?? '',
            $_POST['tarifa'] ?? ''
        );

        header('Location: ' . app_url('/?msg=' . ($ok ? 'editado' : 'error')));
        exit;
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $ok = $id && $id > 0 && $this->destinoModel->eliminar($id);

        header('Location: ' . app_url('/?msg=' . ($ok ? 'eliminado' : 'error')));
        exit;
    }
}
