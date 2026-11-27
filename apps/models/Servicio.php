<?php

require_once __DIR__ . '/Vehiculo.php';

class Servicio
{
    private $conexion;

    private $nombre;
    private $descripcion;
    private $precio;
    private $duracionMinutos;

    public function __construct(
        $conexion,
        $nombre = '',
        $descripcion = '',
        $precio = 0,
        $duracionMinutos = 0
    ) {
        $this->conexion = $conexion;

        if ($nombre !== '') {
            $this->setNombre($nombre);
        }

        if ($descripcion !== '') {
            $this->setDescripcion($descripcion);
        }

        if ($precio > 0) {
            $this->setPrecio($precio);
        }

        if ($duracionMinutos > 0) {
            $this->setDuracionMinutos($duracionMinutos);
        }
    }

    public function setNombre($nombre)
    {
        if (trim($nombre) === '') {
            throw new InvalidArgumentException(
                'El nombre no puede estar vacío.'
            );
        }

        $this->nombre = $nombre;
    }

    public function getNombre()
    {
        return $this->nombre;
    }


    public function setDescripcion($descripcion)
    {
        if (trim($descripcion) === '') {
            throw new InvalidArgumentException(
                'La descripción no puede estar vacía.'
            );
        }

        $this->descripcion = $descripcion;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }


    public function setPrecio($precio)
    {
        if ($precio <= 0) {
            throw new InvalidArgumentException(
                'El precio debe ser mayor que cero.'
            );
        }

        $this->precio = $precio;
    }

    public function getPrecio()
    {
        return $this->precio;
    }


    public function setDuracionMinutos($duracionMinutos)
    {
        if ($duracionMinutos <= 0) {
            throw new InvalidArgumentException(
                'La duración debe ser mayor que cero.'
            );
        }

        $this->duracionMinutos = $duracionMinutos;
    }

    public function getDuracionMinutos()
    {
        return $this->duracionMinutos;
    }

    /**
     * Valida y normaliza los datos de un servicio (campos de la confirmación
     * que se envía al conductor). Lanza InvalidArgumentException con un
     * mensaje claro si algo no es válido.
     */
    private function validarDatos(array $datos)
    {
        $parametros = [];
        $ids = [
            'cliente_id' => 'cliente',
            'pasajero_id' => 'pasajero',
            'conductor_id' => 'conductor',
            'vehiculo_id' => 'vehículo',
            'destino_id' => 'ruta',
        ];
        foreach ($ids as $campo => $nombre) {
            $valor = filter_var($datos[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false || $valor < 1) {
                throw new InvalidArgumentException('Selecciona un ' . $nombre . ' válido.');
            }
            $parametros[':' . $campo] = $valor;
        }

        $textos = [
            'telefono_contacto' => ['Teléfono', 20, true],
            'ciudad_origen' => ['Ciudad de origen', 100, true],
            'direccion_origen' => ['Dirección de origen', 150, true],
            'ciudad_destino' => ['Ciudad de destino', 100, true],
            'direccion_destino' => ['Dirección de destino', 150, true],
            'ticket' => ['Ticket', 30, false],
            'estado' => ['Estado', 30, true],
            'observaciones' => ['Observaciones', 1000, false],
        ];
        foreach ($textos as $campo => [$etiqueta, $maximo, $obligatorio]) {
            $valor = trim((string) ($datos[$campo] ?? ''));
            if ($obligatorio && $valor === '') {
                throw new InvalidArgumentException('Completa el campo "' . $etiqueta . '".');
            }
            if (mb_strlen($valor) > $maximo) {
                throw new InvalidArgumentException('"' . $etiqueta . '" admite máximo ' . $maximo . ' caracteres.');
            }
            $parametros[':' . $campo] = $valor !== '' ? $valor : null;
        }

        $fecha = trim((string) ($datos['fecha_servicio'] ?? ''));
        $hora = trim((string) ($datos['hora_servicio'] ?? ''));
        $fechaValida = DateTime::createFromFormat('!Y-m-d', $fecha);
        if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
            throw new InvalidArgumentException('La fecha del servicio no es válida.');
        }
        $horaLlegada = trim((string) ($datos['hora_llegada'] ?? ''));
        $patronHora = '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/';
        if (!preg_match($patronHora, $hora)) {
            throw new InvalidArgumentException('La hora de salida no es válida.');
        }
        if (!preg_match($patronHora, $horaLlegada)) {
            throw new InvalidArgumentException('Completa una hora de llegada válida.');
        }
        $parametros[':fecha_servicio'] = $fecha;
        $parametros[':hora_servicio'] = strlen($hora) === 5 ? $hora . ':00' : $hora;
        $parametros[':hora_llegada'] = strlen($horaLlegada) === 5 ? $horaLlegada . ':00' : $horaLlegada;

        $flete = str_replace([' ', '$'], '', (string) ($datos['flete'] ?? ''));
        $flete = filter_var($flete, FILTER_VALIDATE_FLOAT);
        if ($flete === false || $flete < 0 || $flete > 9999999999) {
            throw new InvalidArgumentException('El flete debe ser un valor numérico mayor o igual a cero.');
        }
        $parametros[':flete'] = number_format($flete, 2, '.', '');

        return $parametros;
    }

    public function guardar($datos)
    {
        $parametros = $this->validarDatos($datos);

        try {
            $sql = 'INSERT INTO servicios
                        (cliente_id, pasajero_id, conductor_id, vehiculo_id, destino_id,
                         telefono_contacto, ciudad_origen, direccion_origen, ciudad_destino,
                         direccion_destino, fecha_servicio, hora_servicio, hora_llegada, ticket, flete,
                         estado, observaciones)
                    VALUES
                        (:cliente_id, :pasajero_id, :conductor_id, :vehiculo_id, :destino_id,
                         :telefono_contacto, :ciudad_origen, :direccion_origen, :ciudad_destino,
                         :direccion_destino, :fecha_servicio, :hora_servicio, :hora_llegada, :ticket, :flete,
                         :estado, :observaciones)';

            return $this->conexion->prepare($sql)->execute($parametros);
        } catch (PDOException $e) {
            error_log('Error al guardar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos)
    {
        $parametros = $this->validarDatos($datos);
        $parametros[':id'] = (int) $id;

        try {
            $sql = 'UPDATE servicios
                    SET cliente_id = :cliente_id, pasajero_id = :pasajero_id,
                        conductor_id = :conductor_id, vehiculo_id = :vehiculo_id,
                        destino_id = :destino_id, telefono_contacto = :telefono_contacto,
                        ciudad_origen = :ciudad_origen, direccion_origen = :direccion_origen,
                        ciudad_destino = :ciudad_destino, direccion_destino = :direccion_destino,
                        fecha_servicio = :fecha_servicio, hora_servicio = :hora_servicio,
                        hora_llegada = :hora_llegada,
                        ticket = :ticket, flete = :flete, estado = :estado,
                        observaciones = :observaciones
                    WHERE id = :id';

            return $this->conexion->prepare($sql)->execute($parametros);
        } catch (PDOException $e) {
            error_log('Error al actualizar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'DELETE FROM servicios WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error al eliminar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    private function consultaBase()
    {
        return "SELECT
                    servicios.*,
                    clientes.nombre AS cliente,
                    pasajeros.nombre AS pasajero,
                    pasajeros.telefono AS telefono_pasajero,
                    conductores.nombre AS conductor,
                    vehiculos.tipo AS tipo_vehiculo,
                    vehiculos.capacidad AS capacidad_vehiculo,
                    vehiculos.placa AS placa,
                    CONCAT(destinos.ciudad_origen, ' - ', destinos.ciudad_destino) AS destino,
                    destinos.tarifa
                FROM servicios
                LEFT JOIN clientes ON servicios.cliente_id = clientes.id
                LEFT JOIN pasajeros ON servicios.pasajero_id = pasajeros.id
                LEFT JOIN conductores ON servicios.conductor_id = conductores.id
                LEFT JOIN vehiculos ON servicios.vehiculo_id = vehiculos.id
                LEFT JOIN destinos ON servicios.destino_id = destinos.id";
    }

    /**
     * Agrega campos de presentación para la tabla del panel.
     */
    private function prepararParaVista(array $servicio)
    {
        $unir = static function ($ciudad, $direccion) {
            $partes = array_filter([trim((string) $ciudad), trim((string) $direccion)], 'strlen');
            return $partes ? implode(' — ', $partes) : '—';
        };

        $servicio['telefono'] = $servicio['telefono_contacto'] ?: ($servicio['telefono_pasajero'] ?? '');
        $servicio['origen'] = $unir($servicio['ciudad_origen'] ?? '', $servicio['direccion_origen'] ?? '');
        $servicio['destino_completo'] = $unir($servicio['ciudad_destino'] ?? '', $servicio['direccion_destino'] ?? '');
        $servicio['fecha_hora'] = trim(($servicio['fecha_servicio'] ?? '') . ' ' . substr((string) ($servicio['hora_servicio'] ?? ''), 0, 5));
        $servicio['direccion_origen_legible'] = trim((string) ($servicio['direccion_origen'] ?? '')) !== '' ? $servicio['direccion_origen'] : '—';
        $servicio['direccion_destino_legible'] = trim((string) ($servicio['direccion_destino'] ?? '')) !== '' ? $servicio['direccion_destino'] : '—';
        $servicio['tipo_capacidad'] = Vehiculo::etiquetaTipo($servicio['tipo_vehiculo'] ?? '', $servicio['capacidad_vehiculo'] ?? 0);
        $servicio['hora_salida'] = !empty($servicio['hora_servicio']) ? substr((string) $servicio['hora_servicio'], 0, 5) : '—';
        $servicio['hora_llegada_legible'] = !empty($servicio['hora_llegada']) ? substr((string) $servicio['hora_llegada'], 0, 5) : '—';
        $servicio['flete_legible'] = $servicio['flete'] !== null && $servicio['flete'] !== ''
            ? '$' . number_format((float) $servicio['flete'], 0, ',', '.')
            : '—';
        $observaciones = trim((string) ($servicio['observaciones'] ?? ''));
        $servicio['observaciones_corta'] = mb_strlen($observaciones) > 60
            ? mb_substr($observaciones, 0, 57) . '…'
            : ($observaciones !== '' ? $observaciones : '—');

        return $servicio;
    }

    public function obtenerTodos()
    {
        try {
            $consulta = $this->conexion->prepare($this->consultaBase() . ' ORDER BY servicios.id DESC');
            $consulta->execute();

            return array_map([$this, 'prepararParaVista'], $consulta->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log('Error al obtener los servicios: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            if ($id <= 0) {
                throw new Exception('El ID del servicio no es válido.');
            }

            $consulta = $this->conexion->prepare($this->consultaBase() . ' WHERE servicios.id = :id');
            $consulta->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $consulta->execute();
            $servicio = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                throw new Exception('Servicio no encontrado.');
            }

            return $this->prepararParaVista($servicio);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return null;
        }
    }
}
