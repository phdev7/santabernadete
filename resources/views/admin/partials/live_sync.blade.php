@push('scripts')
    <script>
        (() => {
            const endpoint = @json(route('admin.sync-state'));
            let currentVersion = Number(@json((int) ($syncVersion ?? 1)));

            const poll = async () => {
                if (document.visibilityState !== 'visible') {
                    return;
                }

                try {
                    const response = await fetch(endpoint, {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const nextVersion = Number(payload.sync_version ?? currentVersion);

                    if (nextVersion !== currentVersion) {
                        window.location.reload();
                    }
                } catch (error) {
                    // Ignore polling errors and retry on next cycle.
                }
            };

            setInterval(poll, 2000);
        })();
    </script>
@endpush
