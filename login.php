<?php

include "system/php/functions.php";


$pagetitle = "Meine Lieblingsorte";


$error = "";

//check username and password
if(isset($_POST["login"]))
{
  $stm = $dbh->prepare("SELECT id,pw,firstname,isAdmin,isActive FROM user WHERE email = ?");
  $stm->execute(array($_POST["email"]));
  $response = $stm->fetch();
  if($response != null)
  {

    if($response->isActive == 1)
    {
      //if(password_verify($_POST["pw"], $response->pw))
      if(verifyPw($_POST["pw"], $response->pw))
      {
        $_SESSION['firstname'] = $response->firstname;
        $_SESSION['id'] = $response->id;
        $_SESSION['isAdmin'] = $response->isAdmin;
        header("Location: index.php");
        exit;
      }
     else
      {
       $error = "<br>Benutzername und Passwort stimmen nicht überein!";
      }
    }
    else
    {
      $error = "<br>Konto ist nicht aktiviert!";
    }
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
 <img src="img/logo_new.png">

<?php if(isset($_GET['msgId'])):?>
  <?php if($_GET['msgId'] == 1):?>
      <div class="alert alert-info"><small><b>Aktivierungsemail erfolgreich versendet!</b><br>Bitte checke deine Mailbox (auch den Spamordner) und bestätige
      deine Emailadresse mit dem darin enthaltenen Link.</small></div>
  <?php endif;?>
  <?php if($_GET['msgId'] == 2):?>
      <div class="alert alert-success"><small><b>Konto erfolgreich aktviert!</b><br>Du kannst dich nun mit deinen Zugangsdaten einloggen.</small></div>
  <?php endif;?>
  <?php if($_GET['msgId'] == 3):?>
      <div class="alert alert-danger"><small><b>Aktvierung nicht erfolgreich</b><br>Entweder dein Konto wurde schon aktviert oder dein Aktvierungscode ist ungültig.</small></div>
  <?php endif;?>
  <?php if($_GET['msgId'] == 4):?>
      <div class="alert alert-info"><small><b>Email erfolgreich versendet!</b><br>Bitte bestätige die Passwortänderung mit dem Link in deiner Email<br></small></div>
  <?php endif;?>
   <?php if($_GET['msgId'] == 5):?>
      <div class="alert alert-success"><small><b>Passwort erfolgreich geändert!</b><br>Du kannst dich nun mit deinem neuen Passwort anmelden.<br></small></div>
  <?php endif;?>
   <?php if($_GET['msgId'] == 6):?>
      <div class="alert alert-danger"><small><b>Änderung war nicht erfolgreich!</b><br>Dein Key ist ungültig. Versuche es bitte nocheinmal.<br></small></div>
  <?php endif;?>
<?php else:?>
  <?php if($error == ""):?>
    <h4>Teile deine Lieblingsorte mit anderen.</h4>
  <?php else:?>
    <div class="alert alert-danger"><small><?php echo $error; ?></small></div>
  <?php endif;?>
<?php endif;?>
  <form class="form-signin center" role="form" action="login.php" method="post">
    <input type="email" name="email" class="form-control " placeholder="Email-Adresse">
    <input type="password" name="pw" id="pwForm" class="form-control" placeholder="Passwort" >
    <small><small><a href="requestPasswordChange.php">Passwort vergessen</a></small></small>
   <input type="submit" name="login"  class="form-control input-sm" value="Anmelden">
    <p><small>Du hast noch kein Konto? - <a href="register.php">Neues Konto erstellen</a></small></p>
  </form>
</div>
<?php
include 'template/footer.php';
?>
