document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Verhindert das Standard-Formular-Submit

    // Sammeln der Eingabedaten
    const emailOrUsername = document.getElementById('email').value;  // Hier den Namen des Eingabefelds nach deinen Anforderungen anpassen
    const password = document.getElementById('password').value;

    // Erstelle ein JSON-Objekt mit den Login-Daten
    const loginData = {
        username_or_email: emailOrUsername,  // Der Key muss mit dem übereinstimmen, was der Controller erwartet
        password: password
    };

    // API-Anfrage (Beispiel-URL: api/login)
    fetch('http://localhost/pinterest/api/router.php/login', {
        method: 'POST',  // POST-Methode zum Senden von Daten
        headers: {
            'Content-Type': 'application/json'  // Stelle sicher, dass die Daten als JSON gesendet werden
        },
        body: JSON.stringify(loginData)  // Die Login-Daten als JSON übermitteln
    })
        .then(response => response.json())  // Antwort als JSON parsen
        .then(data => {
            console.log(data);  // Server-Antwort im Browser anzeigen (optional)

            // Hier kannst du entscheiden, was du mit der Antwort tun möchtest
            if (data.success) {
                alert('Login erfolgreich!');

            } else {
                alert('Login fehlgeschlagen: ' + data.message);
            }
        })
        .catch(error => {
            // Fehlerfall: Ausgabe des Fehlers, falls die Anfrage nicht erfolgreich war
            alert('Fehler beim Login: ' + error.message);
        });
});
