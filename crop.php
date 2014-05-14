<?php
include 'system/php/functions.php';
checklogin();





$pagetitle = "Meine Lieblingsorte";


/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

$src ="";
 $id ="";
if(isset($_POST['crop']))
{
  $response = getPlaceData($_POST['id'],$dbh);
  $src  = "img/upload/{$response->userId}/{$response->id}/{$response->cover}";
}
else if(isset($_SESSION['placeId']))
{

  $response = getPlaceData($_SESSION['placeId'],$dbh);
  $src  = "img/upload/{$response->userId}/{$response->id}/{$response->cover}";
  unset($_SESSION['placeId']);
}

function getPlaceData($id,$dbh)
{
  $stm = $dbh->prepare("SELECT * FROM places WHERE id = ?");
  $stm->execute(array($id));
  $response = $stm->fetch();
  return $response;
}

if (isset($_POST['cropNow']))
{
  $targ_w = $targ_h = 500;
  $jpeg_quality = 90;
  $src = $_POST['src'];
  $srcParts = pathinfo($src);

  if($srcParts['extension'] == 'jpg')
     $img_r = imagecreatefromjpeg($src);
   else if ($srcParts['extension'] == 'png')
     $img_r = imagecreatefrompng($src);
   else
      exit;

  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

  imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
  $targ_w,$targ_h,$_POST['w'],$_POST['h']);

  $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];

  //if $srcParts['extension'] =="jpgp";

  if($srcParts['extension'] == 'jpg')
     imagejpeg($dst_r,$newSrc);
   else if ($srcParts['extension'] == 'png')
    imagepng($dst_r,$newSrc);

  imagedestroy($img_r);

   header("Location:index.php");
   exit;

}

include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<script src="system/jcrop/js/jquery.Jcrop.js"></script>
<link rel="stylesheet" href="system/jcrop/css/jquery.Jcrop.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="system/css/crop.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>

<script type="text/javascript">

  $(function(){

    $('#cropbox').Jcrop({
      aspectRatio: 1,
      onSelect: updateCoords
    });

  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };

</script>

<div class="container">
    <h1>Bildausschnitt festlegen</h1>
  <img src="<?php echo $src; ?>" id="cropbox">

  <p><form action="crop.php" method="post" onsubmit="return checkCoords();">
      <input type="hidden" id="x" name="x" />
      <input type="hidden" id="y" name="y" />
      <input type="hidden" id="w" name="w" />
      <input type="hidden" id="h" name="h" />
      <input type="hidden" name="src" value="<?php echo  $src; ?>"/>
      <input type="submit" name="cropNow" value="Fertig" class="btn" />
  </form></p>
</div>



<?php
include 'template/footer.php';
?>