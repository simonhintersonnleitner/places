<?php

include "system/php/functions.php";

if(isset($_GET['key']))
{
  $key = $_GET['key'];
  echo $key;

  //fetch userId with key
  $stm = $dbh->prepare("SELECT userId FROM activation WHERE actKey = ?");
  $stm->execute(array($key));
  $response = $stm->fetch();

  //delete key and userid
  $sth = $dbh->prepare("DELETE FROM activation WHERE actKey = ?;");
  $sth->execute(array($key));

  if($response != null)
  {
     //set user as active
    $id = $response->userId;
    $sth = $dbh->prepare("UPDATE user SET isActive = ? WHERE id = ?;");
    $sth->execute(array(1,$id));

    header("Location:login.php?msgId=2");
    exit;
  }
  else
  {
    header("Location:login.php?msgId=3");
    exit;
  }


}


?>