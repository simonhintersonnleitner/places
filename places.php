<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";



 $stm = $dbh->query("SELECT * FROM places WHERE public ='1' ORDER BY time DESC ;");
 $response = $stm->fetchAll();


 $userId = $_SESSION['id'];
 $stm = $dbh->query("SELECT * FROM user WHERE id = $userId;");
 $response1 = $stm->fetch();

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<div class="container">
  <h1>Alle Orte</h1>
      <table class="table">
    <?php foreach ($response as $place):
      ?>
      <tr>
        <td>
           <a href="place.php?id=<?php echo $place->id; ?>"><?php echo $place->name;?>
        </td>
          <?php if($response1->isAdmin != 0 ||  $place->userId == $_SESSION['id']):?>
        <td>
          <form action="placeDelete.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $place->id; ?>'>
            <input type="submit" name="del"  class="form-control input-sm" value="löschen">
          </form>
        </td>
        <td>
          <form action="editPlace.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $place->id; ?>'>
            <input type="submit" name="edit"  class="form-control input-sm" value="bearbeiten">
          </form>
        </td>
        <?php endif; ?>
    </tr>
   <?php endforeach; ?>
</table>


  </div>
</div>



<?php
include 'template/footer.php';
?>