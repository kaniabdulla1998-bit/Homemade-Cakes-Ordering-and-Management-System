document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form[data-validate="true"]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#d9534f';
                } else {
                    field.style.borderColor = '#e4c8b5';
                }
            });

            const password = form.querySelector('input[name="password"]');
            const confirm = form.querySelector('input[name="confirm_password"]');
            if (password && confirm && password.value && confirm.value && password.value !== confirm.value) {
                valid = false;
                confirm.style.borderColor = '#d9534f';
                alert('Password and confirm password do not match.');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    });
});
