<?php


    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));

    $token_hash = hash("sha256",$token);

    $expiry = date("Y-m-d H:i:s",time()+60*30);
    require_once "../php/dbh.inc.php";
    $query = "UPDATE `haushaltmitglieder` SET `resetTokenHash`= :token,`resetTokenExpiresAt`= :zeit WHERE email = :email ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':token', $token_hash, PDO::PARAM_STR);
    $stmt->bindParam(':zeit', $expiry, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    $stmt->execute();
    
    if ($stmt->rowCount()>0){
//Create an instance; passing `true` enables exceptions
        
    $mail = require __DIR__."/mailer.php";
        
        $mail->setFrom("noreply@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
        Click <a href= "http://localhost/Haushhaltkasse3/reset-password.php?token=$token">here</a>
        to reset your Password
        END;
        try {
            $mail->send();
        }
        catch (Exception $e){
                echo "Email kann nicht geschickt werden. Mailer error: {$mail->ErrorInfo}";
        }
    }
    header('Location: ../successful.php');
    exit();

