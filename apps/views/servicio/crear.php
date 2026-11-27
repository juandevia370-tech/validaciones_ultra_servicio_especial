<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Formulario de servicio</h2>

<form class="crud-form" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="entidad" value="servicio">

    <?php require __DIR__ . '/_campos.php'; ?>

    <button type="submit">Guardar servicio</button>
    <a href="<?= htmlspecialchars(app_url('/?tabla=servicios'), ENT_QUOTES, 'UTF-8') ?>">Volver</a>
</form>
</main>
<script src="<?= htmlspecialchars(app_url('/js/app.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
