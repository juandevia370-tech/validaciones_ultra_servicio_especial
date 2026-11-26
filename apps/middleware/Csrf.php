<?php

/**
 * Protección CSRF: cada formulario POST lleva un token secreto ligado a la
 * sesión del usuario, de modo que otro sitio no pueda enviar formularios en
 * su nombre.
 */
if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    function csrf_field()
    {
        return '<input type="hidden" name="_csrf" value="'
            . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    function csrf_valid()
    {
        $token = $_POST['_csrf'] ?? '';

        return is_string($token)
            && $token !== ''
            && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }

    function csrf_regenerate()
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
}
