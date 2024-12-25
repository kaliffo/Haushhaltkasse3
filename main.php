<?php
try{
    require_once "php/dbh.inc.php";
    // Get the current year and month
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Query to select data for the current month
    $stmt = $pdo->prepare("
SELECT (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID 
WHERE e.einnahmeAusgabeID =1 AND YEAR(h.datum) = :year AND MONTH(h.datum) = :month) AS Einnahmen, (SELECT IFNULL(SUM(h.betrag), 0)
 FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID = 2 AND YEAR(h.datum) = :year 
 AND MONTH(h.datum) = :month) AS Ausgaben , (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON 
 h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND YEAR(h.datum) = :year AND MONTH(h.datum) = :month)-
 (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE 
 e.einnahmeAusgabeID = 2 AND YEAR(h.datum) = :year AND MONTH(h.datum) = :month) AS Differenz;");


    // Bind parameters
    $stmt->bindParam(':year', $currentYear, PDO::PARAM_INT);
    $stmt->bindParam(':month', $currentMonth, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdo = null;
    $stmt = null;
}
catch(PDOException $e){
die("query failed: ".$e->getMessage());

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
        <main>
            <nav>
                
                <ul class="list">
                    <li><a href="main.php">Home</a></li>
                    <!--<li><a href="uebersichten.php">Übersichtenen</a></li>-->
                    <li><a href="aktualisieren.php">Aktualisieren</a></li>
                    <li><a  href= "AnmeldReg.php" >Sign up</a></li>
                </ul>
            </nav>
            <div class="maincontent">
                <article class="title">
                <h1> Willkomen in deinem Finanzen Manager </h1>
                <p>Anbei den Übersicht für den aktuellen Monat:</p>
                </article>
                <article class="inUndout">
                    <h3 class="einnahmen">Einnahmen:<?php foreach ($results as $row) echo $row["Einnahmen"]." €" ?></h3>
                    <h3 class="ausgaben">Ausgaben:<?php foreach ($results as $row) echo $row["Ausgaben"]." €" ?></h3>
                </article>
                <article >
                    <h3 class="bilanz">Differenz:<?php foreach ($results as $row) echo $row["Differenz"]." €" ?></h3>
                </article>
            </div>   
            
        </main>
                
        
        <footer>
        <p>&copy; 2024 Systemhaus IT KG. Alle Rechte vorbehalten.</p>
        </footer>
    </body>
</html>