<?php
$pagetitle = "";

session_start();

include "system/php/config.php";


if( ! $DB_NAME ) die('please create config.php, define $DB_NAME, $DB_USER, $DB_PASS there');

try {
  $dbh = new PDO($DSN, $DB_USER, $DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
  $dbh->exec('SET CHARACTER SET utf8') ;
} catch (Exception $e) {
  die("Problem connecting to database $DB_NAME as $DB_USER: " . $e->getMessage() );
}



function checkLogin()
{   //if user is not logged in - redirect to login.php
  if(!isset($_SESSION['id']))
  {
   header("Location:login.php");
   exit;
 }
}

function sendActivationEmail($mail,$firstname,$key)
{
try
  {
$message = "
<html>
<head>
  <meta charset='UTF-8'>
</head>
<style>
@import url(http://fonts.googleapis.com/css?family=Raleway:400,300,200;);
body {
 font-family: 'Raleway', sans-serif;
 font-weight: 300;
}
.container {
  width: 200px;
  margin: 0 auto;
}
</style>
<body>

<img src='http://".$_SERVER['HTTP_HOST']."/mmp1/img/logo_new.png'>
<div style='width: 200px;margin: 0 auto'>
<h1 style='font-family: sans-serif'>Hallo ".$firstname."</h1>
<p style='font-family: sans-serif; font-size: 12px;'>Um die Anmeldung erfolgreich abzuschlie&szlig;en ist es erforderlich, dass du deine E-Mail-Adresse best&auml;tigst.
  <br>Klicke dazu bitte auf den folgenden <a href='http://".$_SERVER['HTTP_HOST']."/mmp1/activation.php?key=".$key."'>Link</a></p>
</div>
</body>
</html>";

$from   = "Meine Lieblingorte";
$subject    = "Kontoaktvierung";

$header  = "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1\r\n";

$header .= "From: $from\r\n";
$header .= "Reply-To: s.hintersonnleitner@chello.at\r\n";
$header .= "X-Mailer: PHP ". phpversion();

if(mail($mail,$subject,$message,$header))
    echo "true";
else
  echo "false";
}
catch (Exception $e)
  {
    die("Problem with sending email " . $e->getMessage() );
  }

}


function checkExt($filename)
{
 $ext = strtolower(substr($filename, -4));
 if( $ext == '.jpg' || $ext == '.png')
 {
  return "";
}
else
 return "ungültige Dateiendung!";

}

function removedir($dir) {
 if (is_dir($dir)) {
   $objects = scandir($dir);
   foreach ($objects as $object) {
     if ($object != "." && $object != "..") {
       if (filetype($dir."/".$object) == "dir") rmdir($dir."/".$object); else unlink($dir."/".$object);
     }
   }
   reset($objects);
   rmdir($dir);
 }
}

function deletAllFilesInFolder($path)
{
    $files = glob($path); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file))
      unlink($file); // delete file
  }

}

// remove umlauts
function cleanFilename($filename)
{
  $toReplace = Array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/");
  $replace = Array("ae","oe","ue","Ae","Oe","Ue","ss");
  return preg_replace($toReplace, $replace, $filename);
}

//upload functions
function uploadPlaceImage($newId,$userId)
{
 $folder ="./img/upload/";
 $filename = cleanFilename(basename($_FILES['file']['name']));


 if(!file_exists($folder.$userId."/"))
 {
  mkdir($folder.$userId."/");
}
       //delete all old images
if(file_exists($folder.$userId."/".$newId."/"))
{

 removedir($folder.$userId."/".$newId."/");
}

mkdir($folder.$userId."/".$newId."/");

$uploaddir = $folder.$userId."/".$newId."/";
$uploadfile = $uploaddir . $filename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
{
  resize($uploadfile, $uploadfile, 1000, 500, false);
}
else
{
  echo "Problem beim Hochladen der Datei.\n";
}
}

function uploadProfileImage($userId)
{
  $folder ="./img/upload/";
  $filename = cleanFilename(basename($_FILES['file']['name']));

  if(!file_exists($folder.$userId."/"))
  {
    mkdir($folder.$userId."/");
  }

  //delete all old image
  deletAllFilesInFolder($folder.$userId."/*");

  $uploaddir = $folder.$userId."/";
  $uploadfile = $uploaddir . $filename;

  if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
  {
    resize($uploadfile, $uploadfile, 1000, 500, false);
  }
  else
  {
    echo "Problem beim Hochladen der Datei.\n";
  }
}

function getCroppedPlaceImageNameById($id,$dbh)
{
  $place = getPlaceData($id,$dbh);
  $srcParts = pathinfo("img/upload/".$place->userId.'/'.$place->id.'/'.$place->cover);
  return $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];
}



function getDateFromTimeStamp($timeStamp)
{
  $value = $timeStamp;
  $datetime = new DateTime($value);
  return $datetime->format('d.m.Y');
}


//database querys functions

function getPlaceCountByUserId($id,$dbh)
{
  try
  {
    $stm = $dbh->prepare("SELECT count(*) as count FROM places WHERE userId = ? GROUP BY userId");
    $stm->execute(array($id));
    $response1 = $stm->fetch();
    if($response1 != null)
      return strval($response1->count);
    return 0;
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}

function getAllCategories($dbh)
{
  try
  {
    return $dbh->query("SELECT * FROM categories;")->fetchAll();
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}

function getPlaceData($id,$dbh)
{
  try
  {
    $stm = $dbh->prepare("SELECT * FROM places WHERE id = ?");
    $stm->execute(array($id));
    return $stm->fetch();
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}

function getUserData($id,$dbh)
{
  try
  {
    $stm = $dbh->prepare("SELECT * FROM user WHERE id = ?");
    $stm->execute(array($id));
    $response = $stm->fetch();
    return $response;
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}

function getAllPlacesByUserID($id,$dbh)
{
  try
  {
    $stm = $dbh->prepare("SELECT * FROM places WHERE userId = ?");
    $stm->execute(array($id));
    return $stm->fetchAll();
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}

function getAllPublicPlaces($dbh)
{
  try
  {
    return $dbh->query("SELECT * FROM places WHERE public = 1")->fetchAll();
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}


function getFirstnameById($dbh,$id)
{
  try
  {
    $stm = $dbh->prepare("SELECT firstname FROM user WHERE id = ?");
    $stm->execute(array($id));
    $response1 = $stm->fetch();
    if( $response1 != null)
      return $response1->firstname;
    else
      return "";
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}



function getUserNameById($dbh,$id)
{
  try
  {
    $stm = $dbh->prepare("SELECT firstname,lastname FROM user WHERE id = ?");
    $stm->execute(array($id));
    $response1 = $stm->fetch();
    return $response1->firstname." ".$response1->lastname;
  }
  catch (Exception $e)
  {
    die("Problem with fetching Data " . $e->getMessage() );
  }
}


//hashing functions
function hashPasswordSecure($pw)
{
  $cost = 10;
  $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
  $salt = sprintf("$2a$%02d$", $cost) . $salt;
  return crypt($pw, $salt);
}

function verifyPw($pw,$pwFromDB)
{
  if(crypt($pw, $pwFromDB) === $pwFromDB)
  {
    return true;
  }
  return false;
}

/**
 * @author    Patrick W.
 * @website    http://www.it-talent.de
 * @date    02/06/2013
 **/
function resize($path, $new_path, $new_width, $new_height, $cut, $size = false) {
    /*
     *
     * @description
     *
     * Falls die getimagesize() schon vorliegt, kann sie der Methode
     * übergeben werden, das ist performanter.
     *
     **/
    $size = ($size) ? $size : getimagesize($path);

    $height_skaliert = (int)$size[1]*$new_width/$size[0];

    if (($cut) ? ($new_height < $height_skaliert) : ($new_height > $height_skaliert)) {
      $height_skaliert = $height_skaliert;
      $width_skaliert = $new_width;
    } else {
      $width_skaliert = (int)$size[0]*$new_height/$size[1];
      $height_skaliert = $new_height;
    }

    switch ($size[2]) {
        case 1:    // GIF
        $image_func = 'imagecreatefromGIF';
        $image_out = 'imageGIF';
        $q = 100;
        break;

        case 2:    // JPG
        $image_func = 'imagecreatefromJPEG';
        $image_out = 'imageJPEG';
        $q = 100;
        break;

        case 3:    // PNG
        $image_func = 'imagecreatefromPNG';
        $image_out = 'imagePNG';
        $q = 9;
        break;

        default:
        return false;
      }

      $old_image = $image_func($path);

      $new_image_skaliert = imagecreatetruecolor($width_skaliert, $height_skaliert);
      $bg = imagecolorallocatealpha($new_image_skaliert, 255, 255, 255, 75);
      ImageFill($new_image_skaliert, 0, 0, $bg);

      imagecopyresampled($new_image_skaliert, $old_image, 0,0,0,0, $width_skaliert, $height_skaliert, $size[0], $size[1]);

      if ($cut) {
        $new_image_cut = imagecreatetruecolor($new_width, $new_height);
        $bg = imagecolorallocatealpha($new_image_cut, 255, 255, 255, 75);
        imagefill($new_image_cut, 0, 0, $bg);
        imagecopy($new_image_cut, $new_image_skaliert, 0,0,0,0, $width_skaliert, $height_skaliert);
      }

      $image_out(($cut) ? $new_image_cut : $new_image_skaliert, $new_path, $q);

      if ($cut) {
        return array($new_width, $new_height, $size[2]);
      } else {
        return array(floor($width_skaliert), floor($height_skaliert), $size[2]);
      }
    }




    ?>