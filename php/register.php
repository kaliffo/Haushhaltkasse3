<?php
var_dump($_SERVER["REQUEST_METHOD"]);
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    $hashedpass = password_hash($password, PASSWORD_DEFAULT);

    try{
        require_once "dbh.inc.php";
        if (!empty($name) && !empty($username) && !empty($password) && !empty($email)){
        $query = "INSERT INTO haushaltmitglieder( haushaltMitgliederName , userName , password, email) VALUES (:name, :username, :password, :email);";

        $st = $pdo->prepare($query);
        $st->bindParam(":name", $name, PDO::PARAM_STR);
        $st->bindParam(":username", $username, PDO::PARAM_STR);
        $st->bindParam(":password", $hashedpass, PDO::PARAM_STR);
        $st->bindParam(":email", $email, PDO::PARAM_STR);
        $st->execute();
        if ($st->rowCount()>0){
            header("Location: ../anmelden.php");
            exit();
        }
        }
    }
    catch(PDOException $e){
            die("query failed: ".$e->getMessage());
            echo "something wrong happend";
        
            }
        }   