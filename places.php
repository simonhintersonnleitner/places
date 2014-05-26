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

include 'template/beginHeader.php'; ?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endHeader.php';
include 'template/menue.php';
?>

<div class="container">
  <?php if(isset($_GET['userId'])):?>
    <h3><?php echo getFirstnameById($dbh,$_GET['userId'])?>'s Lieblingsorte</h3>
    <a href="places.php?view=1&userId=<?php echo $_GET['userId'] ?>" class="btn btn-default  btn-sm">Gitteransicht</a>
    <a href="places.php?view=2&userId=<?php echo $_GET['userId'] ?>" class="btn btn-default  btn-sm">Tabellenansicht</a>
    <a href="map.php?userId=<?php echo $_GET['userId'] ?>" class="btn btn-default  btn-sm">Mapansicht</a>
  <?php else:?>
    <h3>Alle Lieblingsorte</h3>
    <a href="places.php?view=1" class="btn btn-default  btn-sm">Gitteransicht</a>
    <a href="places.php?view=2" class="btn btn-default  btn-sm">Tabellenansicht</a>
   <a href="map.php" class="btn btn-default  btn-sm">Mapansicht</a>
  <?php endif;?>
   <a href="newPlace.php" class="btn btn-default btn-sm">neuen Ort eintragen</a>
</div>

<div class="container">
  <?php if($view == 1): /*Gridview*/?>
  <?php foreach ($response as $place):
  ?>
  <div class='col-md-3'>
    <h3><?php echo $place->name; ?></h3>
    <?php $class ="c".$place->category; ?>
    <?php $img = "img/icons/".$place->category.".png"; ?>
    <small>von <a href="person.php?id=<?php echo  $place->userId; ?>"><?php echo getUserNameById($dbh,$place->userId); ?></a></small>
    <div class="<?php echo$class; ?> icon "><img src="<?php echo $img; ?>" alt=""></div>
    <a href="place.php?id=<?php echo $place->id; ?>"><img class="<?php echo $class; ?>" src= <?php echo getCroppedPlaceImageNameById($place->id,$dbh); ?> alt=""></a>

  </div>

<?php endforeach; ?>

<?php endif;?>

<?php if($view == 2):/*ListView*/?>
<div class="list-group">
    <?php foreach ($response as $place):?>

       <a href="place.php?id=<?php echo $place->id; ?>" class="list-group-item"><?php echo $place->name;?>

       <?php if($response1->isAdmin != 0 ||  $place->userId == $_SESSION['id']):?>
        <form action="placeDelete.php" method="post" class="form-inline">
          <input type="hidden" name="id" value='<?php echo $place->id; ?>'>
          <input type="submit" name="del"  class="btn btn-default btn-xs" value="löschen">
        </form>
        <form action="editPlace.php" method="post" class="form-inline">
          <input type="hidden" name="id" value='<?php echo $place->id; ?>'>
          <input type="submit" name="edit"  class="btn btn-default btn-xs" value="bearbeiten">
        </form>

    <?php endif; ?>
</a>
<?php endforeach; ?>
</div>
<?php endif;?>

</div>
<?php include 'template/footer.php';?>