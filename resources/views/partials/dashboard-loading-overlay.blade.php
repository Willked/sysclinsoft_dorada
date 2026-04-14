{{-- Spinner global: formularios POST/PUT/DELETE (excluye GET). Opcional: data-no-loading="true" en el <form> --}}
<div id="dashboard-loading-overlay" class="dashboard-loading-overlay" aria-hidden="true">
    <div class="dashboard-loading-backdrop" aria-hidden="true"></div>
    <div class="dashboard-loading-panel" role="status" aria-live="polite">
        <div class="dashboard-loading-spinner" aria-hidden="true"></div>
        <p class="dashboard-loading-label">{{ __('Procesando…') }}</p>
        <p class="dashboard-loading-hint">{{ __('No cierre esta ventana.') }}</p>
    </div>
</div>
<script>
    (function () {
        if (window.__dashboardLoadingInit) {
            return;
        }
        window.__dashboardLoadingInit = true;

        var overlay = document.getElementById('dashboard-loading-overlay');
        if (!overlay) {
            return;
        }

        function showLoading() {
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('dashboard-loading-active');
        }

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }
            if (form.dataset.noLoading === 'true') {
                return;
            }
            var method = (form.getAttribute('method') || 'get').toLowerCase();
            if (method === 'get') {
                return;
            }
            showLoading();
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (el) {
                el.disabled = true;
                el.setAttribute('aria-disabled', 'true');
            });
        }, true);
    })();
</script>
