<?php
//require_once('C:\xampp\htdocs\Haushhaltkasse3\vendor\tecnickcom\tcpdf\tcpdf.php');

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $von = $_POST["von"];
    $bis = $_POST["bis"];
try {
    require_once "php/dbh.inc.php";
    $stmt1 = $pdo->prepare("SELECT haushaltKasseEintraegeID FROM `haushaltkasseeintraege` WHERE 
    datum BETWEEN :von AND :bis ORDER BY haushaltKasseEintraegeID;");
    $stmt2 = $pdo->prepare("SELECT haushaltMitgliederID, userName FROM `haushaltmitglieder`;");
    $stmt3 = $pdo->prepare("SELECT eintraegeID , eintragName FROM `eintraege`;");
    $stmt4 = $pdo->prepare("SELECT h.haushaltKasseEintraegeID AS ID, h.datum AS Datum, m.haushaltMitgliederName 
    AS Mitglieder, e.eintragName AS Name, h.betrag AS Betrag, a.art AS FixVar, ein.art AS EinnahmeAusgabe  
    FROM haushaltkasseeintraege h JOIN haushaltmitglieder m ON h.haushaltMitgliederID = m.haushaltMitgliederID 
    JOIN eintraege e ON h.EintragID = e.eintraegeID JOIN einnahmeausgabe ein ON e.einnahmeAusgabeID = ein.einnahmeAusgabeID 
    JOIN eintragart a ON e.eintragArtID = a.eintragArt WHERE h.datum BETWEEN :von AND :bis ORDER BY h.haushaltKasseEintraegeID;");
    $stmt5 = $pdo->prepare("SELECT (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h 
JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN :von AND :bis) 
AS Einnahmen, (SELECT IFNULL(SUM(h.betrag), 0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID
 WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis ) AS Ausgaben , (SELECT IFNULL(SUM(h.betrag),0) 
 FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN
  :von AND :bis)-(SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = 
  e.eintraegeID WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis) AS Differenz;");

    $stmt1->bindParam(':von', $von, PDO::PARAM_STR);
    $stmt1->bindParam(':bis', $bis, PDO::PARAM_STR);
    $stmt5->bindParam(':von', $von, PDO::PARAM_STR);
    $stmt5->bindParam(':bis', $bis, PDO::PARAM_STR);
    $stmt4->bindParam(':von', $von, PDO::PARAM_STR);
    $stmt4->bindParam(':bis', $bis, PDO::PARAM_STR);

    // Execute the query
    $stmt1->execute();
    $stmt2->execute();
    $stmt3->execute();
    $stmt5->execute();
    $stmt4->execute();
    
    

    // Fetch results
    $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    $stmt1 = null;
    $stmt2 = null;
    $stmt3 = null;
    $stmt4 = null;
    $stmt5 = null;

    // Extract all unique keys to create table headers dynamically
$headers = [];
foreach ($results4 as $row) {
$headers = array_merge($headers, array_keys($row));

}
$headers = array_unique($headers);

$headers2 = [];
foreach ($results5 as $row) {
$headers2 = array_merge($headers2, array_keys($row));

}
$headers2 = array_unique($headers2);

}
catch(PDOException $e){
    die("query failed: ".$e->getMessage());
    
    }
}

     //PDF erstellen
    /* if (ob_get_contents()&& $von != null && $bis != null) {
        ob_end_clean();
    
$pdf = new TCPDF();
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('HaushhaltKasse3');
$pdf->setTitle('List Haushalteinträge');
$pdf->setMargins(10 , 10, 10);
$pdf->AddPage();

//Titel
$pdf->setFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'List Haushalteinträge', 0, 1 , 'C');

//Tabelle PDF erstellen
$pdf->setFont('helvetica', '', 12);
$html = '<table border="1" cellspacing="3" cellpadding="4">
        <tr>
            <th>ID</th>
            <th>Datum</th>
            <th>Name</th>
            <th>Eintrag</th>
            <th>Betrag</th>
            <th>Fix-Var</th>
            <th>Einnahmen-Ausgaben</th>
        </tr>
            ';

// Daten in PDF Tabelle einfügen
if(!empty($results4)) {
    foreach($results4 as $row){
        $html .= '<tr>
        <td>'.$row['ID'].'</td>
        <th>'.$row['Datum'].'</th>
        <th>'.$row['Mitglieder'].'</th>
        <th>'.$row['Name'].'</th>
        <th>'.$row['Betrag'].'</th>
        <th>'.$row['FixVar'].'</th>
        <th>'.$row['EinnahmeAusgabe'].'</th>
    </tr>';
    }
    $html .= '<table border="1" cellspacing="3" cellpadding="4">
        <tr>
            <th>Einnahmen</th>
            <th>Ausgaben</th>
            <th>Different</th>
        </tr>
            ';
            if(!empty($results5)) {
                foreach($results5 as $row){
                    $html .= '<tr>
                    <td>'.$row['Einnahmen'].'</td>
                    <th>'.$row['Ausgaben'].'</th>
                    <th>'.$row['Differenz'].'</th>
                </tr>';
                }
                }
            


} else {
    $html .= '<tr><td colspan="3>Keine Daten gefunden</td></tr>';
}
    $html .= '</table>';

//in PDF hinzufügen
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('query-result.pdf', 'D');


   
}


    
*/



    
    
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
        <main class="contentuebersicht" >
        <div class="manipulieren" >
                    <form  class="blockelement" method="POST">
                    <label class="blockelement" for="von">Von</label>
                    <input class="blockelement" type="date" id="von" name="von" required>
                    <label class="blockelement" for="bis">Bis</label>
                    <input class="blockelement" type="date" id="bis" name="bis" required>
                    <button class="blockelement" typ ="submit">Aktualisieren</button>
                    </form>
                    
                    
                    <form action="php/update.php" method="POST">
                    <label class="blockelement"  for="menu">Menu: </label>
                    <select class="blockelement" id="menu" name="menu">
                    <option value=""> </option>
                    <option value="addieren">addieren</option>
                    <option value="löschen">löschen</option>
                    </select>
                    <label class="blockelement" for="eintragnr">Eintrag Nr: </label>
                    <select class="manform" class="blockelement" id="eintragnr" name="eintragnr">
            <?php
            foreach($results1 as $row){
                echo "<option>".$row["haushaltKasseEintraegeID"]."</option>";}
        
            ?>
                    </select>
                    <label class="blockelement" for="name">Name: </label>
                    <select class="manform" class="blockelement" id="name" name="name">
                    <?php
                    foreach($results2 as $row){
                    echo "<option>".$row["haushaltMitgliederID"]." ".$row["userName"]."</option>";}
        
            ?>
                    </select>
                    <label class="blockelement" for="eintragsart">Eintragsart: </label>
                    <select class="manform" class="blockelement" id="eintragsart" name="eintragsart">
            <?php
                    foreach($results3 as $row){
                echo "<option>".$row["eintraegeID"]." ".$row["eintragName"]."</option>";}
        
            ?>
                    </select>
                        <label class="blockelement" for="betrag">Betrag</label>
                        <input class="manform" class="blockelement" type="number" id="betrag" name="betrag" required>
                        <label class="blockelement" for="datum">datum</label>
                        <input class="manform" class="blockelement" type="date" id="datum" name="datum" required>
                        <button id="submitbutton" class="blockelement" type="submit" >Senden</button>
                    </form>
                    
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                    const select = document.getElementById("menu");
                    const selectID = document.getElementById("eintragnr");
                    selectID.disabled = true;
                    const selectName = document.getElementById("name");
                    selectName.disabled = true;
                    const selectEintrag = document.getElementById("eintragsart");
                    selectEintrag.disabled = true;
                    const inputBetrag = document.getElementById("betrag");
                    inputBetrag.disabled = true;
                    const inputDatum = document.getElementById("datum");
                    inputDatum.disabled = true;
                    const button = document.getElementById("submitbutton")
                    button.disabled = true;
                    select.addEventListener("change", () => {
                    if (select.value === "") {
                        selectID.disabled = true;
                        selectName.disabled = true;
                        selectEintrag.disabled = true;
                        inputBetrag.disabled = true;
                        inputDatum.disabled = true;
                        button.disabled = true;
                    } else if (select.value === "addieren"){
                        selectID.disabled = true;
                        selectName.disabled = false;
                        selectEintrag.disabled = false;
                        inputBetrag.disabled = false;
                        inputDatum.disabled = false;
                        button.disabled = false;
                    }
                    else if (select.value === "löschen"){
                        selectID.disabled = false;
                        selectName.disabled = true;
                        selectEintrag.disabled = true;
                        inputBetrag.disabled = true;
                        inputDatum.disabled = true;
                        button.disabled = false;
                    }
                    
                });
                
                    });
                </script>
                 <form  action = "displaypdf.php" method="POST">
                    <label class="blockelement" for="von">Von</label>
                    <input class="blockelement" type="date" id="von" name="von" required>
                    <label class="blockelement" for="bis">Bis</label>
                    <input class="blockelement" type="date" id="bis" name="bis" required>
                    <a class="blockelement" href="displaypdf.php"><button class="blockelement">PDF</button></a>
                </form>
        </div>
        <nav>
                <ul class="list">
                    <li><a href="main.php">Home</a></li>
                    <!--<li><a href="uebersichten.php">Übersichtenen</a></li>-->
                    <li><a href="aktualisieren.php">Aktualisieren</a></li>
                    <li><a href="AnmeldReg.php" >Sign up</a></li>
                </ul>
        </nav>   
            
                
                    <article class ="uebersicht">
                    <table>
                    <thead>
                    <tr>
                    <?php if (!empty($results4)): ?>
                        <?php if (!empty($headers)): ?>
                        <?php foreach ($headers as $header): ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($results4)): ?>
                        <?php foreach ($results4 as $row): ?>
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
                    <?php if (!empty($results5)): ?>
                        <?php foreach ($headers2 as $header2): ?>
                            <th><?= htmlspecialchars($header2) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($results5)): ?>
                        <?php foreach ($results5 as $row): ?>
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