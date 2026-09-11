document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('accountCountryDropdownBtn');
    const dropdown = document.getElementById('accountCountryDropdown');
    const hiddenInput = document.getElementById('account_country_code');
    const selectedFlag = document.getElementById('accountSelectedFlag');
    const selectedCode = document.getElementById('accountSelectedCode');

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
                selectedFlag.src = '{{ asset("assets/images/flags") }}/' + iso + '.svg';
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