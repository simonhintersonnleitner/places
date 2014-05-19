<?php
include 'system/php/functions.php';
checklogin();

$pagetitle = "Meine Lieblingsorte";

if(isset($_GET['userId']))
{
  try{
  $stm = $dbh->prepare("SELECT * FROM places WHERE userId = ?;");
  $stm->execute(array($_GET['userId']));
  $response = $stm->fetchAll();

} catch (Exception $e) {
  die("Problem" . $e->getMessage() );
}

}
else
{

  try{
  $stm = $dbh->query("SELECT * FROM places ;");
  $response = $stm->fetchAll();
} catch (Exception $e) {
  die("Problem" . $e->getMessage() );
}


}

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<div class="container">
  <div class="hero-unit">
    <h3>
    <?php
      if(isset($_GET['userId']))
         if($name = getFirstnameById($dbh,$_GET['userId']))
            echo $name."'s Lieblingsorte";
      else
        echo "Mapansicht";
     ?>
   </h3>

  </div>
  <form class="form-inline" role="form">
    <?php
    $count=0;
    $allCategories = getAllCategories($dbh);
    foreach ( $allCategories as $cat):
      $count ++;
    ?>
    <div class="checkbox">
      <label>
        <input type="checkbox" checked id="<?php echo $count; ?>" value="<?php echo $cat->id; ?>">  <?php echo $cat->category; ?>
      </label>
    </div>
  <?php endforeach;?>
</form>
<div id="map-big"></div>
</div>


<script type="text/javascript">

options = new Array(0,1,1,1,1,1,1,1,1,1);

for (var i = 1; i <= <?php echo  $count; ?>; i++) {
 var h = document.getElementById(i);
 h.addEventListener('click',changeCheckboxHandler,false);
}


function changeCheckboxHandler() {

  options[this.id] = this.checked;
  //alert(this.id + " " + this.checked);

  updateMarker();

}

// create a map in the "map" div, set the view to a given place and zoom

var map = L.map('map-big').setView([47.2715, 11.2489], 14);
// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

//creat an array for each group of markers
  for (var i = 1; i <= 9; i++) {
    this["group"+i] = new Array();
}

<?php
$count = 0;
foreach ($response as $place):
$toRemove = array("LatLng(", ")");
$coordinates = str_replace($toRemove,"",$place->coordinates);
?>
var i = group<?php echo $place->category;?>.length;

group<?php echo $place->category;?>[i] = new L.Marker([<?php echo $coordinates; ?>], {draggable:false});
group<?php echo $place->category;?>[i].bindPopup('<?php echo $place->name;?><br><a href="place.php?id=<?php echo $place->id; ?>"><img id=map-img src= <?php echo getCroppedPlaceImageNameById($place->id,$dbh); ?> alt=""></a>').openPopup();

<?php  $count ++;
endforeach; ?>

//add all marker groups to map
function updateMarker()
{
  var allGroups = new Array();

for (var i = 1; i <= 9; i++)
{
    for(var x = 0; x < this["group"+i].length; x++)
    {
      if(options[i] == 1)
         map.addLayer(this["group"+i][x]);
       else
         map.removeLayer(this["group"+i][x]);
    }
    if(options[i] == 1)
      allGroups = allGroups.concat(this["group"+i]);
}
  //set view that all marker are visible
  var group = new L.featureGroup(allGroups);
  map.fitBounds(group.getBounds());

}

//first time call
updateMarker();







</script>


<?php
include 'template/footer.php';
?>