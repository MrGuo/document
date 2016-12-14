<?php
namespace lib\mailer;

class Send {

    static $to = array(
        array('email' => 'xxx@xxxx.com', 'name' => 'Chao Guo'),
        array('email' => 'xxxx@xxxx.com', 'name' => 'Chao Guo'),
    );

    static $from = array('email' => 'xxx@xxx.com', 'name' => 'cguo');

    public static function run($subject, $html) {

        $mail = new \lib\mailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';

        $mail->Host = "smtp.partner.outlook.cn";
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = "xxxxx@xx.com";
        $mail->Password = "pass";

        $mail->setFrom(self::$from['email'], self::$from['name']);
        foreach (self::$to as $to) {
            $mail->addAddress($to['email'], $to['name']);
        }
        $mail->Subject = $subject;
        $mail->msgHTML($html, dirname(__FILE__));
        $mail->AltBody = '';

        if (!$mail->send()) {
            return false;
        } 
        else {
            return true;
        }
    }
}
