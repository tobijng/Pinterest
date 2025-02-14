<?php

//test script for mailhelper.php


require_once __DIR__ . '/../helpers/MailHelper.php';
use Helpers\MailHelper;

$email = 'tobiasjung112@gmail.com';// E-Mail des Benutzers
$userid = 1;
$result = MailHelper::sendPasswordResetEmail($email, $userid);

echo $result;  // Ausgabe des Ergebnisses (Erfolg oder Fehler)
