function runWhenReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        fn();
    }
}

runWhenReady(function () {
    const emailRadio = document.getElementById('login-email');
    const phoneRadio = document.getElementById('login-phone');
    const emailSection = document.getElementById('email-section');
    const phoneSection = document.getElementById('phone-section');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (emailRadio && phoneRadio && emailSection && phoneSection && emailInput && phoneInput) {
        function toggleLoginType(isUserAction = false) {
            if (emailRadio.checked) {
                emailSection.style.display = 'block';
                phoneSection.style.display = 'none';
                emailInput.disabled = false;
                phoneInput.disabled = true;
                if (isUserAction) {
                    setTimeout(() => emailInput.focus(), 50);
                }
            } else {
                emailSection.style.display = 'none';
                phoneSection.style.display = 'block';
                emailInput.disabled = true;
                phoneInput.disabled = false;
                if (isUserAction) {
                    setTimeout(() => phoneInput.focus(), 50);
                }
            }
        }

        emailRadio.addEventListener('change', () => toggleLoginType(true));
        phoneRadio.addEventListener('change', () => toggleLoginType(true));
        toggleLoginType(false);
    }
});

