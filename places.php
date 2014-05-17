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

if(isset($_GET['userId']))
{
  $id = $_GET['userId'];
  $response = getAllPlacesByUserID($id,$dbh);
}
else
{
  $response = getAllPublicPlaces($dbh);
}

$response1 = getUserData($_SESSION['id'],$dbh);

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<div class="container">
  <?php if(isset($_GET['userId'])):?>
    <a href="places.php?view=1&userId=<?php echo $_GET['userId'] ?>" class="btn btn-default  btn-sm">Gitteransicht</a>
    <a href="places.php?view=2&userId=<?php echo $_GET['userId'] ?>" class="btn btn-default  btn-sm">Tabellenansicht</a>
  <?php else:?>
    <a href="places.php?view=1" class="btn btn-default  btn-sm">Gitteransicht</a>
    <a href="places.php?view=2" class="btn btn-default  btn-sm">Tabellenansicht</a>
  <?php endif;?>
</div>

<div class="container">
  <?php if($view == 1): /*Gridview*/?>
  <?php foreach ($response as $place):
  ?>
  <div class='col-md-3'>
    <h3><?php echo $place->name; ?></h3>
    <small>von <a href="person.php?id=<?php echo  $place->userId; ?>"><?php echo getUserNameById($dbh,$place->userId); ?></a></small>
    <a href="place.php?id=<?php echo $place->id; ?>"><img src= <?php echo getCroppedPlaceImageNameById($place->id,$dbh); ?> alt=""></a>

  </div>

<?php endforeach; ?>

<?php endif;?>

<?php if($view == 2):/*ListView*/?>
  <table class="table">
    <?php foreach ($response as $place):?>
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
<?php endif;?>

</div>
<?php include 'template/footer.php';?>