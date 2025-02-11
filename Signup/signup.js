document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupForm');
    const email = document.getElementById('email');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const passwordRepeat = document.getElementById('password-repeat');
    const passwordError = document.getElementById('password-error');
    const passwordRepeatError = document.getElementById('password-repeat-error');
    const serverResponse = document.getElementById('server-response');

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
        event.preventDefault(); // Verhindert das Neuladen der Seite

        let errors = [];
        password.classList.remove("error-border");
        passwordRepeat.classList.remove("error-border");
        passwordError.textContent = "";
        passwordRepeatError.textContent = "";
        serverResponse.textContent = "";

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
            passwordError.innerHTML = errors.join('<br>');
            return;
        }

        // Formulardaten in JSON umwandeln
        const formData = {
            email: email.value,
            username: username.value,
            password: password.value
        };

        // JSON-Ausgabe für Debugging
        console.log("Formulardaten als JSON:", JSON.stringify(formData));

        // Fetch-Anfrage senden
        fetch('http://localhost/pinterest/api/router.php/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                console.log("API Antwort:", data);  // Debugging
                if (data.success) {
                    serverResponse.textContent = "Registrierung erfolgreich!";
                    serverResponse.style.color = "green";
                } else {
                    serverResponse.textContent = "Fehler: " + data.message;
                    serverResponse.style.color = "red";
                }
            })
            .catch(error => {
                console.error("Fehler beim Absenden des Formulars:", error);
                serverResponse.textContent = "Serverfehler. Bitte versuche es später erneut.";
                serverResponse.style.color = "red";
            });
    });
});
