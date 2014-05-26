<?php

/**
 * @author Simon Hintersonnleitner <shintersonnleitner.mmt-b2013@fh-salzburg.ac.at>
 * Meine Lieblingsorte ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */


include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";

try
{
  $stm = $dbh->query("SELECT * FROM places ORDER BY time DESC LIMIT 6;");
  $response = $stm->fetchAll();
}
catch (Exception $e)
{
  die("Problem with inserting Data!" . $e->getMessage() );
}


include 'template/beginHeader.php';
?>

<link rel="stylesheet" type="text/css" href="system/css/index.css">

<?php
include 'template/endHeader.php';
include 'template/menue.php';
?>

<div class="container">
  <h1>Hallo <?php echo $_SESSION['firstname'];?>,</h1>

  <div class='col-md-5'>
    <p>schön dass du deine Lieblingorte mit anderen Teilen willst. Beginne am Besten gleich damit neue Orte einzutragen.</p>
    <p><a href="newPlace.php" class="btn btn-default">neuen Ort eintragen</a></p>
  </div>

  <div class='col-md-5'>
    <p>Beginne am Besten auch gleich damit dein Profil mit einer Beschreibung und einem Profilbild zu vervollständigen.</p>
    <p>
      <form action="editPerson.php" method="post" class="form-inline">
        <input type="hidden" name="id" value='<?php echo $_SESSION['id']; ?>'>
        <input type="submit" name="edit"  class="btn btn-default form-control" value="mein Profil bearbeiten">
      </form>
    </p>
  </div>
</div>

<div class="container">
  <div class="row">
    <h3>kürzlich geteilte Orte</h3>
    <?php foreach ($response as $place):?>
      <div class='col-md-3'>
        <h3><?php echo $place->name; ?></h3>
        <?php $class ="c".$place->category; ?>
        <?php $img = "img/icons/".$place->category.".png"; ?>
        <small>von <a href="person.php?id=<?php echo  $place->userId; ?>"><?php echo getUserNameById($dbh,$place->userId); ?></a></small>
        <?php
        $srcParts = pathinfo("img/upload/".$place->userId.'/'.$place->id.'/'.$place->cover);
        $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];
        ?>
        <div class="<?php echo $class; ?> icon "><img src="<?php echo $img; ?>" alt=""></div>
        <a href="place.php?id=<?php echo $place->id; ?>"><img  class="<?php echo $class; ?>" src="<?php echo  $newSrc; ?>" alt=""></a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'template/footer.php';?>