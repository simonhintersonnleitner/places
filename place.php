<?php
include 'system/php/functions.php';
checklogin();

$pagetitle = "Ortsprofil";


if(isset($_GET['id']))
{

  $id = $_GET['id'];

  $stm = $dbh->prepare("SELECT * FROM places WHERE id = ?");
  $stm->execute(array($id));
  $response = $stm->fetch();

  $pagetitle = $response->name;

  $id = $response->userId;

  $stm = $dbh->prepare("SELECT id,firstname, lastname,isAdmin FROM user WHERE id = ?");
  $stm->execute(array($id));
  $response1 = $stm->fetch();


}

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<div class="container">
  <h1><?php echo $response->name;  ?>
    <?php if($response1->id == $_SESSION['id'] ||  $_SESSION['isAdmin'] == 1 ): ?>
   <form action="editPlace.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo  $response->id; ?>'>
            <input type="submit" name="edit"  class="form-control input-sm" value="Ort bearbeiten">
          </form>
        <?php endif; ?>
  </h1><small> - eingetragen von
  <a href='person.php?id=<?php echo $response1->id; ?>'>
    <?php echo $response1->firstname." ".$response1->lastname;?></a></small><br><br>
  <div class="row">
    <div class="col-md-3">
      <p ><?php echo $response->description; ?></p>
    </div>
    <div class="col-md-8">
     <img src="img/upload/<?php echo $response1->id."/". $response->id."/".$response->cover; ?>" alt="">
    </div>
  </div>
  <h3>Map</h3>
  <div id="map"></div>
  <small>eingetragen am <?php echo $response->time; ?></small>
</div>


<script type="text/javascript">

// create a map in the "map" div, set the view to a given place and zoom

<?php
//remove LatLng( ) from coordinates
$toRemove = array("LatLng(", ")");
$coordinates = str_replace($toRemove, "",$response->coordinates);
?>

var map= L.map('map').setView([<?php echo $coordinates;?>], 14);
// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

    marker = new L.Marker([<?php echo $coordinates;?>], {draggable:false});
    map.addLayer(marker);
    marker.bindPopup('<?php echo $response->name;?>').openPopup();


function onMapClick(e) {

  map.setView([<?php echo $coordinates;?>], 14);
}



map.on('click', onMapClick);




</script>


      <?php
      include 'template/footer.php';
      ?>