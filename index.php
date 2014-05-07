<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";



$stm = $dbh->query("SELECT * FROM places ORDER BY time DESC;");
$response = $stm->fetchAll();


include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<div class="container">
  <div class="row">
    <h1>Hallo <?php echo $_SESSION['firstname'];?>,</h1>
    <div class='col-md-5'>
      <p>schön dass du deine Lieblingorte mit anderen Teilen willst. Beginne am Besten gleich damit neue Orte einzutragen.</p>
      <p><a href="newPlace.php" class=" btn ">Neuen Ort eintragen &raquo;</a></p>
    </div>
    <div class='col-md-5'>
    <p>Beginne am Besten auch gleich damit dein Profil mit einer Beschreibung und einem Profilbild zu vervollständigen.</p>
    <p>
      <form action="editPerson.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $_SESSION['id']; ?>'>
            <input type="submit" name="edit"  class="form-control input-sm" value="mein Profil bearbeiten">
          </form></p>
     </div>

  </div>
  <div class="row">
     <h2>kürzlich geteilte Orte</h2>
    <?php foreach ($response as $place):
    ?>
    <div class='col-md-3'>
      <h3><?php echo $place->name; ?></h3>
      <p><img src="img/upload/<?php echo $place->userId.'/'.$place->id.'/'.$place->cover; ?>" alt=""></p>
      <p>
        <a href="place.php?id=<?php echo $place->id; ?>"> >> mehr Details</a>
      </p>
    </div>

  <?php endforeach; ?>



</div>
</div>



<?php
include 'template/footer.php';
?>px