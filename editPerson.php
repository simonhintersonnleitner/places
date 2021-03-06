<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


include 'system/php/functions.php';
checklogin();


$pagetitle = "Profil bearbeiten";

$firstname = "";
$lastname ="";
$description = "";
$email = "";
$pw = "";
$pw_control ="";
$cover = "";


$error = array("","","","","","","");

//fetch acutal user date from database
if(isset($_POST['edit']))
{
  $id = $_POST['id'];
  $_SESSION['userEditId'] = $id;
  $stm = $dbh->prepare("SELECT * FROM user WHERE id = ?");
  $stm->execute(array($id));
  $response = $stm->fetch();

  $firstname = $response->firstname;
  $lastname = $response->lastname;
  $description = $response->description;
  $email = $response->email;
  $_SESSION['cover'] = $response->cover;
}

//update values in database
if(isset($_POST['submit']))
{
  $id =  $_SESSION['userEditId'];//save user id for update query
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $description = strip_tags($_POST['description'], '<br><p><b><strong><a><ul><li><ol>');


  if($_POST['pw'] != "")
  {
    $pw = $_POST['pw'];
    $error3 = checkValue($pw);
  }

  if($_POST['pw_control'] != "")
  {
    $pw_control = $_POST['pw_control'];
    $error[3] = checkValue($pw_control);
  }

  //check if is an image to upload
  if(basename($_FILES['file']['name']))
  {
    $cover = basename($_FILES['file']['name']);
    $error[4] = checkExt($cover);
    $newImage = true;
  }
  else
  {
    $cover = $_SESSION['cover'];
  }
  unset($_SESSION['cover']);

  $error[0] = checkValue($firstname);
  $error[1] = checkValue($lastname);

  if($error3 == "" &&  $error4 == "")
  {
    $error4 = checkPw($pw,$pw_control);
  }

  if($error[0] == "" && $error[1] == ""  && $error[3] =="" && $error[4]  == "")
  {
    try
    {
      if( $pw_control != "") //override the old password if a new is set
      {
        //$pw = password_hash($pw, PASSWORD_DEFAULT);
        $pw = hashPasswordSecure($pw);
        $sth = $dbh->prepare("UPDATE user SET
          firstname = ?, lastname = ?, description = ?, pw = ?, cover = ?
          WHERE id = ?;");
        $sth->execute(array($firstname,$lastname,$description,$pw,$cover,$id));
      }
      else
      {
       $sth = $dbh->prepare("UPDATE user SET firstname = ?, lastname = ?, description = ?,cover = ? WHERE id = ?;");
       $sth->execute(array($firstname,$lastname,$description,$cover,$id));
      }

      if($newImage)
      {
        uploadProfileImage($id);
      }
    }
    catch (Exception $e)
    {
      die("Problem with updating Data!" . $e->getMessage() );
    }

    if($newImage)
    {
      $_SESSION['userId'] = $id;
      header("Location: crop.php");
      exit;
    }
    else
    {
      header("Location: person.php?id={$id}");
      exit;
    }
}


}

function checkValue ($value)
{
  if($value == "")
    return  "dieses Feld darf nicht leer sein";
  else
    return "";
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

<link rel="stylesheet" type="text/css" href="system/css/index.css">
<script src="system/tinymce/js/tinymce/tinymce.min.js" type="text/javascript"></script>

<?php
include 'template/endHeader.php';
include 'template/menue.php';
?>

<script type="text/javascript">

function chkForm ()
{
  noError = true;
  errorMsg = "dieses Feld darf nicht leer sein";
  errorMsg1 = "Passwort muss mindestens 6 Zeichen besitzen";

  for (var i =  1; i <= 5; i++)
  {
    //reset all errors
    document.getElementById([i]).innerHTML = "";
  }

  //check firstname and lastname
  for (var i =  1; i <= 2; i++)
  {
    if( document.getElementById("input"+[i]).value == "")
    {
      document.getElementById([i]).innerHTML = errorMsg;
      noError = false;
    }
  }
  //check pw and pwcontrol
  if(document.getElementById("input3").value != "")
  {
    for (var i =  3; i <= 4; i++)
    {
     if(document.getElementById("input"+[i]).value.length < 6)
     {
      document.getElementById([i]).innerHTML = errorMsg1;
      noError = false;
    }
  }

  if(document.getElementById("input3").value != document.getElementById("input4").value)
  {
    document.getElementById("4").innerHTML = "Passwörter stimmen nicht überein!";
    noError = false;
  }
}
  //check fileextension
  if(document.getElementById("input5").value != "")
  {
    var ext = document.getElementById("input5").value.split(".");

    if(ext[ext.length-1].toLowerCase()  != "png" && ext[ext.length-1].toLowerCase() != "jpg")
    {
      document.getElementById("5").innerHTML = "ungültige Dateiendung!";
      noError = false;
    }
  }

  return noError;
}

$( document ).ready(function() {

  if ($( window ).width() > 600)
  {
    tinyMCE.init({
      selector:'textarea',
      menubar:false,
      theme: "modern",
      skin: 'lightgray',
      plugins: [
      "advlist autolink link lists charmap print preview hr anchor pagebreak paste"
      ],
      toolbar: "bold alignleft aligncenter alignright alignjustify bullist numlist outdent indent  link preview",
      statusbar: false
    })

  }

});

$( window ).resize(function() {

  if ($( window ).width() > 600)
  {
    tinyMCE.init({
      selector:'textarea',
      menubar:false,
      theme: "modern",
      skin: 'lightgray',
      plugins: [
      "advlist autolink link lists charmap print preview hr anchor pagebreak paste"
      ],
      toolbar: "bold alignleft aligncenter alignright alignjustify bullist numlist outdent indent  link preview",
      statusbar: false
    })
  }

});

</script>

<div class="container">
  <div class="hero-unit">
    <h2>Mein Profil bearbeiten</h2><br>
  </div>

  <form class="form-horizontal" action="editPerson.php" method="post" role="form" onsubmit="return chkForm()" enctype="multipart/form-data">

    <div class="form-group">
      <label for="input1" class="col-sm-2 control-label" >Vorname</label>
      <div class="col-sm-7">
        <input type="text" class="form-control" name="firstname" id="input1" placeholder="Vorname" value="<?php echo $firstname; ?>">
        <span class="error-inline" id="1"><?php echo $error[0]; ?></span>
      </div>
    </div>


    <div class="form-group">
      <label for="input2" class="col-sm-2 control-label" >Nachname</label>
      <div class="col-sm-7">
        <input type="text" class="form-control" name="lastname" id="input2" placeholder="Nachname" value="<?php echo $lastname; ?>">
        <span class="error-inline" id="2"><?php echo $error[1]; ?></span>
      </div>
    </div>

    <div class="form-group">
      <label for="input3" class="col-sm-2 control-label" >Email</label>
      <div class="col-sm-7">
        <input type="text" class="form-control" name="email" id="input7" placeholder="Email" value="<?php echo $email; ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label for="input4" class="col-sm-2 control-label" >Beschreibung</label>
      <div class="col-sm-7">
        <textarea class="form-control" rows="5" name="description" id="input8" placeholder="Beschreibung"><?php echo $description; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="input5" class="col-sm-2 control-label" >neues Passwort</label>
      <div class="col-sm-7">
        <input type="password" class="form-control" name="pw" id="input3"  value="<?php echo $pw; ?>" placeholder="Passwort">
        <span class="error-inline" id="3"><?php echo $error[2]; ?></span>
      </div>
    </div>


    <div class="form-group">
      <label for="input6" class="col-sm-2 control-label" >Passwort wiederholen</label>
      <div class="col-sm-7">
        <input type="password" class="form-control" name="pw_control" id="input4" value="<?php echo $pw_control; ?>" placeholder="Passwort wiederholen">
        <span class="error-inline" id="4"><?php echo $error[3]; ?></span>
      </div>
    </div>

     <div class="form-group">
      <label for="input7" class="col-sm-2 control-label" >neues Profilfoto</label>
      <div class="col-sm-7">
        <input  name="file" type="file" id="input5">
        <span class="error-inline" id="5"><?php echo $error[4]; ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="submit"></label>
      <div class="col-sm-2">
        <input  class="form-control" type="submit" name="submit" value="Fertig" >
      </div>
    </div>

  </form>
</div>

<?php
include 'template/footer.php';
?>