

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
                <form action="vendor/send-password.php" method="POST">
                  <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                  </div>
                  <button type="submit" class="btn" name="submitButton">Senden</button>
                </form>
                <a  href= "AnmeldReg.php" >Sign up</a>
            </div>
            <script
			  src="https://code.jquery.com/jquery-3.7.1.min.js"
			  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
			  crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        var messageText = "<?= $_SESSION['status']?? ''; ?>"
        if (messageText != ''){
        Swal.fire({title: "Dankeschön!",
          text: messageText,
          icon: "Erfolg"
        });
        <?php unset($_SESSION['status']); ?>
        }  
          </script>
        </main>
                
        
        <footer>
          <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
        </footer>
    </body>
</html>