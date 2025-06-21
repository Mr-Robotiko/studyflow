$(document).ready(function () {
    // Zeigt das benutzerdefinierte Popup
    window.openCustomAlert = function (title, message) {
        $("#popupTitle").text(title);
        $("#alertMessage").text(message);
        $("#customAlert").fadeIn();

        // Prüfen ob der Titel "Registrierung erfolgreich" ist
        if (title === "Registrierung erfolgreich") {
            // Nach 1,5 Sekunden automatisch zur Login-Seite weiterleiten
            setTimeout(function() {
                window.location.href = "login.php";
            }, 1500); 
        }
    };

    // Schließt das Popup
    window.closeCustomAlert = function () {
        $("#customAlert").fadeOut(); // jQuery Animation
    };

    // Wenn eine PHP-Fehlermeldung existiert → Popup anzeigen
    const errorDiv = $("#phpErrorMessage");
    if (errorDiv.length && errorDiv.data("message")) {
        const title = errorDiv.data("title") || "Fehler";
        const message = errorDiv.data("message");
        openCustomAlert(title, message);
    }

    // Schließen-Button
    $("#closePopup").click(function () {
        closeCustomAlert();
    });
});
