<?php
$empfaenger = 's.hintersonnleitner@chello.at';
$betreff = 'Kontoaktvierung';
$nachricht = 'Du musst nur noch dein Email adresse verifizieren und schon kannst du Lieblingsorte mit anderen teilen.';

$header = 'From: register@meinelieblingsorte.at' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($empfaenger, $betreff, $nachricht, $header);
?>