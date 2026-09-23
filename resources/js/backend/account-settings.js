document.addEventListener('DOMContentLoaded', function () {

<<<<<<< HEAD
    if (button && dropdown && hiddenInput && selectedFlag && selectedCode) {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('d-none');
        });

        document.querySelectorAll('.account-country-option').forEach(function (option) {
            option.addEventListener('click', function () {
                const code = this.dataset.code;
                const iso = this.dataset.iso;
                const name = this.dataset.name;

                hiddenInput.value = code;
                selectedCode.textContent = code;
                selectedFlag.src = '/assets/images/flags/' + iso + '.svg';
                selectedFlag.alt = name;

                dropdown.classList.add('d-none');
            });
        });

        document.addEventListener('click', function (e) {
            if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('d-none');
            }
        });
    }
});
=======
});
>>>>>>> bdf7495170109f506a561e0979777dc1a028702e
