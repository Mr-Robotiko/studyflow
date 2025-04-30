function input_to_var() {
    event.preventDefault();

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();

    // Regex für gültige Zeichen: Buchstaben, Zahlen, Unterstrich, Punkt, Bindestrich
    var validInput = /^[a-zA-Z0-9_.-]+/; // $ verboten

    // Leere Felder prüfen
    if (!username) {
        alert("Bitte Benutzername eingeben.");
        return;
    }
    if (!password) {
        alert("Bitte Passwort eingeben.");
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

    document.querySelector("form").submit(); // Formular absenden
}