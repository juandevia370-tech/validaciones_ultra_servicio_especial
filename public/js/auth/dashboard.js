(() => {
    const basePath = document.body.dataset.basePath || '';
    const logout = document.querySelector('#logout-button');
    let syncedAccessToken = '';
    let syncPromise = null;
    let logoutInProgress = false;

    const syncSession = async (session) => {
        if (!session) {
            window.location.assign(`${basePath}/login`);
            return;
        }

        if (session.access_token === syncedAccessToken) {
            return;
        }
        if (syncPromise) {
            return syncPromise;
        }

        syncPromise = fetch(`${basePath}/auth/session`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                access_token: session.access_token,
                refresh_token: session.refresh_token
            })
        }).then(async (response) => {
            if (!response.ok) {
                await window.supabaseClient.auth.signOut();
                window.location.assign(`${basePath}/login`);
                return;
            }

            syncedAccessToken = session.access_token;
        }).finally(() => {
            syncPromise = null;
        });

        return syncPromise;
    };

    window.supabaseClient.auth.getSession().then(({ data, error }) => {
        if (error) {
            window.location.assign(`${basePath}/login`);
            return;
        }

        syncSession(data.session);
    });

    window.supabaseClient.auth.onAuthStateChange((event, session) => {
        if (event === 'SIGNED_OUT' && !logoutInProgress) {
            window.location.assign(`${basePath}/login`);
        } else if (session) {
            syncSession(session);
        }
    });

    logout.addEventListener('click', async () => {
        logout.disabled = true;
        logoutInProgress = true;
        try {
            const response = await fetch(`${basePath}/auth/logout`, {
                method: 'POST',
                credentials: 'same-origin'
            });
            if (!response.ok) {
                throw new Error('No se pudo cerrar la sesión del servidor.');
            }

            const { error } = await window.supabaseClient.auth.signOut();
            if (error) {
                throw error;
            }
            window.location.assign(`${basePath}/login`);
        } catch (error) {
            logoutInProgress = false;
            logout.disabled = false;
            window.alert('No fue posible cerrar la sesión. Intenta de nuevo.');
        }
    });
})();
