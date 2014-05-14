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

function uploadImage($newId)
{
   $folder ="./img/upload/";
   $filename = basename($_FILES['file']['name']);


     if(!file_exists($folder.$_SESSION["id"]."/"))
       {
          mkdir($folder.$_SESSION["id"]."/");
       }
       //delete all old images
      if(file_exists($folder.$_SESSION["id"]."/".$newId."/"))
       {

         removedir($folder.$_SESSION["id"]."/".$newId."/");
       }

       mkdir($folder.$_SESSION["id"]."/".$newId."/");

       $uploaddir = $folder.$_SESSION["id"]."/".$newId."/";
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


function getAllCategories($dbh)
{
  try
  {
    return $dbh->query("SELECT * FROM categorys;")->fetchAll();
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

/**
 *
 * @author    Patrick W.
 * @website    http://www.it-talent.de
 * @date    02/06/2013
 *
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