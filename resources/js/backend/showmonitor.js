document.addEventListener('DOMContentLoaded', () => {
    // 2. Handle AJAX Trigger Check Button
    document.querySelectorAll('.trigger-check-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = this.querySelector('.trigger-btn');
            const iconIdle = this.querySelector('.icon-idle');
            const iconSpin = this.querySelector('.icon-spin');

            if (iconIdle) iconIdle.classList.add('d-none');
            if (iconSpin) iconSpin.classList.remove('d-none');
            if (btn) btn.disabled = true;

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
                        toastr.success(data.message || 'Checks updated successfully.');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Check failed.');
                    }
                    if (iconIdle) iconIdle.classList.remove('d-none');
                    if (iconSpin) iconSpin.classList.add('d-none');
                    if (btn) btn.disabled = false;
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while checking.');
                }
                if (iconIdle) iconIdle.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btn) btn.disabled = false;
            });
        });
    });

    // 3. Test Notification Modal AJAX Submission
    const testNotificationForm = document.getElementById('formSendTestNotification');
    if (testNotificationForm) {
        testNotificationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmitTestNotification');
            const iconBell = btn.querySelector('.icon-bell');
            const iconSpin = btn.querySelector('.icon-spin');
            const btnText = btn.querySelector('.btn-text');

            if (iconBell) iconBell.classList.add('d-none');
            if (iconSpin) iconSpin.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Sending notification...';
            btn.disabled = true;

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
                        toastr.success(data.message || 'Test notification sent successfully.');
                    }
                    const modalEl = document.getElementById('testNotificationModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to send test notification.');
                    }
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while sending notification.');
                }
            })
            .finally(() => {
                if (iconBell) iconBell.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btnText) btnText.textContent = 'Send test notifications';
                btn.disabled = false;
            });
        });
    }
});