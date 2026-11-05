<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};
$totalRegistros = count($clientes) + count($conductores) + count($pasajeros)
    + count($servicios) + count($destinos) + count($vehiculos);
$emailUsuario = $authUser['email'] ?? 'Usuario autenticado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f3f6f8">
    <title>Panel de transporte | Ultra Servicio Especial</title>
    <link rel="stylesheet" href="<?= $escape(app_url('/css/app.css')) ?>">
</head>
<body data-base-path="<?= $escape(APP_BASE_PATH) ?>">
    <div class="app-shell">
        <aside class="app-sidebar">
            <a class="app-brand" href="<?= $escape(app_url('/')) ?>">
                <span class="app-brand-mark" aria-hidden="true">U</span>
                <span>ULTRA<br>SERVICIO ESPECIAL</span>
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
                <p class="sidebar-user-email"><?= $escape($emailUsuario) ?></p>
            </div>
        </aside>

        <main class="app-main">
            <header class="app-topbar">
                <div class="app-topbar-copy">
                    <p>Administración / <?= $selectedTable === 'todos' ? 'Vista general' : $escape($visibleTables[0]['titulo']) ?></p>
                    <h1>Panel de transporte</h1>
                </div>
                <button class="button button-quiet" id="logout-button" type="button">Cerrar sesión</button>
            </header>

            <div class="dashboard-content">
                <section class="welcome-card" aria-labelledby="welcome-title">
                    <div>
                        <p>Panel de control</p>
                        <h2 id="welcome-title">Gestiona tu operación desde un solo lugar</h2>
                        <span><?= $totalRegistros ?> registros en las seis tablas del sistema.</span>
                    </div>
                    <a class="button button-primary" href="<?= $escape(app_url('/servicio/crear')) ?>">+ Nuevo servicio</a>
                </section>

                <?php if ($mensaje !== ''): ?>
                    <p class="auth-message" role="status"><?= $escape($mensaje) ?></p>
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
                    <section class="data-card" aria-labelledby="table-<?= $escape($tabla['clave']) ?>" style="margin-top: 20px;">
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
                                                    <td class="<?= $campo === 'id' ? 'record-id' : '' ?>"><?= $escape($registro[$campo] ?? '') ?></td>
                                                <?php endforeach; ?>
                                                <td>
                                                    <div class="row-actions">
                                                        <a class="button" href="<?= $escape(app_url('/' . $tabla['ruta'] . '/editar?id=' . (int) $registro['id'])) ?>">Editar</a>
                                                        <form class="delete-form" method="post" action="<?= $escape(app_url('/' . $tabla['ruta'] . '/eliminar')) ?>" onsubmit="return confirm('¿Confirmas que quieres eliminar este registro?');">
                                                            <input type="hidden" name="id" value="<?= (int) $registro['id'] ?>">
                                                            <button class="delete-button" type="submit">Eliminar</button>
                                                        </form>
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
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="<?= $escape(app_url('/js/auth/config.js')) ?>"></script>
    <script src="<?= $escape(app_url('/js/auth/supabase.js')) ?>"></script>
    <script src="<?= $escape(app_url('/js/auth/dashboard.js')) ?>"></script>
</body>
</html>
