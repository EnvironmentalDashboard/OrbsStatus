<html>
<body>

  <?php
  $newvalue=substr($_POST["relval"],0,1);
  $newvalueuse=(floatval($newvalue)/4)*100;
  $iduse=substr($_POST["relval"],1,4);
  $nameu=substr($_POST["relval"],5,-1).substr($_POST["relval"],-1);
  $nameuse="".$nameu."";
  require 'db.php';
  $con = "mysql:host={$host};dbname={$dbname};charset=utf8;port=3306";
  try {
    $db = new PDO($con, "{$username}", "{$password}"); // cast as string bc cant pass as reference
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }
  catch (PDOException $e) { die($e->getMessage()); }
  $updateinput='UPDATE relative_values SET relative_value='.$newvalueuse.' WHERE id='.$iduse;
  $updatedisabled='UPDATE orbs SET disabled=1 WHERE name="'.$nameuse.'"';;
  $db->query($updateinput);
  //$db->query($updatedisabled); If you uncomment it sets disable to 1 when submited but
  //doesn't fix the refresh problem
  ?>

</body>
</html>
