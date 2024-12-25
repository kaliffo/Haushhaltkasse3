<?php

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $menu = $_POST["menu"];
    if ($menu ==="addieren"){
    $eintragsart = $_POST["eintragsart"];
    $name = $_POST["name"];
    $datum = $_POST["datum"];
    $betrag = $_POST["betrag"];
    
    
   
try {
    require_once "dbh.inc.php";
    if(!empty($eintragsart) && !empty($name)&& !empty($betrag) && !empty($datum)){
    
    $stmt = $pdo->prepare("INSERT INTO `haushaltkasseeintraege`(`haushaltMitgliederID`, `EintragID`, `betrag`, `datum`) VALUES (:name,:eintragsart,:betrag,:datum);");

    preg_match('/\d+/', $eintragsart, $matches);
    preg_match('/\d+/', $name, $matches2);
    

    $zahl1 = (int)$matches[0];
    $zahl2 = (int)$matches2[0];
    

    // Bind parameters
    
    $stmt->bindParam(':eintragsart', $zahl1, PDO::PARAM_INT);
    $stmt->bindParam(':name', $zahl2, PDO::PARAM_INT);
    $stmt->bindParam(':betrag', $betrag, PDO::PARAM_STR);
    $stmt->bindParam(':datum', $datum, PDO::PARAM_STR);
    
    // Execute the query
    $stmt->execute();
    $stmt = null;
    $pdo = null;
    
        header("Location: ../aktualisieren.php");
        exit();
    

    }
}
   
    
    catch(PDOException $e){
    die("query failed: ".$e->getMessage());
    
    }
    }
    else if ($menu === "löschen") {
        $eintragID = $_POST["eintragnr"];
    try {
        require_once "dbh.inc.php";

        $stmt = $pdo->prepare("DELETE FROM haushaltkasseeintraege WHERE haushaltKasseEintraegeID = :eintragID;");
        
        
        // Bind parameters
       $stmt->bindParam(':eintragID', $eintragID, PDO::PARAM_STR);
    
        // Execute the query
        $stmt->execute();
    $stmt = null;
    $pdo = null;
        
        header("Location: ../aktualisieren.php");
        exit();
        }
       
        
        catch(PDOException $e){
        die("query failed: ".$e->getMessage());
        
        }
    }
    
}
    ?>