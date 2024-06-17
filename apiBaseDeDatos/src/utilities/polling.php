<?php
require_once "../../vendor/autoload.php";

use Collections\CollectionsStream;
use Collections\Collector;

//require_once "../../public/index.php";
/*
    ESTE ARCHIVO SE TIENE QUE EJECUTAR POR SEPARADO
*/
use PHPMailer\PHPMailer\PHPMailer;

$credenciales = (array) json_decode(file_get_contents('credenciales.json'));

// create a new object
$mail = new PHPMailer();
// configure an SMTP
$mail->isSMTP();
$mail->Host = 'sandbox.smtp.mailtrap.io';
$mail->SMTPAuth = true;
$mail->Username = $credenciales['Username'];
$mail->Password = $credenciales['Password'];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('trueca-notificador@trueca.com', 'Your Hotel');
$mail->addAddress('receiver@gmail.com', 'Me');
$mail->Subject = 'INTERCAMBIO MODIFICADO!!!';
// Set HTML 
$mail->isHTML(TRUE);
$mail->Body = '<html><h1>Intercambio Modificado</h1></br> Tu intercambio con "user" ha sido modificado </html>';
$mail->AltBody = 'SEXO';

// send the message
if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
/* 
while (true){
    $publis = (array) json_decode($publiDB->getAll([]));

    $collector = Collector::of(Collector::TO_FLAT_ARRAY, fn ($obj) => $obj);
    $stream = new CollectionsStream($publis);
    error_log("antes del stream: \n" . count($publis));
    $publis = $stream
    ->reject(function ($publi) {
        global $publiCentroDB, $centroVolunDB;
        $publi = (array) $publi;
        $valido = true;
        $centros = (array) json_decode($publiCentroDB->getAll(['publicacion' => $publi['id']]));
        //error_log(json_encode($centros));
        foreach ($centros as $key) {
            $id = (array) $key;
            $id = $id['centro'];
            //error_log('Centro '.$id.': '. $centroVolunDB->getFirst(['centro'=>$id]));
            $valido = $valido && !empty((array)json_decode($centroVolunDB->getFirst(['centro' => $id])));
            //error_log('valid='.((!$valido)?'true':'false'));
        }
        return $valido;
    })
    ->reject(function ($publi){
        $publi = (array) $publi;
        return $publi['estado'] != 'alta';
    })
    ->collect($collector);
    error_log("dsp del stream \n" . count($publis));
    sleep(86400); // 24 hs
} */
?>