// Event Listener für den Button hinzufügen
document.querySelector(".btn").addEventListener("click", function(event) {
    input_to_var(event); 
});

function input_to_var() {
    // Prevent form submission
    event.preventDefault();

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();
    var passwordwdhwdh = document.getElementById("passwordwdh").value.trim();
    var securityanswer = document.getElementById("securityanswer").value.trim();

    // Regex für gültige Zeichen: Buchstaben, Zahlen, Unterstrich, Punkt, Bindestrich
    var validInput = /^[a-zA-Z0-9_.-]+/; //$ verboten

    // Leere Felder prüfen
    if (!username) {
        alert("Bitte Benutzername eingeben.");
        return;
    }
    if (!password) {
        alert("Bitte Passwort eingeben.");
        return;
    }
    if (!passwordwdh) {
        alert("Bitte Passwortwiederholung eingeben.");
        return;
    }
    if (!securityanswer) {
        alert("Bitte Sicherheitsfrage eingeben.");
        return;
    }


    // Passwort und Passwortwiederholung abgleichen
    if (password != passwordwdhwdh) {
        alert("Passwort und Passwortwiederholung stimmen nicht überein");
        return;
    }

    // Eingaben validieren
    if (!validInput.test(username)) {
        alert("Ungültige Zeichen im Benutzernamen.");
        return;
    }

    if (!validInput.test(password)) {
        alert("Ungültige Zeichen im Passwort.");
        return;
    }
    // Wenn alles gut ist, zeige die Werte
    alert("In Variable username: " + username + " || In Variable password: " + password);

    // Das Formular nach erfolgreicher Validierung absenden
    document.querySelector("form").submit(); // Formular absenden
}