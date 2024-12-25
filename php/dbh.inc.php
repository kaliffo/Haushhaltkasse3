<?php



$dsn ="mysql:host=localhost;dbname=hashaltKasse";
$dbusername = "root";
$dbpassword = "";

try{
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected";
}catch(PDOException $e){
    echo "Connection failed: ".$e->getMessage();

}