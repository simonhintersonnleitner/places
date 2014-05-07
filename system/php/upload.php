<?php


$uploaddir = dirname( $_SERVER["SCRIPT_FILENAME"] ) . "/img/upload/";

$filename = basename($_FILES['file']['name']);

$ext = substr($filename, -4);

if( $ext == '.jpg' || $ext == '.png') {

	$uploadfile = $uploaddir . $filename;

  if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    echo "Datei wurde erfolgreich hochgeladen nach <a href='upload/'>upload/</a>\n";
  } else {
    echo "Problem beim Hochladen der Datei.\n";
  }

}else
{
  die("Es dürfen nur .jpep oder .png Dateien hochladen werden!");
}

?>