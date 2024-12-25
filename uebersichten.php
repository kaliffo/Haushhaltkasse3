<?php

var_dump($_SERVER["REQUEST_METHOD"]);
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $von = $_POST["von"];
    $bis = $_POST["bis"];
   

try {
    require_once "php/dbh.inc.php";
    
    $stmt = $pdo->prepare("SELECT h.haushaltKasseEintraegeID AS ID, h.datum AS Datum, m.haushaltMitgliederName AS Mitglieder, e.eintragName AS Name, h.betrag AS Betrag, a.art AS FixVar, ein.art AS EinnahmeAusgabe  FROM haushaltkasseeintraege h JOIN haushaltmitglieder m ON h.haushaltMitgliederID = m.haushaltMitgliederID JOIN eintraege e ON h.EintragID = e.eintraegeID JOIN einnahmeausgabe ein ON e.einnahmeAusgabeID = ein.einnahmeAusgabeID JOIN eintragart a ON e.eintragArtID = a.eintragArt WHERE h.datum BETWEEN :von AND :bis ORDER BY h.haushaltKasseEintraegeID;");
    $stmt2 = $pdo->prepare("SELECT (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h 
JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN :von AND :bis) 
AS Einnahmen, (SELECT IFNULL(SUM(h.betrag), 0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID
 WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis ) AS Ausgaben , (SELECT IFNULL(SUM(h.betrag),0) 
 FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN :von AND :bis)-(SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = 
  e.eintraegeID WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis) AS Differenz;");
    // Bind parameters
    $stmt->bindParam(':von', $von, PDO::PARAM_STR);
    $stmt->bindParam(':bis', $bis, PDO::PARAM_STR);
    $stmt2->bindParam(':von', $von, PDO::PARAM_STR);
    $stmt2->bindParam(':bis', $bis, PDO::PARAM_STR);


    // Execute the query
    $stmt->execute();
    $stmt2->execute();

    // Fetch results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Extract all unique keys to create table headers dynamically
    $headers = [];
    foreach ($results as $row) {
    $headers = array_merge($headers, array_keys($row));

    }
    $headers = array_unique($headers);

    $headers2 = [];
    foreach ($results2 as $row) {
    $headers2 = array_merge($headers2, array_keys($row));

    }
    $headers2 = array_unique($headers2);

    $pdo = null;
    $stmt = null;
    
   
    }
    catch(PDOException $e){
    die("query failed: ".$e->getMessage());
    
    }
    }
    ?>
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
           <h1>Haushaltkasse</h1> 
        </header>
        <div class="control">
                    <h1>Wählen Sie bitte eine Periode:</h1>
                    <form method="POST">
                        <label for="von">Von</label>
                        <input type="date" id="von" name="von" required>
                        <label  for="bis">Bis</label>
                        <input  type="date" id="bis" name="bis" required>
                        <button type="submit" class="btn">Display</button>
                    </form>
        </div>
        <nav>
                
                <ul class="list">
                    <li ><a href="main.php">Home</a></li>
                    <li ><a href="uebersichten.php">Übersichtenen</a></li>
                    <li ><a href="aktualisieren.php">Aktualisieren</a></li>
                    <li ><a  href= "AnmeldReg.php" >Sign up</a></li>
                </ul>
        </nav>
        <main class="contentuebersicht">    
            
                
                    <article class ="uebersicht">
                    <table>
                    <thead>
                    <tr>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($headers as $header): ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                        <?php foreach ($headers as $header): ?>
                            <td><?= isset($row[$header]) ? htmlspecialchars($row[$header]) : '-' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                    </tbody>
                </table>
                <table>
                    <thead>
                    <tr>
                    <?php if (!empty($results2)): ?>
                        <?php foreach ($headers2 as $header2): ?>
                            <th><?= htmlspecialchars($header2) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($results2)): ?>
                        <?php foreach ($results2 as $row): ?>
                            <tr>
                        <?php foreach ($headers2 as $header2): ?>
                            <td><?= isset($row[$header2]) ? htmlspecialchars($row[$header2]) : '-' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                    </tbody>
                </table>
            </article>  
            
        </main>
                
        
        <footer>
        <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
        </footer>
    </body>
</html>