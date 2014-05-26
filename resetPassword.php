<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


include "system/php/functions.php";


$pagetitle = "Meine Lieblingsorte";

$error = "";
$pw = "";
$pw_control = "";

//check email and send resetmail
if(isset($_POST["reset"]))
{
  $pw = $_POST["pw"];
  $pw = $_POST["pw_control"];

  $key = $_SESSION['key'];

  $error = checkPw($_POST["pw"],$_POST["pw_control"]);

  if($error == "")
  {
    //fetch userId with key
    $stm = $dbh->prepare("SELECT userId FROM reset WHERE resetKey = ?");
    $stm->execute(array($key));
    $response = $stm->fetch();

    //delete key and userid
    $sth = $dbh->prepare("DELETE FROM reset WHERE resetKey = ?;");
    $sth->execute(array($key));

    if($response != null)
    {
      //set new pw
      $pw = hashPasswordSecure($_POST['pw']);
      $id = $response->userId;
      $sth = $dbh->prepare("UPDATE user SET pw = ? WHERE id = ?;");
      $sth->execute(array( $pw,$id));

      unset($_SESSION['key']);
      header("Location:login.php?msgId=5");
      exit;
    }
    else
    {
      unset($_SESSION['key']);
      header("Location:login.php?msgId=6");
      exit;
    }
  }
}

//store key for next call
if(isset($_GET['key']))
{
  $key = $_GET['key'];
  $_SESSION['key'] = $_GET['key'];
}

function checkPw($pw1,$pw2)
{

  if(strlen($pw1) < 5 || strlen($pw2) < 5 )
  {
    return "Passwörter sind zu kurz!";
  }
  if($pw1 != $pw2)
  {
    return "Passwörter stimmen nicht überein!";
  }
  return "";
}



include  "template/beginHeader.php";
?>

<link rel="stylesheet" type="text/css" href="system/css/login.css">

<?php include  "template/endHeader.php"; ?>

<script type="text/javascript">

function chkForm ()
{
  noError = true;
  errorMsg = "dieses Feld darf nicht leer sein";
  errorMsg1 = "Passwörter sind zu kurz!";

  document.getElementById("error").innerHTML = "";

  //check pw and pwcontrol

  for (var i =  1; i <= 2; i++)
  {
    if(document.getElementById("input"+[i]).value.length < 6)
    {
      document.getElementById("error").innerHTML = errorMsg1;
      noError = false;
    }
  }

if(document.getElementById("input1").value != document.getElementById("input2").value)
{
  document.getElementById("error").innerHTML = "Passwörter stimmen nicht überein!";
  noError = false;
}


return noError;
}

</script>

<div class="container">
  <img src="img/logo_new.png" href="login.php">

  <?php if($error == ""): ?>
     <div class="alert alert-info"><small>Gib bitte dein neues <b>Passwort</b> an.</small></div>
  <?php else:?>
      <div class="alert alert-danger"><small><?php echo $error; ?></small></div>
  <?php endif;?>

  <form class="form-signin center" role="form" action="resetPassword.php" method="post" onsubmit="return chkForm()">
    <input type="password" name="pw" id="input1" class="form-control " placeholder="Password">
    <input type="password" name="pw_control" id="input2" class="form-control " placeholder="Password wiederholen">
    <span class="error" id="error"></span>
    <input type="submit" name="reset"  class="form-control input-sm" value="Ändern">
  </form>

</div>

<?php include 'template/footer.php'; ?>
