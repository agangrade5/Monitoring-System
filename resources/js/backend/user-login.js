document.addEventListener('DOMContentLoaded', function () {

    const emailRadio =
        document.getElementById('login-email');

    const phoneRadio =
        document.getElementById('login-phone');

    const emailSection =
        document.getElementById('email-section');

    const phoneSection =
        document.getElementById('phone-section');

    const emailInput =
        document.getElementById('email');

    const phoneInput =
        document.getElementById('phone');

    function toggleLoginType() {

        if (emailRadio.checked) {

            emailSection.style.display = 'block';
            phoneSection.style.display = 'none';

            emailInput.disabled = false;
            phoneInput.disabled = true;

        } else {

            emailSection.style.display = 'none';
            phoneSection.style.display = 'block';

            emailInput.disabled = true;
            phoneInput.disabled = false;

        }
    }

    emailRadio.addEventListener(
        'change',
        toggleLoginType
    );

    phoneRadio.addEventListener(
        'change',
        toggleLoginType
    );

    /*
     * Initial state
     */
    toggleLoginType();

});
