<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


$pagetitle = "Löschen";

include "system/php/functions.php";
checklogin();


if(isset($_POST['del']))
{
  $id   = $_POST['id'];
  $sth = $dbh->prepare("DELETE FROM user WHERE id = ?");
  $sth->execute(array($id));

  if($sth = 1)
  {
      header("Location: persons.php");
      exit;
  }
}
?>


<p>Löschen war nicht erfolgreich!</p>

