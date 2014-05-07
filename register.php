<?php
include 'system/php/functions.php';
$pagetitle = "Meine Lieblingsorte";

$error1 = "";
$error2 = "";
$error3 = "";
$error4 = "";
$error5 = "";

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


  $error1 = checkValue($firstname,1,$dbh);
  $error2 = checkValue($lastname,2,$dbh);
  $error3 = checkValue($email,3,$dbh);
  $error4 = checkValue($pw,4,$dbh);
  $error5 = checkValue($pw_control,5,$dbh);
    //ceck that passwords are equal
    if($error4  == "" && $error5 == "")
    {
      $error5 = checkPw($pw,$pw_control);
    }


  if($error1 == "" && $error2 == "" && $error3 == "" && $error4 == "" && $error5 == "")
  {

    $pw = $_POST['pw'];
    $hashOfPw = password_hash($pw, PASSWORD_DEFAULT);

    $stm = $dbh->prepare("INSERT INTO user (firstname,lastname,email,pw) VALUES (?,?,?,?);");
    $stm->execute(array($firstname,$lastname,$email,$hashOfPw));

    header("Location: login.php");
    exit;

  }
  else
  {
      $error3 = "diese Email-Adresse ist schon registiert!";
  }


}

function ceckEmailAvailability($emailToCheck,$dbh)
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
         if(ceckEmailAvailability($value,$dbh))
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

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/login.css">
<script type="text/javascript" src="system/js/formValidation.js"></script>
<?php
include 'template/endheader.php';
?>


<div class="container">
  <form class="form-signin center" name="registerForm" role="form" action="register.php" method="post" onsubmit="return chkForm()">
    <h2 class="form-signin-heading">Neu Registieren</h2>
    <input type="text"  id="input1" name="firstname" class="form-control" placeholder="Vorname"  value="<?php echo  $firstname ?>">
    <p><span class="error" id="1"><?php echo $error1; ?></span></p>
    <input type="text" id="input2" name="lastname" class="form-control" placeholder="Nachname"  value="<?php echo  $lastname ?>">
    <p><span  class="error" id="2"><?php echo $error2; ?></span></span></p>
    <input type="email" id="input3" name="email" class="form-control" placeholder="Email-Adresse" value="<?php echo  $email ?>" >
    <p><span class="error" id="3"><?php echo $error3; ?></span></span></p>
    <input type="password" id="input4" name="pw" class="form-control" placeholder="Passwort" value="<?php echo  $pw ?>">
    <p><span class="error" id="4"><?php echo $error4; ?></span></span></p>
    <input type="password" id="input5" name="pw_control" class="form-control" placeholder="Passwort wiederholen"  value="<?php echo  $pw_control ?>">
    <p><span class="error" id="5"><?php echo $error5; ?></span></span></p>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="register">Registieren</button>
  </form>
</div>

<?php
include 'template/footer.php';
?>