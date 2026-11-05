(() => {
    const config = window.APP_CONFIG;

    if (!config || !window.supabase) {
        throw new Error('No se pudo cargar la configuración de autenticación.');
    }

    window.supabaseClient = window.supabase.createClient(
        config.SUPABASE_URL,
        config.SUPABASE_PUBLISHABLE_KEY,
        {
            auth: {
                autoRefreshToken: true,
                persistSession: true,
                detectSessionInUrl: true
            }
        }
    );
})();
