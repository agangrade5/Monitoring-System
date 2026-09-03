document.addEventListener('DOMContentLoaded', () => {
    // 1. Interactive client-side user search helper
    const userSearchInput = document.getElementById('user-search');
    if (userSearchInput) {
        userSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach((row) => {
                const nameNode = row.querySelector('.fw-bold');
                const emailNode = row.querySelector('.text-secondary');

                if (nameNode && emailNode) {
                    const name = nameNode.textContent.toLowerCase();
                    const email = emailNode.textContent.toLowerCase();

                    if (name.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    // 2. Auto-open modal if validation errors exist (detected via .is-invalid inside modal)
    const editModalEl = document.getElementById('editUserModal');
    const addModalEl = document.getElementById('addUserModal');

    if (editModalEl && editModalEl.querySelector('.is-invalid')) {
        const editUserId = document.getElementById('edit_user_id')?.value;
        if (editUserId) {
            const form = document.getElementById('edit-user-form');
            if (form) {
                form.action = `/admin/users/update/${editUserId}`;
            }
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const editUserModal = new bootstrap.Modal(editModalEl);
            editUserModal.show();
        }
    } else if (addModalEl && addModalEl.querySelector('.is-invalid')) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const addUserModal = new bootstrap.Modal(addModalEl);
            addUserModal.show();
        }
    }

    // 3. Edit user button modal handler
    document.querySelectorAll('.edit-user-btn').forEach((button) => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const active = this.dataset.active;

            const form = document.getElementById('edit-user-form');
            if (form) {
                form.action = `/admin/users/update/${id}`;
            }

            const editIdInput = document.getElementById('edit_user_id');
            const editNameInput = document.getElementById('edit_user_name');
            const editEmailInput = document.getElementById('edit_user_email');
            const editStatusInput = document.getElementById('edit_user_status');
            const editPassInput = document.getElementById('edit_user_password');

            if (editIdInput) editIdInput.value = id;
            if (editNameInput) editNameInput.value = name;
            if (editEmailInput) editEmailInput.value = email;
            if (editStatusInput) editStatusInput.value = active;
            if (editPassInput) editPassInput.value = '';

            if (editModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        });
    });
});