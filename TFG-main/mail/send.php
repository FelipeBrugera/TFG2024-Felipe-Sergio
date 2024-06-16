<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['send'])) {

    require_once('../index.php');

    correo('madridalcala2012@gmail.com', 'Prueba3', 'hola hola');
    echo "<script>
        alert('Correo enviado');
        document.location.href = '../index.php';
        </script>";
}

function correo($correo_destinatario, $asunto, $mensaje){
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '0padelnosmatao@gmail.com';
        $mail->Password = 'wlroutbcikpiutva'; // Asegúrate de que no haya espacios
        //$mail->SMTPSecure = 'ssl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 'tls' si usas el puerto 587
        $mail->Port = 465;

        $mail->setFrom('0padelnosmatao@gmail.com');
        $mail->addAddress($correo_destinatario); // Email receptor

        $mail->isHTML(true);
        $mail->Subject = $asunto; // Asunto
        $mail->Body = $mensaje; // Mensaje
        //$mail->Body = '<h1>Hola, esto es una prueba</h1><p>Este es un correo con <strong>HTML</strong> formateado.</p>'; // Mensaje

        $mail->send();

    } catch (Exception $e) {
        echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
    }
}

function htmlpsw($nombre, $contraseña): string{
    $html = "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Pista de Pádel</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                background-color: #4CAF50;
                padding: 20px;
                text-align: center;
                color: #ffffff;
            }
            .header img {
                max-width: 100px;
                margin-bottom: 10px;
            }
            .content {
                padding: 20px;
                text-align: center;
            }
            .content h1 {
                color: #333333;
            }
            .content p {
                color: #666666;
                line-height: 1.5;
            }
            .content .cta-button {
                display: inline-block;
                padding: 10px 20px;
                margin: 20px 0;
                background-color: #4CAF50;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
            }
            .footer {
                background-color: #333333;
                padding: 10px;
                text-align: center;
                color: #ffffff;
            }
            .footer p {
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Padel Nos Matao</h1>
            </div>
            <div class='content'>
                <h1>Nueva contraseña</h1>
                <p>Hola $nombre,</p>
                <p>Recibimos una solicitud para restablecer tu contraseña. Si no realizaste esta solicitud, puedes ignorar este correo electrónico.</p>
                <p>Tu nueva contraseña es: <strong>$contraseña</strong></p>
                <p>Para tu seguridad, te recomendamos que cambies esta contraseña inmediatamente después de iniciar sesión.</p>
                <a href='http://localhost/TFG/layouts/usuario.php' class='cta-button'>Cambiar Contraseña</a>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Padel Nos Matao. TFG Sergio Marco.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return $html;
}
