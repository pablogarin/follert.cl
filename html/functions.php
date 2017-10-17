<?php
error_reporting(E_ALL);
ini_set("display_errors",true);
require_once('libs/swift/swift_required.php');

$cur = $dbh->query("SELECT * FROM configs;");
$configs = array();
if( isset($cur[0]) ){
  foreach( $cur as $row ){
    $configs[$row['nombre']] = $row;
  }
}

function sendEmail($to, $title, $htmlBody, $from, $attachments=null){
  global $configs;
  $mail = $configs['Cuenta de Correo']['valor'];
  $pass = $configs['Clave del Correo']['valor'];
  $serv = $configs['Servidor de Salida de Correo']['valor'];
  $port = $configs['Pruerto de Salida de Correo']['valor'];
  $secu = $configs['Tipo de Seguridad para el Correo']['valor'];
  $transport = Swift_SmtpTransport::newInstance($serv, (int)$port, $secu)
    ->setUsername($mail)
    ->setPassword($pass);
  $mailer = Swift_Mailer::newInstance($transport);
  $message = Swift_Message::newInstance($title)
    ->setFrom(array( $mail => 'Contacto '.$configs['Nombre Sitio']['valor']))
    ->setTo(array($to))
    ->setContentType("text/html")
    ->setBody($htmlBody, 'text/html')
    ->setReplyTo(array(
      $from
    ));
  if( $attachments!=null && is_array($attachments) ){
    foreach( $attachments as $name=>$file ){
      $message->attach(
        Swift_Attachment::fromPath($file)->setFilename($name)
      );
    }	
  }
  $result = $mailer->send($message);
  return $result;
}

?>
