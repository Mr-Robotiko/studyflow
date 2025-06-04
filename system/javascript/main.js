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
  const darkModeInput = document.getElementById('dark_mode_value');

  function updateModeLabel(isDark) {
    if (modeLabel) {
      modeLabel.textContent = isDark ? '🌙' : '☀️';
    }
  }

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

// --- EINTRAG und TODO POPUP MIT JQUERY ---
$(document).ready(function () {
  // --- ENTRY POPUP ---
  $('#openEntryPopup').on('click', function () {
    $('#entryPopupOverlay').fadeIn();
  });

  $('#closeEntryPopup').on('click', function () {
    $('#entryPopupOverlay').fadeOut();
  });

  $('#entryPopupOverlay').on('click', function (e) {
    if (e.target === this) {
      $(this).fadeOut();
    }
  });

  // --- TODO POPUP ---
  $('#openTodoPopup').on('click', function () {
    $('#todoPopupOverlay').fadeIn();
  });

  $('#closeTodoPopup').on('click', function () {
    $('#todoPopupOverlay').fadeOut();
  });

  $('#todoPopupOverlay').on('click', function (e) {
    if (e.target === this) {
      $(this).fadeOut();
    }
  });

  $(document).on('change', '.todo-checkbox', function () {
    if (this.checked) {
      const $item = $(this).closest('.todo-item');
      const todoId = $(this).data('id');

      // 1) Frontend: Text durchstreichen
      $item.find('.todo-text').css('text-decoration', 'line-through');

      // 2) AJAX: TID an start.php senden (delete_todo-Flag)
      $.post('start.php', { delete_todo: 1, TID: todoId })
        .done(function (response) {
          // Bei Bedarf: response.success prüfen
          if (!response.success) {
            alert('DB-Löschung fehlgeschlagen: ' + response.error);
            $item.find('.todo-checkbox').prop('checked', false);
            $item.find('.todo-text').css('text-decoration', 'none');
          }
        })
        .fail(function () {
          alert('AJAX-Fehler beim Entfernen');
          $item.find('.todo-checkbox').prop('checked', false);
          $item.find('.todo-text').css('text-decoration', 'none');
        });

      // 3) Element nach 2 Sekunden ausblenden
      setTimeout(function () {
        $item.fadeOut(300, function () {
          $(this).remove();
          if ($('.todo-item').length === 0) {
            $('#todo-empty-info').show();
          }
        });
      }, 2000);
    }
  });
});

// --- SETTINGS FORM ---
function showSettings(event) {
  if (event) event.preventDefault();
  document.querySelector('.kalender').style.display = 'none';
  document.querySelector('.settings').style.display = 'block';
}

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
    document.body.classList.toggle('dark-mode', isDark);
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    const modeLabel = document.getElementById('mode-label');
    if (modeLabel) modeLabel.textContent = isDark ? '🌙' : '☀️';
  }
}

document.getElementById('darkModeToggle').addEventListener('change', () => {
  const modeLabel = document.getElementById('mode-label');
  modeLabel.textContent = document.getElementById('darkModeToggle').checked ? "🌙" : "☀️";
});

// --- SLIDER VALUE ---
document.addEventListener('DOMContentLoaded', () => {
  const slider = document.getElementById('study-slider');
  const hiddenLitValue = document.getElementById('lit_value');

  if (slider && hiddenLitValue) {
    slider.addEventListener('input', () => {
      hiddenLitValue.value = slider.value;
      const labels = ['30 min', '45 min', '60 min', '120 min', '180 min'];
      document.getElementById('selected-duration').textContent = labels[slider.value] ?? '';
    });
  }
});
