<?php
var_dump($_SERVER["REQUEST_METHOD"]);
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];


    try{
        require_once "dbh.inc.php";
        if (!empty($username)&&!empty($password)){
        $query = "SELECT password FROM haushaltmitglieder WHERE userName = :username;";
        $st = $pdo->prepare($query);
        $st->bindParam(":username", $username, PDO::PARAM_STR);
        $st->execute();
        $result = $st->fetch(PDO::FETCH_ASSOC);

            if ($result && password_verify($password, $result['password'])) {
                header("Location: ../main.php");
                exit();
            } else {
                header("Location: ../anmelden.php");
            
            }
            
        } else {
        echo "Bitte geben Sie sowohl Benutzername als auch Passwort ein.";
        
        }
        }
    catch(PDOException $e){
    die("query failed: ".$e->getMessage());
    

    }
}