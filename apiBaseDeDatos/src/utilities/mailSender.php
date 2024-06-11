<?php
use PHPMailer\PHPMailer\PHPMailer;
class mailSender{
    function __construct(private string $Username,private string $Password, private string $senderAddress){
    }

    private function getMailer(){
        // create a new object
        $mail = new PHPMailer();
        // configure an SMTP
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = $this->Username;
        $mail->Password = $this->Password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom($this->senderAddress);
        return $mail;
    }

    public function send(string $receiver, string $titulo,string $mensaje,bool $html = false){
        $mail = $this->getMailer();
        $mail->addAddress($receiver);
        $mail->Subject = $titulo;
        $mail->isHTML($html);
        if($html){
            $mail->Body = "<html><div style=\"border: 2px solid #000; border-radius: 15px; padding: 15px; background-color: #f0f0f0;\"><h1 style=\"color: #000; text-shadow: 0 5px 10px rgba(0, 255, 255, 0.7);\">$titulo</h1></br><p>$mensaje</p></div></html>";
            $mail->AltBody = "$titulo \n$mensaje";
        }
        // send the message
        return $mail->send();
    }
}

?>