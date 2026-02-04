<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once 'Exception.php';
require_once 'PHPMailer.php';
require_once 'SMTP.php';

/**
 * Envoie les identifiants de connexion à un nouvel utilisateur
 */
function envoyerMailMdp($emailDest, $prenom, $mdp) {
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0; 


    
    try {
        // --- CONFIGURATION SERVEUR ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dhss.lara@gmail.com'; 
        $mail->Password   = 'kplm jsdo lrgw pnqv'; //A ABSOLUMENT CHANGER PAR FABLAB
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // --- DESTINATAIRES ---
        $mail->setFrom('admin@fablab.fr', 'Gestion Fablab');
        $mail->addAddress($emailDest, $prenom);

        // --- CONTENU ---
        $mail->isHTML(true);
        $mail->Subject = "Vos identifiants Fablab";
        
        $mail->Body = "
        <div style='font-family: sans-serif; padding: 20px;'>
            <h2 style='color: #4f46e5;'>Bienvenue $prenom !</h2>
            <p>Voici tes accès pour te connecter :</p>
            <p><strong>Login :</strong> $emailDest</p>
            <p><strong>Mot de passe :</strong> <span style='color: #4f46e5;'>$mdp</span></p>
        </div>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}