<?php

require_once __DIR__ . '/env.php';

// Solo la usa el servidor PHP. La clave "publishable" es pública por diseño,
// pero ya no se envía al navegador.
// Nunca coloques aquí una clave service_role / secret.
return [
    'url' => app_env('SUPABASE_URL', 'https://xvxycplzdjtuvgsvnkrn.supabase.co'),
    'publishable_key' => app_env('SUPABASE_PUBLISHABLE_KEY', 'sb_publishable_MAVk3xfvZ8oQeW_1-lMZ1w_n-i-aFdg'),
];
