function input_to_var() {
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();

    // Regex für gültige Zeichen: Buchstaben, Zahlen, Unterstrich, Punkt, Bindestrich
    var validInput = /^[a-zA-Z0-9_.-]+$/;

    // Leere Felder prüfen
    if (!username || !password) {
        alert("Bitte Benutzername und Passwort eingeben.");
        return;
    }

    // Eingaben validieren
    if (!validInput.test(username) || !validInput.test(password)) {
        alert("Ungültige Zeichen im Benutzernamen oder Passwort.");
        return;
    }

    // Wenn alles gut ist, zeige die Werte
    alert("In Variable username: " + username + " || In Variable password: " + password);
}























