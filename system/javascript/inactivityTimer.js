const sessionTimeout = 600; // 10 Minuten Timeout
let inactivityTimer;

// Funktion zur Umleitung nach Inaktivität
function redirectToIndex() {
  window.location.href = "index.html"; // Umleitung zur Startseite (index.html)
}

// Funktion, um den Timer zurückzusetzen, wenn der Benutzer aktiv ist
function resetInactivityTimer() {
  // Clear den bestehenden Timer
  clearTimeout(inactivityTimer);
  
  // Starte einen neuen Timer
  inactivityTimer = setTimeout(redirectToIndex, sessionTimeout * 1000);
}

// Event Listener für Benutzeraktivität
window.onload = function() {
  // Setzt den Timer bei jeder Benutzeraktion zurück (Klick, Tastatureingabe etc.)
  document.body.addEventListener('mousemove', resetInactivityTimer);
  document.body.addEventListener('keydown', resetInactivityTimer);

  // Setze den Timer direkt beim Laden der Seite
  resetInactivityTimer();
};