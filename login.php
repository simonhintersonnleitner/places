<?php
include 'system/php/functions.php';

$pagetitle = "Meine Lieblingsorte";

$error = "";

//check username and password
if(isset($_POST["login"]))
{
  $stm = $dbh->prepare("SELECT id,pw,firstname,isAdmin FROM user WHERE email = ?");
  $stm->execute(array($_POST["email"]));
  $response = $stm->fetch();
  if($response != null)
  {
    //if(password_verify($_POST["pw"], $response->pw))
    if(verifiyPw($_POST["pw"], $response->pw))
    {
      $_SESSION['firstname'] = $response->firstname;
      $_SESSION['id'] = $response->id;
      $_SESSION['isAdmin'] = $response->isAdmin;
      header("Location: index.php");
      exit;
    }
   else
    {
     $error = "Benutzername und Passwort stimmen nicht überein!";
    }
  }
  else
  {
   $error = "Benutzername nicht vorhanden!";
  }
}

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/login.css">
<?php
include 'template/endheader.php';
//<p><b>Teile deine Lieblingsorte mit anderen.</b></p>
?>


<div class="container">

  <form class="form-signin center" role="form" action="login.php" method="post">
      <img src="img/logo_new.png"><br>


    <input type="email" name="email" class="form-control" placeholder="Email-Adresse"  >
    <input type="password" name="pw" class="form-control" placeholder="Passwort" >

    <span class="error"><?php echo $error; ?><br></span>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Anmelden</button>
    <p>Du hast noch kein Konto? - <a href="register.php">Registieren</a></p>
  </form>
</div>

<?php
include 'template/footer.php';
?>