<?php if (!is_array($registros) || $registros === []): ?>
    <p>No hay registros para mostrar.</p>
<?php else: ?>
    <?php $columnas = array_keys($registros[0]); ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <?php foreach ($columnas as $columna): ?>
                    <th><?= htmlspecialchars((string) $columna, ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $registro): ?>
                <tr>
                    <?php foreach ($columnas as $columna): ?>
                        <td><?= htmlspecialchars((string) ($registro[$columna] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>