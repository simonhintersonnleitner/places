<?php

$pagetitle = "Löschen";

include "system/php/functions.php";
checklogin();


if(isset($_POST['del']))
{
  $id   = $_POST['id'];
  $sth = $dbh->prepare("DELETE FROM places WHERE id = ?");
  $sth->execute(array($id));

  if($sth = 1)
  {
      header("Location: places.php");
      exit;
  }
}
include "header.php";

?>
<article id="body" class="row">

<p>Löschen war nicht erfolgreich!</p>

<?php
include "footer.php";
?>
