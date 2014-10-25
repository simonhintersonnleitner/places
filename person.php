<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


include 'system/php/functions.php';
checklogin();

$pagetitle = "Personenprofil";

if(isset($_GET['id']))
{

  $id = $_GET['id'];
}
else
{
 $id = $_SESSION['id'];
}

$response = getUserData($id,$dbh);

if($response == null)
{
  echo "Diese ID exsitiert nicht!";
  exit;
}

$stm = $dbh->prepare("SELECT * FROM places WHERE public ='1' AND userId = ? ORDER BY time DESC ;");
$stm->execute(array($id));
$response1 = $stm->fetchAll();


$pagetitle = $response->firstname." ".$response->lastname;


include 'template/beginHeader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endHeader.php';
include 'template/menue.php';
?>

<div class="container">
  <div class="row">
    <h1><?php echo $response->firstname." ".$response->lastname; ?></h1><small> - registiert seit <?php  echo getDateFromTimeStamp($response->time);  ?></small><br><br>
    <?php if($id == $_SESSION['id'] ||  $_SESSION['isAdmin'] == 1 ):?>
      <form action="editPerson.php" method="post" class="form-inline">
        <input type="hidden" name="id" value='<?php echo $response->id; ?>'>
        <input type="submit" name="edit"  class="form-control input-sm" value="Profil bearbeiten">
      </form>
    <?php endif;?>
    <?php if($response->cover != null):?>
      <form action="crop.php" method="post" class="form-inline">
        <input type="hidden" name="id" value='<?php echo $response->id; ?>'>
        <input type="submit" name="cropUser"  class="form-control input-sm" value="Bildausschnitt ändern">
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-4">
      <?php
        if($response->cover != null)
        {
          $srcParts = pathinfo("img/upload/".$response->id.'/'.$response->cover);
          $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];
        }
        else
        {
          $newSrc = "img/upload/placeholder.png";
        }
        ?>
      <img src="<?php echo $newSrc; ?>" alt="">
    </div>
    <div class="col-md-4">
      <p>
        <?php
        if($response->description != null)
          echo $response->description;
        else
          echo "Leider keine Beschreibung verfügbar!";
        ?>
      </p>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <h3><?php echo $response->firstname;?>'s Lieblingsorte</h3>
     <a href="map.php?userId=<?php echo  $id; ?>" class="btn btn-default btn-sm">Mapansicht</a>
     <a href="places.php?userId=<?php echo  $id; ?>" class="btn btn-default btn-sm">Gitteransicht</a>
     <br><br>
    <div class="list-group">
      <?php foreach ($response1 as $place):?>
        <a href="place.php?id=<?php echo $place->id; ?>" class="list-group-item" ><?php echo $place->name;?></a>
      <?php endforeach;?>
    </div>
  </div>
</div>

<?php include 'template/footer.php'; ?>