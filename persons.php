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


$stm = $dbh->query("SELECT * FROM user WHERE isActive = 1 ORDER BY  cover DESC;");
$response = $stm->fetchAll();

$userId = $_SESSION['id'];
$stm = $dbh->query("SELECT * FROM user WHERE id = $userId;");
$response1 = $stm->fetch();


include 'template/beginHeader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endHeader.php';
include 'template/menue.php';
?>

<div class="container">
  <h3>Alle Personen</h3>
  <a href="persons.php?view=1" class="btn btn-default  btn-sm">Gitteransicht</a>
  <a href="persons.php?view=2" class="btn btn-default  btn-sm">Tabellenansicht</a>
  <br>
</div>

<div class="container">
<?php if($view == 1): /*Gridview*/?>
  <?php foreach ($response as $user):?>
    <div class='col-md-3'>
 <h3><?php echo $user->firstname." ".$user->lastname; ?></h3>
        <small><a href="places.php?userId=<?php echo  $user->id; ?>"><?php  $count = getPlaceCountByUserId($user->id,$dbh);  echo  ($count == 1) ? $count." Ort" : $count." Orte" ;?> veröffentlicht</a> </small>
      <?php
        if($user->cover != null)
        {
          $srcParts = pathinfo("img/upload/".$user->id.'/'.$user->cover);
          $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];
        }
        else
        {
          $newSrc = "img/upload/placeholder.png";
        }
       ?>
      <a href="person.php?id=<?php echo $user->id; ?>"><img src="<?php echo  $newSrc ; ?>" alt=""></a>

   </div>
  <?php endforeach; ?>
<?php endif;?>

<?php if($view == 2):/*ListView*/?>

<div class="list-group">
    <?php foreach ($response as $user):?>

       <a href='person.php?id=<?php echo $user->id; ?>' class="list-group-item"><?php echo $user->firstname." ".$user->lastname;?><span class="badge"><?php echo getPlaceCountByUserId($user->id,$dbh);?></span>

        <?php if($response1->isAdmin != 0 || $response1->id == $user->id):?>

          <form action="personDelete.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $user->id; ?>'>
            <input type="submit" name="del"  class="btn  btn-default btn-xs" value="löschen">
          </form>

          <form action="editPerson.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $user->id; ?>'>
            <input type="submit" name="edit"  class="btn btn-default btn-xs" value="bearbeiten">
          </form>

        <?php endif; ?>
</a>

    <?php endforeach; ?>
</div>

<?php endif;?>




</div>
</div>



<?php
include 'template/footer.php';
?>