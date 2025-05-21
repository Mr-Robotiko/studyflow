<?php
session_start();            // Session starten
session_unset();            // Alle Session-Variablen löschen
session_destroy();          // Die Session zerstören

header("Location: index.html");
exit;