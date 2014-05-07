<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";

$name = "";
$description ="";
$category = "";
$latlang = "";
$public = "";
$id = "";

$error1 = "";
$error2 = "";
$error3 = "";


if(isset($_POST['edit']))
{

  $id = $_POST['id'];
  $_SESSION['placeID'] = $id;
  $stm = $dbh->prepare("SELECT * FROM places WHERE id = ?");
  $stm->execute(array($id));
  $response = $stm->fetch();

  $name = $response->name;
  $description = $response->description;
  $category = $response->category;
  $latlang = $response->coordinates;
  $public = $response->public;
}


if(isset($_POST['submit']))
{

  $name = $_POST['name'];
  $description =$_POST['description'];
  $description = strip_tags($description, '<p><b><strong>');

  $category = $_POST['category'];
  $latlang = $_POST['latlang'];
  $public = isset($_POST['public']);

  $error1 = checkValue($name,1);
  $error2 = checkValue($description,2);
  $error3 = checkValue($latlang,3);
  $id = $_SESSION['placeID'];

  if($error1 == "" && $error2 == "" && $error3 == "")
  {
    try{
      echo $id;
      $sth = $dbh->prepare("UPDATE places SET
        name = ?, description = ?, category = ?, coordinates = ?, public = ?
        WHERE id = ?;");
      $sth->execute(array($name,$description,$category,$latlang,$public,$id));
    }
    catch (Exception $e) {
      die("Problem with updating Data!" . $e->getMessage() );
    }

    header("Location: place.php?id={$id}");
    exit;


  }


}

function checkValue ($value,$pos)
{
  if($value == "")
  {
    if($pos != 3)
    {
      return  "dieses Feld darf nicht leer sein";
    }
    else
    {
      return  "wähle bitte einen Punkt auf der Karte";
    }
  }
  else
    return "";
}




include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<script type="text/javascript">

function chkForm () {

  noError = true;
  errorMsg = "dieses Feld darf nicht leer sein";
  errorMsg1 = "wähle bitte einen Punkt auf der Karte";


  for (var i =  1; i < 4; i++) {
  //reset all errors
  document.getElementById([i]).innerHTML = "";
  if( document.getElementById("input"+[i]).value == "")
  {
    if(i != 3)
      document.getElementById([i]).innerHTML = errorMsg;
    else
      document.getElementById([i]).innerHTML = errorMsg1;

    noError = false;
  }
}
return noError;

}

</script>

<div class="container">
  <div class="hero-unit">
    <h1>Ort bearbeiten</h1><br>
  </div>

  <form class="form-horizontal" action="editPlace.php" method="post" role="form" onsubmit="return chkForm()">

    <div class="form-group">
      <label for="input1" class="col-sm-2 control-label" >Name*</label>
      <div class="col-sm-7">
        <input type="text" class="form-control" name="name" id="input1" placeholder="Name" value="<?php echo $name; ?>">
        <span class="error-inline" id="1"><?php echo $error1; ?></span>
      </div>
    </div>

    <div class="form-group">
      <label for="input2" class="col-sm-2 control-label" >Beschreibung*</label>
      <div class="col-sm-7">
        <textarea class="form-control" rows="5" name="description" id="input2" placeholder="Beschreibung"><?php echo $description; ?></textarea>
        <span class="error-inline" id="2"><?php echo $error2; ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="category"><b>Kategorie*</b></label>
      <div class="col-sm-7">
        <select class="form-control" name="category" id="category">
          <option value="1" <?php if($category == 1) echo "selected"; ?>>Bar</option>
          <option value="2" <?php if($category == 2) echo "selected"; ?>>Restaurant</option>
          <option value="3" <?php if($category == 3) echo "selected"; ?>>Hotel</option>
          <option value="4" <?php if($category == 4) echo "selected"; ?>>Shop</option>
          <option value="5" <?php if($category == 5) echo "selected"; ?>>Stadt</option>
          <option value="6" <?php if($category == 6) echo "selected"; ?>>Natur</option>
          <option value="7" <?php if($category == 7) echo "selected"; ?>>Aussicht</option>
          <option value="8" <?php if($category == 8) echo "selected"; ?>>Sontige</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="input3" class="col-sm-2 control-label" >Karte*</label>
      <div class="col-sm-7">
        <div id="map">
        </div>
        <p>zum Markieren einfach in die Map klicken.</p>
      </div>
      <input type="hidden" id="input3" name="latlang">
       <span class="error-inline" id="3"><?php echo $error3; ?></span>
    </div>

    <div class="form-group">
      <label for="public" class="col-sm-2 control-label" ></label>
      <div class="col-sm-7">
         <input  type="checkbox" name="public" value="1" checked> Ort soll für alle sichtbar sein
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="submit">* Pflichtfelder</label>
      <div class="col-sm-2">
        <input  class="form-control" type="submit" name="submit" value="Fertig" >
      </div>
    </div>

  </form>
</div>


<script type="text/javascript">

  <?php

  $toRemove = array("LatLng(", ")");
  $coordinates = str_replace($toRemove,"",$response->coordinates);
  ?>

// create a map in the "map" div, set the view to a given place and zoom
var map= L.map('map').setView([<?php echo $coordinates; ?>], 14);
// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);





    marker = new L.Marker([<?php echo $coordinates; ?>], {draggable:true});
    map.addLayer(marker);

    document.getElementById('input3').value = marker.getLatLng();
          //set eventhanlder on marker that cooridnats will be updated when marker is moved
    marker.on('dragend', update);


 function update(e)
 {
   document.getElementById('input3').value = marker.getLatLng();
 }



var osmGeocoder = new L.Control.OSMGeocoder();

map.addControl(osmGeocoder);

</script>


      <?php
      include 'template/footer.php';
      ?>