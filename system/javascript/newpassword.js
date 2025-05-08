/* 
function checkSecurityAnswer() {
    const username = document.getElementById('username').value;
    const securityAnswer = document.getElementById('securityAnswer').value;
  
    // Dummy-Sicherheitsantwort (ersetzen durch echten Abgleich)
    const correctAnswer = "meinHund123";
  
    if (securityAnswer === correctAnswer) {
      document.getElementById('questionForm').style.display = 'none';
      document.getElementById('passwordForm').style.display = 'block';
    } else {
      alert("Die Antwort auf die Sicherheitsfrage war falsch. Versuche es erneut.");
    }
  }
  
  function updatePassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
  
    if (newPassword === confirmPassword) {
      alert("Passwort erfolgreich geändert!");
      // Hier später: AJAX oder Weiterleitung zur Datenbankverarbeitung
    } else {
      alert("Die Passwörter stimmen nicht überein.");
    }
  }
  */
  function checkSecurityAnswer() {
    const username = document.getElementById('username').value;
    const securityAnswer = document.getElementById('securityAnswer').value;
  
    // TESTMODUS: Sicherheitsfrage wird immer als richtig bewertet
    if (username.trim() !== "" && securityAnswer.trim() !== "") {
      document.getElementById('questionForm').style.display = 'none';
      document.getElementById('passwordForm').style.display = 'block';
    } else {
      alert("Bitte fülle alle Felder aus.");
    }
  }
  
  function updatePassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
  
    if (newPassword === confirmPassword && newPassword.trim() !== "") {
      alert("Passwort erfolgreich geändert! (Testmodus)");
      // TEST: Hier könnte man z.B. zurück zur Loginseite leiten
      window.location.href = "login.php";
    } else {
      alert("Die Passwörter stimmen nicht überein oder sind leer.");
    }
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

  