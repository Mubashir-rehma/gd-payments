<?php

require __DIR__ . './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';

$Server = 'localhost';
$userName = 'root';
$password = '';
$DB = 'logisticscrm';
date_default_timezone_set('America/New_York');

$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'accounting@gtmmtransportation.com';                 // SMTP username
$mail->Password = 'qcsyiljldvjuiwtp';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->SMTPAutoTLS = false;
$mail->Port = 587;

$mail->From = 'accounting@gtmmtransportation.com';
$mail->FromName = 'Accounts';


$mail->WordWrap = 50;                                 // Set word wrap to 50 characters

$mysqli = new mysqli($Server, $userName, $password, $DB) or die(mysqli_error($mysqli));

$now = new DateTime("now", new DateTimeZone('America/New_York'));
$now = $now->format('Y-m-d H:i:s');