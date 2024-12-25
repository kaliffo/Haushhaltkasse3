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
            <div class = "registrierung">
                <h1>Registrieren</h1>
                <form action="php/register.php" method="POST">

                    <label for="name">Name:</label>
                    <input type="text" id="username" name="name" required>

                    <label for="username">Benutzername:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="email">E-Mail:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password">Passwort bestätigen:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit">Registrieren</button>
                </form>
            </div>
        </main>
                
        
        <footer>
            <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
        </footer>
    </body>
</html>