<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};
$totalRegistros = array_sum(array_map(static function ($tabla) {
    return count($tabla['registros']);
}, $tablas));
$emailUsuario = $authUser['correo'] ?? ($authUser['email'] ?? '');
$nombreUsuario = $authUser['nombre'] ?? 'Usuario';
$usuarioActualId = (int) ($authUser['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3c0054">
    <title>Panel de transporte | ULTRA Servicio Especial</title>
    <link rel="icon" type="image/png" href="<?= $escape(app_url('/img/favicon.png')) ?>">
    <link rel="stylesheet" href="<?= $escape(app_url('/css/app.css')) ?>">
</head>
<body>
    <div class="app-shell">
        <aside class="app-sidebar">
            <a class="app-brand" href="<?= $escape(app_url('/')) ?>">
                <img class="app-brand-logo" src="<?= $escape(app_url('/img/logo-ultra-3d-sm.webp')) ?>" alt="ULTRA Servicio Especial" width="300" height="262">
                <span class="app-brand-text">Sistema de transporte</span>
            </a>

            <p class="sidebar-label">Administración</p>
            <nav class="table-nav" aria-label="Seleccionar tabla">
                <a href="<?= $escape(app_url('/?tabla=todos')) ?>" <?= $selectedTable === 'todos' ? 'aria-current="page"' : '' ?>>
                    <span>Vista general</span>
                    <span class="nav-count"><?= $totalRegistros ?></span>
                </a>
                <?php foreach ($tablas as $tabla): ?>
                    <a href="<?= $escape(app_url('/?tabla=' . rawurlencode($tabla['clave']))) ?>" <?= $selectedTable === $tabla['clave'] ? 'aria-current="page"' : '' ?>>
                        <span><?= $escape($tabla['titulo']) ?></span>
                        <span class="nav-count"><?= count($tabla['registros']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-spacer"></div>
            <div class="sidebar-user">
                <p class="sidebar-user-label">Sesión iniciada</p>
                <p class="sidebar-user-name"><?= $escape($nombreUsuario) ?></p>
                <p class="sidebar-user-email"><?= $escape($emailUsuario) ?></p>
            </div>
        </aside>

        <main class="app-main">
            <header class="app-topbar">
                <div class="app-topbar-copy">
                    <p>Administración / <?= $selectedTable === 'todos' ? 'Vista general' : $escape($visibleTables[0]['titulo']) ?></p>
                    <h1>Panel de transporte</h1>
                </div>
                <form class="logout-form" method="post" action="<?= $escape(app_url('/auth/logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="button button-quiet" id="logout-button" type="submit">Cerrar sesión</button>
                </form>
            </header>

            <div class="dashboard-content">
                <section class="welcome-card" aria-labelledby="welcome-title">
                    <div>
                        <p>Panel de control</p>
                        <h2 id="welcome-title">Gestiona tu operación desde un solo lugar</h2>
                        <span><?= $totalRegistros ?> registros en las <?= count($tablas) ?> tablas del sistema.</span>
                    </div>
                    <a class="button button-primary" href="<?= $escape(app_url('/servicio/crear')) ?>">+ Nuevo servicio</a>
                </section>

                <?php if ($mensaje !== ''): ?>
                    <p class="auth-message" role="status" data-type="<?= !empty($mensajeEsError) ? 'error' : 'success' ?>"><?= $escape($mensaje) ?></p>
                <?php endif; ?>

                <section aria-labelledby="select-table-title">
                    <div class="section-heading">
                        <div>
                            <h2 id="select-table-title">Selecciona la información</h2>
                            <p>Elige una tabla para consultar y administrar sus registros.</p>
                        </div>
                    </div>
                    <nav class="table-chips" aria-label="Filtrar por tabla">
                        <a class="table-chip" href="<?= $escape(app_url('/?tabla=todos')) ?>" <?= $selectedTable === 'todos' ? 'aria-current="page"' : '' ?>>
                            Todas las tablas <span class="table-chip-count"><?= $totalRegistros ?></span>
                        </a>
                        <?php foreach ($tablas as $tabla): ?>
                            <a class="table-chip" href="<?= $escape(app_url('/?tabla=' . rawurlencode($tabla['clave']))) ?>" <?= $selectedTable === $tabla['clave'] ? 'aria-current="page"' : '' ?>>
                                <?= $escape($tabla['titulo']) ?>
                                <span class="table-chip-count"><?= count($tabla['registros']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </section>

                <?php foreach ($visibleTables as $tabla): ?>
                    <section class="data-card" aria-labelledby="table-<?= $escape($tabla['clave']) ?>">
                        <header class="data-card-header">
                            <div>
                                <h2 id="table-<?= $escape($tabla['clave']) ?>"><?= $escape($tabla['titulo']) ?></h2>
                                <span><?= count($tabla['registros']) ?> registros</span>
                            </div>
                            <a class="button button-primary" href="<?= $escape(app_url('/' . $rutasCrear[$tabla['clave']] . '/crear')) ?>">+ Crear</a>
                        </header>

                        <?php if ($tabla['registros'] === []): ?>
                            <div class="empty-state">Todavía no hay registros. Puedes crear el primero con el botón superior.</div>
                        <?php else: ?>
                            <div class="table-scroll">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php foreach ($tabla['columnas'] as $titulo => $campo): ?>
                                                <th><?= $escape($titulo) ?></th>
                                            <?php endforeach; ?>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tabla['registros'] as $registro): ?>
                                            <tr>
                                                <?php foreach ($tabla['columnas'] as $campo): ?>
                                                    <?php if ($campo === 'estado'): ?>
                                                        <?php $estadoClase = strtolower(str_replace(' ', '-', (string) ($registro['estado'] ?? ''))); ?>
                                                        <td><span class="status-badge status-<?= $escape($estadoClase) ?>"><?= $escape($registro['estado'] ?? '') ?></span></td>
                                                    <?php elseif ($campo === 'placa'): ?>
                                                        <td><span class="plate"><?= $escape($registro['placa'] ?? '') ?></span></td>
                                                    <?php else: ?>
                                                        <td class="<?= $campo === 'id' ? 'record-id' : (in_array($campo, ['salida', 'llegada', 'fecha_servicio', 'hora_servicio'], true) ? 'cell-nowrap' : '') ?>"><?= $escape($registro[$campo] ?? '') ?></td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <td>
                                                    <div class="row-actions">
                                                        <?php if ($tabla['clave'] === 'servicios' && ($registro['estado'] ?? '') === Servicio::ESTADO_FINALIZADO): ?>
                                                            <span class="locked-label" title="Los servicios finalizados no se pueden editar">Bloqueado</span>
                                                        <?php else: ?>
                                                            <a class="button" href="<?= $escape(app_url('/' . $tabla['ruta'] . '/editar?id=' . (int) $registro['id'])) ?>">Editar</a>
                                                        <?php endif; ?>
                                                        <?php if ($tabla['clave'] === 'usuarios' && (int) $registro['id'] === $usuarioActualId): ?>
                                                            <span class="locked-label you-label" title="Es tu usuario">Tú</span>
                                                        <?php else: ?>
                                                        <form class="delete-form" method="post" action="<?= $escape(app_url('/' . $tabla['ruta'] . '/eliminar')) ?>" data-confirm="¿Confirmas que quieres eliminar este registro?">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="id" value="<?= (int) $registro['id'] ?>">
                                                            <button class="delete-button" type="submit">Eliminar</button>
                                                        </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    <script src="<?= $escape(app_url('/js/app.js')) ?>" defer></script>
</body>
</html>
