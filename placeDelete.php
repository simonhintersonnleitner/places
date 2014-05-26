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

  $sth = $dbh->prepare("SELECT userId FROM places WHERE id = ?");
  $sth->execute(array($id));
  $response =  $sth->fetch();

  $sth = $dbh->prepare("DELETE FROM places WHERE id = ?");
  $sth->execute(array($id));
  //delete image folder
  echo $response->userId;
  removedir("img/upload/".$response->userId."/".$id."/");

  if($sth == 1)
  {
    header("Location: places.php");
    exit;
  }
}


?>

<p>Löschen war nicht erfolgreich!</p>

