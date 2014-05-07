<?php
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

$stm = $dbh->prepare("SELECT * FROM user WHERE id = ?");
$stm->execute(array($id));
$response = $stm->fetch();

$stm = $dbh->prepare("SELECT * FROM places WHERE public ='1' AND userId = ? ORDER BY time DESC ;");
$stm->execute(array($id));
$response1 = $stm->fetchAll();


$pagetitle = $response->firstname." ".$response->lastname;


include 'template/beginheader.php';
?>
<link rel="stylesheet" type="text/css" href="system/css/index.css">
<?php
include 'template/endheader.php';
include 'template/menue.php';
?>



<div class="container">
  <h1><?php echo $response->firstname." ".$response->lastname; ?>
    <?php if($id == $_SESSION['id'] ||  $_SESSION['isAdmin'] == 1 ):?>
  <form action="editPerson.php" method="post" class="form-inline">
            <input type="hidden" name="id" value='<?php echo $_SESSION['id']; ?>'>
            <input type="submit" name="edit"  class="form-control input-sm" value="Profil bearbeiten">
          </form>
        <?php   endif; ?>
</h1><small> - registiert seit <?php echo $response->time;?></small><br><br>
  <div class="row">
    <div class="col-md-3">
      <p><?php
      if($response->description != null)
        echo $response->description;
      else
        echo "Leider keine Beschreibung verfügbar!";
      ?>
    </p>
    </div>
    <div class="col-md-8">
      <p >PHOTO!!</p>
    </div>
  </div>

  <h3><?php echo $response->firstname;?>'s Lieblingsorte</h3>
  <table class="table">
    <?php foreach ($response1 as $place):?>
    <tr>
      <td>
         <a href="place.php?id=<?php echo $place->id; ?>"><?php echo $place->name;?></a>
      </td>
    </tr>
  <?php endforeach;?>
  </table>
</div>

<?php
include 'template/footer.php';
?>