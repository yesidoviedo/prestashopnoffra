<?php 
include_once(_PS_SWIFT_DIR_.'swift_required.php');

class Mail extends MailCore
{

public static function sendMailTest($smtp_checked, $smtp_server, $content, $subject, $type, $to, $from, $smtp_login, $smtp_password, $smtp_port = 25, $smtp_encryption)
    {

        $result = false;
        try {
            if ($smtp_checked) {
                if (Tools::strtolower($smtp_encryption) === 'off') {
                    $smtp_encryption = false;
                }
                $smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port, $smtp_encryption)
                    ->setUsername($smtp_login)
                    ->setPassword($smtp_password);
                $swift = Swift_Mailer::newInstance($smtp);
            } else {
                $swift = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
            }

            $message = Swift_Message::newInstance();

            $message
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($content);

            if ($swift->send($message)) {
                $result = true;
            }
        } catch (Swift_SwiftException $e) {
            $result = $e->getMessage();
        }

        return $result;
    }



}

 ?>