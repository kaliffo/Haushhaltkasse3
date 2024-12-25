<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meine Website</title>
        <link rel="stylesheet" href="css/styling.css">
    </head>
    <body>
        <header>
           <img src="image/logo.png" alt="Logo">
           <h1>Haushalkasse</h1> 
        </header>
        
        
        <main class="anmeld">
            <div class = "anmeldung">
                <h2>Anmelden</h2>
                <form action="php/login.php" method="POST">
                  <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                  </div>
                  <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" required>
                  </div>
                  <button type="submit" class="btn">Anmelden</button>
                </form>
                <a   href="forgot-password.php" >Passwort vergessen?</a>
                <a  href= "registrieren.php" >Noch nicht registriert?</a>
            </div>
        </main>
                
        
        <footer>
          <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
        </footer>
    </body>
</html>