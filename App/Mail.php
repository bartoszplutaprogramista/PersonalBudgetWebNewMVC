<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
 
class Mail
{
    public static function send($to, $subject, $text, $html)
    {

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = Config::MAIL_HOST();
            $mail->SMTPAuth = true;
            $mail->Username = Config::MAIL_USERNAME();
            $mail->Password = Config::MAIL_PASSWORD();
            $mail->SMTPSecure = Config::MAIL_SECURE();
            $mail->Port = Config::MAIL_PORT();
			$mail->CharSet = "UTF-8";
            $mail->isHTML(true);

            $mail->setFrom(Config::MAIL_FROM());
            $mail->FromName = Config::MAIL_FROM_NAME();
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $html;

            $mail->send();

            $sent = true;

        } catch (Exception $e) {

            $errors[] = $mail->ErrorInfo;

        }
    }
}