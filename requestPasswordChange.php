<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


include "system/php/functions.php";


$pagetitle = "Meine Lieblingsorte";

$error = "";

//check email and send resetmail
if(isset($_POST["reset"]))
{
  //check if email address is in database
  $stm = $dbh->prepare("SELECT count(*) as count, id, firstname, email FROM user WHERE email = ?");
  $stm->execute(array($_POST["email"]));
  $response = $stm->fetch();
  if($response->count != 0)
  {

    $stm = $dbh->prepare("INSERT INTO  reset (resetKey,userId) VALUES (?,?);");
    $key = md5(microtime().rand());
    $stm->execute(array($key,$response->id));

    sendResetEmail($response->email,$response->firstname,$key);

    header("Location:login.php?msgId=4");
    exit;

  }
  else
  {
     $error = "<br>Benutzername nicht vorhanden!";
  }
}

include  "template/beginHeader.php";

?>
<link rel="stylesheet" type="text/css" href="system/css/login.css">

<?php
include  "template/endHeader.php";
?>


<div class="container">
  <img src="img/logo_new.png" href="login.php">
  <?php if($error == ""):?>
    <div class="alert alert-info"><small>Gib bitte deine <b>E-Mail Adresse</b> ein um dein Passwort zurückzusetzen.</small></div>
    <?php else:?>
    <div class="alert alert-danger"><small><?php echo $error; ?></small></div>
  <?php endif;?>
  <form class="form-signin center" role="form" action="requestPasswordChange.php" method="post">
    <input type="email" name="email" class="form-control " placeholder="Email-Adresse">
    <input type="submit" name="reset"  class="form-control input-sm" value="Senden">
  </form>
</div>

<?php include 'template/footer.php'; ?>
