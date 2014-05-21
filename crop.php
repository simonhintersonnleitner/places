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

$src = "";
$id = "";

if(isset($_POST['cropPlace']))
{
  $response = getPlaceData($_POST['id'],$dbh);
  $src  = "img/upload/{$response->userId}/{$response->id}/{$response->cover}";
  $_SESSION['src'] = $src;
}
else if(isset($_POST['cropUser']))
{
  $response = getUserData($_POST['id'],$dbh);
  $src  = "img/upload/{$response->id}/{$response->cover}";
  $_SESSION['src'] = $src;
}
else if(isset($_SESSION['placeId']))
{
  $response = getPlaceData($_SESSION['placeId'],$dbh);
  $src  = "img/upload/{$response->userId}/{$response->id}/{$response->cover}";
  $_SESSION['src'] = $src;
  unset($_SESSION['placeId']);
}
else if(isset($_SESSION['userId']))
{
  $response = getUserData($_SESSION['userId'],$dbh);
  $src  = "img/upload/{$response->id}/{$response->cover}";
  $_SESSION['src'] = $src;
  unset($_SESSION['userId']);
}


if (isset($_POST['cropNow']))
{
  $targ_w = $targ_h = 500;
  $jpeg_quality = 90;
  $src = $_SESSION['src'];
  unset($_SESSION['src']);

  $srcParts = pathinfo($src);



  if(strtolower($srcParts['extension']) == 'jpg')
     $img_r = imagecreatefromjpeg($src);
   else if (strtolower($srcParts['extension']) == 'png')
     $img_r = imagecreatefrompng($src);
   else
      exit;

   $size = getimagesize($src);
   $width =  $size[0];
   $height = $size[1];

  $x = $_POST['x'];
  $y = $_POST['y'];
  $w = $_POST['w'];
  $h = $_POST['h'];
  $real_x = $_POST['real_x'];
  $real_y = $_POST['real_y'];


  //handle if pic is scaled eg. on mobile devices
  $zoomFactor = $width / $real_x;
  $x = intval($x) * $zoomFactor;
  $y = intval($y) * $zoomFactor;
  $w = intval($w) * $zoomFactor;
  $h = intval($h) * $zoomFactor;

 // echo  $x." ". $y." ". $w." ". $h;

  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

  imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);

  $newSrc = $srcParts['dirname'] . '/' . $srcParts['filename'] . '_croped.'. $srcParts['extension'];
  if(strtolower($srcParts['extension']) == 'jpg')
     imagejpeg($dst_r,$newSrc);
   else if (strtolower($srcParts['extension']) == 'png')
     imagepng($dst_r,$newSrc);

  imagedestroy($img_r);

   header("Location:index.php");
   exit;

}

include 'template/beginHeader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<script src="system/jcrop/js/jquery.Jcrop.js"></script>
<link rel="stylesheet" href="system/jcrop/css/jquery.Jcrop.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="system/css/crop.css">
<?php
include 'template/endHeader.php';
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
  //or however you get a handle to the IMG
  var width = $("#cropbox").width();
  var height = $("#cropbox").height();

    $('#real_x').val(width);
    $('#real_y').val(height);

  //alert("x: "+c.x+" y: "+c.y+" w: "+c.w+" h: "+c.h+"width: "+width+" height: "+height);

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
  <img  src="<?php echo $src; ?>" id="cropbox">

  <p><form action="crop.php" method="post" onsubmit="return checkCoords();">
      <input type="hidden" id="x" name="x" />
      <input type="hidden" id="y" name="y" />
      <input type="hidden" id="w" name="w" />
      <input type="hidden" id="h" name="h" />
      <input type="hidden" id="real_x" name="real_x" />
      <input type="hidden" id="real_y" name="real_y" />
      <input type="submit" name="cropNow" value="Fertig" class="btn btn-default" />
  </form></p>
</div>



<?php
include 'template/footer.php';
?>