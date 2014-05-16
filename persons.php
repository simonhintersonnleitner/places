<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";

if(isset($_GET['view']))
{
  $view = $_GET['view'];
}
else
{
  $view = 1;
}


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
  <a href="persons.php?view=1" class="btn btn-default  btn-sm">Gitteransicht</a>
  <a href="persons.php?view=2" class="btn btn-default  btn-sm">Tabellenansicht</a>
  <br>
</div>

<div class="container">
<?php if($view == 1): /*Gridview*/?>
  <?php foreach ($response as $user):?>
    <div class='col-md-3'>
      <h3><?php echo $user->firstname." ".$user->lastname; ?></h3>
      <?php
        /*$srcParts = pathinfo("img/upload/".$user->userId.'/'.$place->id.'/'.$place->cover);
        $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];*/
       ?>
      <a href="person.php?id=<?php echo $user->id; ?>"><img src="" alt=""></a>
   </div>
  <?php endforeach; ?>
<?php endif;?>

<?php if($view == 2):/*ListView*/?>
<div class="table-responsive">
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
<?php endif;?>




</div>
</div>



<?php
include 'template/footer.php';
?>