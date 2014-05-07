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

?>