<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar servicio #<?= (int) $servicio['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/servicio/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $servicio['id'] ?>">

    <?php require __DIR__ . '/_campos.php'; ?>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/?tabla=servicios'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
<script src="<?= htmlspecialchars(app_url('/js/app.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
