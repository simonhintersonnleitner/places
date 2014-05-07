<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";



 $stm = $dbh->query("SELECT * FROM user;");
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
  <h1>Alle User</h1>
      <table class="table">
    <?php foreach ($response as $user):?>
      <tr>
        <td>
          <a href='person.php?id=<?php echo $user->id; ?>'><?php echo $user->firstname." ".$user->lastname;?>
        </td>
        <?php if($response1->isAdmin != 0 || $response1->id == $user->id):?>
        <td>
          <form action="personDelete.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $user->id; ?>'>
            <input type="submit" name="del"  class="form-control input-sm" value="löschen">
          </form>
        </td>
        <td>
          <form action="editPerson.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $user->id; ?>'>
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