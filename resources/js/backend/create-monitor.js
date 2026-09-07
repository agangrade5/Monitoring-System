document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('urls-container');
    const btnAddUrl = document.getElementById('btn-add-url');

    function createUrlRow() {
        const row = document.createElement('div');
        row.className = 'url-row input-group';
        row.innerHTML = `
            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
            <input type="text" name="urls[]" class="form-control url-input" placeholder="e.g. example.com or https://example.com" required>
            <button type="button" class="btn btn-outline-danger px-3 remove-url-btn" title="Remove Domain">
                <i class="bi bi-dash-lg"></i>
            </button>
        `;
        container.appendChild(row);
        const input = row.querySelector('.url-input');
        if (input) input.focus();
    }

    if (btnAddUrl) {
        btnAddUrl.addEventListener('click', function (e) {
            e.preventDefault();
            createUrlRow();
        });
    }

    if (container) {
        container.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.add-url-btn');
            if (addBtn) {
                e.preventDefault();
                createUrlRow();
            }

            const removeBtn = e.target.closest('.remove-url-btn');
            if (removeBtn) {
                e.preventDefault();
                const row = removeBtn.closest('.url-row');
                if (row) {
                    row.remove();
                }
            }
        });
    }
});