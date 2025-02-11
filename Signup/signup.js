document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupForm');
    const password = document.querySelector('input[name="password"]');
    const passwordRepeat = document.querySelector('input[name="password-repeat"]');
    const passwordError = document.getElementById('password-error');
    const passwordRepeatError = document.getElementById('password-repeat-error');
   

    // Passwortvalidierung
    function validatePassword(passwordValue) {
        let errors = [];
        if (passwordValue.length < 8) {
            errors.push("Das Passwort muss mindestens 8 Zeichen lang sein.");
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(passwordValue)) {
            errors.push("Das Passwort muss mindestens ein Sonderzeichen enthalten.");
        }
        if (!/[0-9]/.test(passwordValue)) {
            errors.push("Das Passwort muss mindestens eine Zahl enthalten.");
        }
        return errors;
    }

    form.addEventListener('submit', function (event) {
        let errors = [];
        password.classList.remove("error-border");
        passwordRepeat.classList.remove("error-border");
        passwordError.textContent = "";
        passwordRepeatError.textContent = "";
        captchaError.textContent = "";

        // Passwortprüfung
        let passwordValidationErrors = validatePassword(password.value);
        if (passwordValidationErrors.length > 0) {
            errors.push(...passwordValidationErrors);
            password.classList.add("error-border");
        }

        // Passwortwiederholung prüfen
        if (password.value !== passwordRepeat.value) {
            errors.push("Die Passwörter stimmen nicht überein.");
            passwordRepeat.classList.add("error-border");
            passwordRepeatError.textContent = "Die Passwörter stimmen nicht überein.";
        }


        // Falls Fehler vorhanden sind, Formular nicht absenden
        if (errors.length > 0) {
            event.preventDefault();
            passwordError.innerHTML = errors.join('<br>');
        }
    });
});