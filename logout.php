<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


// Löschen aller Session-Variablen.
$_SESSION = array();

// Löscht das Session-Cookie.
if (isset($_COOKIE[session_name()])) {
  setcookie(
    session_name(),  // Cookie-Name war gleich Name der Session
    '',             // Cookie-Daten. Achtung! Leerer String hier hilft nicht!
    time()-42000,  // Ablaufdatum in der Vergangenheit. Erst das löscht!
    '/'           // Wirkungsbereich des Cookies: der ganze Server
   );
}
session_destroy();
header("Location: login.php")

?>