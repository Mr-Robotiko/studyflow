// --- DROPDOWN-MENÜ ---

function toggleDropdown(event, dropdown_profilbild) {
    event.preventDefault();
    document.getElementById("dropdown-menu").classList.toggle("show");
  }
  
  window.onclick = function(event) {
    if (!event.target.closest('#profilbild')) {
      const dropdowns = document.getElementsByClassName("dropdown-content");
      for (let i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove('show');
      }
    }
  };
  

function toggleDropdown(event, dropdown_eintrag) {
    event.preventDefault();
    document.getElementById("dropdown-menu").classList.toggle("show");
  }
  
  window.onclick = function(event) {
    if (!event.target.closest('#neuer_eintrag')) {
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
  