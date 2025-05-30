// --- DROPDOWN-MENÜ ---

function toggleDropdown(event, id) {
  event.preventDefault();
  document.getElementById(id).classList.toggle("show");
}

window.onclick = function(event) {
  if (!event.target.closest('.dropdown')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
      dropdowns[i].classList.remove('show');
    }
  }
};

// DARK MODE

document.addEventListener('DOMContentLoaded', () => {
  const darkModeToggle = document.getElementById('darkModeToggle');
  const modeLabel = document.getElementById('mode-label');
  const darkModeInput = document.getElementById('dark_mode_value');

  function updateModeLabel(isDark) {
    if (modeLabel) {
      modeLabel.textContent = isDark ? '🌙' : '☀️';
    }
  }

  // Wenn localStorage nicht gesetzt ist, orientiere dich am serverseitigen Wert (aus dem <body>-Tag)
  const isDarkStored = localStorage.getItem('darkMode');
  const isDarkByClass = document.body.classList.contains('dark-mode');

  if (isDarkStored === null) {
    localStorage.setItem('darkMode', isDarkByClass ? 'enabled' : 'disabled');
  }

  updateModeLabel(isDarkByClass);
  if (darkModeToggle) darkModeToggle.checked = isDarkByClass;
  if (darkModeInput) darkModeInput.value = isDarkByClass ? 1 : 0;

  if (darkModeToggle) {
    darkModeToggle.addEventListener('change', () => {
      const isDark = darkModeToggle.checked;
      document.body.classList.toggle('dark-mode', isDark);
      localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
      updateModeLabel(isDark);
      if (darkModeInput) darkModeInput.value = isDark ? 1 : 0;
    });
  }
});


  // --- EINTRAG POPUP MIT JQUERY ---

  $(document).ready(function () {
    $('#openEntryPopup').on('click', function () {
      $('#entryPopupOverlay').fadeIn();
    });
  
    $('#closeEntryPopup').on('click', function () {
      $('#entryPopupOverlay').fadeOut();
    });
  
    // Optional: schließen, wenn außerhalb geklickt
    $('#entryPopupOverlay').on('click', function (e) {
      if (e.target === this) {
        $(this).fadeOut();
      }
    });
  });
  
  // Show Settings Form

  function showSettings(event) {
    if (event) event.preventDefault();
    document.querySelector('.kalender').style.display = 'none';
    document.querySelector('.settings').style.display = 'block';
  }

  // Show Clalendar
  function showCalendar() {
    document.querySelector('.settings').style.display = 'none';
    document.querySelector('.kalender').style.display = 'block';
  }

  function updateSettingsHiddenFields() {
    const slider = document.getElementById('study-slider');
    const darkToggle = document.getElementById('darkModeToggle');
    const litHidden = document.getElementById('lit_value');
    const darkHidden = document.getElementById('dark_mode_value');
  
    if (slider && litHidden) litHidden.value = slider.value;
    if (darkToggle && darkHidden) {
      const isDark = darkToggle.checked;
      darkHidden.value = isDark ? 1 : 0;
  
      // 👉 Direkt die Klasse auf <body> setzen
      document.body.classList.toggle('dark-mode', isDark);
  
      // 👉 localStorage aktualisieren (für künftige Sitzungen)
      localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
  
      // 👉 Optional: Darkmode-Label aktualisieren
      const modeLabel = document.getElementById('mode-label');
      if (modeLabel) modeLabel.textContent = isDark ? '🌙' : '☀️';
    }
  }
  
  
  document.getElementById('darkModeToggle').addEventListener('change', () => {
    const modeLabel = document.getElementById('mode-label');
    modeLabel.textContent = document.getElementById('darkModeToggle').checked ? "🌙" : "☀️";
  });