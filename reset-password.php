<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $tokenHash = hash("sha256",$token);

    //Überprüfen, ob der Token gültig ist 
    require_once "php/dbh.inc.php";
    $stmt = $pdo->prepare("SELECT * FROM haushaltmitglieder WHERE resetTokenHash= :token");

    $stmt->bindParam(':token', $tokenHash, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    if($results === null){
        die("token not found");
    }
    foreach($results as $row){
    if (strtotime($row["resetTokenExpiresAt"])<= time()){
        die("token expired");
    }
   else {
    echo "Ungültiger oder abgelaufener Token.";
}
}
}
else{
echo "Kein Token angegeben.";
}

$stmt = null;


            ?>
            <!DOCTYPE html>
            <html lang="de">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Passwort zurücksetzen</title>
                <link rel="stylesheet" href="css/styling.css">
            </head>
            <body>
            <header>
           <img src="image/logo.png" alt="Logo">
           <h1>Haushalkasse</h1> 
            </header>
            <main class="anmeld">
            <div class = "anmeldung">
                <h2>Neues Passwort setzen</h2>
                <form action="" method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Neues Passwort:</label>
                    <input type="password" id="password" name="password" required>
                    <label for="passwordv">Neues Passwort wiederholen:</label>
                    <input type="password" id="passwordv" name="passwordv" required>
                    <button type="submit">Passwort setzen</button>
                    </form>
                    <?php
                    if ($_SERVER["REQUEST_METHOD"]=="POST") {
                    $password = $_POST['password'];
                    $passwordv = $_POST['passwordv'];
                    $email = $_POST['email'];
                    
                    if ($password === $passwordv){
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        //require_once "php/dbh.inc.php";
                        $query1 = "UPDATE `haushaltmitglieder` SET `password`= :password WHERE email = :email;";
                        $stmt1 = $pdo->prepare($query1);
                        $stmt1->bindParam(':email',$email,PDO::PARAM_STR);
                        $stmt1->bindParam(':password',$passwordHash,PDO::PARAM_STR);
                        
                        $stmt1->execute();
                        if ($stmt1->rowCount()>0) ?>
                        <p>Passwort zurückgesetzt</p>
                    
                        <a href='anmelden.php'>Anmelden</a>
                        <?php

                    }
                 else{ ?> <p>Die Passwords stimmen nicht überein! <?php

                 }
                 $pdo = null;
                }

                    ?>
            </div>
            </main>
            <footer>
          <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
            </footer>
            </body>
            </html>
            <?php
        
    