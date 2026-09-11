document.addEventListener('DOMContentLoaded', () => {
    const monitorSearchInput = document.getElementById('monitor-search');
    if (monitorSearchInput) {
        if (monitorSearchInput.value) {
            monitorSearchInput.focus();
            const val = monitorSearchInput.value;
            monitorSearchInput.value = '';
            monitorSearchInput.value = val;
        }

        monitorSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('.py-5')) return;
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle AJAX Trigger Check
    document.querySelectorAll('.trigger-check-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = this.querySelector('.trigger-btn');
            const iconIdle = this.querySelector('.icon-idle');
            const iconSpin = this.querySelector('.icon-spin');
            const row = this.closest('tr');
            const preparingBadge = row ? row.querySelector('.monitor-preparing-badge') : null;
            const checkingBadges = row ? row.querySelectorAll('.status-checking-badge') : [];
            const statusContents = row ? row.querySelectorAll('.status-content') : [];

            // Hide icon, show spinner, disable button
            if (iconIdle) iconIdle.classList.add('d-none');
            if (iconSpin) iconSpin.classList.remove('d-none');
            if (btn) btn.disabled = true;

            // Show "HTTP Preparing..." badge and column checking badges in the row
            if (preparingBadge) {
                preparingBadge.classList.remove('d-none');
            }
            checkingBadges.forEach(badge => badge.classList.remove('d-none'));
            statusContents.forEach(content => content.classList.add('d-none'));

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Check completed successfully.');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to complete check.');
                    }
                    if (iconIdle) iconIdle.classList.remove('d-none');
                    if (iconSpin) iconSpin.classList.add('d-none');
                    if (btn) btn.disabled = false;
                    if (preparingBadge) preparingBadge.classList.add('d-none');
                    checkingBadges.forEach(badge => badge.classList.add('d-none'));
                    statusContents.forEach(content => content.classList.remove('d-none'));
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while running check.');
                }
                if (iconIdle) iconIdle.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btn) btn.disabled = false;
                if (preparingBadge) preparingBadge.classList.add('d-none');
                checkingBadges.forEach(badge => badge.classList.add('d-none'));
                statusContents.forEach(content => content.classList.remove('d-none'));
            });
        });
    });
});