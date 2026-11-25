<?php

require_once __DIR__ . '/env.php';

// La clave "publishable" es pública por diseño (también la usa el navegador).
// Nunca coloques aquí una clave service_role / secret.
return [
    'url' => app_env('SUPABASE_URL', 'https://xvxycplzdjtuvgsvnkrn.supabase.co'),
    'publishable_key' => app_env('SUPABASE_PUBLISHABLE_KEY', 'sb_publishable_MAVk3xfvZ8oQeW_1-lMZ1w_n-i-aFdg'),
];
