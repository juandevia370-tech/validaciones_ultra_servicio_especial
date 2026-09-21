<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Servicio.php';


$sqlCliente = "SELECT * FROM clientes LIMIT 5";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->execute();
$clientes = $stmtCliente->fetchAll(PDO::FETCH_ASSOC);


$sqlConductor = "SELECT * FROM conductores LIMIT 5";
$stmtConductor = $conexion->prepare($sqlConductor);
$stmtConductor->execute();
$conductores = $stmtConductor->fetchAll(PDO::FETCH_ASSOC);


$sqlPasajero = "SELECT * FROM pasajeros LIMIT 5";
$stmtPasajero = $conexion->prepare($sqlPasajero);
$stmtPasajero->execute();
$pasajeros = $stmtPasajero->fetchAll(PDO::FETCH_ASSOC);


// SERVICIO CONSULTADO - SOLO 1 REGISTRO
$sqlServicio = "SELECT * FROM servicios LIMIT 1";
$stmtServicio = $conexion->prepare($sqlServicio);
$stmtServicio->execute();
$servicioConsultado = $stmtServicio->fetch(PDO::FETCH_ASSOC);

?>


<h2>Servicio consultado</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Pasajero</th>
        <th>Conductor</th>
        <th>Vehículo</th>
        <th>Destino</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Estado</th>
    </tr>

    <tr>
        <td><?= $servicioConsultado['id'] ?></td>
        <td><?= $servicioConsultado['cliente_id'] ?></td>
        <td><?= $servicioConsultado['pasajero_id'] ?></td>
        <td><?= $servicioConsultado['conductor_id'] ?></td>
        <td><?= $servicioConsultado['vehiculo_id'] ?></td>
        <td><?= $servicioConsultado['destino_id'] ?></td>
        <td><?= $servicioConsultado['fecha_servicio'] ?></td>
        <td><?= $servicioConsultado['hora_servicio'] ?></td>
        <td><?= $servicioConsultado['estado'] ?></td>
    </tr>
</table>

<h2>Cliente</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Documento</th>
        <th>Correo</th>
        <th>Teléfono</th>
    </tr>

    <?php foreach ($clientes as $cliente): ?>
        <tr>
            <td><?= $cliente['id'] ?></td>
            <td><?= $cliente['nombre'] ?></td>
            <td><?= $cliente['documento'] ?></td>
            <td><?= $cliente['correo'] ?></td>
            <td><?= $cliente['telefono'] ?></td>
        </tr>
    <?php endforeach; ?>

</table>


<h2>Conductor</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Documento</th>
        <th>Licencia</th>
        <th>Teléfono</th>
    </tr>

    <?php foreach ($conductores as $conductor): ?>
        <tr>
            <td><?= $conductor['id'] ?></td>
            <td><?= $conductor['nombre'] ?></td>
            <td><?= $conductor['documento'] ?></td>
            <td><?= $conductor['licencia'] ?></td>
            <td><?= $conductor['telefono'] ?></td>
        </tr>
    <?php endforeach; ?>

</table>
<h2>Pasajero</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Documento</th>
        <th>Teléfono</th>
    </tr>

    <?php foreach ($pasajeros as $pasajero): ?>
        <tr>
            <td><?= $pasajero['id'] ?></td>
            <td><?= $pasajero['nombre'] ?></td>
            <td><?= $pasajero['documento'] ?></td>
            <td><?= $pasajero['telefono'] ?></td>
        </tr>
    <?php endforeach; ?>

</table>


