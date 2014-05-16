<?php
include 'system/php/functions.php';
checklogin();


$pagetitle = "Meine Lieblingsorte";

$name = "";
$description ="";
$category = "";
$latlang = "";
$file = "";
$public = "";


$error1 = "";
$error2 = "";
$error3 = "";
$error4 = "";

if(isset($_POST['submit']))
{

  $name = $_POST['name'];
  $description =  nl2br($_POST['description']);
  $description = strip_tags($description, '<p><b><strong>');


  $category = $_POST['category'];
  $latlang = $_POST['latlang'];
  $filename = basename($_FILES['file']['name']);

  $public = isset($_POST['public']);

  $error1 = checkValue($name,1);
  $error2 = checkValue($description,2);
  $error3 = checkValue($latlang,3);
  $error4 = checkValue($file,4);
  if( $error4 = "")
  {
    $error4 = checkExt($filename);
  }


  if($error1 == "" && $error2 == "" && $error3 == "" && $error4 == "")
  {
    try{


      $sth = $dbh->prepare("INSERT INTO places
        (name, description, category, coordinates, public,userId,cover)
        VALUES (?, ?, ?, ?, ?,?,?);");
      $sth->execute(array($name,$description,$category,$latlang,$public,$_SESSION['id'],basename($_FILES['file']['name'])));

      uploadPlaceImage($dbh->lastInsertId());

    }
    catch (Exception $e) {
      die("Problem with inserting Data!" . $e->getMessage() );
    }

   $_SESSION['placeId'] =  $dbh->lastInsertId();
   header("Location: crop.php");
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
  errorMsg2 = "bitte wähle ein Foto für deinen Ort aus";

  //alert(document.getElementById("input4").value);
  for (var i =  1; i < 5; i++) {
  //reset all errors
  document.getElementById([i]).innerHTML = "";
  if( document.getElementById("input"+[i]).value == "")
  {
    if(i == 3)
      document.getElementById([i]).innerHTML = errorMsg1;
    else if( i == 4)
      document.getElementById([i]).innerHTML = errorMsg2;
    else
      document.getElementById([i]).innerHTML = errorMsg;

    noError = false;
  }else{

   if( i == 4)
   {
    var ext = document.getElementById("input"+[i]).value.split(".");

    if(ext[ext.length-1].toLowerCase()  != "png" && ext[ext.length-1].toLowerCase() != "jpg")
    {
      document.getElementById([i]).innerHTML = "ungültige Dateiendung!";
      noError = false;
    }

  }

}

}
return noError;

}

</script>

<div class="container">
  <div class="hero-unit">
    <h1>neuen Lieblingsort eintragen</h1><br>
  </div>

  <form class="form-horizontal" action="newPlace.php" method="post"  onsubmit="return chkForm()" enctype="multipart/form-data">

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
          <?php
          $allCategories = getAllCategories($dbh);
          foreach ($allCategories as $cat):?>
              <option value="<?php echo $cat->id; ?>">  <?php echo $cat->category; ?></option>
          <?php endforeach;?>
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
    <label for="input4" class="col-sm-2 control-label" >Foto *</label>
    <div class="col-sm-7">
      <input  name="file" type="file" id="input4" value="">
      <span class="error-inline" id="4"><?php echo $error4; ?></span>
    </div>
  </div>


  <div class="form-group">
    <label class="col-sm-2 control-label" for="submit">* Pflichtfelder</label>
    <div class="col-sm-2">
      <input  class="form-control" type="submit" name="submit" value="Eintragen" >
    </div>
  </div>

</form>
</div>


<script type="text/javascript">

// create a map in the "map" div, set the view to a given place and zoom
var map= L.map('map').setView([47.2715, 11.2489], 14);
// add an OpenStreetMap tile layer
map.locate({setView: true, maxZoom: 16});
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);




var marker_set = false;

function onMapClick(e) {

  if(!marker_set)
  {
    marker = new L.Marker(e.latlng, {draggable:true});
    map.addLayer(marker);
    marker_set = true;

    document.getElementById('input3').value = marker.getLatLng();
        //set eventhanlder on marker that cooridnats will be updated when marker is moved
        marker.on('dragend', update);
      }
      else
      {
        marker.bindPopup("Du hast bereits einen Ort markiert.<br> Du kannst mich aber einfach verschieben.").openPopup();
      }

    }

    function update(e)
    {
     document.getElementById('input3').value = marker.getLatLng();
   }

   map.on('click', onMapClick);

   var osmGeocoder = new L.Control.OSMGeocoder();

   map.addControl(osmGeocoder);

   </script>


   <?php
   include 'template/footer.php';
   ?>