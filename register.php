<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

include 'system/php/functions.php';

$pagetitle = "Meine Lieblingsorte";


$error = array("","","","","","","");



$firstname ="";
$lastname ="";
$email ="";
$pw ="";
$pw_control ="";


//check username and password
if(isset($_POST["register"]))
{
  $firstname = $_POST["firstname"];
  $lastname = $_POST["lastname"];
  $email = $_POST["email"];
  $pw = $_POST["pw"];
  $pw_control = $_POST["pw_control"];


  $error[0] = checkValue($firstname,1,$dbh);
  $error[1] = checkValue($lastname,2,$dbh);
  $error[2]= checkValue($email,3,$dbh);
  $error[3] = checkValue($pw,4,$dbh);
  $error[4] = checkValue($pw_control,5,$dbh);
    //ceck that passwords are equal
  if($error[3]  == "" && $error[4] == "")
  {
    $error[4] = checkPw($pw,$pw_control);
  }

  if($error[0] == "" && $error[1] == "" && $error[2] == "" && $error[3] == "" && $error[4] == "")
  {

    $pw = $_POST['pw'];
    //$hashOfPw = password_hash($pw, PASSWORD_DEFAULT); requirdes
    $hashOfPw = hashPasswordSecure($pw);

    $stm = $dbh->prepare("INSERT INTO user (firstname,lastname,email,pw) VALUES (?,?,?,?);");
    $stm->execute(array($firstname,$lastname,$email,$hashOfPw));

    $newId = $dbh->lastInsertId();

    $stm = $dbh->prepare("INSERT INTO  activation (actKey,userId) VALUES (?,?);");
    $key = md5(microtime().rand());
    $stm->execute(array($key,$newId));

    sendActivationEmail($email,$firstname,$key);

    header("Location: login.php?msgId=1");
    exit;
  }
  else
  {
    $error[2] = "diese Email-Adresse ist schon registiert!";
  }


}

function checkEmailAvailability($emailToCheck,$dbh)
{
  $sth = $dbh->query("SELECT count(*) as anzahl FROM user WHERE email = '{$emailToCheck}'")->fetch();
  if($sth->anzahl == 0)
    return true;
  return false;
}

function checkValue ($value,$pos,$dbh)
{
  if($value != "")
  {
    if($pos != 3)
    {
      return "";
    }
    else //if email special validation
    {
      $isAt = strpos($value, '@');
      if($isAt === false)
        return "Keine gültige E-Mail-Adresse!";
      else
         if(checkEmailAvailability($value,$dbh))
          return "";
       else
        return "diese Adresse exsitiert bereits";
    }
  }
  else
  {
    return  "dieses Feld darf nicht leer sein";
  }
}

function checkPw($pw1,$pw2)
{
  if($pw1 != $pw2)
  {
    return "Passwörter stimmen nicht überein!";
  }
  return "";
}

include 'template/beginHeader.php';
?>

<link rel="stylesheet" type="text/css" href="system/css/login.css">
<script type="text/javascript" src="system/js/formValidation.js"></script>

<?php
include 'template/endHeader.php';
?>


<div class="container">
  <img src="img/logo_new.png">
  <form class="form-signin center" name="registerForm" role="form" action="register.php" method="post" onsubmit="return chkForm()">
    <h3 class="form-signin-heading">neues Konto erstellen</h3>
    <input type="text"  id="input1" name="firstname" class="form-control" placeholder="Vorname"  value="<?php echo  $firstname ?>">
    <p><span class="error" id="1"><?php echo $error[0]; ?></span></p>
    <input type="text" id="input2" name="lastname" class="form-control" placeholder="Nachname"  value="<?php echo  $lastname ?>">
    <p><span  class="error" id="2"><?php echo $error[1]; ?></span></p>
    <input type="email" id="input3" name="email" class="form-control" placeholder="Email-Adresse" value="<?php echo  $email ?>" >
    <p><span class="error" id="3"><?php echo $error[2]; ?></span></p>
    <input type="password" id="input4" name="pw" class="form-control" placeholder="Passwort" value="<?php echo  $pw ?>">
    <p><span class="error" id="4"><?php echo $error[3]; ?></span></p>
    <input type="password" id="input5" name="pw_control" class="form-control" placeholder="Passwort wiederholen"  value="<?php echo  $pw_control ?>">
    <p><span class="error" id="5"><?php echo $error[4]; ?></span></p>
    <input type="submit" class="form-control input-sm" name="register" value="Registieren">
  </form>
</div>

<?php include 'template/footer.php'; ?>

