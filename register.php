<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyCal - Registrierung</title>
  <link rel="stylesheet" href="main.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body style="display: flex; justify-content: center; flex-direction: column; min-height: 100vh;">
  <div class="container">

    <h1 class="title" style="height: 60px; text-align: center; line-height: 60px;">Willkommen bei StudyCal</h1>

    <form action="#" method="post" style="max-width: 500px; margin: 0 auto; text-align: left;">
      <!-- Benutzername -->
      <div style="margin-bottom: 20px;">
        <label for="username" style="font-weight: bold; display: block; margin-bottom: 8px;">Benutzername</label>
        <input type="text" id="username" name="username" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Passwort -->
      <div style="margin-bottom: 20px;">
        <label for="password" style="font-weight: bold; display: block; margin-bottom: 8px;">Passwort</label>
        <input type="password" id="password" name="password" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Passwort wiederholen -->
      <div style="margin-bottom: 20px;">
        <label for="passwordwdh" style="font-weight: bold; display: block; margin-bottom: 8px;">Passwort wiederholen</label>
        <input type="password" id="passwordwdh" name="passwordwdh" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

      <!-- Sicherheitsfrage (fest) + Antwort -->
      <div style="margin-bottom: 20px;">
        <label style="font-weight: bold; display: block; margin-bottom: 8px;">
          Sicherheitsfrage:
        </label>
        <p style="margin-bottom: 10px; font-style: italic;">Platzhalter?</p>
        <input type="text" id="securityanswer" name="securityanswer" required 
          style="width: 100%; padding: 12px; border-radius: var(--border-radius); border: 1px solid var(--gray-dark); font-family: var(--font-family);"/>
      </div>

     
      <div class="buttons" style="justify-content: center;">
        <button type="submit" class="btn">Registrieren</button>
      </div>
    </form>
  </div>
</body>
</html>
