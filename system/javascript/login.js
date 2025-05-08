function input_to_var(event) {
    event.preventDefault();

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();


    var validInput = /^[a-zA-Z0-9_.-]+/;

    if (!username) {
        openCustomAlert("Benutzername", "Bitte Benutzername eingeben."); 
        return;
    }
    if (!password) {
        openCustomAlert("Passwort fehlt", "Bitte Passwort eingeben."); 
        return;
    }

    if (!validInput.test(username)) {
        openCustomAlert("Ungültige Eingabe", "Ungültige Zeichen im Benutzernamen."); 
        return;
    }

    if (!validInput.test(password)) {
        openCustomAlert("Ungültige Eingabe", "Ungültige Zeichen im Passwort."); 
        return;
    }

   
    openCustomAlert("Erfolg", "In Variable username: " + username + " || In Variable password: " + password);

    document.querySelector("form").submit(); // Formular absenden
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
