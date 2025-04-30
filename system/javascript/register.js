// Event Listener für den Button hinzufügen
document.querySelector(".btn").addEventListener("click", function(event) {
    input_to_var(event); 
});

function input_to_var() {
    // Prevent form submission
    event.preventDefault();

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();
    var passwordwdh = document.getElementById("passwordwdh").value.trim();
    var securityanswer = document.getElementById("securityanswer").value.trim();
    var vorname = document.getElementById("vorname").value.trim();
    var nachname = document.getElementById("nachname").value.trim();

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
    if (!vorname) {
        alert("Bitte Benutzername eingeben.");
        return;
    }
    if (!nachname) {
        alert("Bitte Benutzername eingeben.");
        return;
    }


    // Passwort und Passwortwiederholung abgleichen
    if (password != passwordwdh) {
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
    if (!validInput.test(passwordwdh)) {
        alert("Ungültige Zeichen in der Passwortwiederholung.");
        return;
    }
    if (!validInput.test(vorname)) {
        alert("Ungültige Zeichen im Vorname.");
        return;
    }
    if (!validInput.test(nachname)) {
        alert("Ungültige Zeichen im Nachname.");
        return;
    }


    // Wenn alles gut ist, zeige die Werte
    alert("In Variable vorname: "+ vorname + " || In Variable nachname: " + nachname + " || In Variable username: " + username + " || In Variable password: " + password) + " || In Variable passwordwdh: " + passwordwdh;

    // Das Formular nach erfolgreicher Validierung absenden
    document.querySelector("form").submit(); // Formular absenden
}