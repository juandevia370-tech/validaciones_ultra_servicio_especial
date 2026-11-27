<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../middleware/Autenticacion.php';

class UsuarioController
{
    private $usuarioModel;
    private $usuarioActualId;

    public function __construct($conexion, $usuarioActualId = 0)
    {
        $this->usuarioModel = new Usuario($conexion);
        $this->usuarioActualId = (int) $usuarioActualId;
    }

    private function volver($codigo, $error = '')
    {
        if ($error !== '') {
            $_SESSION['flash_error'] = $error;
        }
        header('Location: ' . app_url('/?tabla=usuarios&msg=' . $codigo));
        exit;
    }

    public function crear()
    {
        require __DIR__ . '/../views/usuario/crear.php';
    }

    public function guardar()
    {
        try {
            return $this->usuarioModel->crear(
                $_POST['nombre'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['contrasena'] ?? '',
                $_POST['confirmar_contrasena'] ?? ''
            );
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return false;
        }
    }

    public function editar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            http_response_code(400);
            exit('ID de usuario no válido.');
        }

        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            http_response_code(404);
            exit('Usuario no encontrado.');
        }

        $esUsuarioActual = (int) $usuario['id'] === $this->usuarioActualId;
        require __DIR__ . '/../views/usuario/editar.php';
    }

    public function actualizar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->volver('error');
        }

        $actual = $this->usuarioModel->getById($id);
        if (!$actual) {
            $this->volver('error', 'El usuario no existe.');
        }

        $activo = isset($_POST['activo']);
        $esUsuarioActual = $id === $this->usuarioActualId;
        if (!$activo && $esUsuarioActual) {
            $this->volver('error', 'No puedes desactivar tu propio usuario.');
        }
        if (!$activo && $actual['activo'] && $this->usuarioModel->contarActivos() <= 1) {
            $this->volver('error', 'Debe quedar al menos un usuario activo.');
        }

        $nuevaContrasena = (string) ($_POST['contrasena'] ?? '');
        try {
            $ok = $this->usuarioModel->actualizar(
                $id,
                $_POST['nombre'] ?? '',
                $_POST['correo'] ?? '',
                $activo,
                $nuevaContrasena,
                $_POST['confirmar_contrasena'] ?? ''
            );
        } catch (InvalidArgumentException $e) {
            $this->volver('error', $e->getMessage());
        }

        // Si el usuario cambió su propia contraseña, su sesión sigue abierta;
        // las demás sesiones de ese usuario se cierran automáticamente.
        if ($ok && $esUsuarioActual && $nuevaContrasena !== '') {
            $datos = $this->usuarioModel->buscarPorIdParaSesion($id);
            if ($datos) {
                $_SESSION['usuario_huella'] = Autenticacion::huella($datos['contrasena_hash']);
            }
        }

        $this->volver($ok ? 'editado' : 'error');
    }

    public function eliminar()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->volver('error');
        }
        if ($id === $this->usuarioActualId) {
            $this->volver('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario = $this->usuarioModel->getById($id);
        if ($usuario && $usuario['activo'] && $this->usuarioModel->contarActivos() <= 1) {
            $this->volver('error', 'Debe quedar al menos un usuario activo.');
        }

        $this->volver($this->usuarioModel->eliminar($id) ? 'eliminado' : 'error');
    }
}
