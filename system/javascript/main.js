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

  
  // --- DARK MODE ---
  
  document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const modeLabel = document.getElementById('mode-label');
  
    function updateModeLabel(isDark) {
      if (modeLabel) {
        modeLabel.textContent = isDark ? '🌙' : '☀️';
      }
    }
  
    const isDarkStored = localStorage.getItem('darkMode') === 'enabled';
    if (isDarkStored) {
      document.body.classList.add('dark-mode');
      if (darkModeToggle) darkModeToggle.checked = true;
      updateModeLabel(true);
    } else {
      updateModeLabel(false);
    }
  
    if (darkModeToggle) {
      darkModeToggle.addEventListener('change', () => {
        const isDark = darkModeToggle.checked;
        document.body.classList.toggle('dark-mode', isDark);
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        updateModeLabel(isDark);
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
