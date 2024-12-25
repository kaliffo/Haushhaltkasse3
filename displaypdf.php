<?php


require_once('C:\xampp\htdocs\Haushhaltkasse3\vendor\tecnickcom\tcpdf\tcpdf.php');

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $von = $_POST["von"];
    $bis = $_POST["bis"];

    try {
        require_once "php/dbh.inc.php";

        $stmt = $pdo->prepare("SELECT h.haushaltKasseEintraegeID AS ID, h.datum AS Datum, m.haushaltMitgliederName AS Mitglieder, e.eintragName AS Name, h.betrag AS Betrag, a.art AS FixVar, ein.art AS EinnahmeAusgabe  FROM haushaltkasseeintraege h JOIN haushaltmitglieder m ON h.haushaltMitgliederID = m.haushaltMitgliederID JOIN eintraege e ON h.EintragID = e.eintraegeID JOIN einnahmeausgabe ein ON e.einnahmeAusgabeID = ein.einnahmeAusgabeID JOIN eintragart a ON e.eintragArtID = a.eintragArt WHERE h.datum BETWEEN :von AND :bis ORDER BY h.haushaltKasseEintraegeID;");
        $stmt5 = $pdo->prepare("SELECT (SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h 
JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN :von AND :bis) 
AS Einnahmen, (SELECT IFNULL(SUM(h.betrag), 0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID
 WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis ) AS Ausgaben , (SELECT IFNULL(SUM(h.betrag),0) 
 FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = e.eintraegeID WHERE e.einnahmeAusgabeID =1 AND h.datum BETWEEN :von AND :bis)-(SELECT IFNULL(SUM(h.betrag),0) FROM haushaltkasseeintraege h JOIN eintraege e ON h.EintragID = 
  e.eintraegeID WHERE e.einnahmeAusgabeID = 2 AND h.datum BETWEEN :von AND :bis) AS Differenz;");

        $stmt->bindParam(':von', $von, PDO::PARAM_STR);
        $stmt->bindParam(':bis', $bis, PDO::PARAM_STR);
        $stmt5->bindParam(':von', $von, PDO::PARAM_STR);
        $stmt5->bindParam(':bis', $bis, PDO::PARAM_STR);


        $stmt->execute();
        $stmt5->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);


        //PDF erstellen
        if (ob_get_contents()) {
            ob_end_clean();
        }
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
    if(!empty($results)) {
        foreach($results as $row){
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

    
    $stmt = null;
    $pdo = null;


} catch(PDOException $e){
    die("query failed: ".$e->getMessage());
    
    }
    /*header('Location: aktualisieren.php');
    exit();*/
}
