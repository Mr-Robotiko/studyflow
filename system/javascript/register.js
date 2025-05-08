document.querySelector(".btn").addEventListener("click", function(event) {
    input_to_var(event);
});

function input_to_var(event) {
    event.preventDefault();

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();
    var passwordwdh = document.getElementById("passwordwdh")?.value.trim(); // optional
    var securityanswer = document.getElementById("securityanswer")?.value.trim();
    var vorname = document.getElementById("vorname")?.value.trim();
    var nachname = document.getElementById("nachname")?.value.trim();

    var validInput = /^[a-zA-Z0-9_.-]+$/;

    if (!username) {
        openCustomAlert("Benutzername", "Bitte Benutzername eingeben."); 
        return;
    }
    if (!password) {
        openCustomAlert("Passwort", "Bitte Passwort eingeben."); 
        return;
    }

    if (typeof passwordwdh !== "undefined" && password !== passwordwdh) {
        openCustomAlert("Passwort stimmt nicht überein", "Passwort und Wiederholung sind unterschiedlich.");
        return;
    }

    if (!validInput.test(username)) {
        openCustomAlert("Ungültiger Benutzername", "Nur Buchstaben, Zahlen, _, -, . erlaubt."); 
        return;
    }

    if (!validInput.test(password)) {
        openCustomAlert("Ungültiges Passwort", "Nur Buchstaben, Zahlen, _, -, . erlaubt."); 
        return;
    }

    // Optional: Felder validieren, falls vorhanden (Registrierung)
    if (vorname && !validInput.test(vorname)) {
        openCustomAlert("Vorname ungültig", "Nur Buchstaben, Zahlen, _, -, . erlaubt."); 
        return;
    }

    if (nachname && !validInput.test(nachname)) {
        openCustomAlert("Nachname ungültig", "Nur Buchstaben, Zahlen, _, -, . erlaubt."); 
        return;
    }

    if (securityanswer && !validInput.test(securityanswer)) {
        openCustomAlert("Sicherheitsantwort ungültig", "Nur Buchstaben, Zahlen, _, -, . erlaubt."); 
        return;
    }

    // Alles OK
    document.querySelector("form").submit();
}

function openCustomAlert(title, message) {
    var alertOverlay = document.getElementById("customAlert");
    var alertMessage = document.getElementById("alertMessage");
    var popupTitle = document.getElementById("popupTitle");

    popupTitle.innerHTML = title;
    alertMessage.innerHTML = message;

    alertOverlay.style.display = "flex"; 
}

function closeCustomAlert() {
    document.getElementById("customAlert").style.display = "none";
}
